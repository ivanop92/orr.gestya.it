<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Invia un SMS tramite messaggisms.com (gruppo Openia).
     * @return array ['ok' => bool, 'message' => string, 'response' => mixed]
     */
    public static function send(string $numero, string $messaggio): array
    {
        $token  = env('MESSAGGISMS_TOKEN', '693ff94f8c8b1f97500ccb65');
        $sender = env('MESSAGGISMS_SENDER', 'KAIROS');
        $url    = 'https://ws.messaggisms.com/messages/';

        $numero = self::normalizzaNumero($numero);
        if (!$numero) {
            return ['ok' => false, 'message' => 'Numero di telefono non valido'];
        }

        // L'API rifiuta E.164 con '+', vuole il prefisso paese senza '+'
        $numeroApi = ltrim($numero, '+');

        $payload = [
            'sender'     => $sender,
            'recipients' => [$numeroApi],
            'body'       => $messaggio,
        ];

        try {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => json_encode($payload),
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer '.$token,
                    'Content-Type: application/json',
                    'Accept: application/json',
                ],
                CURLOPT_TIMEOUT        => 15,
                CURLOPT_CONNECTTIMEOUT => 5,
            ]);
            $body = curl_exec($ch);
            $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $err  = curl_error($ch);
            curl_close($ch);

            // Log completo per debug
            Log::info('SMS messaggisms', [
                'http_code' => $code,
                'sender'    => $sender,
                'recipient' => $numeroApi,
                'message'   => $messaggio,
                'response'  => $body,
                'curl_err'  => $err,
            ]);

            if ($err) {
                return ['ok' => false, 'message' => 'Errore di connessione: '.$err];
            }

            $resp = json_decode($body, true);
            if ($code >= 200 && $code < 300) {
                return ['ok' => true, 'message' => 'SMS inviato', 'response' => $resp];
            }

            // Estrai messaggio errore dalla risposta API se presente
            $apiErr = is_array($resp) ? (
                $resp['message'] ?? $resp['error'] ?? ($resp['errors'] ?? json_encode($resp))
            ) : $body;
            if (is_array($apiErr)) $apiErr = json_encode($apiErr);

            return ['ok' => false, 'message' => 'API HTTP '.$code.' — '.$apiErr];
        } catch (\Exception $e) {
            Log::warning('SMS exception', ['err' => $e->getMessage()]);
            return ['ok' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Normalizza un numero italiano: rimuove spazi/trattini, aggiunge +39 se mancante.
     */
    public static function normalizzaNumero(string $n): ?string
    {
        $n = preg_replace('/[\s\-\.]/', '', $n);
        if (!$n) return null;
        if (strpos($n, '+') === 0) return $n;
        if (strpos($n, '00') === 0) return '+'.substr($n, 2);
        if (strpos($n, '39') === 0 && strlen($n) >= 11) return '+'.$n;
        return '+39'.$n; // assume IT
    }
}

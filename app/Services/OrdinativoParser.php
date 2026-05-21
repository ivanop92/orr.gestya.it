<?php

namespace App\Services;

class OrdinativoParser
{
    /**
     * Estrae i campi rilevanti dal testo di un ordinativo cliente (PDF testuale).
     * Best-effort: i campi non trovati restano null. L'utente correggera nel form.
     */
    public static function parsePdf(string $path): array
    {
        $out = [
            'raw_text'              => '',
            'numero_ordine_cliente' => null,
            'codice_cuu'            => null,
            'impianto'              => null,
            'odl_numero'            => null,
            'pdm_riferimento'       => null,
            'data_apertura'         => null,
            'reason_intake'         => null,
            'automezzo'             => null,
            'note'                  => null,
        ];

        if (!file_exists($path)) return $out;

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($path);
            $text = $pdf->getText();
        } catch (\Throwable $e) {
            return $out;
        }

        $text = preg_replace("/[ \t]+/", ' ', $text);
        $text = preg_replace("/\r\n?/", "\n", $text);
        $out['raw_text'] = $text;

        // Numero ordine: cerco label tipo "N. Ordine", "Ordine n", "Order No", "Ordinativo"
        if (preg_match('/(?:N\.?\s*Ordine|Ordinativo\s*N\.?|Ordine\s*N\.?|Order\s*N(?:o|umber)?\.?|Numero\s*Ordine|N\.?\s*Ordinativo)\s*[:#]?\s*([A-Z0-9\-\/]{4,30})/i', $text, $m)) {
            $out['numero_ordine_cliente'] = trim($m[1]);
        }

        // CUU formato X.Y.Z o X.Y.Z.W
        if (preg_match('/(?:CUU|Codice\s*(?:Avaria|CUU))[\s:#]*([0-9]+(?:\.[0-9]+){2,4})/i', $text, $m)) {
            $out['codice_cuu'] = $m[1];
        } elseif (preg_match('/\b([0-9]+\.[0-9]+\.[0-9]+(?:\.[0-9]+)?)\b/', $text, $m)) {
            $out['codice_cuu'] = $m[1];
        }

        // Impianto: match diretto su impianti noti ORR
        $impianti = ['NOLA', 'BARI', 'SAN VITALIANO', 'MARCIANISE'];
        foreach ($impianti as $imp) {
            if (stripos($text, $imp) !== false) {
                $out['impianto'] = $imp;
                break;
            }
        }

        // OdL ORR
        if (preg_match('/(?:OdL|O\.d\.L\.?|N\.?\s*OdL|Ordine\s*di\s*Lavoro)\s*[:#]?\s*([0-9]{2,10})/i', $text, $m)) {
            $out['odl_numero'] = $m[1];
        }

        // PdM riferimento: VPI/EMG codes (default VPI)
        if (preg_match('/\b(VPI(?:-EMG)?(?:\s*\d{1,2})?)\b/i', $text, $m)) {
            $out['pdm_riferimento'] = strtoupper(trim($m[1]));
        }

        // Data: gg/mm/aaaa o aaaa-mm-gg
        if (preg_match('/\b([0-3]?\d)[\/\-]([01]?\d)[\/\-](20\d{2})\b/', $text, $m)) {
            $g = str_pad($m[1], 2, '0', STR_PAD_LEFT);
            $mm = str_pad($m[2], 2, '0', STR_PAD_LEFT);
            $out['data_apertura'] = $m[3] . '-' . $mm . '-' . $g;
        } elseif (preg_match('/\b(20\d{2})-([01]\d)-([0-3]\d)\b/', $text, $m)) {
            $out['data_apertura'] = $m[1] . '-' . $m[2] . '-' . $m[3];
        }

        // Carro / matricola UIC: 12 cifre con eventuali spazi, oppure formato "31 80..."
        if (preg_match('/\b(\d{2}[\s\.\-]?\d{2}[\s\.\-]?\d{4}[\s\.\-]?\d{3}[\s\.\-]?\d)\b/', $text, $m)) {
            $out['automezzo'] = trim($m[1]);
        } elseif (preg_match('/(?:Carro|Vagone|Wagon|N\.?\s*Carro|Matricola)\s*[:#]?\s*([A-Z0-9\-\/\s]{6,30})/i', $text, $m)) {
            $out['automezzo'] = trim($m[1]);
        }

        // Motivo / Reason intake (riga dopo "Motivo", "Causa", "Reason", "Anomalia")
        if (preg_match('/(?:Motivo|Causa|Reason|Anomalia|Difetto|Avaria)\s*[:#]?\s*(.+?)(?:\n|$)/i', $text, $m)) {
            $r = trim($m[1]);
            if (strlen($r) > 4 && strlen($r) < 250) {
                $out['reason_intake'] = $r;
            }
        }

        return $out;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Services\SmsService;

class FirmaController extends Controller
{
    /**
     * Pagina pubblica del preventivo per il cliente (token URL).
     */
    public function preventivo($token, \Illuminate\Http\Request $request)
    {
        $dotes = DB::table('dotes')->where('firma_token', $token)->first();
        if (!$dotes) abort(404, 'Preventivo non trovato');

        $righe = DB::table('dorig')
            ->where('id_dotes', $dotes->id)
            ->orderBy('n_riga')
            ->get();

        $azienda = DB::table('aziende')->where('id', $dotes->id_azienda)->first();

        // Modalita' sola lettura se ?view=1 (toggle firma OFF nell'invio email)
        $viewOnly = $request->input('view') === '1';

        return view('firma.preventivo', compact('dotes', 'righe', 'azienda', 'viewOnly'));
    }

    /**
     * Genera OTP e lo invia al numero indicato.
     */
    public function invia_otp($token, Request $request)
    {
        $dotes = DB::table('dotes')->where('firma_token', $token)->first();
        if (!$dotes) return response()->json(['ok' => false, 'error' => 'Preventivo non trovato'], 404);
        if (!empty($dotes->firmato_il)) {
            return response()->json(['ok' => false, 'error' => 'Preventivo già firmato']);
        }

        $telefono = trim((string) $request->input('telefono', ''));
        if ($telefono === '') return response()->json(['ok' => false, 'error' => 'Telefono mancante']);

        $numero = SmsService::normalizzaNumero($telefono);
        if (!$numero) return response()->json(['ok' => false, 'error' => 'Numero non valido']);

        // Rate-limit: max 3 invii ogni 5 minuti
        if ($dotes->firma_otp_inviato_il && (time() - strtotime($dotes->firma_otp_inviato_il)) < 60) {
            return response()->json(['ok' => false, 'error' => 'Aspetta 60 secondi prima di richiedere un nuovo codice']);
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $azienda = DB::table('aziende')->where('id', $dotes->id_azienda)->first();
        $mittente = $azienda->ragione_sociale ?? 'Gestya';
        $msg = $mittente.': il tuo codice di firma preventivo n.'.$dotes->numero_doc.' e\' '.$otp.'. Valido 10 minuti.';

        $res = SmsService::send($numero, $msg);
        if (!$res['ok']) {
            return response()->json(['ok' => false, 'error' => 'Invio SMS fallito: '.$res['message']]);
        }

        DB::table('dotes')->where('id', $dotes->id)->update([
            'firma_otp_hash'      => password_hash($otp, PASSWORD_DEFAULT),
            'firma_otp_inviato_il'=> date('Y-m-d H:i:s'),
            'firma_otp_tentativi' => 0,
            'firma_telefono'      => $numero,
        ]);

        return response()->json(['ok' => true, 'message' => 'Codice OTP inviato a '.$numero]);
    }

    /**
     * Il cliente segnala un problema con il preventivo (no firma, no OTP).
     */
    public function segnala($token, Request $request)
    {
        $dotes = DB::table('dotes')->where('firma_token', $token)->first();
        if (!$dotes) return response()->json(['ok' => false, 'error' => 'Preventivo non trovato'], 404);

        $testo    = trim((string) $request->input('testo', ''));
        $contatto = trim((string) $request->input('contatto', ''));
        if ($testo === '') return response()->json(['ok' => false, 'error' => 'Inserisci una descrizione del problema']);

        DB::table('dotes_segnalazioni')->insert([
            'id_dotes'   => $dotes->id,
            'id_azienda' => $dotes->id_azienda,
            'testo'      => $testo,
            'contatto'   => $contatto ?: null,
            'ip'         => $request->ip(),
            'created_at' => date('Y-m-d H:i:s'),
        ]);

        // Log anche sull'intervento collegato
        $intervento = DB::table('interventi')->where('id_dotes_preventivo', $dotes->id)->first();
        if ($intervento) {
            DB::table('interventi_log')->insert([
                'id_intervento' => $intervento->id,
                'id_azienda'    => $intervento->id_azienda,
                'id_utente'     => null,
                'step'          => $intervento->step_corrente,
                'azione'        => 'segnalazione_cliente',
                'note'          => 'Cliente ha segnalato un problema: '.\Illuminate\Support\Str::limit($testo, 200).($contatto ? ' [contatto: '.$contatto.']' : ''),
                'created_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        return response()->json(['ok' => true, 'message' => 'Segnalazione inviata. Verrai ricontattato a breve.']);
    }

    /**
     * Verifica l'OTP e marca il preventivo come firmato.
     */
    public function verifica_otp($token, Request $request)
    {
        $dotes = DB::table('dotes')->where('firma_token', $token)->first();
        if (!$dotes) return response()->json(['ok' => false, 'error' => 'Preventivo non trovato'], 404);
        if (!empty($dotes->firmato_il)) {
            return response()->json(['ok' => false, 'error' => 'Preventivo già firmato']);
        }

        $otp  = trim((string) $request->input('otp', ''));
        $nome = trim((string) $request->input('nome', ''));

        if ($otp === '' || strlen($otp) !== 6) {
            return response()->json(['ok' => false, 'error' => 'OTP non valido']);
        }
        if ($nome === '') {
            return response()->json(['ok' => false, 'error' => 'Inserisci il tuo nome e cognome']);
        }
        if (empty($dotes->firma_otp_hash)) {
            return response()->json(['ok' => false, 'error' => 'Devi prima richiedere il codice OTP']);
        }
        if ($dotes->firma_otp_inviato_il && (time() - strtotime($dotes->firma_otp_inviato_il)) > 600) {
            return response()->json(['ok' => false, 'error' => 'Codice OTP scaduto. Richiedine uno nuovo.']);
        }
        if ($dotes->firma_otp_tentativi >= 5) {
            return response()->json(['ok' => false, 'error' => 'Troppi tentativi falliti. Richiedi un nuovo codice.']);
        }
        if (!password_verify($otp, $dotes->firma_otp_hash)) {
            DB::table('dotes')->where('id', $dotes->id)->increment('firma_otp_tentativi');
            return response()->json(['ok' => false, 'error' => 'Codice errato']);
        }

        // Firma valida
        $now = date('Y-m-d H:i:s');
        DB::table('dotes')->where('id', $dotes->id)->update([
            'firmato_il'      => $now,
            'firmato_da_nome' => $nome,
            'firma_ip'        => $request->ip(),
            'firma_user_agent'=> substr((string) $request->userAgent(), 0, 500),
            'firma_otp_hash'  => null,
        ]);

        // Aggiorna intervento collegato se esiste
        $intervento = DB::table('interventi')
            ->where('id_dotes_preventivo', $dotes->id)
            ->where('id_azienda', $dotes->id_azienda)
            ->first();
        if ($intervento && (int) $intervento->step_corrente === 5) {
            DB::table('interventi')->where('id', $intervento->id)->update([
                'accettato_il'           => $now,
                'step_corrente'          => 6,
                'step_5_completato_il'   => $now,
                'updated_at'             => $now,
            ]);
            DB::table('interventi_log')->insert([
                'id_intervento' => $intervento->id,
                'id_azienda'    => $intervento->id_azienda,
                'id_utente'     => null,
                'step'          => 5,
                'azione'        => 'firmato_dal_cliente',
                'note'          => 'Firmato dal cliente: '.$nome.' ('.$dotes->firma_telefono.')',
                'created_at'    => $now,
            ]);
        }

        return response()->json(['ok' => true, 'message' => 'Preventivo firmato con successo']);
    }
}

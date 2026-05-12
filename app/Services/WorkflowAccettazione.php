<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class WorkflowAccettazione
{
    const STATO_EMESSO        = 'emesso';
    const STATO_IN_REVISIONE  = 'in_revisione';
    const STATO_ACCETTATO     = 'accettato';
    const STATO_RIFIUTATO     = 'rifiutato';
    const STATO_RILAVORAZIONE = 'rilavorazione';
    const STATO_RIAPPROVATO   = 'riapprovato';
    const STATO_FATTURABILE   = 'fatturabile';

    public static function labels(): array
    {
        return [
            self::STATO_EMESSO        => 'Emesso',
            self::STATO_IN_REVISIONE  => 'In Revisione',
            self::STATO_ACCETTATO     => 'Accettato',
            self::STATO_RIFIUTATO     => 'Rifiutato',
            self::STATO_RILAVORAZIONE => 'In Rilavorazione',
            self::STATO_RIAPPROVATO   => 'Riapprovato',
            self::STATO_FATTURABILE   => 'Fatturabile',
        ];
    }

    public static function colori(): array
    {
        return [
            self::STATO_EMESSO        => 'secondary',
            self::STATO_IN_REVISIONE  => 'info',
            self::STATO_ACCETTATO     => 'success',
            self::STATO_RIFIUTATO     => 'danger',
            self::STATO_RILAVORAZIONE => 'warning',
            self::STATO_RIAPPROVATO   => 'success',
            self::STATO_FATTURABILE   => 'primary',
        ];
    }

    /**
     * Mappa azione -> stati di partenza permessi.
     * Lo stato di destinazione è gestito in transiziona() perché "accetta" può
     * portare ad accettato OPPURE riapprovato in base al numero di tentativi.
     */
    public static function azioni(): array
    {
        return [
            'invia_revisione'   => [self::STATO_EMESSO, null, self::STATO_RILAVORAZIONE],
            'accetta'           => [self::STATO_IN_REVISIONE],
            'rifiuta'           => [self::STATO_IN_REVISIONE],
            'rilavora'          => [self::STATO_RIFIUTATO],
            'marca_fatturabile' => [self::STATO_ACCETTATO, self::STATO_RIAPPROVATO],
        ];
    }

    public static function azioniDisponibili(?string $stato_attuale): array
    {
        $disp = [];
        foreach (self::azioni() as $azione => $partenze) {
            if (in_array($stato_attuale, $partenze, true)) {
                $disp[] = $azione;
            }
        }
        return $disp;
    }

    /**
     * Esegue una transizione. Ritorna [bool $ok, string $messaggio].
     */
    public static function transiziona(int $id_dotes, int $id_azienda, string $azione, int $id_utente, ?string $motivo_rifiuto = null): array
    {
        $azioni = self::azioni();
        if (!isset($azioni[$azione])) {
            return [false, 'Azione non valida'];
        }

        $doc = DB::table('dotes')
            ->where('id', $id_dotes)
            ->where('id_azienda', $id_azienda)
            ->first();

        if (!$doc) {
            return [false, 'Documento non trovato'];
        }

        $partenze = $azioni[$azione];
        $statoAttuale = $doc->stato_accettazione ?? null;
        if (!in_array($statoAttuale, $partenze, true)) {
            return [false, 'Transizione "'.$azione.'" non permessa dallo stato attuale'];
        }

        $now = date('Y-m-d H:i:s');
        $update = [];

        switch ($azione) {
            case 'invia_revisione':
                $update['stato_accettazione']  = self::STATO_IN_REVISIONE;
                $update['inviato_revisione_il'] = $now;
                $update['motivo_rifiuto']      = null;
                break;
            case 'accetta':
                $tentativi = (int) ($doc->tentativi ?? 0);
                $update['stato_accettazione']     = $tentativi > 0 ? self::STATO_RIAPPROVATO : self::STATO_ACCETTATO;
                $update['accettato_il']           = $now;
                $update['accettato_da_id_utente'] = $id_utente;
                $update['motivo_rifiuto']         = null;
                break;
            case 'rifiuta':
                $update['stato_accettazione'] = self::STATO_RIFIUTATO;
                $update['rifiutato_il']       = $now;
                $update['motivo_rifiuto']     = $motivo_rifiuto;
                break;
            case 'rilavora':
                $update['stato_accettazione'] = self::STATO_RILAVORAZIONE;
                $update['tentativi']          = (int) ($doc->tentativi ?? 0) + 1;
                break;
            case 'marca_fatturabile':
                $update['stato_accettazione'] = self::STATO_FATTURABILE;
                break;
        }

        DB::table('dotes')
            ->where('id', $id_dotes)
            ->where('id_azienda', $id_azienda)
            ->update($update);

        $labels = self::labels();
        $statoFinale = $update['stato_accettazione'];
        return [true, 'Stato aggiornato a "'.($labels[$statoFinale] ?? $statoFinale).'"'];
    }
}

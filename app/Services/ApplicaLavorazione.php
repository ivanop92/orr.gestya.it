<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class ApplicaLavorazione
{
    /**
     * Applica le righe di una o più lavorazioni-template a un documento dotes.
     * Aggiunge righe in coda. Ricalcola gli aggregati testata.
     *
     * Ritorna [bool $ok, string $messaggio, int $righe_aggiunte].
     */
    public static function applicaA(int $id_dotes, int $id_azienda, array $id_lavorazioni, int $id_utente): array
    {
        $dotes = DB::table('dotes')
            ->where('id', $id_dotes)
            ->where('id_azienda', $id_azienda)
            ->first();

        if (!$dotes) {
            return [false, 'Documento non trovato', 0];
        }

        $maxNRiga = (int) DB::table('dorig')
            ->where('id_dotes', $id_dotes)
            ->where('id_azienda', $id_azienda)
            ->max('n_riga');

        $righeAggiunte = 0;

        foreach ($id_lavorazioni as $id_lav) {
            $id_lav = (int) $id_lav;
            if ($id_lav <= 0) continue;

            $lavorazione = DB::table('lavorazioni')
                ->where('id', $id_lav)
                ->where('id_azienda', $id_azienda)
                ->first();
            if (!$lavorazione) continue;

            $righeTemplate = DB::table('lavorazioni_righe')
                ->where('id_lavorazione', $id_lav)
                ->where('id_azienda', $id_azienda)
                ->orderBy('ordinamento')
                ->get();

            foreach ($righeTemplate as $r) {
                $maxNRiga++;
                $imponibile = (float) $r->imponibile;
                $imposta    = (float) $r->imposta;
                $totale     = $imponibile + $imposta;

                $isOrario = ((float) $r->minuti) > 0;
                $attivita = isset($r->attivita) && (float) $r->attivita > 0 ? (float) $r->attivita : 1;
                $qtaEffettiva = $isOrario
                    ? round(((float) $r->minuti) / 60, 3)
                    : round(((float) $r->qta) * $attivita, 3);

                DB::table('dorig')->insert([
                    'id_azienda'                  => $id_azienda,
                    'id_utente'                   => $id_utente,
                    'id_cliente'                  => $dotes->id_cliente ?? null,
                    'id_dotes'                    => $id_dotes,
                    'id_testata'                  => $id_dotes,
                    'cd_do'                       => $dotes->cd_do,
                    'tipo_documento'              => $dotes->tipo_documento ?? null,
                    'numero_doc'                  => $dotes->numero_doc ?? null,
                    'data_doc'                    => $dotes->data_doc ?? null,
                    'cd_ar'                       => $r->codice,
                    'n_riga'                      => $maxNRiga,
                    'descrizione'                 => $r->descrizione,
                    'qta'                         => $qtaEffettiva,
                    'um'                          => $isOrario ? 'H' : 'PZ',
                    'pu'                          => (float) $r->pu,
                    'pt'                          => (float) $r->pt,
                    'prezzo_unitario'             => (float) $r->pu,
                    'prezzo_totale'               => (float) $r->pt,
                    'prezzo_totale_iva'           => $totale,
                    'iva'                         => (int) $r->aliquota,
                    'imponibile'                  => $imponibile,
                    'imposta'                     => $imposta,
                    'totale'                      => $totale,
                    // Campi lavorazione preservati nella riga del documento
                    'servizio'                    => $r->servizio,
                    'setup_tank'                  => isset($r->setup_tank) ? (int) $r->setup_tank : 0,
                    'attivita'                    => $attivita,
                    'minuti'                      => (float) $r->minuti,
                    'materiale'                   => isset($r->materiale) ? (float) $r->materiale : 0,
                    'descrizione_materiale'       => $r->descrizione_materiale ?? null,
                    'id_lavorazione_origine'      => $id_lav,
                    'id_lavorazione_riga_origine' => $r->id,
                ]);
                $righeAggiunte++;
            }
        }

        self::ricalcolaAggregatiDotes($id_dotes, $id_azienda);

        if ($righeAggiunte === 0) {
            return [false, 'Nessuna riga aggiunta (verifica che le lavorazioni selezionate abbiano righe)', 0];
        }

        return [true, $righeAggiunte . ' righe aggiunte al documento', $righeAggiunte];
    }

    public static function ricalcolaAggregatiDotes(int $id_dotes, int $id_azienda): void
    {
        $agg = DB::table('dorig')
            ->where('id_dotes', $id_dotes)
            ->where('id_azienda', $id_azienda)
            ->selectRaw('IFNULL(SUM(imponibile),0) AS imponibile, IFNULL(SUM(imposta),0) AS imposta, IFNULL(SUM(totale),0) AS totale')
            ->first();

        DB::table('dotes')
            ->where('id', $id_dotes)
            ->where('id_azienda', $id_azienda)
            ->update([
                'imponibile' => $agg->imponibile,
                'imposta'    => $agg->imposta,
                'totale'     => $agg->totale,
            ]);
    }
}

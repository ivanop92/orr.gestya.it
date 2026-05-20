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

    /**
     * Applica un set di SINGOLE righe lavorazione (per id_riga) al documento.
     * Ritorna [bool $ok, string $msg, int $n_righe_aggiunte].
     */
    public static function applicaRigheA(int $id_dotes, int $id_azienda, array $id_righe, int $id_utente): array
    {
        $dotes = DB::table('dotes')->where('id', $id_dotes)->where('id_azienda', $id_azienda)->first();
        if (!$dotes) return [false, 'Documento non trovato', 0];

        $maxNRiga = (int) DB::table('dorig')
            ->where('id_dotes', $id_dotes)
            ->where('id_azienda', $id_azienda)
            ->max('n_riga');
        $aggiunte = 0;

        $righe = DB::table('lavorazioni_righe')
            ->whereIn('id', array_map('intval', $id_righe))
            ->where('id_azienda', $id_azienda)
            ->orderBy('ordinamento')
            ->orderBy('id')
            ->get();

        foreach ($righe as $r) {
            $maxNRiga++;
            $imp = (float) $r->imponibile;
            $tax = (float) $r->imposta;
            $tot = $imp + $tax;
            $isOrario = ((float) $r->minuti) > 0;
            $attivita = $r->attivita > 0 ? (float) $r->attivita : 1;
            $qtaEff = $isOrario ? round(((float) $r->minuti) / 60, 3) : round(((float) $r->qta) * $attivita, 3);

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
                'qta'                         => $qtaEff,
                'um'                          => $isOrario ? 'H' : 'PZ',
                'pu'                          => (float) $r->pu,
                'pt'                          => (float) $r->pt,
                'prezzo_unitario'             => (float) $r->pu,
                'prezzo_totale'               => (float) $r->pt,
                'prezzo_totale_iva'           => $tot,
                'iva'                         => (int) $r->aliquota,
                'imponibile'                  => $imp,
                'imposta'                     => $tax,
                'totale'                      => $tot,
                'servizio'                    => $r->servizio,
                'setup_tank'                  => isset($r->setup_tank) ? (int) $r->setup_tank : 0,
                'attivita'                    => $attivita,
                'minuti'                      => (float) $r->minuti,
                'materiale'                   => isset($r->materiale) ? (float) $r->materiale : 0,
                'descrizione_materiale'       => $r->descrizione_materiale ?? null,
                'id_lavorazione_origine'      => $r->id_lavorazione,
                'id_lavorazione_riga_origine' => $r->id,
            ]);
            $aggiunte++;
        }

        self::ricalcolaAggregatiDotes($id_dotes, $id_azienda);

        if ($aggiunte === 0) return [false, 'Nessuna riga aggiunta', 0];
        return [true, $aggiunte.' righe singole aggiunte', $aggiunte];
    }

    /**
     * Copia tutte le righe di uno o più preventivi (dotes) esistenti nel documento corrente.
     */
    public static function applicaDaDotes(int $id_dotes_target, int $id_azienda, array $id_dotes_origine, int $id_utente): array
    {
        $target = DB::table('dotes')->where('id', $id_dotes_target)->where('id_azienda', $id_azienda)->first();
        if (!$target) return [false, 'Documento target non trovato', 0];

        $maxN = (int) DB::table('dorig')->where('id_dotes', $id_dotes_target)->where('id_azienda', $id_azienda)->max('n_riga');
        $aggiunte = 0;

        $righeSrc = DB::table('dorig')
            ->whereIn('id_dotes', array_map('intval', $id_dotes_origine))
            ->where('id_azienda', $id_azienda)
            ->orderBy('id_dotes')
            ->orderBy('n_riga')
            ->get();

        foreach ($righeSrc as $r) {
            $maxN++;
            $nuova = (array) $r;
            unset($nuova['id']);
            $nuova['id_dotes']  = $id_dotes_target;
            $nuova['id_testata'] = $id_dotes_target;
            $nuova['n_riga']    = $maxN;
            $nuova['id_utente'] = $id_utente;
            $nuova['cd_do']           = $target->cd_do;
            $nuova['tipo_documento']  = $target->tipo_documento ?? null;
            $nuova['numero_doc']      = $target->numero_doc ?? null;
            $nuova['data_doc']        = $target->data_doc ?? null;
            $nuova['id_cliente']      = $target->id_cliente ?? null;
            DB::table('dorig')->insert($nuova);
            $aggiunte++;
        }

        self::ricalcolaAggregatiDotes($id_dotes_target, $id_azienda);

        if ($aggiunte === 0) return [false, 'Nessuna riga copiata dai preventivi selezionati', 0];
        return [true, $aggiunte.' righe copiate da preventivi esistenti', $aggiunte];
    }

    /**
     * Copia singole righe di dorig (di altri preventivi) nel documento corrente.
     */
    public static function applicaDaDorig(int $id_dotes_target, int $id_azienda, array $id_dorig, int $id_utente): array
    {
        $target = DB::table('dotes')->where('id', $id_dotes_target)->where('id_azienda', $id_azienda)->first();
        if (!$target) return [false, 'Documento target non trovato', 0];

        $maxN = (int) DB::table('dorig')->where('id_dotes', $id_dotes_target)->where('id_azienda', $id_azienda)->max('n_riga');
        $aggiunte = 0;

        $righeSrc = DB::table('dorig')
            ->whereIn('id', array_map('intval', $id_dorig))
            ->where('id_azienda', $id_azienda)
            ->get();

        foreach ($righeSrc as $r) {
            $maxN++;
            $nuova = (array) $r;
            unset($nuova['id']);
            $nuova['id_dotes']  = $id_dotes_target;
            $nuova['id_testata'] = $id_dotes_target;
            $nuova['n_riga']    = $maxN;
            $nuova['id_utente'] = $id_utente;
            $nuova['cd_do']           = $target->cd_do;
            $nuova['tipo_documento']  = $target->tipo_documento ?? null;
            $nuova['numero_doc']      = $target->numero_doc ?? null;
            $nuova['data_doc']        = $target->data_doc ?? null;
            $nuova['id_cliente']      = $target->id_cliente ?? null;
            DB::table('dorig')->insert($nuova);
            $aggiunte++;
        }

        self::ricalcolaAggregatiDotes($id_dotes_target, $id_azienda);

        if ($aggiunte === 0) return [false, 'Nessuna riga copiata', 0];
        return [true, $aggiunte.' righe copiate da altri preventivi', $aggiunte];
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

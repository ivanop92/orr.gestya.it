<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;

class MagazzinoController extends Controller
{
    protected function getIdAzienda() {
        return session('utente')->id_azienda;
    }


    public function generaMovimentiMancanti($id_dotes = null)
    {
        if($id_dotes != null){
            DB::delete('delete from mgmov where id_dorig IN (select id from dorig where id_dotes ='.$id_dotes.')');
        }

        try {
            $id_azienda = $this->getIdAzienda();

            // Recupera documenti movimentabili con i magazzini configurati
            $documenti_movimentabili = DB::table('do')
                ->where('id_azienda', $id_azienda)
                ->where(function($query) {
                    $query->where('carico', 1)
                        ->orWhere('scarico', 1)
                        ->orWhere('trasferimento', 1);
                })
                ->get();

            $cd_do_movimentabili = $documenti_movimentabili->pluck('cd_do')->toArray();


            // Gestione documenti (dotes)
            if($id_dotes != null){
                $testate = DB::table('dotes')->where('id', $id_dotes)->where('id_azienda', $id_azienda)->whereIn('cd_do', $cd_do_movimentabili)->get();
            } else {
                $testate = DB::table('dotes')->whereIn('cd_do', $cd_do_movimentabili)->where('id_azienda', $id_azienda)->get();
            }

            $movimenti_generati = 0;
            $errori = [];

            // Gestione movimenti documenti (dotes)
            foreach($testate as $testata) {
                try {
                    DB::beginTransaction();

                    $tipo_doc = $documenti_movimentabili->firstWhere('cd_do', $testata->cd_do);

                    $righe = DB::table('dorig')
                        ->where('id_dotes', $testata->id)
                        ->where('id_azienda', $id_azienda)
                        ->get();


                    foreach($righe as $riga) {
                        $movimento_esistente = DB::table('mgmov')
                            ->where('id_dorig', $riga->id)
                            ->where('id_azienda', $id_azienda)
                            ->exists();


                        if(!$movimento_esistente && $riga->id_articolo) {

                            // Determinazione del magazzino e tipo movimento
                            if($tipo_doc->trasferimento == 1) {
                                // Per trasferimento creiamo due movimenti
                                // Scarico dal magazzino di partenza
                                $this->creaMovimento($testata, $riga, $tipo_doc, -$riga->qta, $tipo_doc->id_mg_p);
                                // Carico nel magazzino di arrivo
                                $this->creaMovimento($testata, $riga, $tipo_doc, $riga->qta, $tipo_doc->id_mg_a);
                            } else {

                                $is_carico = $tipo_doc->carico == 1;
                                $qta = $is_carico ? $riga->qta : -$riga->qta;
                                // Usa id_mg_a dal documento
                                $id_mg = $tipo_doc->id_mg_a;


                                $this->creaMovimento($testata, $riga, $tipo_doc, $qta, $id_mg);
                            }

                            $movimenti_generati++;
                        }
                    }

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    echo 'errore';
                    print_r($e->getMessage());
                    exit();
                    $errori[] = "Errore nel documento {$testata->id}: " . $e->getMessage();
                }
            }

            // AGGIUNTA: Gestione movimenti ODL completati
            // Recupera ODL che sono stati completati (stato 2)
            if ($id_dotes === null) {
                $odl_completati = DB::table('odl')
                    ->where('id_azienda', $id_azienda)
                    ->where('stato', 2) // ODL completati
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                            ->from('mgmov')
                            ->whereRaw('mgmov.id_odl = odl.id');
                    })
                    ->get();

                foreach ($odl_completati as $odl) {
                    try {
                        DB::beginTransaction();

                        // Recupera l'articolo prodotto
                        $articolo = DB::table('articoli')
                            ->where('id', $odl->id_articolo)
                            ->where('id_azienda', $id_azienda)
                            ->first();

                        if ($articolo) {
                            // Magazzino prodotti finiti: prima per tipologia+default, poi fallback flag produzione
                            $magazzino_produzione = DB::table('mg')
                                ->where('id_azienda', $id_azienda)
                                ->where('tipologia', 'prodotti_finiti')
                                ->where('is_default', 1)
                                ->first();
                            if (!$magazzino_produzione) {
                                $magazzino_produzione = DB::table('mg')
                                    ->where('id_azienda', $id_azienda)
                                    ->where('produzione', 1)
                                    ->first();
                            }

                            $id_mg = $magazzino_produzione ? $magazzino_produzione->id : null;

                            if ($id_mg) {
                                // Carico a magazzino dell'articolo prodotto
                                $movimento = [
                                    'id_utente' => $odl->id_utente,
                                    'id_azienda' => $id_azienda,
                                    'id_odl' => $odl->id,
                                    'datamov' => $odl->data_chiusura ?? now(),
                                    'causale' => 'Carico da ODL #' . $odl->numero,
                                    'id_articolo' => $odl->id_articolo,
                                    'qta' => $odl->qta,
                                    'car' => 1, // Carico
                                    'sca' => 0,
                                    'ret' => 0,
                                    'id_mg' => $id_mg
                                ];

                                // Propaga la commessa dall'ODL al movimento di magazzino
                                if(!empty($odl->id_commessa)) {
                                    $movimento['id_commessa'] = $odl->id_commessa;
                                }

                                DB::table('mgmov')->insert($movimento);

                                // Scarica dal magazzino i componenti utilizzati (dalla distinta base)
                                $fasi_articolo = DB::table('fasi_articoli')
                                    ->where('id_articolo', $odl->id_articolo)
                                    ->where('id_azienda', $id_azienda)
                                    ->pluck('id_fase');

                                if (!empty($fasi_articolo)) {
                                    $componenti = DB::table('distinta_base')
                                        ->where('id_articolo', $odl->id_articolo)
                                        ->where('id_azienda', $id_azienda)
                                        ->whereIn('id_fase_articolo', $fasi_articolo)
                                        ->get();

                                    foreach ($componenti as $componente) {
                                        // Cerca il magazzino dove il materiale ha giacenza
                                        $mg_materiale = DB::table('giacenze')
                                            ->where('id_articolo', $componente->id_materiale)
                                            ->where('id_azienda', $id_azienda)
                                            ->where('qta', '>', 0)
                                            ->value('id_mg');

                                        $movimento_componente = [
                                            'id_utente' => $odl->id_utente,
                                            'id_azienda' => $id_azienda,
                                            'id_odl' => $odl->id,
                                            'datamov' => $odl->data_chiusura ?? now(),
                                            'causale' => 'Scarico per ODL #' . $odl->numero,
                                            'id_articolo' => $componente->id_materiale,
                                            'qta' => -($componente->qta * $odl->qta), // Quantità negativa per scarico
                                            'car' => 0,
                                            'sca' => 1, // Scarico
                                            'ret' => 0,
                                            'id_mg' => $mg_materiale ?: $id_mg // Usa il magazzino con giacenza, fallback a produzione
                                        ];

                                        // Propaga la commessa dall'ODL al movimento di scarico
                                        if(!empty($odl->id_commessa)) {
                                            $movimento_componente['id_commessa'] = $odl->id_commessa;
                                        }

                                        DB::table('mgmov')->insert($movimento_componente);
                                        $movimenti_generati++;
                                    }
                                }

                                $movimenti_generati++;
                            }
                        }

                        DB::commit();
                    } catch (\Exception $e) {
                        DB::rollBack();
                        $errori[] = "Errore nell'ODL {$odl->id}: " . $e->getMessage();
                    }
                }
            }

            // Aggiorna le giacenze degli articoli
            $this->ricalcolaGiacenze();

            return response()->json([
                'successo' => true,
                'movimenti_generati' => $movimenti_generati,
                'errori' => $errori
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'successo' => false,
                'errore' => $e->getMessage()
            ], 500);
        }
    }

    protected function creaMovimento($testata, $riga, $tipo_doc, $qta, $id_mg)
    {
      
        // Crea il movimento di magazzino
        DB::table('mgmov')->insert([
            'id_azienda' => $testata->id_azienda,
            'id_dorig' => $riga->id,
            'datamov' => $testata->data_doc,
            'causale' => $this->determinaCausale($tipo_doc, $qta,$testata),
            'lotto' => $riga->lotto,
            'scadenza_lotto' => $riga->scadenza_lotto,
            'id_articolo' => $riga->id_articolo,
            'qta' => $qta,
            'car' => $qta > 0 ? 1 : 0,
            'sca' => $qta < 0 ? 1 : 0,
            'id_mg' => $id_mg
        ]);


        $this->ricalcolaGiacenze($riga->id_articolo);
    }

    protected function determinaCausale($tipo_doc, $qta,$testata)
    {
        if($tipo_doc->trasferimento == 1) {
            return $qta > 0 ? 'Trasferimento IN '.$testata->cd_do.' '.$testata->numero_doc : 'Trasferimento OUT '.$testata->cd_do.' '.$testata->numero_doc;
        }
        return $qta > 0 ? 'Carico '.$testata->cd_do.' '.$testata->numero_doc : 'Scarico '.$testata->cd_do.' '.$testata->numero_doc;
    }

    public function ricalcolaGiacenze($id_articolo = null)
    {
        try {
            $id_azienda = session('utente')->id_azienda;
            DB::beginTransaction();
            if($id_articolo != null){
                DB::table('giacenze')->where('id_articolo', $id_articolo)->where('id_azienda', $id_azienda)->delete();
            } else {
                DB::table('giacenze')->where('id_azienda', $id_azienda)->delete();
            }

            if($id_articolo != null) {
                $giacenze = DB::table('mgmov')
                    ->select(
                        'id_articolo',
                        'id_mg',
                        'lotto',
                        'scadenza_lotto',
                        DB::raw('SUM(qta) as qta_totale')
                    )
                    ->where('id_azienda', $id_azienda)
                    ->where('id_articolo', $id_articolo)
                    ->whereNotNull('id_articolo')
                    ->whereNotNull('id_mg')
                    ->groupBy('id_articolo', 'id_mg', 'lotto', 'scadenza_lotto')
                    ->having('qta_totale', '!=', 0)
                    ->get();
            } else {
                $giacenze = DB::table('mgmov')
                    ->select(
                        'id_articolo',
                        'id_mg',
                        'lotto',
                        'scadenza_lotto',
                        DB::raw('SUM(qta) as qta_totale')
                    )
                    ->where('id_azienda', $id_azienda)
                    ->whereNotNull('id_articolo')
                    ->whereNotNull('id_mg')
                    ->groupBy('id_articolo', 'id_mg', 'lotto', 'scadenza_lotto')
                    ->having('qta_totale', '!=', 0)
                    ->get();
            }

            foreach ($giacenze as $g) {
                DB::table('giacenze')->insert([
                    'id_azienda' => $id_azienda,
                    'id_articolo' => $g->id_articolo,
                    'id_mg' => $g->id_mg,
                    'lotto' => $g->lotto,
                    'scadenza_lotto' => $g->scadenza_lotto,
                    'qta' => $g->qta_totale
                ]);
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return false;
        }
    }

}
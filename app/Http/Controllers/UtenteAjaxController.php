<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PHPMailer\PHPMailer\PHPMailer;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TariffeImport;


class UtenteAjaxController extends Controller {

    /*public function modifica_evento($id){

        $this->is_loggato();

        $eventi = DB::select('select * from eventi where id='.$id);
        if(sizeof($eventi) > 0) {
            return View::make('admin.ajax.modifica_evento', compact('eventi'));
        }
    }*/
    public function getOrdini(Request $request)
    {
        $utente = session('utente');

        // Recupera gli ordini con tipo_documento = 'ord'
        $ordini = DB::table('dotes as d')
            ->where('d.id_azienda', $utente->id_azienda)
            ->where('d.tipo_documento', 'ord')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('dorig')
                    ->whereColumn('dorig.id_dotes', 'd.id')
                    ->where('dorig.qta_evadibile_prod', '>', 0);
            })
            ->get();

        return response()->json(['ordini' => $ordini]);
    }



    public function get_prezzo_articolo_simple(Request $request)
    {
        // Ottieni utente loggato
        $utente = session('utente');
        if (!$utente) {
            return response()->json(['success' => false, 'message' => 'Utente non autenticato']);
        }

        // Ottieni parametri
        $id_articolo = $request->input('id_articolo');
        $id_cliente = $request->input('id_cliente');

        // Validazione di base
        if (empty($id_articolo) || empty($id_cliente)) {
            return response()->json(['success' => false, 'message' => 'Parametri mancanti']);
        }

        try {
            // Recupera articolo
            $articolo = DB::table('articoli')
                ->where('id', $id_articolo)
                ->where('id_azienda', $utente->id_azienda)
                ->first();

            if (!$articolo) {
                return response()->json(['success' => false, 'message' => 'Articolo non trovato']);
            }

            // Cerca il prezzo nel listino
            $oggi = date('Y-m-d');
            $prezzo_listino = DB::select("
    SELECT 
        ROUND(la.prezzo * (1 - la.sconto_percentuale / 100), 2) as prezzo_finale,
        l.descrizione as nome_listino
    FROM listini l
    JOIN listini_clienti lc ON l.id = lc.id_listino
    JOIN listini_articoli la ON l.id = la.id_listino
    WHERE l.id_azienda = ?
    AND lc.id_cliente = ?
    AND la.id_articolo = ?
    AND l.attivo = 1
    AND (lc.data_inizio IS NULL OR lc.data_inizio <= ?)
    AND (lc.data_fine IS NULL OR lc.data_fine >= ?)
    ORDER BY l.priorita DESC
    LIMIT 1",
                [$utente->id_azienda, $id_cliente, $id_articolo, $oggi, $oggi]
            );

            if (!empty($prezzo_listino)) {
                // Restituisci il prezzo dal listino
                return response()->json([
                    'success' => true,
                    'prezzo' => (float)$prezzo_listino[0]->prezzo_finale,
                    'nome_listino' => $prezzo_listino[0]->nome_listino,
                    'da_listino' => true,
                    'messaggio' => 'Prezzo applicato dal listino: ' . $prezzo_listino[0]->nome_listino
                ]);
            } else {
                // Nessun prezzo da listino, restituisci il prezzo base
                return response()->json([
                    'success' => true,
                    'prezzo' => (float)$articolo->prezzo,
                    'da_listino' => false,
                    'messaggio' => 'Prezzo standard applicato'
                ]);
            }
        } catch (\Exception $e) {
            // In caso di errore, restituisci comunque una risposta con dettagli sull'errore
            return response()->json([
                'success' => false,
                'message' => 'Errore: ' . $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    public function getArticoli(Request $request)
    {
        $idOrdine = $request->query('id_ordine');
        $utente = session('utente');

        // Recupera gli articoli dalla tabella dorig collegati all'ordine specificato
        $articoli = DB::table('dorig')
            ->where('id_dotes', $idOrdine)
            ->where('id_azienda', $utente->id_azienda)
            ->get();

        return response()->json(['articoli' => $articoli]);
    }



    public function getClienteForOrdine(Request $request) {

        $utente = session('utente');

        $dati = $request->all();
        $cliente = DB::table('clienti')->where('id_azienda',  $utente->id_azienda)->where('cd_cf', $dati['cd_cf'])->first();

        $result = $cliente;
        return response()->json($result, 200);
    }

    public function modifica_testata_fattura($id)
    {
        $this->is_loggato();
        $utente = session('utente'); // Assumendo che tu abbia una sessione attiva per l'utente loggato

        // Uso di parameter binding per evitare SQL Injection e aggiunta del controllo su `id_azienda`
        $fatture = DB::select('SELECT * FROM dotes WHERE id = ? AND id_azienda = ?', [$id, $utente->id_azienda]);

        if (count($fatture) > 0) {
            $f = $fatture[0];

            // Aggiunta del controllo su `id_azienda` nella query `dorig`
            $f->righe = DB::select('SELECT * FROM dorig WHERE id_testata = ? AND id_azienda = ?', [$f->id, $utente->id_azienda]);

            return View::make('ajax.modifica_testata_fattura', compact('f'));
        }

        // In caso non ci sia nessuna fattura trovata, puoi restituire un errore o una vista vuota
        return response()->json(['message' => 'Fattura non trovata o accesso negato.'], 404);
    }



    public function evadi_documento(Request $request, $id)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Recupera i dotes e i dorig come già stai facendo
        $dotes = DB::table('dotes')->where('id', $id)->get();
        $dorig = DB::table('dorig')->whereIn('id_dotes', $dotes->pluck('id'))->get();

        // Recupera i documenti dalla tabella 'do'
        $documenti = DB::table('do')->where('id_azienda', $utente->id_azienda)->get();

        $num_fattura = DB::select('SELECT IFNULL(MAX(numero) + 1, 1) AS numero FROM dotes WHERE anno = YEAR(NOW()) AND id_azienda = ?', [$utente->id_azienda])[0]->numero;


        // Passa anche i documenti alla vista
        return View::make('default.ajax.evadi_documento', compact('utente', 'dotes', 'dorig', 'documenti', 'num_fattura'));
    }




    public function modifica_articolo_carico($id_articolo,Request $request){

        $utente = session('utente');

        $articoli = DB::select('SELECT *,(select sum(qta) as giacenza from mgmov where id_articolo = articoli.id) as giacenza from articoli where id ='.$id_articolo.' and id_utente='.$utente->id);

        foreach($articoli as $a){


            $a->mgmov = DB::select('SELECT * from mgmov where id_articolo  ='.$a->id.' and id_utente = '.$utente->id);

        }

        return View::make('default.ajax.modifica_articolo_carico', compact('utente','articoli'));
    }
    public function modifica_articolo_scarico($id_articolo,Request $request){

        $utente = session('utente');

        $articoli = DB::select('SELECT *,(select sum(qta) as giacenza from mgmov where id_articolo = articoli.id) as giacenza from articoli where id ='.$id_articolo.' and id_utente='.$utente->id);

        foreach($articoli as $a){


            $a->mgmov = DB::select('SELECT * from mgmov where id_articolo  ='.$a->id.' and id_utente = '.$utente->id);

        }

        return View::make('default.ajax.modifica_articolo_scarico', compact('utente','articoli'));
    }

    public function modifica_articolo_rettifica($id_articolo,Request $request){

        $utente = session('utente');

        $articoli = DB::select('SELECT *,(select sum(qta) as giacenza from mgmov where id_articolo = articoli.id) as giacenza from articoli where id ='.$id_articolo.' and id_utente='.$utente->id);

        foreach($articoli as $a){


            $a->mgmov = DB::select('SELECT * from mgmov where id_articolo  ='.$a->id.' and id_utente = '.$utente->id);

        }

        return View::make('default.ajax.modifica_articolo_rettifica', compact('utente','articoli'));
    }

    public function modifica_articolo_movimenti($id_articolo,Request $request){

        $utente = session('utente');

        $articoli = DB::select('SELECT *,(select sum(qta) as giacenza from mgmov where id_articolo = articoli.id) as giacenza from articoli where id ='.$id_articolo.' and id_utente='.$utente->id);

        foreach($articoli as $a){


            $a->mgmov = DB::select('SELECT * from mgmov where id_articolo  ='.$a->id.' and id_utente = '.$utente->id);

            $a->giacenze_lotti = DB::select('
    SELECT SUM(qta) AS giacenza, lotto, scadenza_lotto 
    FROM mgmov 
    WHERE id_articolo = '.$a->id.' 
    GROUP BY lotto 
    HAVING giacenza != 0');

        }

        return View::make('default.ajax.modifica_articolo_mgmov', compact('utente','articoli'));
    }

    public function salva_token_onesignal($tipologia,$token){
        $this->is_loggato();
        $utente = session('utente');

        if($tipologia == 'mobile') {
            DB::update('update utenti set onesignal_token_mobile = "' . $token . '" where id=' . $utente->id);
        } else {
            DB::update('update utenti set onesignal_token = "' . $token . '" where id=' . $utente->id);
        }
    }

    public function aggiungi_allegati($id_bando){

        $this->is_loggato();
        $utente = session('utente');

        $bandi = DB::table('bandi')->where('id', $id_bando)->get();
        $allegati_bandi = DB::table('bandi_allegati')->where('id_utente', $utente->id)->get();

        return View::make('default.ajax.aggiungi_allegati', compact('bandi', 'allegati_bandi'));
    }

    public function aggiungi_utenti($id_bando){

        $this->is_loggato();
        $utente = session('utente');

        $bandi = DB::table('bandi')->where('id', $id_bando)->get();
        $utenti = DB::table('utenti')->where('id_tipologia', '>', 0)->orderBy('ragione_sociale', 'asc')->get();

        return View::make('default.ajax.aggiungi_utenti', compact('bandi', 'utenti'));
    }

    public function modifica_bandi_allegati($id){

        $this->is_loggato();
        $utente = session('utente');

        $bandi_allegati = DB::table('bandi_allegati')->where('id', $id)->get();
        /*$utenti = DB::table('utenti')->where('id_tipologia', '>', 0)->orderBy('ragione_sociale', 'asc')->get();*/

        return View::make('default.ajax.modifica_bandi_allegato', compact('bandi_allegati'));
    }

    public function modifica_bando($id){

        $this->is_loggato();
        $utente = session('utente');

        $bandi = DB::table('bandi')->where('id', $id)->get();
        /*$utenti = DB::table('utenti')->where('id_tipologia', '>', 0)->orderBy('ragione_sociale', 'asc')->get();*/

        return View::make('default.ajax.modifica_bando', compact('bandi'));
    }

    public function modifica_cliente($id){

        $this->is_loggato();
        $utente = session('utente');

        $clienti = DB::select('SELECT * from clienti where id = '.$id.' and id_azienda ='.$utente->id_azienda);
        $agenti = DB::select('SELECT id,nome,cognome from utenti where id_tipologia = 1 and id_azienda = '.$utente->id_azienda);


        return View::make('utente.ajax.modifica_cliente', compact('clienti','agenti','utente'));
    }

    public function visualizza_stato_clienti($id){

        $this->is_loggato();
        $utente = session('utente');

        $bandi = DB::table('bandi')->where('id', $id)->get();

        return View::make('default.ajax.visualizza_stato_clienti', compact('bandi'));
    }

    public function invia_mail_bando_clienti($id){

        $this->is_loggato();
        $utente = session('utente');

        $bandi = DB::table('bandi')->where('id', $id)->get();

        return View::make('default.ajax.invia_mail_bando_clienti', compact('bandi'));
    }

    public function modifica_dipendente($id){

        $this->is_loggato();
        $utente = session('utente');

        $dipendenti = DB::table('utenti')->where('id_tipologia', 3)->where('id', $id)->get();
        $reparti = DB::table('reparti')->get();

        return View::make('default.ajax.modifica_dipendente', compact('dipendenti', 'reparti'));
    }

    public function modifica_reparto($id){

        $this->is_loggato();
        $utente = session('utente');

        $reparti = DB::table('reparti')->where('id', $id)->get();

        return View::make('default.ajax.modifica_reparto', compact('reparti'));
    }

    public function aggiungi_task($id){

        $this->is_loggato();
        $utente = session('utente');

        $lavori = DB::table('lavori')->where('id', $id)->get();
        $dipendenti = DB::table('utenti')->where('id_tipologia', 3)->get();


        return View::make('default.ajax.aggiungi_task', compact('lavori', 'dipendenti'));
    }

    public function modifica_task($id){

        $this->is_loggato();
        $utente = session('utente');

        $task = DB::table('task')->where('id', $id)->get();
        $dipendenti = DB::table('utenti')->where('id_tipologia', 3)->get();

        return View::make('default.ajax.modifica_task', compact('task', 'dipendenti'));
    }

    public function chiudi_task($id){

        $this->is_loggato();
        $utente = session('utente');

        $task = DB::table('task')->where('id', $id)->get();

        return View::make('default.ajax.chiudi_task', compact('task'));
    }

    public function info_task_chiuso($id){

        $this->is_loggato();
        $utente = session('utente');

        $task = DB::table('task')->where('id', $id)->get();

        return View::make('default.ajax.info_task_chiuso', compact('task'));
    }

    public function sospendi_task($id){

        $this->is_loggato();
        $utente = session('utente');

        $task = DB::table('task')->where('id', $id)->get();

        return View::make('default.ajax.sospendi_task', compact('task'));
    }

    public function info_task_sospeso($id){

        $this->is_loggato();
        $utente = session('utente');

        $task = DB::table('task')->where('id', $id)->get();

        return View::make('default.ajax.info_task_sospeso', compact('task'));
    }

    public function assegna_task($id){

        $this->is_loggato();
        $utente = session('utente');

        $task = DB::table('task')->where('id', $id)->get();
        $dipendenti = DB::table('utenti')->where('id_tipologia', 3)->get();

        return View::make('default.ajax.assegna_task', compact('task', 'dipendenti'));
    }


    public function riepilogo_documenti_mese(Request $request)
    {
        $this->is_loggato();
        $utente = session()->get('utente');

        $anno = $request->input('anno');
        $mese = $request->input('mese');
        $tipo_documento = $request->input('tipo_documento'); // Tipo di documento (opzionale)

        // Query principale per ottenere i documenti con join su clienti
        $query = DB::table('dotes')
            ->leftJoin('clienti', 'dotes.cd_cf', '=', 'clienti.cd_cf')
            ->whereYear('dotes.data_doc', $anno)
            ->whereMonth('dotes.data_doc', $mese)
            ->where('dotes.id_azienda', $utente->id_azienda);

        if ($tipo_documento) {
            $query->where('dotes.tipo_documento', $tipo_documento);
        }

        $documenti = $query->get([
            'dotes.id',
            'dotes.stato',
            'clienti.ragione_sociale AS cliente',
            'dotes.data_doc',
            'dotes.numero',
            'dotes.totale AS importo',
            'dotes.tipo_documento',
            'dotes.scadenze_rate',
            'dotes.saldata', // Aggiungi questo campo
        ]);


        // Aggiungi la prossima scadenza a ogni documento
        $documenti->transform(function ($doc) {
            $scadenze = json_decode($doc->scadenze_rate, true) ?? [];
            $prox_scadenza = null;

            foreach ($scadenze as $date) {
                if (strtotime($date) >= strtotime(date('Y-m-d'))) {
                    if ($prox_scadenza === null || strtotime($date) < strtotime($prox_scadenza)) {
                        $prox_scadenza = $date;
                    }
                }
            }

            $doc->prox_scadenza = $prox_scadenza ?: '-';
            unset($doc->scadenze_rate); // Rimuovi scadenze_rate per pulizia
            return $doc;
        });

        // Recupera il conteggio e il totale per i documenti filtrati
        $count_documenti = $documenti->count();
        $total_importo = $documenti->sum('importo');

        // Recupera i tipi di documento unici e il loro conteggio
        $tipi_documento = DB::table('dotes')
            ->select('tipo_documento', DB::raw('COUNT(*) as count'))
            ->whereYear('data_doc', $anno)
            ->whereMonth('data_doc', $mese)
            ->where('id_azienda', $utente->id_azienda)
            ->groupBy('tipo_documento')
            ->get();

        return response()->json([
            'documenti' => $documenti,
            'count_documenti' => $count_documenti,
            'total_importo' => $total_importo,
            'tipi_documento' => $tipi_documento,
        ]);
    }

    public function get_lotti_disponibili($id_articolo)
    {
        $utente = session('utente');
        
        $lotti = DB::select('
            SELECT DISTINCT lotto, scadenza_lotto 
            FROM mgmov 
            WHERE id_articolo = ? 
            AND id_azienda = ? 
            AND lotto IS NOT NULL 
            AND scadenza_lotto IS NOT NULL
            AND (
                SELECT SUM(CASE 
                    WHEN car = 1 THEN qta 
                    WHEN sca = 1 THEN -qta 
                    ELSE 0 
                END)
                FROM mgmov m2 
                WHERE m2.id_articolo = mgmov.id_articolo 
                AND m2.lotto = mgmov.lotto
            ) > 0
            ORDER BY scadenza_lotto ASC', 
            [$id_articolo, $utente->id_azienda]
        );
        
        return response()->json($lotti);
    }

    /**
     * Verifica se l'utente è loggato
     * @return \Illuminate\Http\RedirectResponse
     */
    public function is_loggato(){
        if(!session()->has('utente')) return Redirect::to('admin/login')->send();
    }

}

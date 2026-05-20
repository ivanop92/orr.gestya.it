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


class AjaxController extends Controller {

    /*public function modifica_evento($id){

        $this->is_loggato();

        $eventi = DB::select('select * from eventi where id='.$id);
        if(sizeof($eventi) > 0) {
            return View::make('admin.ajax.modifica_evento', compact('eventi'));
        }
    }*/


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
            return response()->json([
                'success' => false,
                'message' => 'Parametri mancanti: articolo o cliente non specificati'
            ]);
        }

        try {
            // Recupera articolo
            $articolo = DB::table('articoli')
                ->where('id', $id_articolo)
                ->where('id_azienda', $utente->id_azienda)
                ->first();

            if (!$articolo) {
                return response()->json([
                    'success' => false,
                    'message' => 'Articolo non trovato'
                ]);
            }

            // Cerca il prezzo nel listino
            $oggi = date('Y-m-d');
            $prezzo_listino = DB::select("
            SELECT 
                l.nome as nome_listino,
                la.prezzo as prezzo_listino,
                la.sconto_percentuale,
                ROUND(la.prezzo * (1 - la.sconto_percentuale / 100), 4) as prezzo_finale 
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
                // Log dell'applicazione del prezzo da listino
                $info_listino = $prezzo_listino[0];

                // Registra l'utilizzo del listino (opzionale)
                DB::table('listini_utilizzo')->insert([
                    'id_listino' => $info_listino->id_listino ?? 0,
                    'id_articolo' => $id_articolo,
                    'id_cliente' => $id_cliente,
                    'id_utente' => $utente->id,
                    'id_azienda' => $utente->id_azienda,
                    'prezzo_applicato' => $info_listino->prezzo_finale,
                    'prezzo_originale' => $articolo->prezzo,
                    'timestamp' => now()
                ]);

                // Restituisci il prezzo dal listino
                return response()->json([
                    'success' => true,
                    'prezzo' => (float)$info_listino->prezzo_finale,
                    'prezzo_originale' => (float)$articolo->prezzo,
                    'sconto_applicato' => (float)$info_listino->sconto_percentuale ?? 0,
                    'nome_listino' => $info_listino->nome_listino ?? 'Listino',
                    'da_listino' => true
                ]);
            } else {
                // Nessun prezzo da listino, restituisci il prezzo base
                return response()->json([
                    'success' => true,
                    'prezzo' => (float)$articolo->prezzo,
                    'prezzo_originale' => (float)$articolo->prezzo,
                    'da_listino' => false
                ]);
            }
        } catch (\Exception $e) {
            // Log dell'errore
            \Log::error('Errore nel recupero prezzo listino: ' . $e->getMessage());

            // In caso di errore, restituisci comunque una risposta
            return response()->json([
                'success' => false,
                'message' => 'Errore durante il recupero del prezzo: ' . $e->getMessage()
            ]);
        }
    }


    public function getOrdini(Request $request)
    {
        $utente = session('utente');

        // Recupera gli ordini con tipo_documento = 'ord'
        $ordini = DB::table('dotes as d')
            ->leftJoin('clienti as c', 'c.id', '=', 'd.id_cliente')
            ->where('d.id_azienda', $utente->id_azienda)
            ->where('d.cd_do', 'ORD')
            ->whereExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('dorig')
                    ->whereColumn('dorig.id_dotes', 'd.id')
                    ->where('dorig.qta_evadibile_prod', '>', 0);
            })
            ->select('d.*', 'c.ragione_sociale')
            ->get();

        return response()->json(['ordini' => $ordini]);
    }

    public function getArticoli(Request $request)
    {
        $idOrdine = $request->query('id_ordine');
        $utente = session('utente');

        // Recupera gli articoli dalla tabella dorig collegati all'ordine specificato
        $articoli = DB::table('dorig')
            ->leftJoin('articoli', 'articoli.id', '=', 'dorig.id_articolo')
            ->where('dorig.id_dotes', $idOrdine)
            ->where('dorig.id_azienda', $utente->id_azienda)
            ->where('dorig.qta_evadibile_prod', '>', 0)
            ->where('dorig.stato_prod', '<', 2)
            ->select('dorig.*', 'articoli.titolo as nome_prodotto', 'articoli.codice_articolo')
            ->get();

        return response()->json(['articoli' => $articoli]);
    }



    public function getClienteForOrdine(Request $request) {

        $utente = session('utente');

        $dati = $request->all();
        $tabella = (isset($dati['tipo']) && $dati['tipo'] == 'fornitore') ? 'fornitori' : 'clienti';
        $q = DB::table($tabella)->where('id_azienda',  $utente->id_azienda);
        if (!empty($dati['id'])) {
            $q->where('id', $dati['id']);
        } elseif (!empty($dati['cd_cf'])) {
            $q->where('cd_cf', $dati['cd_cf']);
        } else {
            return response()->json(null, 200);
        }
        $cliente = $q->first();

        return response()->json($cliente, 200);
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

        $mg = DB::select('select * from mg where id_azienda ='.$utente->id_azienda);

        return View::make('default.ajax.modifica_articolo_carico', compact('utente','articoli','mg'));
    }
    public function modifica_articolo_scarico($id_articolo,Request $request){

        $utente = session('utente');

        $articoli = DB::select('SELECT *,(select sum(qta) as giacenza from mgmov where id_articolo = articoli.id) as giacenza from articoli where id ='.$id_articolo.' and id_utente='.$utente->id);

        foreach($articoli as $a){


            $a->mgmov = DB::select('SELECT * from mgmov where id_articolo  ='.$a->id.' and id_utente = '.$utente->id);

        }

        $mg = DB::select('select * from mg where id_azienda ='.$utente->id_azienda);


        return View::make('default.ajax.modifica_articolo_scarico', compact('utente','articoli','mg'));
    }

    public function modifica_articolo_rettifica($id_articolo,Request $request){

        $utente = session('utente');

        $articoli = DB::select('SELECT *,(select sum(qta) as giacenza from mgmov where id_articolo = articoli.id) as giacenza from articoli where id ='.$id_articolo.' and id_utente='.$utente->id);

        foreach($articoli as $a){


            $a->mgmov = DB::select('SELECT * from mgmov where id_articolo  ='.$a->id.' and id_utente = '.$utente->id);

        }

        $mg = DB::select('select * from mg where id_azienda ='.$utente->id_azienda);


        return View::make('default.ajax.modifica_articolo_rettifica', compact('utente','articoli','mg'));
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

        $mg = DB::select('select * from mg where id_azienda ='.$utente->id_azienda);


        return View::make('default.ajax.modifica_articolo_mgmov', compact('utente','articoli','mg'));
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

        $clienti = DB::table('utenti')->where('id', $id)->get();
        $agenti = DB::select('SELECT id,nome,cognome from utenti where id_tipologia = 1');


        return View::make('default.ajax.modifica_cliente', compact('clienti','agenti'));
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

    /**
     * Verifica se l'utente è loggato
     * @return \Illuminate\Http\RedirectResponse
     */
    public function is_loggato(){
        if(!session()->has('utente')) return Redirect::to('admin/login')->send();
    }

}

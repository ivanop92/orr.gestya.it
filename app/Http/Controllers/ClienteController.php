<?php

namespace App\Http\Controllers;

use App\Exports\MassiveViewExport;
use App\Exports\MassiveViewExport2;
use App\Exports\MassiveViewExportGTS;
use App\Exports\SearchResultExport;
use App\Imports\ArticoliImport;
use App\Imports\BOMImport;
use App\Imports\MagazzinoImport;
use App\Imports\BPImport;
use App\Imports\StoricoImport;
use App\Imports\VenditeImport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PHPMailer\PHPMailer\PHPMailer;
use phpseclib3\Net\SFTP;
use phpseclib3\Crypt\PublicKeyLoader;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TariffeImport;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Ayeo\Barcode;
use NGT\Barcode\GS1Decoder\Decoder;
class ClienteController extends Controller{



    public function login(Request $request){

        $dati = $request->all();
        $error = '';

        if (isset($dati['login'])) {

            $utenti = DB::select('SELECT * from utenti where abilitato = 1 and email = "' . htmlentities($dati['email'], 3, 'UTF-8' . '') . '" and password = "' . htmlentities($dati['password'], 3, 'UTF-8' . '') . '"');

            if (sizeof($utenti) > 0) {

                $utente = $utenti[0];
                session(['utente' => $utente]);
                session()->save();
                return Redirect::to('admin/index');
            } else $error = 'Inserisci username e password corretti';

        }

        return View::make('default.login', compact('error'));
    }

    public function effettua_login(Request $request) {
        $dati = $request->all();

        if (isset($dati['effettua_login_admin'])) {
            unset($dati['effettua_login_admin']);

            // L'utente che sta facendo login
            $utente = DB::table('utenti')->where('id', $dati['id_utente'])->first();

            // Salva l'ID del super admin se disponibile, per tornare alla sessione del super admin
            $utente->torna_super_admin = $dati['id_super_admin'] ?? null;

            session(['utente' => $utente]);
            session()->save();

            return Redirect::to('admin/index');
        }

        if (isset($dati['torna_super_admin'])) {
            unset($dati['torna_super_admin']);
            // Recupera l'account del super admin e ripristina la sessione
            $utente = DB::table('utenti')->where('id', $dati['id_super_admin'])->first();

            session(['utente' => $utente]);
            session()->save();

            return Redirect::to('aziende');
        }
    }


    public function utentiSuperAdmin(request $request) {


        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if (isset($dati['crea_utente'])) {
            DB::table('utenti')->insert([
                'super_admin' => 1,
                'nome' => $dati['nome'],
                'cognome' => $dati['cognome'],
                'email' => $dati['email'],
                'password' => $dati['password']
            ]);
            return redirect()->back()->with('success', 'Azienda creata con successo!');
        }

        if (isset($dati['modifica_utente'])) {
            DB::table('utenti')
                ->where('id', $dati['id_utente']) // Identifica l'azienda da modificare
                ->update([
                    'nome' => $dati['nome'],
                    'cognome' => $dati['cognome'],
                    'email' => $dati['password'],
                ]);
            return redirect()->back()->with('success', 'Azienda creata con successo!');

        }

        if (isset($dati['elimina'])) {
            DB::table('utenti')
                ->where('id', $dati['id_utente']) // Identifica l'azienda da eliminare
                ->delete();

            return redirect()->back()->with('success', 'Azienda creata con successo!');

        }

        $utenti = DB::table('utenti')->where('super_admin', 1)->get();

        return View::make('default.utenti', compact( 'utente', 'utenti'));
    }

    public function utentiAdmin(request $request) {


        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if (isset($dati['crea_utente'])) {
            DB::table('utenti')->insert([
                'super_admin' => 0,
                'nome' => $dati['nome'],
                'cognome' => $dati['cognome'],
                'email' => $dati['password'],
                'admin_azienda' => 2,
            ]);
            return redirect()->back()->with('success', 'Azienda creata con successo!');
        }

        if (isset($dati['modifica_utente'])) {
            DB::table('utenti')
                ->where('id', $dati['id_utente']) // Identifica l'azienda da modificare
                ->update([
                    'nome' => $dati['nome'],
                    'cognome' => $dati['cognome'],
                    'email' => $dati['password'],
                ]);
            return redirect()->back()->with('success', 'Azienda creata con successo!');

        }

        if (isset($dati['elimina'])) {
            DB::table('utenti')
                ->where('id', $dati['id_utente']) // Identifica l'azienda da eliminare
                ->delete();

            return redirect()->back()->with('success', 'Azienda creata con successo!');

        }

        $utenti = DB::table('utenti')->where('super_admin', 0)->where('admin_azienda' , 2)->get();

        return View::make('default.utentiAdmin', compact( 'utente', 'utenti'));
    }
    public function aziende(request $request) {


        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();
        // Recupera l'azienda che deve essere eliminata
        if(isset($dati['aggiungi'])) {
            unset($dati['aggiungi']);

            $dati['immagine'] = '/placehold_immagine.png';


            $id_azienda_inserita = DB::table('aziende')->insertGetId([
                'partita_iva' => $dati['p_iva'],
                'ragione_sociale' => $dati['ragione_sociale'],
                'dipendenti' => $dati['dipendenti'],
                'codice_ateco' => $dati['codice_ateco'],
                'descrizione_codice_ateco' => $dati['descrizione_codice_ateco'],
                'regione' => $dati['regione'],
                'indirizzo' => $dati['indirizzo'],
                'cap' => $dati['cap'],
                'comune' => $dati['comune'],
                'provincia' => $dati['provincia'],
                'codice_sdi' => $dati['codice_sdi'],
                'pec' => $dati['pec'],
                'regime_fiscale' => $dati['regime_fiscale'],
                'nazione' => $dati['nazione'],

            ]);

            $dati['immagine-user'] = '/default/assets/images/users/user-dummy-img.jpg';



            DB::table('utenti')->insert([
                'id_azienda' => $id_azienda_inserita,
                'nome' => $dati['nome'],
                'cognome' => $dati['cognome'],
                'data_nascita' => $dati['data_nascita'],
                'luogo_nascita' => $dati['luogo_nascita'],
                'email' => $dati['email'],
                'password' => $dati['password'],
                'telefono' => $dati['telefono'],
                'abilitato' => isset($dati['abilitato']) ? 1 : 0,
                'admin_azienda' => 1
            ]);

            // Setup automatico delle tipologie documento standard per la nuova azienda
            UtenteController::inserisci_tipologie_standard($id_azienda_inserita, 0);
            // Setup automatico dei magazzini standard
            UtenteController::inserisci_magazzini_standard($id_azienda_inserita);

            return Redirect::to('/aziende');


        }

        if(isset($dati['modifica'])){
            unset($dati['modifica']);


            DB::table('aziende')->where('id', $dati['id'])->update([
                'p_iva' => $dati['p_iva'],
                'ragione_sociale' => $dati['ragione_sociale'],
                'dipendenti' => $dati['dipendenti'],
                'codice_ateco' => $dati['codice_ateco'],
                'descrizione_codice_ateco' => $dati['descrizione_codice_ateco'],
                'regione' => $dati['regione'],
                'indirizzo' => $dati['indirizzo'],
                'cap' => $dati['cap'],
                'comune' => $dati['comune'],
                'provincia' => $dati['provincia'],
                'codice_sdi' => $dati['codice_sdi'],
                'pec' => $dati['pec']
            ]);

            DB::table('utenti')->where('id', $dati['id_utente'])->update([
                'nome' => $dati['nome'],
                'cognome' => $dati['cognome'],
                'data_nascita' => $dati['data_nascita'],
                'luogo_nascita' => $dati['luogo_nascita'],
                'email' => $dati['email'],
                'password' => $dati['password'],
                'telefono' => $dati['telefono'],
                'abilitato' => isset($dati['abilitato']) ? 1 : 0
            ]);

            if(isset($dati['immagine-user'])){
                DB::table('utenti')->where('id', $dati['id_utente'])->update([
                    'immagine' => $dati['immagine-user'],
                ]);
            }

            if(isset($dati['immagine'])){
                DB::table('aziende')->where('id', $dati['id'])->update([
                    'immagine' => $dati['immagine'],
                ]);
            }

            return Redirect::to('admin/aziende');

        }

        if(isset($dati['elimina'])){
            unset($dati['elimina']);

            $vecchio_path = DB::table('aziende')->where('id', $dati['id_azienda'])->first();

            /*if (file_exists($vecchio_path->immagine)) {
                unlink($vecchio_path->immagine);
            }*/

            DB::table('aziende')->where('id', $dati['id_azienda'])->delete();

/*
            $vecchio_path_utente = DB::table('utenti')->where('id_azienda', $dati['id_azienda'])->get();

            foreach ($vecchio_path_utente as $vpu){

                if (file_exists($vpu->immagine)) {
                    unlink($vpu->immagine);
                }

            }*/

            DB::table('utenti')->where('id_azienda', $dati['id_azienda'])->delete();

            return Redirect::to('aziende');

        }

        $aziende = DB::table('aziende')
            ->leftJoin('utenti', 'aziende.id', '=', 'utenti.id_azienda')
            ->select('aziende.*', 'utenti.nome as nome', 'utenti.cognome as cognome', 'utenti.id as id_utente')
            ->where('utenti.admin_azienda', '=', 1)->get();
        return View::make('default.aziende', compact( 'utente', 'aziende'));
    }


    public function moduli(Request $request)
    {
        // Verifica che l'utente sia loggato
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Operazioni diverse in base all'azione richiesta

            // Creazione di un nuovo modulo
            if(isset($dati['aggiungi'])) {
                unset($dati['aggiungi']);
                $request->validate([
                    'nome' => 'required|string|max:255',
                    'descrizione' => 'nullable|string',
                ]);

                // Inserisci il nuovo modulo con gli ID azienda come stringa separata da virgole
                DB::table('moduli')->insert([
                    'nome' => $dati['nome'],
                    'descrizione' => $dati['descrizione'],
                ]);

                return redirect()->back()->with('success', 'Modulo creato con successo.');
            }

            // Modifica di un modulo esistente
            if(isset($dati['modifica'])) {
                unset($dati['modifica']);



                // Aggiorna il modulo con i nuovi dati e gli ID azienda aggiornati
                DB::table('moduli')
                    ->where('id', $dati['id_modulo'])
                    ->update([
                        'nome' => $dati['nome'],
                        'descrizione' => $dati['descrizione'],
                    ]);

                return redirect()->back()->with('success', 'Modulo aggiornato con successo.');
            }



            // Eliminazione di un modulo
            if (isset($dati['elimina'])) {
                unset($dati['elimina']);
                $request->validate([
                    'id_modulo' => 'required|integer|exists:moduli,id',
                ]);

                // Elimina il modulo
                DB::table('moduli')->where('id', $dati['id_modulo'])->delete();

                return redirect()->back()->with('success', 'Modulo eliminato con successo.');
            }


        // Recupera i moduli con gli ID delle aziende decodificati
        $moduli = DB::table('moduli')->get();
        foreach ($moduli as $modulo) {
            $modulo->aziende = DB::table('aziende')
                ->whereIn('id', explode(',', $modulo->azienda_id))
                ->select('id', 'ragione_sociale')
                ->get();
        }

        // Recupera tutte le aziende disponibili per l'associazione
        $aziende = DB::table('aziende')->select('id', 'ragione_sociale')->get();

        // Ritorna la vista con i dati
        return View::make('default.moduli', compact('utente', 'moduli', 'aziende'));
    }
    public function aggiungiAziende(Request $request)
    {
        // Recupera i parametri dalla request
        $moduloId = $request->query('modulo_id');
        $aziende = explode(',', $request->query('aziende', ''));

        if (empty($moduloId)) {
            return response()->json(['success' => false, 'message' => 'Modulo ID mancante.'], 400);
        }

        // Recupera il modulo dal database
        $modulo = DB::table('moduli')->where('id', $moduloId)->first();

        if (!$modulo) {
            return response()->json(['success' => false, 'message' => 'Modulo non trovato.'], 404);
        }

        // Aggiorna la colonna azienda_id
        DB::table('moduli')->where('id', $moduloId)->update([
            'azienda_id' => implode(',', $aziende),
        ]);

        return response()->json(['success' => true, 'message' => 'Aziende aggiornate con successo.']);
    }







    public function contratti(Request $request)
    {
        // Verifica che l'utente sia loggato
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Gestione aggiunta o modifica contratto
        if ($request->isMethod('post')) {
            if (isset($dati['azione'])) {
                // Aggiungi contratto
                if ($dati['azione'] === 'aggiungi') {
                    $request->validate([
                        'cliente_id' => 'required|integer',
                        'descrizione' => 'required|string',
                        'data' => 'required|date',
                        'allegati.*' => 'file|max:2048',
                    ]);

                    $allegati = [];
                    if ($request->hasFile('allegati')) {
                        foreach ($request->file('allegati') as $file) {
                            // Salva il file nella directory 'contratti_allegati'
                            $path = $file->store('contratti_allegati', 'public');
                            $allegati[] = $path; // Aggiungi solo il percorso
                        }
                    }

                    $contrattoOrario = isset($dati['contratto_orario']) ? 1 : 0;

                    DB::table('contratti')->insert([
                        'id_azienda' => $utente->id_azienda,
                        'cliente_id' => $dati['cliente_id'],
                        'descrizione' => $dati['descrizione'],
                        'prezzo' => $contrattoOrario ? 0 : $dati['prezzo'],
                        'iva' => $contrattoOrario ? 0 : $dati['iva'],
                        'data' => $dati['data'],
                        'contratto_orario' => $contrattoOrario,
                        'allegati' => json_encode($allegati), // Salva solo i percorsi
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    return redirect()->back()->with('success', 'Contratto aggiunto con successo.');
                }


                // Modifica contratto
                if ($dati['azione'] === 'modifica') {
                    $request->validate([
                        'id' => 'required|integer|exists:contratti,id',
                        'cliente_id' => 'required|integer',
                        'descrizione' => 'required|string',
                        'data' => 'required|date',
                        'allegati.*' => 'file|max:2048',
                    ]);

                    $contratto = DB::table('contratti')
                        ->where('id', $dati['id'])
                        ->where('id_azienda', $utente->id_azienda)
                        ->first();

                    $allegati = json_decode($contratto->allegati, true) ?? [];
                    if ($request->hasFile('allegati')) {
                        foreach ($request->file('allegati') as $file) {
                            $path = $file->store('contratti_allegati', 'public');
                            $allegati[] = $path;
                        }
                    }

                    DB::table('contratti')
                        ->where('id', $dati['id'])
                        ->where('id_azienda', $utente->id_azienda)
                        ->update([
                            'cliente_id' => $dati['cliente_id'],
                            'descrizione' => $dati['descrizione'],
                            'prezzo' => $dati['prezzo'],
                            'iva' => $dati['iva'],
                            'data' => $dati['data'],
                            'allegati' => json_encode($allegati),
                            'updated_at' => now(),
                        ]);

                    return redirect()->back()->with('success', 'Contratto aggiornato con successo.');
                }

                // Eliminazione contratto
                // Eliminazione contratto


            }

            if (isset($dati['elimina'])) {
                unset($dati['elimina']);

                $contratto = DB::table('contratti')
                    ->where('id', $dati['id_contratto'])
                    ->where('id_azienda', $utente->id_azienda)
                    ->first();

                if ($contratto) {
                    // Rimuovi i file allegati
                    $allegati = json_decode($contratto->allegati, true) ?? [];
                    foreach ($allegati as $file) {
                        // Percorso reale del file nella directory pubblica
                        $filePath = public_path($file); // Usa direttamente il percorso relativo dal database
                        if (file_exists($filePath)) {
                            unlink($filePath); // Elimina il file
                        }
                    }

                    // Elimina il contratto dal database
                    DB::table('contratti')
                        ->where('id', $dati['id_contratto'])
                        ->where('id_azienda', $utente->id_azienda)
                        ->delete();

                    return redirect()->back()->with('success', 'Contratto e relativi allegati eliminati con successo.');
                } else {
                    return redirect()->back()->with('error', 'Contratto non trovato.');
                }
            }



        }

        // Recupera i contratti filtrando per id_azienda
        $contratti = DB::table('contratti')
            ->leftJoin('clienti', 'contratti.cliente_id', '=', 'clienti.id')
            ->select(
                'contratti.*',
                'clienti.ragione_sociale as cliente_ragione_sociale',
                'clienti.piva as cliente_piva',
                'clienti.indirizzo as cliente_indirizzo',
                'clienti.cap as cliente_cap',
                'clienti.comune as cliente_comune',
                'clienti.provincia as cliente_provincia',
                'clienti.nazione as cliente_nazione',
                'clienti.sdi as cliente_sdi',
                'clienti.pec as cliente_pec'
            )
            ->where('contratti.id_azienda', $utente->id_azienda)
            ->get();

        // Recupera i clienti per l'azienda dell'utente loggato
        $clienti = DB::table('clienti')
            ->where('id_azienda', $utente->id_azienda)
            ->select('id', 'ragione_sociale')
            ->get();

        // Ritorna la vista con i dati
        return View::make('default.contratti', compact('utente', 'contratti', 'clienti'));
    }

    public function aggiornaOreContratto(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        $data = $request->validate([
            'id' => 'required|integer|exists:contratti,id',
            'ore' => 'required|numeric|min:0',
            'costo_orario' => 'required|numeric|min:0',
            'iva' => 'required|numeric|min:0|max:100', // Controlla che l'IVA sia valida
        ]);

        // Calcola il prezzo totale basato sulle ore e il costo orario
        $prezzoTotale = $data['ore'] * $data['costo_orario'];

        // Aggiorna il contratto con i nuovi dati
        DB::table('contratti')
            ->where('id', $data['id'])
            ->where('id_azienda', $utente->id_azienda)
            ->update([
                'ore' => $data['ore'],
                'costo_orario' => $data['costo_orario'],
                'iva' => $data['iva'], // Aggiorna l'IVA
                'prezzo' => $prezzoTotale,
                'updated_at' => now(),
            ]);

        return redirect()->back()->with('success', 'Contratto aggiornato con successo.');
    }



    public function dettagliContratto($id)
    {
        // Verifica che l'utente sia loggato
        $this->is_loggato();
        $utente = session('utente');

        // Recupera i dettagli del contratto e le informazioni del cliente
        $contratto = DB::table('contratti')
            ->leftJoin('clienti', 'contratti.cliente_id', '=', 'clienti.id')
            ->select(
                'contratti.*',
                'clienti.ragione_sociale as cliente_ragione_sociale',
                'clienti.piva as cliente_piva',
                'clienti.indirizzo as cliente_indirizzo',
                'clienti.cap as cliente_cap',
                'clienti.comune as cliente_comune',
                'clienti.provincia as cliente_provincia',
                'clienti.nazione as cliente_nazione',
                'clienti.sdi as cliente_sdi',
                'clienti.pec as cliente_pec'
            )
            ->where('contratti.id', $id)
            ->first();

        // Se il contratto non esiste, reindirizza con errore
        if (!$contratto) {
            return redirect('contratti')->with('error', 'Contratto non trovato.');
        }

        // Passa il contratto alla vista
        return View::make('default.dettaglio_contratto', compact('contratto', 'utente'));
    }
    public function aggiornaFatturazionePeriodica(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        $data = $request->all();

        if (!isset($data['contratti']) || empty($data['giorno_fatturazione'])) {
            return redirect()->back()->with('error', 'Seleziona almeno un contratto e specifica un giorno del mese.');
        }

        foreach ($data['contratti'] as $idContratto) {
            DB::table('contratti')
                ->where('id', $idContratto)
                ->where('id_azienda', $utente->id_azienda) // Aggiunto filtro per id_azienda
                ->update([
                    'giorno_fatturazione' => $data['giorno_fatturazione'],
                    'prossima_fattura' => now()->startOfMonth()->addMonth()->day($data['giorno_fatturazione']),
                ]);
        }

        return redirect('contratti')->with('success', 'Fatturazione periodica aggiornata con successo.');
    }


    /*funzione che invia la fattura dei contratti in modo programmato con CRON*/
    public function fatturazioneAutomatica()
    {
        $utente = session('utente');

        $contratti = DB::table('contratti')
            ->join('clienti', 'contratti.cliente_id', '=', 'clienti.id')
            ->where('contratti.id_azienda', $utente->id_azienda)
            ->whereNotNull('contratti.giorno_fatturazione')
            ->where('contratti.prossima_fattura', '=', now()->format('Y-m-d'))
            ->select('contratti.*',
                'clienti.ragione_sociale',
                'clienti.piva',
                'clienti.indirizzo',
                'clienti.cap',
                'clienti.comune',
                'clienti.provincia',
                'clienti.nazione',
                'clienti.sdi',
                'clienti.pec',
                'clienti.cd_cf' // Aggiunto il campo cd_cf
            )
            ->get();

        if ($contratti->isEmpty()) {
            return "Nessun contratto da fatturare.";
        }

        foreach ($contratti as $contratto) {
            // Calcolo numero fattura incrementale
            $ultimoNumeroDoc = DB::table('dotes')
                ->where('id_azienda', $contratto->id_azienda)
                ->orderBy('id', 'desc')
                ->value('numero_doc');

            $numeroIncrementale = $ultimoNumeroDoc ? (int)substr($ultimoNumeroDoc, 2) + 1 : 1;
            $numeroFattura = 'FT' . str_pad($numeroIncrementale, 6, '0', STR_PAD_LEFT);

            // Calcolo numero progressivo (campo 'numero')
            $num_fattura = DB::select('SELECT IFNULL(MAX(numero) + 1, 1) AS numero FROM dotes WHERE anno = YEAR(NOW()) AND id_azienda = ?', [$contratto->id_azienda])[0]->numero;

            // Calcolo dei valori
            $pu = (float)$contratto->prezzo;
            $ivaPercentuale = (float)$contratto->iva;

            // Calcolo imponibile e imposta
            $imponibile = round($pu / (1 + ($ivaPercentuale / 100)), 2);
            $imposta = round($pu - $imponibile, 2);
            $prezzoTotale = $pu;

            // Creazione del documento dotes (Testata)
            $idDotes = DB::table('dotes')->insertGetId([
                'id_utente' => $utente->id,
                'cd_do' => 'CTR',
                'anno' => now()->year,
                'tipo_documento' => 'fattura',
                'tipologia_documento' => 'TD01',
                'data_doc' => now(),
                'data' => now(),
                'id_azienda' => $contratto->id_azienda,
                'numero_doc' => $numeroFattura,
                'numero' => $num_fattura,
                'imponibile' => $imponibile,
                'imposta' => $imposta,
                'totale' => $prezzoTotale,
                'esigibilita_iva' => 'I',
                'nominativo' => $contratto->ragione_sociale,
                'cf' => $contratto->piva,
                'piva' => $contratto->piva,
                'indirizzo' => $contratto->indirizzo,
                'cap' => $contratto->cap,
                'citta' => $contratto->comune,
                'provincia' => $contratto->provincia,
                'nazione' => $contratto->nazione,
                'sdi' => $contratto->sdi,
                'pec' => $contratto->pec,
                'condizioni_pagamento' => 'TP02',
                'tipologia_pagamento' => 'MP05',
                'stato' => 0,
                'cd_cf' => $contratto->cd_cf, // Inserisci il valore di cd_cf
            ]);

            // Creazione della riga in dorig (Dettaglio)
            DB::table('dorig')->insert([
                'id_utente' => $utente->id,
                'id_dotes' => $idDotes,
                'id_testata' => $idDotes,
                'descrizione' => $contratto->descrizione,
                'qta' => 1,
                'prezzo_unitario' => $imponibile,
                'pu' => $pu,
                'pt' => $prezzoTotale,
                'imposta' => $imposta,
                'imponibile' => $imponibile,
                'id_azienda' => $contratto->id_azienda,
                'iva' => $ivaPercentuale,
                'fattura' => 1,
                'rif_normativo' => null,
            ]);

            // Aggiorna la data della prossima fattura
            $prossimaFattura = now()->startOfMonth()->addMonth()->day($contratto->giorno_fatturazione);
            DB::table('contratti')
                ->where('id', $contratto->id)
                ->update(['prossima_fattura' => $prossimaFattura]);
        }

        return "Fatturazione automatica completata.";
    }

    /*fine della funzione che invia la fattura dei contratti in modo programmato*/


    /*Funzione che invia la fattura singola per ogni contratto*/
    public function fatturaContratto(Request $request)
    {
        $utente = session('utente');
        $idContratto = $request->input('id_contratto');

        $contratto = DB::table('contratti')
            ->join('clienti', 'contratti.cliente_id', '=', 'clienti.id')
            ->where('contratti.id', $idContratto)
            ->where('contratti.id_azienda', $utente->id_azienda)
            ->select(
                'contratti.*',
                'clienti.ragione_sociale',
                'clienti.piva',
                'clienti.indirizzo',
                'clienti.cap',
                'clienti.comune',
                'clienti.provincia',
                'clienti.nazione',
                'clienti.sdi',
                'clienti.pec',
                'clienti.cd_cf' // Aggiunto cd_cf
            )
            ->first();

        if (!$contratto) {
            return redirect()->back()->with('error', 'Contratto non trovato.');
        }

        // Calcolo numero fattura incrementale
        $ultimoNumeroDoc = DB::table('dotes')
            ->where('id_azienda', $contratto->id_azienda)
            ->orderBy('id', 'desc')
            ->value('numero_doc');

        $numeroIncrementale = $ultimoNumeroDoc ? (int)substr($ultimoNumeroDoc, 2) + 1 : 1;
        $numeroFattura = 'FT' . str_pad($numeroIncrementale, 6, '0', STR_PAD_LEFT);

        // Calcolo numero progressivo
        $num_fattura = DB::select('SELECT IFNULL(MAX(numero) + 1, 1) AS numero FROM dotes WHERE anno = YEAR(NOW()) AND id_azienda = ?', [$contratto->id_azienda])[0]->numero;

        // Calcolo dei valori
        $pu = (float)$contratto->prezzo;
        $ivaPercentuale = (float)$contratto->iva;

        // Calcolo imponibile e imposta
        $imponibile = round($pu / (1 + ($ivaPercentuale / 100)), 2);
        $imposta = round($pu - $imponibile, 2);
        $prezzoTotale = $pu;

        // Creazione della fattura (dotes)
        $idDotes = DB::table('dotes')->insertGetId([
            'id_utente' => $utente->id,
            'cd_do' => 'CTR',
            'anno' => now()->year,
            'tipo_documento' => 'fattura',
            'tipologia_documento' => 'TD01',
            'data_doc' => now(),
            'data' => now(),
            'id_azienda' => $contratto->id_azienda,
            'numero_doc' => $numeroFattura,
            'numero' => $num_fattura,
            'imponibile' => $imponibile,
            'imposta' => $imposta,
            'totale' => $prezzoTotale,
            'esigibilita_iva' => 'I',
            'nominativo' => $contratto->ragione_sociale,
            'cf' => $contratto->piva,
            'piva' => $contratto->piva,
            'indirizzo' => $contratto->indirizzo,
            'cap' => $contratto->cap,
            'citta' => $contratto->comune,
            'provincia' => $contratto->provincia,
            'nazione' => $contratto->nazione,
            'sdi' => $contratto->sdi,
            'pec' => $contratto->pec,
            'cd_cf' => $contratto->cd_cf, // Aggiunto cd_cf
            'condizioni_pagamento' => 'TP02',
            'tipologia_pagamento' => 'MP05',
            'stato' => 0,
        ]);

        // Creazione della riga fattura (dorig)
        DB::table('dorig')->insert([
            'id_utente' => $utente->id,
            'id_dotes' => $idDotes,
            'id_testata' => $idDotes,
            'descrizione' => $contratto->descrizione,
            'qta' => 1,
            'prezzo_unitario' => $imponibile,
            'pu' => $pu,
            'pt' => $prezzoTotale,
            'imposta' => $imposta,
            'imponibile' => $imponibile,
            'id_azienda' => $contratto->id_azienda,
            'iva' => $ivaPercentuale,
            'fattura' => 1,
            'rif_normativo' => null,
        ]);

        return redirect()->back()->with('success', 'Il contratto è stato evaso in fattura con successo.');
    }



    /*Fine della Funzione che invia la fattura singola per ogni contratto*/



    public function index(Request $request)
    {

        $this->is_loggato();
        $utente = session('utente');
        $page = 'index';

        $dati = $request->all();

        if (isset($dati['aggiungi_sal'])) {
            unset($dati['aggiungi_sal']);
            $dati['id_operatore_ultimo_sal'] = $utente->id;
            $dati['timestamp_ultimo_sal'] = date('Y-m-d H:i:s');
            DB::table('progetti')->where('id', $dati['id'])->update($dati);
            $dati['id_progetto'] = $dati['id'];
            unset($dati['id']);
            DB::table('sal_progetti')->insert($dati);


            $progetti = DB::select('SELECT * from progetti where id =' . $dati['id_progetto']);
            $progetto = $progetti[0];

            $utenti = DB::select('select * from utenti where id_reparto =' . $utente->id_reparto);
            if (sizeof($utenti) > 0) {


                $titolo = 'Aggiornamento Progetto ' . $progetto->titolo . ' ';
                $contenuto = $dati['descrizione_ultimo_sal'];

                $headings = array("en" =>  $titolo);
                $content = array("en" => $contenuto);
                $fields = array(
                    'app_id' => "7981e39d-e1a4-4b4b-8f84-f3007aaf15ee",
                    'included_segments' => array('All'),
                    'url' => "https://crm.ingenia.cloud",
                    'contents' => $content,
                    'headings' => $headings
                );

                $fields = json_encode($fields);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Authorization: Basic YWE5N2ViZTQtNjg4Mi00NGU1LThiMjUtZjBkMjQxZjA3Y2Ey'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                $response = curl_exec($ch);
                curl_close($ch);
            }

            return Redirect::to('admin/index');

        }
        // Recupera tutti i tipi di documenti
        $documenti = DB::table('do')
            ->select('descrizione', 'cd_do', 'attivo', 'passivo')
            ->where('id_azienda', $utente->id_azienda)
            ->get();

        // Conta ogni documento in base alla descrizione, anche se non ha record
        $documenti_data = $documenti->map(function ($documento) use ($utente) {
            $totale = DB::table('dotes')
                ->where('id_azienda', $utente->id_azienda)
                ->where('cd_do', $documento->cd_do)
                ->count();

            $tipo = $documento->attivo ? 'ca' : 'cp';
            $documento->totale = $totale;
            $documento->link = url("documenti/{$tipo}/{$documento->cd_do}");
            return $documento;
        });

        // Calcola i dati per il grafico mensile di tutti i documenti
        $documenti_mensili = DB::table('dotes')
            ->select(DB::raw("cd_do, MONTH(data_doc) as mese, COUNT(*) as totale"))
            ->where('id_azienda', $utente->id_azienda)
            ->groupBy('cd_do', 'mese')
            ->get()
            ->groupBy('cd_do');

        $grafico_data = [];
        foreach ($documenti_mensili as $cd_do => $dati) {
            $mensile = array_fill(1, 12, 0);
            foreach ($dati as $record) {
                $mensile[$record->mese] = $record->totale;
            }
            $grafico_data[$cd_do] = array_values($mensile);
        }

        return View::make('default.index', compact('page', 'utente', 'documenti', 'documenti_data', 'grafico_data'));

    }

    public function clienti(Request $request){

        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();
        if(isset($dati['aggiungi'])){
            unset($dati['aggiungi']);
            $dati['id_tipologia'] = 2;
            $dati['id_azienda'] = $utente->id_azienda;

            if($_FILES['immagine']['name'] != ''){

                $pathinfo = pathinfo($_FILES['immagine']['name']);
                $nome = Str::random(20);
                $target = 'immagini/clienti/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['immagine']['tmp_name'], $target);
                $dati['immagine'] = $target;
            }
            // Recupera l'ultimo valore del contatore dal database
            $lastCounter = DB::table('clienti')->max('cd_cf');
            // Estrai solo il numero dall'ultimo valore (rimuovendo 'CF')
            if ($lastCounter) {
                $counterCliente = (int) substr($lastCounter, 2);
            } else {
                $counterCliente = 0; // Inizia da zero se non esiste alcun cliente
            }

            $counterCliente++;
            $counterCliente = str_pad($counterCliente, 6, '0', STR_PAD_LEFT);
            $dati['cd_cf'] = 'CF'.$counterCliente;

            $dati['token_utente_per_bando'] = STR::random(20);

            $dati['id_sezione'] = 0;
            $ateco = DB::select('SELECT * from ateco_codici where codice = \''.$dati['ateco_codice'].'\'');
            if(sizeof($ateco) > 0){
                $dati['id_sezione'] = $ateco[0]->id_sezione;
            }

            DB::table('clienti')->insert($dati);
            return Redirect::to('admin/clienti');
        }

        if(isset($dati['modifica'])){
            unset($dati['modifica']);

            if($_FILES['immagine']['name'] != ''){

                $pathinfo = pathinfo($_FILES['immagine']['name']);
                $nome = Str::random(20);
                $target = 'immagini/clienti/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['immagine']['tmp_name'], $target);
                $dati['immagine'] = $target;

            }

            $dati['id_sezione'] = 0;
            $ateco = DB::select('SELECT * from ateco_codici where codice = \''.$dati['ateco_codice'].'\'');
            if(sizeof($ateco) > 0){
                $dati['id_sezione'] = $ateco[0]->id_sezione;
            }

            DB::table('clienti')->where('id',$dati['id'])->update($dati);
            return Redirect::to('admin/clienti');
        }

        if(isset($dati['elimina'])){
            unset($dati['elimina']);
            DB::table('clienti')->where('id',$dati['id'])->delete();
            return Redirect::to('admin/clienti');
        }

        $page = 'index';
        $clienti = DB::select('
    SELECT c.*, a.descrizione as sezione 
    FROM clienti c 
    LEFT JOIN ateco_sezioni a ON a.id = c.id_sezione 
    WHERE c.id_tipologia = 2 
    AND c.id_azienda = ? 
    ORDER BY c.ragione_sociale ASC',
            [$utente->id_azienda]
        );        $agenti = DB::select('SELECT id,nome,cognome from utenti where id_tipologia = 1');

        return View::make('default.clienti', compact('page', 'utente','clienti','agenti'));

    }

    public function articoli(Request $request)
    {

        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if (isset($dati['aggiungi'])) {
            unset($dati['aggiungi']);
            unset($dati['tipo']);

            // Gestione dell'immagine
            if ($_FILES['immagine']['name'] != '') {
                $pathinfo = pathinfo($_FILES['immagine']['name']);
                $nome = Str::random(20);
                $target = 'immagini/clienti/' . $nome . '.' . $pathinfo['extension'];
                move_uploaded_file($_FILES['immagine']['tmp_name'], $target);
                $dati['immagine'] = $target;
            }

            $dati['id_utente'] = $utente->id;
            $dati['id_azienda'] = $utente->id_azienda;

            // Recupera l'ID del nuovo articolo (GTIN)
             // Incrementa per il nuovo articolo
            $gtin = str_pad($dati['codice_articolo'], 12, '0', STR_PAD_LEFT); // GTIN a 12 cifre

            // Recupera il lotto se esiste
            $lotto = isset($dati['lotto']) ? str_pad($dati['lotto'], 5, '0', STR_PAD_LEFT) : '00000';

            // Ottieni la data di creazione corrente nel formato YYMMDD
            $data_creazione = now()->format('ymd');

            // Composizione del barcode EAN-128
            $barcode = $gtin . $lotto . $data_creazione;

            // Aggiungi il codice a barre nei dati per l'inserimento

            $dati['data_creazione'] = now(); // Salva anche la data di creazione dell'articolo


            /*$gtin = '(01)'.str_pad($dati['codice_articolo'], 14, '0', STR_PAD_LEFT); // GTIN a 12 cifre
            $data_scadenza = $a->scadenza_lotto ? '(17)'.date('dmy', strtotime($a->scadenza_lotto)) : '(17)000000'; // Data scadenza o '000000'
            $quantita = '(400)'.str_pad((int)$a->totale_giacenza, 8, '0', STR_PAD_LEFT); // Quantità totale come stringa, senza decimali
            $lotto = '(10)'.str_pad($a->lotto, 5, '0', STR_PAD_LEFT); // Lotto a 5 cifre*/

            $dati['barcode'] = $barcode;
            /*// Composizione del codice a barre EAN-128
            $barcode = $gtin;
            $dati['barcode'] = $barcode;

            if (!file_exists('barcode/'.$utente->id.'_'.$barcode.'.png')) {
                $builder = new Barcode\Builder();
                $builder->setBarcodeType('gs1-128');
                $builder->setFilename('barcode/'.$utente->id.'_'.$barcode.'.png');
                $builder->setImageFormat('png');
                $builder->setWidth(400);
                $builder->setHeight(150);
                $builder->setFontSize(15);
                $builder->setBackgroundColor(255, 255, 255);
                $builder->setPaintColor(0, 0, 0);
                $builder->saveImage($barcode);
            }*/

            // Rimuoviamo le fasi dai dati prima di inserirli nella tabella articoli
            $datiPerInsert = array_filter($dati, function ($key) {
                return $key !== 'fasi';
            }, ARRAY_FILTER_USE_KEY);

            // Inserimento dell'articolo e recupero dell'ID appena inserito
            $id_articolo = DB::table('articoli')->insertGetId($datiPerInsert);

            // Inserimento delle fasi selezionate nella tabella fasi_articoli
            if (isset($dati['fasi']) && is_array($dati['fasi'])) {
                foreach ($dati['fasi'] as $id_fase) {
                    DB::table('fasi_articoli')->insert([
                        'id_azienda' => $utente->id_azienda,
                        'id_utente' => $utente->id,
                        'id_fase' => $id_fase,
                        'id_articolo' => $id_articolo,
                        'tempo_medio_minuti' => 0 // Puoi impostare il valore di default o modificarlo successivamente
                    ]);
                }
            }

            return redirect()->back();
        }





        if (isset($dati['modifica'])) {
            unset($dati['modifica']);
                unset($dati['tipo']);


                if ($_FILES['immagine']['name'] != '') {

                $pathinfo = pathinfo($_FILES['immagine']['name']);
                $nome = Str::random(20);
                $target = 'immagini/clienti/' . $nome . '.' . $pathinfo['extension'];
                move_uploaded_file($_FILES['immagine']['tmp_name'], $target);
                $dati['immagine'] = $target;

            }

            $datiPerInsert = array_filter($dati, function ($key) {
                return $key !== 'fasi';
            }, ARRAY_FILTER_USE_KEY);

            DB::table('articoli')->where('id', $dati['id'])->update($datiPerInsert);

            // Gestione delle fasi selezionate
            if (isset($dati['fasi']) && is_array($dati['fasi'])) {
                // Prima eliminiamo tutte le fasi collegate all'articolo
                DB::table('fasi_articoli')->where('id_articolo', $dati['id'])->delete();

                // Poi inseriamo le nuove fasi selezionate
                foreach ($dati['fasi'] as $id_fase) {
                    DB::table('fasi_articoli')->insert([
                        'id_utente' => $utente->id,
                        'id_fase' => $id_fase,
                        'id_articolo' => $dati['id'],
                        'tempo_medio_minuti' => 0 //modifico dopo il tempo
                    ]);
                }
            }
            return redirect()->back();
        }

        if (isset($dati['carica_materiale'])) {
            unset($dati['carica_materiale']);
            unset($dati['tipo']);

            $dati['id_utente'] = $utente->id;
            DB::table('mgmov')->insertGetId($dati);
            DB::update('update articoli set giacenza = (select sum(qta) as giacenza from mgmov where id_articolo = ' . $dati['id_articolo'] . ') where id =' . $dati['id_articolo']);


            return redirect()->back();
        }

        if (isset($dati['scarica_materiale'])) {
            unset($dati['scarica_materiale']);
            unset($dati['tipo']);


            $dati['id_utente'] = $utente->id;
            $dati['qta'] = $dati['qta'] * -1;
            $dati['sca'] = 1;
            DB::table('mgmov')->insertGetId($dati);

            DB::update('update articoli set giacenza = (select sum(qta) as giacenza from mgmov where id_articolo = ' . $dati['id_articolo'] . ') where id =' . $dati['id_articolo']);


            return redirect()->back();
        }

        if (isset($dati['rettifica_materiale'])) {
            unset($dati['rettifica_materiale']);
            unset($dati['tipo']);


            $giacenza = DB::select('select sum(qta) as giacenza from mgmov where id_articolo = ' . $dati['id_articolo'])[0]->giacenza;


            $dati['id_utente'] = $utente->id;
            $dati['qta'] = $dati['qta'] - $giacenza;
            $dati['ret'] = 1;
            DB::table('mgmov')->insertGetId($dati);

            DB::update('update articoli set giacenza = (select sum(qta) as giacenza from mgmov where id_articolo = ' . $dati['id_articolo'] . ') where id =' . $dati['id_articolo']);

            return redirect()->back();
        }

        if (isset($dati['modifica_db'])) {
            unset($dati['modifica_db']);
            unset($dati['tipo']);

            // Aggiorna il prezzo dell'articolo
            $update['prezzo'] = $dati["prezzo_totale"];
            DB::table('articoli')->where('id', $dati['id'])->update($update);

            // Cancella le voci esistenti nella distinta_base per questo articolo
            DB::table('distinta_base')->where('id_articolo', $dati['id'])->delete();

            // Itera su ciascuna fase associata all'articolo
            foreach ($dati['materiale'] as $id_fase => $materiale_list) {
                // Itera su ciascun materiale associato a questa fase
                foreach ($materiale_list as $posizione => $id_materiale) {
                    if (!empty($id_materiale)) { // Verifica che l'ID del materiale non sia vuoto
                        // Prepara i dati per l'inserimento nella tabella distinta_base
                        $insert_materiale = [
                            'id_articolo' => $dati['id'], // L'ID dell'articolo
                            'id_fase_articolo' => $id_fase, // Usa l'ID della fase corretta (non dell'articolo)
                            'id_materiale' => $id_materiale, // L'ID del materiale
                            'qta' => $dati['quantita'][$id_fase][$posizione], // Quantità per quel materiale
                        ];

                        // Inserisci il materiale nella tabella distinta_base
                        DB::table('distinta_base')->insert($insert_materiale);
                    }
                }
            }

            // Reindirizza alla pagina degli articoli dopo il salvataggio
            return redirect()->back();
        }


        if (isset($dati['elimina'])) {
            unset($dati['elimina']);
            unset($dati['tipo']);


            DB::table('articoli')->where('id', $dati['id'])->delete();
            return redirect()->back();
        }

        $page = 'index';

        $articoli = DB::table('articoli')
            ->leftJoin('mg', 'articoli.id_mg', '=', 'mg.id')
            ->select('articoli.*', 'mg.descrizione as magazzino_descrizione')
            ->where('articoli.id_azienda', $utente->id_azienda) // Filtra per l'azienda associata all'utente
            ->get();


        foreach ($articoli as $a) {
            $a->distinta_base = DB::select('
        SELECT db.*, m.titolo as materiale, m.um 
        FROM distinta_base db
        JOIN articoli m ON m.id = db.id_materiale
        WHERE db.id_articolo = ' . $a->id . ' 
        ORDER BY db.posizione ASC');
        }



        // Creiamo un array associativo per le fasi associate agli articoli
        $fasi_associate = [];
        foreach ($articoli as $articolo) {
            $fasi_associate[$articolo->id] = DB::table('fasi_articoli')
                ->join('fasi', 'fasi_articoli.id_fase', '=', 'fasi.id')
                ->where('fasi_articoli.id_articolo', $articolo->id)
                ->select('fasi.id', 'fasi.descrizione')
                ->get();

        }
        foreach ($articoli as $a) {

        $a->fasi_associate = DB::table('fasi_articoli')
            ->where('id_articolo', $a->id)
            ->pluck('id_fase')
            ->toArray();
        }
        $materiali = DB::table('articoli')->where('tipologia', 1)->where('id_azienda', $utente->id_azienda)->get();
        $magazzini = DB::table('mg')->where('id_azienda', $utente->id_azienda)->get();
        $fasi = DB::table('fasi')->where('id_azienda', $utente->id_azienda)->get();
        $tipo = $request->query('tipo', 'prodotto_finito');

        return View::make('default.articoli', compact('page', 'utente','articoli','materiali', 'magazzini', 'fasi',  'fasi_associate', 'tipo'));

    }

    public function fasiLavorazione(Request $request) {
        $this->is_loggato();
        $dati = $request->all();
        $utente = session('utente');

        if (isset($dati['crea_fase'])) {
            DB::table('fasi')->insert([
                'descrizione' => $request->descrizione,
                'id_utente' => $utente->id,
                'id_azienda' => $utente->id_azienda,

            ]);

            return redirect()->back()->with('success', 'Fase creata con successo!');
        }

        if (isset($dati['elimina'])) {
            $id = $dati['id_fase'];
            DB::table('fasi')->where('id', $id)->delete();

            return redirect()->back()->with('success', 'Fase eliminata con successo!');
        }

        // Modifica della fase
        if (isset($dati['modifica_fase'])) {
            DB::table('fasi')->where('id', $dati['id_fase'])->update([
                'descrizione' => $dati['edit_descrizione']
            ]);

            return redirect()->back()->with('success', 'Fase aggiornata con successo!');
        }

        // Recupera tutte le fasi
        $fasi = DB::table('fasi')
            ->where('id_azienda', $utente->id_azienda)
            ->get();

        return View::make('default.fasi_di_lavorazione', compact('utente', 'fasi'));
    }

    public function updateFase(Request $request) {
        $id_fase = $request->input('id_fase');
        $ordine = $request->input('ordine');

        // Aggiorna l'ordine della fase
        DB::table('fasi')
            ->where('id', $id_fase)
            ->update(['ordinamento' => $ordine]);

        return response()->json(['success' => true]);
    }



    public function odl(Request $request){

        $this->is_loggato();
        $dati = $request->all();
        $utente = session('utente');
        $reparto = session('reparto');
        $utente = session('utente');


        if(isset($dati['aggiungi'])){
            unset($dati['aggiungi']);
            $dati['data'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-',$dati['data'])));
            $dati['id_utente'] = $utente->id;
            $dati['id_azienda'] = $utente->id_azienda;

            $dati_odl = $dati;
            unset($dati_odl['id_dorig']);

            // Se l'ODL è creato da un ordine, eredita la commessa dal documento
            if(isset($dati['id_dotes']) && $dati['id_dotes'] > 0) {
                $doc_commessa = DB::table('dotes')->where('id', $dati['id_dotes'])->value('id_commessa');
                if($doc_commessa) {
                    $dati_odl['id_commessa'] = $doc_commessa;
                } else {
                    // Controlla anche nella tabella commesse_documenti
                    $commessa_doc = DB::table('commesse_documenti')
                        ->where('id_dotes', $dati['id_dotes'])
                        ->where('id_azienda', $utente->id_azienda)
                        ->value('id_commessa');
                    if($commessa_doc) {
                        $dati_odl['id_commessa'] = $commessa_doc;
                    }
                }
            }

            $insert_riga['id_odl'] = DB::table('odl')->insertGetId($dati_odl);

            //fare query delle fare per ogni articolo

            $fasi_articoli = DB::table('fasi_articoli')
                ->where('id_articolo', $dati['id_articolo'])
                ->where('id_azienda', $utente->id_azienda) // Aggiunge il filtro per l'azienda dell'utente
                ->get();




            foreach ($fasi_articoli as $fase) {
                $insert_riga['id_utente'] = $dati['id_utente'];
                $insert_riga['id_azienda'] = $utente->id_azienda;
                $insert_riga['id_fase'] = $fase->id_fase;
                $insert_riga['id_plc'] = 0;
                $insert_riga['odl'] = $dati['numero'];
                $insert_riga['qta'] = $dati['qta'];
                $insert_riga['id_dorig'] = isset($dati['id_dorig']) ? $dati['id_dorig'] : 0; // Imposta a 0 se non è presente
                DB::table('odl_righe')->insert($insert_riga);
            }


            if (isset($dati['id_dorig']) && $dati['id_dorig'] != 0) {
                DB::table('dorig')
                    ->where('id_azienda', $utente->id_azienda)
                    ->where('id', $dati['id_dorig'])
                    ->update(['stato_prod' => 1]);
            }


            return Redirect::to('admin/dettaglio_odl/'.$insert_riga['id_odl']);
        }


        if(isset($dati['modifica'])){
            unset($dati['modifica']);
            $dati['id_utente'] = $utente->id;
            $dati['data'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-',$dati['data'])));
            DB::table('odl')->where('id',$dati['id'])->update($dati);
            return Redirect::to('admin/odl');

        }

        if(isset($dati['elimina'])){
            unset($dati['elimina']);
            $dati['id_utente'] = $utente->id;
            DB::table('odl')->where('id',$dati['id'])->delete();
            DB::table('odl_righe')->where('Id_odl',$dati['id'])->delete();
            return Redirect::to('admin/odl');
        }

        if(isset($dati['modifica_commessa'])){
            unset($dati['modifica_commessa']);
            DB::table('utenti')->where('id',$utente->id)->update($dati);
            $utente->commessa_attuale = $dati['commessa_attuale'];

            session(['utente' => $utente]);
            session()->save();
            return Redirect::to('cliente/odl');
        }

        $num_odl = DB::select('SELECT ifnull(max(numero)+1,1) as numero from odl where id_utente = ? AND id_azienda = ?', [$utente->id, $utente->id_azienda])[0]->numero;

        if ($reparto) {
            $odl = DB::select('
        SELECT o.*, a.titolo as articolo 
        FROM odl o 
        LEFT JOIN articoli a ON a.id = o.id_articolo  
        WHERE o.id_utente = ? 
        AND o.id_azienda = ?
        AND o.id IN (
            SELECT id_odl 
            FROM odl_righe 
            WHERE FIND_IN_SET(id_fase, ?) 
            AND id_utente = ? 
            AND id_azienda = ?
        )
        ORDER BY data DESC, id DESC',
                [$utente->id, $utente->id_azienda, $reparto->id_fasi, $utente->id, $utente->id_azienda]
            );
        } else {
            $odl = DB::select('
        SELECT o.*, a.titolo as articolo 
        FROM odl o 
        LEFT JOIN articoli a ON a.id = o.id_articolo  
        WHERE o.id_utente = ? 
        AND o.id_azienda = ?
        ORDER BY data DESC, id DESC',
                [$utente->id, $utente->id_azienda]
            );
        }

        $articoli = DB::select('SELECT * from articoli where tipologia = 0 and id_utente = ? AND id_azienda = ?', [$utente->id, $utente->id_azienda]);

        return View::make('default.odl',compact('utente','odl','articoli','num_odl'));


    }

    public function dettaglio_odl($id_odl, Request $request)
    {
        $this->is_loggato();
        $dati = $request->all();
        $utente = session('utente'); // Assumiamo che $utente contenga l'azienda

        if (isset($dati['start_fase'])) {
            unset($dati['start_fase']);

            $righe = DB::SELECT('SELECT * from odl_righe where id = ? and id_azienda = ?', [$dati['id'], $utente->id_azienda]);

            if (sizeof($righe) > 0) {
                $riga = $righe[0];

                $odl = DB::select('SELECT * from odl where id = ? and id_azienda = ?', [$riga->id_odl, $utente->id_azienda]);
                if (sizeof($odl) > 0) {
                    $odl = $odl[0];

                    DB::update('update odl set stato = 1 where stato = 0 and id = ? and id_azienda = ?', [$odl->id, $utente->id_azienda]);

                    DB::update('update odl_righe set inizio = NOW(), id_utente = ? where id = ? and id_azienda = ?', [$dati['id_operatore'], $dati['id'], $utente->id_azienda]);
                }
            }

            return Redirect::to('admin/dettaglio_odl/' . $id_odl);
        }

        if (isset($dati['fine_fase'])) {
            unset($dati['fine_fase']);
            $righe = DB::SELECT('SELECT * from odl_righe where id = ? and id_azienda = ?', [$dati['id'], $utente->id_azienda]);

            if (sizeof($righe) > 0) {
                $riga = $righe[0];
                ApiController::chudi_fase(
                    $dati['id'],
                    $dati['quantita'],
                    $dati['note'],
                    $dati['id_fase'],
                    $dati['lotto'] ?? null,  // Passa il lotto solo se esiste, altrimenti null
                    $utente,
                    $riga->id_odl,
                    $dati['id_dorig']
                );
               /* ApiController::chiudi_odl($riga->id_odl);*/

                return Redirect::to('admin/dettaglio_odl/' . $id_odl);
            }
        }


        $odl = DB::select('SELECT o.*, a.descrizione as articolo from odl o LEFT JOIN articoli a ON a.id = o.id_articolo where o.id = ? and o.id_azienda = ?', [$id_odl, $utente->id_azienda]);

        if (sizeof($odl) > 0) {
            $odl = $odl[0];
            $odl_righe = DB::select('
    SELECT o.*, f.descrizione as nome_fase 
    FROM odl_righe o 
    LEFT JOIN fasi f ON f.id = o.id_fase 
    WHERE o.id_odl = ? AND o.id_azienda = ?
    ORDER BY f.ordinamento', [$id_odl, $utente->id_azienda]);


            $operatori = DB::select('SELECT * from utenti where id_tipologia = 3 and id_azienda = ?', [$utente->id_azienda]);

            // Recupera l'articolo associato all'ODL
            $articoli = DB::select('SELECT * FROM articoli WHERE id = ? and id_azienda = ?', [$odl->id_articolo, $utente->id_azienda]);

            if (count($articoli) > 0) {
                $articolo = $articoli[0];

                // Aggiungi i materiali per ogni riga dell'ODL
                foreach ($odl_righe as &$riga) {
                    // Recupera i materiali associati alla fase specifica dalla distinta base
                    $riga->materiali = DB::select('
                    SELECT a.titolo, a.id, db.qta, db.id_fase_articolo
                    FROM articoli a 
                    LEFT JOIN distinta_base db ON db.id_materiale = a.id
                    WHERE db.id_articolo = ? AND db.id_fase_articolo = ? AND a.id_azienda = ?',
                        [$articolo->id, $riga->id_fase, $utente->id_azienda]
                    );
                }

                return View::make('default.dettaglio_odl', compact('utente', 'odl', 'odl_righe', 'articolo', 'operatori'));
            }
        }
    }

    public function inventario()
    {
        $this->is_loggato();

        $utente = session('utente');

        // Recupera solo gli articoli con tipologia 0
        $articoli = DB::table('articoli')->where('tipologia', 0)->where('id_azienda', $utente->id_azienda)->get();
        foreach ($articoli as $a) {
            $a->distinta_base = DB::select('
        SELECT db.*, m.titolo as materiale, m.um 
        FROM distinta_base db
        JOIN articoli m ON m.id = db.id_materiale
        WHERE db.id_articolo = ' . $a->id . ' 
        ORDER BY db.posizione ASC');
        }
        return view('default.inventario', compact('articoli', 'utente'));
    }
    public function trasferimento_mg()
    {
        $this->is_loggato();

        $utente = session('utente');

        // Recupera solo gli articoli con tipologia 0
        $articoli = DB::table('articoli')->where('tipologia', 0)->where('id_azienda', $utente->id_azienda)->get();
        foreach ($articoli as $a) {
            $a->distinta_base = DB::select('
        SELECT db.*, m.titolo as materiale, m.um 
        FROM distinta_base db
        JOIN articoli m ON m.id = db.id_materiale
        WHERE db.id_articolo = ' . $a->id . ' 
        ORDER BY db.posizione ASC');
        }
        $magazzini = DB::table('mg')
            ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
            ->get();

        return view('default.trasferimento_mg', compact('articoli', 'utente', 'magazzini'));
    }

    public function carico()
    {
        $this->is_loggato();

        $utente = session('utente');

        // Recupera solo gli articoli con tipologia 0
        $articoli = DB::table('articoli')->where('tipologia', 0)->where('id_azienda', $utente->id_azienda)->get();
        foreach ($articoli as $a) {
            $a->distinta_base = DB::select('
        SELECT db.*, m.titolo as materiale, m.um 
        FROM distinta_base db
        JOIN articoli m ON m.id = db.id_materiale
        WHERE db.id_articolo = ' . $a->id . ' 
        ORDER BY db.posizione ASC');
        }

        $magazzini = DB::table('mg')->where('id_azienda', $utente->id_azienda)->get();
        return view('default.carico', compact('articoli', 'utente', 'magazzini'));
    }

    public function scarico()
    {
        $this->is_loggato();

        $utente = session('utente');

        // Recupera solo gli articoli con tipologia 0
        $articoli = DB::table('articoli')->where('tipologia', 0)->where('id_azienda', $utente->id_azienda)->get();
        foreach ($articoli as $a) {
            $a->distinta_base = DB::select('
        SELECT db.*, m.titolo as materiale, m.um 
        FROM distinta_base db
        JOIN articoli m ON m.id = db.id_materiale
        WHERE db.id_articolo = ' . $a->id . ' 
        ORDER BY db.posizione ASC');
        }
        return view('default.scarico', compact('articoli', 'utente'));
    }

    public function trasferimentoMagazzino(Request $request, $id)
    {
        $utente = session('utente');
        $quantityToTransfer = $request->input('quantita');
        $targetWarehouse = $request->input('magazzino_destinazione');
        $magazzinoOrigine = $request->input('magazzino_origine');
        $lotto = $request->input('lotto');
        $scadenza = $request->input('scadenza');// Assumiamo che il lotto sia passato dal frontend

        // Verifica che la quantità da trasferire sia disponibile nel magazzino di origine
        $giacenzaLotto = DB::table('mgmov')
            ->where('id_articolo', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->where('id_mg', $magazzinoOrigine)
            ->where('lotto', $lotto)
            ->sum('qta');

        if ($giacenzaLotto < $quantityToTransfer) {
            return response()->json(['success' => false, 'message' => 'Quantità non disponibile per il trasferimento']);
        }

        // Registra lo scarico dal magazzino di origine in mgmov
        DB::table('mgmov')->insert([
            'id_azienda' => $utente->id_azienda,
            'id_articolo' => $id,
            'id_mg' => $magazzinoOrigine,
            'lotto' => $lotto,
            'scadenza_lotto' => $scadenza,
            'qta' => -$quantityToTransfer,
            'datamov' => now(),
            'causale' => 'Scarico per trasferimento'
        ]);

        // Registra il carico nel magazzino di destinazione in mgmov
        DB::table('mgmov')->insert([
            'id_azienda' => $utente->id_azienda,
            'id_articolo' => $id,
            'id_mg' => $targetWarehouse,
            'lotto' => $lotto,
            'scadenza_lotto' => $scadenza,
            'qta' => $quantityToTransfer,
            'datamov' => now(),
            'causale' => 'Carico per trasferimento'
        ]);

        return response()->json(['success' => true, 'message' => 'Trasferimento completato con successo']);
    }



    // Metodo per verificare se un articolo esiste tramite barcode
    public function controlloArticolo(Request $request)
    {
        $utente = session('utente');
        $barcode = $request->query('barcode');
        $article = DB::table('articoli')->where('id_azienda', $utente->id_azienda)->where('barcode', $barcode)->first();

        if ($article) {
            $magazzinoOrigine = DB::table('mg')
                ->where('id_azienda', $utente->id_azienda)
                ->where('id', $article->id_mg)
                ->first();
            $lottiMagazzini = DB::table('mgmov')
                ->join('mg', 'mg.id', '=', 'mgmov.id_mg')
                ->select('mgmov.lotto as lotto','mgmov.scadenza_lotto as scadenza', 'mg.descrizione as magazzino_descrizione', 'mg.id as id_magazzino', DB::raw('SUM(mgmov.qta) as totale_giacenza'))
                ->where('mgmov.id_articolo', $article->id)
                ->where('mgmov.id_azienda', $utente->id_azienda)
                ->groupBy('mgmov.lotto', 'mg.descrizione', 'mg.id')
                ->havingRaw('SUM(mgmov.qta) > 0') // Solo lotti con giacenza positiva
                ->get();

            $giacenzePerLottoEMagazzino = DB::table('mgmov')
                ->join('mg', 'mg.id', '=', 'mgmov.id_mg')
                ->select(
                    'mgmov.lotto',
                    'mgmov.scadenza_lotto as scadenza',
                    'mg.descrizione as magazzino_descrizione',
                    'mg.id as id_magazzino',
                    DB::raw('SUM(mgmov.qta) as totale_giacenza')
                )
                ->where('mgmov.id_articolo', $article->id)
                ->where('mgmov.id_azienda', $utente->id_azienda)
                ->groupBy('mgmov.lotto', 'mg.descrizione', 'mg.id')
                ->havingRaw('SUM(mgmov.qta) > 0') // Solo lotti con giacenza positiva
                ->get();


            return response()->json([
                'success' => true,
                'article' => $article,
                'magazzinoOrigine' => $magazzinoOrigine,
                'lottiMagazzini' => $lottiMagazzini,
                'giacenze' => $giacenzePerLottoEMagazzino
            ]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function decodeBarcode(Request $request)
    {
        $barcode = $request->input('barcode');


        if (is_null($barcode) || trim($barcode) === '') {
            return response()->json([
                'success' => false,
                'message' => 'Il codice a barre è mancante o non valido.',
            ], 400);
        }

        $decoder = new Decoder();
        try {
            $result = $decoder->decode('010000112233445517210325400000001501012345');

            if ($result->isGS1()) {
                return response()->json([
                    'success' => true,
                    'data' => $result->toArray(),
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Il codice a barre non è conforme allo standard GS1.',
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Errore nella decodifica del codice a barre: ' . $e->getMessage(),
            ], 500);
        }
    }



    // Metodo per aggiornare la giacenza dell'articolo
    public function updateGiacenza(Request $request, $id)
    {
        $utente = session('utente');
        $newQuantity = $request->input('giacenza');
        $causale = $request->input('causale'); // Assume che la causale venga passata dalla richiesta
        $lotto = $request->input('lotto');
        $magazzinoId = $request->input('magazzinoId');
        $scadenza = $request->input('scadenza');


        // Recupera la giacenza corrente per il lotto e il magazzino specificati
        $giacenzaLotto = DB::table('mgmov')
            ->where('id_articolo', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->where('id_mg', $magazzinoId)
            ->where('lotto', $lotto)
            ->sum('qta');
        if ($giacenzaLotto === null) {
            return response()->json(['success' => false, 'message' => 'Lotto non trovato nel magazzino selezionato']);
        }

        // Calcola la differenza di quantità da rettificare per il lotto specifico
        $differenzaGiacenza = $newQuantity - $giacenzaLotto;


        // Crea una nuova registrazione in mgmov per tracciare la rettifica per il lotto e il magazzino specificati
        DB::table('mgmov')->insert([
            'id_articolo' => $id,
            'id_utente' => $utente->id,
            'id_mg' => $magazzinoId,
            'lotto' => $lotto,
            'qta' => $differenzaGiacenza,
            'scadenza_lotto' => $scadenza,
            'causale' => $causale,
            'id_azienda' => $utente->id_azienda,
            'datamov' => now(),
        ]);

        // Aggiorna la giacenza totale dell'articolo in base alla somma della quantità in mgmov
        $updated = DB::update('UPDATE articoli 
                           SET giacenza = (SELECT SUM(qta) FROM mgmov WHERE id_articolo = ?) 
                           WHERE id = ? AND id_azienda = ?',
            [$id, $id, $utente->id_azienda]);

        if ($updated) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function caricoMagazzino(Request $request, $id)
    {
        $utente = session('utente');
        $addQuantity = $request->input('giacenza');
        $causale = $request->input('causale');
        $lotto = $request->input('lotto');
        $mg = $request->input('mg');
        $scadenza = $request->input('addScadenza');
        // Recupera l'articolo e la sua giacenza corrente
        $articolo = DB::table('articoli')->where('id', $id)->where('id_azienda', $utente->id_azienda)->first();
        if (!$articolo) {
            return response()->json(['success' => false, 'message' => 'Articolo non trovato']);
        }

        // Aggiorna la giacenza sommando la nuova quantità
        $newGiacenza = $articolo->giacenza + $addQuantity;

        // Inserisce il movimento in mgmov
        DB::table('mgmov')->insert([
            'id_articolo' => $id,
            'id_utente' => $utente->id,
            'id_mg' => $mg,
            'lotto' => $lotto,
            'scadenza_lotto' => $scadenza,
            'qta' => $addQuantity,
            'causale' => $causale,
            'ret' => 0, // Indica un carico a magazzino
            'id_azienda' => $utente->id_azienda,
            'datamov' => now()
        ]);

        // Aggiorna la giacenza totale dell'articolo
        $updated = DB::table('articoli')
            ->where('id', $id)
            ->update(['giacenza' => $newGiacenza]);

        return response()->json(['success' => $updated ? true : false]);
    }


    public function scaricoMagazzino(Request $request, $id)
    {
        $utente = session('utente');
        $removeQuantity = $request->input('giacenza');
        $causale = $request->input('causale');
        $lotto = $request->input('lotto');
        $magazzinoId = $request->input('magazzinoId');
        $scadenza = $request->input('scadenza');


        // Recupera l'articolo e la sua giacenza corrente
        $articolo = DB::table('articoli')->where('id', $id)->where('id_azienda', $utente->id_azienda)->first();
        if (!$articolo) {
            return response()->json(['success' => false, 'message' => 'Articolo non trovato']);
        }

        // Verifica che la giacenza attuale sia sufficiente
        if ($articolo->giacenza < $removeQuantity) {
            return response()->json(['success' => false, 'message' => 'Giacenza insufficiente per lo scarico richiesto']);
        }

        // Calcola la nuova giacenza dopo lo scarico
        $newGiacenza = $articolo->giacenza - $removeQuantity;

        // Inserisce il movimento in mgmov
        DB::table('mgmov')->insert([
            'id_articolo' => $id,
            'id_utente' => $utente->id,
            'lotto' => $lotto,
            'qta' => -$removeQuantity, // Quantità negativa per indicare uno scarico
            'causale' => $causale,
            'id_mg' => $magazzinoId,
            'scadenza_lotto' => $scadenza,
            'ret' => 1, // Indica uno scarico da magazzino
            'id_azienda' => $utente->id_azienda,
            'datamov' => now()
        ]);

        // Aggiorna la giacenza dell'articolo
        $updated = DB::table('articoli')
            ->where('id', $id)
            ->update(['giacenza' => $newGiacenza]);

        return response()->json(['success' => $updated ? true : false]);
    }


    public function articoliProdottiFiniti()
    {
        $this->is_loggato();

        $utente = session('utente');

        // Recupera solo gli articoli con tipologia 0
        $articoli = DB::table('articoli')->where('tipologia', 0)->where('id_azienda', $utente->id_azienda)->get();
        foreach ($articoli as $a) {
            $a->distinta_base = DB::select('
        SELECT db.*, m.titolo as materiale, m.um 
        FROM distinta_base db
        JOIN articoli m ON m.id = db.id_materiale
        WHERE db.id_articolo = ' . $a->id . ' 
        ORDER BY db.posizione ASC');
        }
        return view('default.prodotti_finiti', compact('articoli', 'utente'));
    }

    public function distintaBase(Request $request, $id)
    {
        $this->is_loggato();

        $utente = session('utente');
        $articolo = DB::table('articoli')->where('id', $id)->first();

        if ($request->has('modifica_db')) {
            $dati = $request->all();

            DB::table('articoli')->where('id', $id)->update(['prezzo' => $dati["prezzo_totale"]]);

            DB::table('distinta_base')->where('id_articolo', $id)->delete();

            foreach ($dati['materiale'] as $id_fase => $materiali) {
                foreach ($materiali as $index => $id_materiale) {
                    if (!empty($id_materiale)) {
                        DB::table('distinta_base')->insert([
                            'id_articolo' => $id,
                            'id_fase_articolo' => $id_fase,
                            'id_materiale' => $id_materiale,
                            'qta' => $dati['quantita'][$id_fase][$index] ?? 0,
                        ]);
                    }
                }
            }

            return Redirect::to('admin/articoli')->with('success', 'Distinta base aggiornata con successo');
        }

        $fasi_associate = DB::table('fasi_articoli')
            ->join('fasi', 'fasi_articoli.id_fase', '=', 'fasi.id')
            ->where('fasi_articoli.id_articolo', $id)
            ->select('fasi.id', 'fasi.descrizione')
            ->get();

        $materiali = DB::table('articoli')->where('id_azienda', $utente->id_azienda)->where('tipologia', 1)->get();

        $distinta_base = DB::table('distinta_base')
            ->join('articoli as m', 'm.id', '=', 'distinta_base.id_materiale')
            ->where('distinta_base.id_articolo', $id)
            ->select('distinta_base.*', 'm.titolo as materiale', 'm.um')
            ->get()
            ->groupBy('id_fase_articolo');

        return view('default.distinta_base', compact('articolo', 'fasi_associate', 'materiali', 'distinta_base', 'utente'));
    }





    /* public function articoli(Request $request){

         $this->is_loggato();
         $dati = $request->all();
         $utente = session('utente');
         if(isset($dati['aggiungi'])){
             unset($dati['aggiungi']);

             if($_FILES['immagine']['name'] != ''){
                 $target = 'img/' . $_FILES['immagine']['name'];
                 move_uploaded_file($_FILES['immagine']['tmp_name'], $target);
                 $dati['immagine'] = $target;
             }


             $dati['id_utente'] = $utente->id;

             DB::table('articoli')->insert($dati);
             return Redirect::to('admin/articoli');
         }

         if(isset($dati['modifica'])){
             unset($dati['modifica']);

             if($_FILES['immagine']['name'] != ''){
                 $target = 'img/' . $_FILES['immagine']['name'];
                 move_uploaded_file($_FILES['immagine']['tmp_name'], $target);
                 $dati['immagine'] = $target;
             }

             $dati['id_utente'] = $utente->id;
             DB::table('articoli')->where('id',$dati['id'])->update($dati);
             return Redirect::to('admin/articoli');
         }


         if(isset($dati['carica_materiale'])){
             unset($dati['carica_materiale']);

             $dati['id_utente'] = $utente->id;
             $dati['car'] = 1;
             DB::table('mgmov')->insertGetId($dati);

             return Redirect::to('admin/articoli');
         }

         if(isset($dati['scarica_materiale'])){
             unset($dati['scarica_materiale']);

             $dati['id_utente'] = $utente->id;
             $dati['qta'] = $dati['qta'] * -1;
             $dati['sca'] = 1;
             DB::table('mgmov')->insertGetId($dati);

             return Redirect::to('admin/articoli');
         }

         if(isset($dati['rettifica_materiale'])){
             unset($dati['rettifica_materiale']);

             $giacenza = DB::select('SELECT ifnull(sum(mgmov.qta),0) as giacenza from mgmov where id_articolo = '.$dati['id_articolo'])[0]->giacenza;

             $dati['id_utente'] = $utente->id;
             $dati['qta'] = $dati['qta'] - $giacenza;
             $dati['ret'] = 1;
             DB::table('mgmov')->insertGetId($dati);

             return Redirect::to('admin/articoli');
         }


         if (isset($dati['modifica_db'])) {
             unset($dati['modifica_db']);

             DB::table('distinta_base')->where('id_articolo',$dati['id'])->delete();
             foreach($dati['materiale'] as $key => $value){
                 if($value != '') {
                     $insert_materiale['id_utente'] = $utente->id;
                     $insert_materiale['posizione'] = $key;
                     $insert_materiale['id_articolo'] = $dati['id'];
                     $insert_materiale['id_fase'] = $dati['id_fase'];
                     $insert_materiale['id_materiale'] = $value;
                     $insert_materiale['qta'] = $dati['quantita'][$key];
                     DB::table('distinta_base')->insert($insert_materiale);
                 }
             }

             return Redirect::to('admin/articoli');
         }

         if (isset($dati['modifica_fasi'])) {
             unset($dati['modifica_fasi']);

             DB::table('fasi_articoli')->where('id_articolo',$dati['id'])->delete();
             foreach($dati['fase'] as $key => $value){
                 if($value != '') {
                     $insert_fase['id_utente'] = $utente->id;
                     $insert_fase['ordinamento'] = $key;
                     $insert_fase['id_articolo'] = $dati['id'];
                     $insert_fase['id_fase'] = $value;
                     $insert_fase['id_plc'] = $dati['plc'][$key];
                     $insert_fase['tipologia'] = $dati['tipologia'][$key];
                     DB::table('fasi_articoli')->insert($insert_fase);
                 }
             }

             return Redirect::to('admin/articoli');
         }


         if(isset($dati['elimina'])){
             unset($dati['elimina']);
             $dati['id_utente'] = $utente->id;
             DB::table('articoli')->where('id',$dati['id'])->delete();
             return Redirect::to('admin/articoli');
         }

         $articoli = DB::select('SELECT * from articoli where id_utente = '. $utente->id .' order by id desc limit 0,10');


         return View::make('default.articoli',compact('utente','articoli'));


     }*/




    public function documenti(Request $request, $ciclo, $cd_do) {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Recupera i documenti dalla tabella 'dotes' in base al cd_do e id_azienda
        $dotes = DB::table('dotes')
            ->where('cd_do', $cd_do)
            ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
            ->get();

        // Per ogni documento 'dotes', trova i documenti correlati
        $documenti_correlati = DB::table('dotes')
            ->whereIn('id_dotes_evade', $dotes->pluck('id')) // Trova i documenti correlati
            ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
            ->get();

        if (isset($dati['elimina'])) {
            unset($dati['elimina']);
            DB::table('dorig')
                ->where('id_dotes', $dati['id_ordine'])
                ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
                ->delete();

            // Elimina il record nella tabella 'dotes'
            DB::table('dotes')
                ->where('id', $dati['id_ordine'])
                ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
                ->delete();

            return Redirect::to('documenti/' . $ciclo . '/' . $cd_do);
        }

        return View::make('default.documenti', compact('utente', 'dotes', 'cd_do', 'documenti_correlati'));
    }

    public function magazzini(Request $request, $id, $codice_magazzino) {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();
        // Recupera il magazzino filtrato per azienda
        $magazzino = DB::table('mg')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
            ->first();

        // Filtra gli articoli in base al codice del magazzino e azienda
        $articoli = DB::select(
            'SELECT mgmov.id_articolo, mgmov.lotto AS lotto, mgmov.scadenza_lotto, articoli.titolo, articoli.um, articoli.tipologia, articoli.descrizione, articoli.prezzo, articoli.codice_articolo, articoli.punto_riordino, 
             SUM(mgmov.qta) AS totale_giacenza
             FROM mgmov
             JOIN articoli ON articoli.id = mgmov.id_articolo
             WHERE mgmov.id_mg = ? AND mgmov.id_azienda = ? AND articoli.id_azienda = ?
             GROUP BY mgmov.id_articolo, mgmov.lotto, articoli.titolo, articoli.um, articoli.codice_articolo',
            [$id, $utente->id_azienda, $utente->id_azienda]
        );



        foreach ($articoli as $a) {
            // Preparazione del barcode EAN-128
            $gtin = '(01)'.str_pad($a->codice_articolo, 14, '0', STR_PAD_LEFT); // GTIN a 12 cifre
            $data_scadenza = $a->scadenza_lotto ? '(17)'.date('dmy', strtotime($a->scadenza_lotto)) : '(17)000000'; // Data scadenza o '000000'
            $quantita = '(400)'.str_pad((int)$a->totale_giacenza, 8, '0', STR_PAD_LEFT); // Quantità totale come stringa, senza decimali
            $lotto = '(10)'.str_pad($a->lotto, 5, '0', STR_PAD_LEFT); // Lotto a 5 cifre


            // Composizione del codice a barre EAN-128
            $a->barcode = $gtin .$data_scadenza.$quantita. $lotto;


            if (!file_exists('barcode/'.$utente->id.'_'.$a->barcode.'.png')) {
                $builder = new Barcode\Builder();
                $builder->setBarcodeType('gs1-128');
                $builder->setFilename('barcode/'.$utente->id.'_'.$a->barcode.'.png');
                $builder->setImageFormat('png');
                $builder->setWidth(400);
                $builder->setHeight(150);
                $builder->setFontSize(15);
                $builder->setBackgroundColor(255, 255, 255);
                $builder->setPaintColor(0, 0, 0);
                $builder->saveImage($a->barcode);
            }

        }


        /*foreach ($articoli as $a) {
            $a->distinta_base = DB::select('
            SELECT db.*, m.titolo as materiale, m.um 
            FROM distinta_base db
            JOIN articoli m ON m.id = db.id_materiale
            WHERE db.id_articolo = ? 
            AND db.id_azienda = ?
            ORDER BY posizione ASC', [$a->id, $utente->id_azienda]);
        }*/

        if (isset($dati['aggiungi'])) {
            unset($dati['aggiungi']);
            $dati['id_mg'] = $id; // Associa l'articolo al magazzino
            $dati['id_utente'] = $utente->id;
            $dati['id_azienda'] = $utente->id_azienda; // Associa l'articolo all'azienda
            $datiPerInsert = array_filter($dati, function ($key) {
                return $key !== 'fasi';
            }, ARRAY_FILTER_USE_KEY);

            // Inserimento dell'articolo e recupero dell'ID appena inserito
            $id_articolo = DB::table('articoli')->insertGetId($datiPerInsert);

            // Inserimento delle fasi selezionate nella tabella fasi_articoli
            if (isset($dati['fasi']) && is_array($dati['fasi'])) {
                foreach ($dati['fasi'] as $id_fase) {
                    DB::table('fasi_articoli')->insert([
                        'id_azienda' => $utente->id_azienda,
                        'id_utente' => $utente->id,
                        'id_fase' => $id_fase,
                        'id_articolo' => $id_articolo,
                        'tempo_medio_minuti' => 0 // Puoi impostare il valore di default o modificarlo successivamente
                    ]);
                }
            }

            return redirect()->back()->with('success', 'Articolo aggiunto correttamente.');
        }

        if (isset($dati['modifica'])) {
            unset($dati['modifica']);

            if ($_FILES['immagine']['name'] != '') {

                $pathinfo = pathinfo($_FILES['immagine']['name']);
                $nome = Str::random(20);
                $target = 'immagini/clienti/' . $nome . '.' . $pathinfo['extension'];
                move_uploaded_file($_FILES['immagine']['tmp_name'], $target);
                $dati['immagine'] = $target;

            }

            $datiPerInsert = array_filter($dati, function ($key) {
                return $key !== 'fasi';
            }, ARRAY_FILTER_USE_KEY);

            DB::table('articoli')->where('id', $dati['id'])->update($datiPerInsert);

            // Gestione delle fasi selezionate
           /* if (isset($dati['fasi']) && is_array($dati['fasi'])) {
                // Prima eliminiamo tutte le fasi collegate all'articolo
                DB::table('fasi_articoli')->where('id_articolo', $dati['id'])->delete();

                // Poi inseriamo le nuove fasi selezionate
                foreach ($dati['fasi'] as $id_fase) {
                    DB::table('fasi_articoli')->insert([
                        'id_utente' => $utente->id,
                        'id_fase' => $id_fase,
                        'id_articolo' => $dati['id'],
                        'tempo_medio_minuti' => 0 //modifico dopo il tempo
                    ]);
                }
            }*/
            return redirect()->back()->with('success', 'Articolo Modificato correttamente.');
        }

        if (isset($dati['carica_materiale'])) {
            unset($dati['carica_materiale']);

            $dati['id_utente'] = $utente->id;
            DB::table('mgmov')->insertGetId($dati);
            DB::update('update articoli set giacenza = (select sum(qta) as giacenza from mgmov where id_articolo = ' . $dati['id_articolo'] . ') where id =' . $dati['id_articolo']);


            return redirect()->back()->with('success', 'Materiale Caricato Correttamente');
        }

        if (isset($dati['scarica_materiale'])) {
            unset($dati['scarica_materiale']);

            $dati['id_utente'] = $utente->id;
            $dati['qta'] = $dati['qta'] * -1;
            $dati['sca'] = 1;
            DB::table('mgmov')->insertGetId($dati);

            DB::update('update articoli set giacenza = (select sum(qta) as giacenza from mgmov where id_articolo = ' . $dati['id_articolo'] . ') where id =' . $dati['id_articolo']);


            return redirect()->back()->with('success', 'Materiale Scaricato Correttamente');
        }

        if (isset($dati['rettifica_materiale'])) {
            unset($dati['rettifica_materiale']);

            $giacenza = DB::select('select sum(qta) as giacenza from mgmov where id_articolo = ' . $dati['id_articolo'])[0]->giacenza;


            $dati['id_utente'] = $utente->id;
            $dati['qta'] = $dati['qta'] - $giacenza;
            $dati['ret'] = 1;
            DB::table('mgmov')->insertGetId($dati);

            DB::update('update articoli set giacenza = (select sum(qta) as giacenza from mgmov where id_articolo = ' . $dati['id_articolo'] . ') where id =' . $dati['id_articolo']);

            return redirect()->back()->with('success', 'Materiale Rettificato Correttamente');
        }
        if (isset($dati['elimina'])) {
            unset($dati['elimina']);

            DB::table('articoli')->where('id', $dati['id'])->delete();
            return redirect()->back()->with('success', 'Materiale Eliminato Correttamente');
        }


        // Filtra anche i materiali in base al magazzino e azienda
        $materiali = DB::select('SELECT * FROM articoli WHERE tipologia = 1 AND id_mg = ? AND id_azienda = ?', [$id, $utente->id_azienda]);
        $fasi_associate = [];
        /*foreach ($articoli as $articolo) {
            $fasi_associate[$articolo->id] = DB::table('fasi_articoli')
                ->join('fasi', 'fasi_articoli.id_fase', '=', 'fasi.id')
                ->where('fasi_articoli.id_articolo', $articolo->id)
                ->select('fasi.id', 'fasi.descrizione')
                ->get();

        }
        foreach ($articoli as $a) {

            $a->fasi_associate = DB::table('fasi_articoli')
                ->where('id_articolo', $a->id)
                ->pluck('id_fase')
                ->toArray();
        }*/
        $fasi = DB::table('fasi')->where('id_azienda', $utente->id_azienda)->get();
        return View::make('default.mg', compact('utente', 'articoli', 'materiali', 'fasi'));
    }


    public function salvaPuntoRiordino(Request $request)
    {
        $utente = session('utente');
        $idArticolo = $request->input('id');
        $puntoRiordino = $request->input('punto_riordino');


        // Recupera la giacenza attuale dell'articolo
        $giacenza = DB::table('articoli')->where('id', $idArticolo)->value('giacenza');

        // Salva l'avviso nella sessione se la giacenza è inferiore al punto di riordino
        if ($giacenza < $puntoRiordino) {
            Session::flash('notifica_riordino', "L'articolo con ID $idArticolo ha una giacenza sotto il punto di riordino!");
        }
        DB::table('articoli')->where('id', $idArticolo)->where('id_azienda', $utente->id_azienda)->update(['punto_riordino' => $puntoRiordino]);

        return response()->json(['success' => true]);
    }

    public function controlloPuntoDiRiordino()
    {
        $utente = session('utente');

        $articoliSottoScorta = DB::table('articoli')
            ->where('id_azienda', $utente->id_azienda)
            ->whereColumn('giacenza', '<', 'punto_riordino')
            ->get();

        if ($articoliSottoScorta->isNotEmpty()) {
            // Recupera le descrizioni degli articoli sottoscorta
            $descrizioniArticoli = $articoliSottoScorta->pluck('titolo')->toArray();
            $listaArticoli = implode(', ', $descrizioniArticoli); // Concatenazione in una stringa

            return response()->json([
                'messaggio' => "Attenzione: alcuni articoli sono sotto il livello di riordino! Articoli: $listaArticoli"
            ]);
        }
        return response()->json(['messaggio' => '']);
    }







    public function dettaglio_documento(Request $request, $id) {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Ottieni i dettagli dell'ordine dalla tabella 'dotes' utilizzando l'ID e l'azienda
        $dotes = DB::table('dotes')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
            ->first();

        // Verifica se è stato trovato il documento prima di procedere
        if (!$dotes) {
            return redirect()->back()->with('error', 'Documento non trovato o non autorizzato.');
        }

        // Ottieni il documento 'do' associato
        $documento = DB::table('do')
            ->where('cd_do', $dotes->cd_do)
            ->first();

        // Ottieni i prodotti dalla tabella 'dorig' collegati a questo ordine
        $prodotti = DB::table('dorig')
            ->where('id_dotes', $id)
            ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
            ->get();

        return View::make('default.dettaglio_documento', compact('utente', 'dotes', 'prodotti', 'documento'));
    }


    public function modifica_documento(Request $request, $id) {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Recupera l'ordine da modificare, filtrando per azienda
        $dotes = DB::table('dotes')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
            ->first();

        if (!$dotes) {
            return redirect()->back()->with('error', 'Documento non trovato o non autorizzato.');
        }

        $clienti = DB::table('clienti')
            ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
            ->get();

        $clienteDotes = DB::table('clienti')
            ->where('cd_cf', $dotes->cd_cf)
            ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
            ->first();

        $dorig = DB::table('dorig')
            ->where('id_dotes', $dotes->id)
            ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
            ->get();

        $documento = DB::table('do')
            ->where('cd_do', $dotes->cd_do)// Filtra per azienda
            ->first();

        if ($request->isMethod('post')) {
            // Se ci sono dati da modificare, esegui l'update
            if (isset($dati['modifica_dotes'])) {
                unset($dati['modifica_dotes']);  // Rimuovi il token di modifica

                // Aggiorna i valori di dotes
                $datiDotes = array_filter($dati, function($key) {
                    return $key !== 'products';
                }, ARRAY_FILTER_USE_KEY);

                // Conversione di valori specifici
                $datiDotes['imponibile'] = (float)str_replace(['€', ' '], '', $datiDotes['imponibile']);
                $datiDotes['costo_totale'] = (float)str_replace(['€', ' '], '', $datiDotes['costo_totale']);
               /* $datiDotes['iva'] = (float)str_replace(['€', ' '], '', $datiDotes['iva']);
                $datiDotes['sconto'] = (float)str_replace(['€', ' '], '', $datiDotes['sconto']);
                $datiDotes['costo_trasporto'] = (float)str_replace(['€', ' '], '', $datiDotes['costo_trasporto']);
                $datiDotes['iva_percentuale'] = intval(str_replace(['%', ' '], '', $datiDotes['iva_percentuale']) * 100);
                $datiDotes['sconto_percentuale'] = intval(str_replace(['%', ' '], '', $datiDotes['sconto_percentuale']) * 100);
                $datiDotes['costo_trasporto_percentuale'] = intval(str_replace(['%', ' '], '', $datiDotes['costo_trasporto_percentuale']) * 100);*/

                // Aggiorna la tabella 'dotes' filtrando per azienda
                DB::table('dotes')
                    ->where('id', $id)
                    ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
                    ->update($datiDotes);

                // Elimina i vecchi prodotti associati a questo ordine
                DB::table('dorig')
                    ->where('id_dotes', $id)
                    ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
                    ->delete();

                // Re-inserisci i nuovi prodotti dal form
                $productCount = count($dati['products']);
                for ($i = 0; $i < $productCount; $i++) {
                    $nRiga = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
                    $anno = date('Y');
                    $barcodeData = $anno . 'C' . $dotes->numero_doc . '  ' . $nRiga;
                    $product = $dati['products'][$i];
                    DB::table('dorig')->insert([
                        'cd_do' => $dotes->cd_do,
                        'tipo_documento' => $dotes->tipo_documento,
                        'cd_cf' => $dati['cd_cf'],
                        'data_doc' => Carbon::now(),
                        'id_articolo' => $product['id_articolo'],
                        'nome_prodotto' => $product['nome_prodotto'],
                        'dettagli_prodotto' => $product['dettagli_prodotto'],
                        'qta' => $product['qta'],
                        'qta_evadibile' => $product['qta'],
                        'qta_evadibile_prod' => $product['qta'],
                        'um' => $product['um'],
                        'prezzo_unitario' => (float)str_replace(['€', ' '], '', $product['prezzo_unitario']),
                        'prezzo_totale' => (float)str_replace(['€', ' '], '', $product['prezzo_totale']),
                        'lotto' => $product['lotto'],
                        'iva' => $product['iva'],
                        'id_dotes' => $id,
                        'n_riga' => $nRiga,
                        'barcode' => $barcodeData,
                        'numero_doc' => $dotes->numero_doc,
                        'id_azienda' => $utente->id_azienda // Aggiungi azienda
                    ]);
                }

                return Redirect::to('modifica_documento/' . $id)->with('success', 'Ordine modificato con successo');
            }
        }
        $prodotti_finiti = DB::table('articoli')->where('id_azienda', $utente->id_azienda)->where('tipologia', 0)->get();


        return View::make('default.modifica_documento', compact('utente', 'dotes', 'clienti', 'clienteDotes', 'dorig', 'documento', 'prodotti_finiti'));
    }




    public function evadiQuantita(Request $request)
    {
        $utente = session('utente');
        // Valida i dati in ingresso
        $validated = $request->validate([
            'documento_creato' => 'required|string', // Il documento da creare
            'id_dotes_originale' => 'required|integer', // ID del dotes originale
            'quantita_evasa' => 'required|array', // Le quantità selezionate per ogni dorig
            'ordine_flag' => 'required|boolean', // Flag ordine passato dal frontend
        ]);

        $idDotesOriginale = $validated['id_dotes_originale'];
        $documentoCreato = $validated['documento_creato'];
        $quantitaEvasa = $validated['quantita_evasa']; // Array con dorig id => quantità evasa
        $ordineFlag = $validated['ordine_flag']; // Valore passato dal frontend tramite input nascosto

        // Calcola il prezzo netto e l'IVA



        // Recupera il dotes originale filtrando per azienda
        $dotesOriginale = DB::table('dotes')
            ->where('id', $idDotesOriginale)
            ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
            ->first();

        if (!$dotesOriginale) {
            return redirect()->back()->with('error', 'Documento originale non trovato.');
        }

        // Trova l'ultimo numero documento e incrementalo
        $ultimoNumeroDoc = DB::table('dotes')
            ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
            ->orderBy('id', 'desc')
            ->value('numero_doc');

        $numeroIncrementale = $ultimoNumeroDoc ? ((int)substr($ultimoNumeroDoc, 2) + 1) : 1;
        $tipoDocumento = $ordineFlag ? 'ord' : null;
        // Crea il nuovo dotes con il documento selezionato
        $nuovoDotesId = DB::table('dotes')->insertGetId([
            'cd_do' => $documentoCreato,
            'id_dotes_evade' => $dotesOriginale->id,
            'data_doc' => now(),
            'indirizzo' => $dotesOriginale->indirizzo,
            'comune' => $dotesOriginale->comune,
            'ragione_sociale' => $dotesOriginale->ragione_sociale,
            'cd_cf' => $dotesOriginale->cd_cf,
            'partita_iva' => $dotesOriginale->partita_iva,
            'tipo_documento' => $tipoDocumento,
            'ragione_sociale_fatturazione' => $dotesOriginale->ragione_sociale_fatturazione,
            'numero_doc' => 'DO' . str_pad($numeroIncrementale, 6, '0', STR_PAD_LEFT),
            'id_azienda' => $utente->id_azienda // Aggiungi azienda
        ]);

        // Itera sui dorig originali e crea i nuovi dorig con le quantità selezionate
        foreach ($quantitaEvasa as $idDorig => $qtaEvasa) {
            // Recupera il dorig originale filtrando per azienda
            $dorigOriginale = DB::table('dorig')
                ->where('id', $idDorig)
                ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
                ->first();
            $prezzoUnitario = $dorigOriginale->prezzo_unitario;
            $iva = $dorigOriginale->iva; // Assumo che sia la percentuale di IVA (es. 22 per il 22%)

// Calcola l'IVA
            $prezzoNetto = $prezzoUnitario * $qtaEvasa;
            $importoIva = $prezzoNetto * ($iva / 100);

// Calcola il prezzo totale comprensivo di IVA
            $prezzoTotale = $prezzoNetto + $importoIva;

            if ($dorigOriginale && $qtaEvasa > 0) {
                // Imposta il valore per 'tipo_documento' se 'ordine_flag' è 1
                $tipoDocumento = $ordineFlag ? 'ord' : null;

                // Crea il nuovo dorig per il nuovo documento
                DB::table('dorig')->insert([
                    'id_dotes' => $nuovoDotesId,
                    'id_dorig_evade' => $dorigOriginale->id,
                    'id_articolo' => $dorigOriginale->id_articolo,
                    'qta' => $qtaEvasa,
                    'prezzo_unitario' =>$dorigOriginale->prezzo_unitario,
                    'prezzo_totale' =>$prezzoTotale,
                    'cd_do' => $documentoCreato,
                    'nome_prodotto' => $dorigOriginale->nome_prodotto,
                    'dettagli_prodotto' => $dorigOriginale->dettagli_prodotto,
                    'qta_evadibile' => $qtaEvasa,
                    'data_doc' => now(),
                    'iva' => $dorigOriginale->iva,
                    'lotto' => $dorigOriginale->lotto,
                    'qta_evadibile_prod' => $qtaEvasa,
                    'barcode' => $dorigOriginale->barcode,
                    'tipo_documento' => $tipoDocumento, // Inserisce 'ord' se il flag è 1
                    'id_azienda' => $utente->id_azienda // Aggiungi azienda
                ]);

                DB::table('dotes')
                    ->where('id', $nuovoDotesId)
                    ->update([
                        'costo_totale' => $prezzoTotale,
                    ]);
                // Aggiorna il dorig originale: riduci qta_evadibile e incrementa qta_evasa
                DB::table('dorig')
                    ->where('id', $dorigOriginale->id)
                    ->update([
                        'qta_evadibile' => $dorigOriginale->qta_evadibile - $qtaEvasa,
                        'qta_evasa' => $dorigOriginale->qta_evasa + $qtaEvasa,
                    ]);
            }
        }

        // Reindirizza con successo
        return redirect()->back()->with('success', 'Evasione completata e documento creato con successo!');
    }








    public function crea_documento(Request $request, $cd_do) {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Recupera i clienti solo per l'azienda dell'utente loggato
        $clienti = DB::table('clienti')->where('id_azienda', $utente->id_azienda)->get();
        $documento = DB::table('do')->where('cd_do', $cd_do)->where('id_azienda', $utente->id_azienda)->first();
        $scanBarcodeEnabled = $documento->scan_code ?? 0;
        $productId = $request->input('id_articolo');

        // Controlla se è stato inviato il form per aggiungere un documento
        if (isset($dati['aggiungi_dotes'])) {
            unset($dati['aggiungi_dotes']);
            /*evita l'inserimento di queste cose*/
            $datiDotes = array_filter($dati, function($key) {
                return $key !== 'products' && $key !== 'product_id' && $key !== 'product_name';
            }, ARRAY_FILTER_USE_KEY);


            // Aggiungi il cd_do dal parametro della request e imposta l'azienda
            $datiDotes['cd_do'] = $cd_do;
            $datiDotes['tipo_documento'] = ($documento->ordine == 1)
                ? 'ord'
                : ($documento->descrizione ?? null);
            $datiDotes['data_doc'] = Carbon::now();
            $datiDotes['id_azienda'] = $utente->id_azienda; // Aggiungi l'azienda
            $datiDotes['imponibile'] = (float)str_replace(['€', ' '], '', $datiDotes['imponibile']);
            $datiDotes['costo_totale'] = (float)str_replace(['€', ' '], '', $datiDotes['costo_totale']);
     /*       $datiDotes['iva'] = (float)str_replace(['€', ' '], '', $datiDotes['iva']);
            $datiDotes['sconto'] = (float)str_replace(['€', ' '], '', $datiDotes['sconto']);
            $datiDotes['costo_trasporto'] = (float)str_replace(['€', ' '], '', $datiDotes['costo_trasporto']);*/
/*            $datiDotes['iva_percentuale'] = intval(str_replace(['%', ' '], '', $datiDotes['iva_percentuale']) * 100);*/
          /*  $datiDotes['sconto_percentuale'] = intval(str_replace(['%', ' '], '', $datiDotes['sconto_percentuale']) * 100);
            $datiDotes['costo_trasporto_percentuale'] = intval(str_replace(['%', ' '], '', $datiDotes['costo_trasporto_percentuale']) * 100);*/

            // Trova l'ultimo numero documento e incrementalo
            $ultimoNumeroDoc = DB::table('dotes')
                ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
                ->orderBy('id', 'desc')
                ->value('numero_doc');
            $numeroIncrementale = $ultimoNumeroDoc ? (int)substr($ultimoNumeroDoc, 2) + 1 : 1;

            // Formatta il numero documento
            $datiDotes['numero_doc'] = 'DO' . str_pad($numeroIncrementale, 6, '0', STR_PAD_LEFT);

            // Inserisci il documento nella tabella 'dotes'
            $idDotes = DB::table('dotes')->insertGetId($datiDotes);

            // Gestione dei prodotti
            if (isset($dati['products'])) {
                foreach ($dati['products'] as $i => $product) {
                    // Genera il numero progressivo della riga
                    $nRiga = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
                    $anno = date('Y');
                    $barcodeData = $anno . 'C' . $datiDotes['numero_doc'] . '  ' . $nRiga;

                    $prodotto = DB::table('articoli')->where('id', $product['id_articolo'])->first();

                    DB::table('dorig')->insert([
                        'cd_do' => $datiDotes['cd_do'],
                        'cd_cf' => $dati['cd_cf'],
                        'tipo_documento' => $datiDotes['tipo_documento'],
                        'numero_doc' => $datiDotes['numero_doc'],
                        'data_doc' => Carbon::now(),
                        'nome_prodotto' => $product['nome_prodotto'],
                        'dettagli_prodotto' => $product['dettagli_prodotto'],
                        'qta' => $product['qta'],
                        'um' => $product['um'],
                        'id_articolo' => $product['id_articolo'],
                        'qta_evadibile' => $product['qta'],
                        'qta_evadibile_prod' => $product['qta'],
                        'prezzo_unitario' => (float)str_replace(['€', ' '], '', $product['prezzo_unitario']),
                        'prezzo_totale' => (float)str_replace(['€', ' '], '', $product['prezzo_totale']),
                        'lotto' => $product['lotto'],
                        'iva' => $product['iva'],
                        'id_dotes' => $idDotes,
                        'n_riga' => $nRiga,
                        'barcode' => isset($prodotto) && isset($prodotto->barcode) ? $prodotto->barcode : null,
                        'id_azienda' => $utente->id_azienda // Aggiungi azienda

                    ]);
                }
            }

            return Redirect::to('modifica_documento/' . $idDotes);
        }

        $prodotti_finiti = DB::table('articoli')->where('id_azienda', $utente->id_azienda)->where('tipologia', 0)->get();

        return View::make('default.crea_documento', compact('utente', 'clienti', 'cd_do', 'documento', 'scanBarcodeEnabled', 'prodotti_finiti'));
    }



    public function gestione_documenti(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if (isset($dati['crea_documento'])) {
            DB::table('do')->insert([
                'cd_do' => $request->cd_do,
                'descrizione' => $request->descrizione,
                'attivo' => $request->has('attivo') ? 1 : 0,
                'passivo' => $request->has('passivo') ? 1 : 0,
                'scarico' => $request->has('scarico') ? 1 : 0,
                'carico' => $request->has('carico') ? 1 : 0,
                'fatturazione' => $request->has('fatturazione') ? 1 : 0,
                'ordine' => $request->has('ordine') ? 1 : 0,
                'scan_code' => $request->has('scan_code') ? 1 : 0,
                'id_azienda' => $utente->id_azienda // Aggiungi azienda
            ]);

            return redirect()->back()->with('success', 'Documento creato con successo!');
        }

        if (isset($dati['elimina'])) {
            DB::table('do')
                ->where('id', $dati['id_documento'])
                ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
                ->delete();

            return redirect()->back()->with('success', 'Documento eliminato con successo!');
        }

        if (isset($dati['modifica_documento'])) {
            DB::table('do')
                ->where('id', $dati['id_documento'])
                ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
                ->update([
                    'cd_do' => $dati['edit_cd_do'],
                    'descrizione' => $dati['edit_descrizione'],
                    'attivo' => $request->has('edit_attivo') ? 1 : 0,
                    'passivo' => $request->has('edit_passivo') ? 1 : 0,
                    'scarico' => $request->has('edit_scarico') ? 1 : 0,
                    'carico' => $request->has('edit_carico') ? 1 : 0,
                    'fatturazione' => $request->has('edit_fatturazione') ? 1 : 0,
                    'ordine' => $request->has('edit_ordine') ? 1 : 0,
                    'scan_code' => $request->has('edit_scan_code') ? 1 : 0,
                ]);

            return redirect()->back()->with('success', 'Documento modificato con successo!');
        }

        $documenti = DB::table('do')
            ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
            ->get();


        return View::make('default.gestione_documenti', compact('utente', 'documenti'));
    }



    public function gestione_magazzini(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if(isset($dati['crea_magazzino'])) {
            DB::table('mg')->insert([
                'codice_magazzino' => $request->codice_magazzino,
                'descrizione' => $request->descrizione,
                'id_azienda' => $utente->id_azienda // Aggiungi azienda
            ]);

            return redirect()->back()->with('success', 'Magazzino creato con successo!');
        }

        if(isset($dati['elimina'])) {
            unset($dati['elimina']);
            $id = $dati['id_mg'];
            DB::table('mg')
                ->where('id', $id)
                ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
                ->delete();

            return redirect()->back()->with('success', 'Magazzino eliminato con successo!');
        }

        $magazzini = DB::table('mg')
            ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
            ->get();

        return view('default.gestione_magazzini', compact('utente', 'magazzini'));
    }


    public function getProductByBarcode($barcode) {
        $prodotto = DB::table('dorig')->where('barcode', $barcode)->first();
        if ($prodotto) {
            return response()->json([
                'success' => true,
                'product' => [
                    'nome_prodotto' => $prodotto->nome_prodotto,
                    'dettagli_prodotto' => $prodotto->dettagli_prodotto,
                    'prezzo_unitario' => $prodotto->prezzo_unitario,
                    'qta' => $prodotto->qta,
                    'prezzo_totale' => $prodotto->prezzo_totale,
                ]
            ]);
        } else {
            return response()->json(['success' => false]);
        }
    }





    public function documenti_di_trasporto(Request $request) {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();
        $dotes = DB::table('dotes')->get();

        if(isset($dati['elimina'])){
            unset($dati['elimina']);
            DB::table('dorig')->where('id_dotes', $dati['id_ordine'])->delete();

            // Elimina il record nella tabella 'dotes'
            DB::table('dotes')->where('id', $dati['id_ordine'])->delete();

            return Redirect::to('ordini');

        }





        return View::make('default.documenti_di_trasporto', compact( 'utente', 'dotes'));
    }


    public function fornitori(Request $request) {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if (isset($dati['invia_credenziali'])) {
            $utenti = DB::select('SELECT * FROM utenti WHERE id = ? AND id_azienda = ?', [$dati['id'], $utente->id_azienda]);
            if (sizeof($utenti) > 0) {
                $usr = $utenti[0];
                $mail = new PHPMailer(true);
                // ... configurazione della mail come nel tuo esempio
                $mail->send();
            }
        }

        if (isset($dati['aggiungi'])) {
            unset($dati['aggiungi']);
            $dati['id_tipologia'] = 1;
            $dati['id_azienda'] = $utente->id_azienda; // Aggiungi l'azienda associata

            // Gestione dell'immagine
            if ($_FILES['immagine']['name'] != '') {
                $pathinfo = pathinfo($_FILES['immagine']['name']);
                $nome = Str::random(20);
                $target = 'immagini/fornitori/' . $nome . '.' . $pathinfo['extension'];
                move_uploaded_file($_FILES['immagine']['tmp_name'], $target);
                $dati['immagine'] = $target;
            }

            $dati['token_utente_per_bando'] = Str::random(20); // Genera il token

            DB::table('utenti')->insert($dati);
            return Redirect::to('admin/fornitori');
        }

        if (isset($dati['modifica'])) {
            unset($dati['modifica']);
            $dati['id_azienda'] = $utente->id_azienda; // Filtra per l'azienda associata

            if ($_FILES['immagine']['name'] != '') {
                $pathinfo = pathinfo($_FILES['immagine']['name']);
                $nome = Str::random(20);
                $target = 'immagini/fornitori/' . $nome . '.' . $pathinfo['extension'];
                move_uploaded_file($_FILES['immagine']['tmp_name'], $target);
                $dati['immagine'] = $target;
            }

            DB::table('utenti')->where('id', $dati['id'])->where('id_azienda', $utente->id_azienda)->update($dati);
            return Redirect::to('admin/fornitori');
        }

        if (isset($dati['elimina'])) {
            unset($dati['elimina']);
            DB::table('utenti')->where('id', $dati['id'])->where('id_azienda', $utente->id_azienda)->delete();
            return Redirect::to('admin/fornitori');
        }

        $page = 'index';
        // Filtra i fornitori per azienda
        $fornitori = DB::select('SELECT * FROM utenti WHERE id_tipologia = 1 AND id_azienda = ?', [$utente->id_azienda]);

        return View::make('default.fornitori', compact('page', 'utente', 'fornitori'));
    }


    public function progetti($id_reparto,Request $request){

        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if(isset($dati['crea_task_veloce'])){
            unset($dati['crea_task_veloce']);
            $dati['descrizione'] = $dati['titolo'];
            $dati['descrizione_ultimo_sal'] = $dati['titolo'];
            $dati['id_assegnatario'] = $utente->id;
            $dati['id_operatore_ultimo_sal'] = $utente->id;
            $dati['id_utente'] = 118;
            DB::table('progetti')->insert($dati);


            $utenti = DB::select('select * from utenti where id_tipologia = 0 or id_reparto ='.$id_reparto);
            if(sizeof($utenti) > 0) {

                $titolo = 'Nuovo Progetto - '.$dati['titolo'];
                $descrizione = $dati['descrizione'];

                $ids = array();

                foreach($utenti as $u){
                    array_push($ids,$u->onesignal_token);
                    array_push($ids,$u->onesignal_token_mobile);
                }

                $headings = array("en" =>  $titolo);
                $content = array("en" => $descrizione);
                $fields = array(
                    'app_id' => "7981e39d-e1a4-4b4b-8f84-f3007aaf15ee",

                    'include_player_ids' => $ids,
                    'url' => "https://crm.ingenia.cloud",
                    'contents' => $content,
                    'headings' => $headings
                );

                $fields = json_encode($fields);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Authorization: Basic YWE5N2ViZTQtNjg4Mi00NGU1LThiMjUtZjBkMjQxZjA3Y2Ey'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                $response = curl_exec($ch);
                curl_close($ch);


            }

            return Redirect::to('admin/progetti/'.$id_reparto);
        }


        if(isset($dati['aggiungi'])){
            unset($dati['aggiungi']);

            $reparti = DB::select('SELECT * from reparti where id ='.$id_reparto);

            if(sizeof($reparti) > 0) {
                $reparto = $reparti[0];
                $dati['label_ex1'] = $reparto->label_ex1;
                $dati['label_ex2'] = $reparto->label_ex2;
                $dati['label_ex3'] = $reparto->label_ex3;
                $dati['label_ex4'] = $reparto->label_ex4;
                $dati['label_ex5'] = $reparto->label_ex5;
                $dati['label_ex6'] = $reparto->label_ex6;
                $dati['label_ex7'] = $reparto->label_ex7;
                $dati['label_ex8'] = $reparto->label_ex8;
                $dati['label_ex9'] = $reparto->label_ex9;
                $dati['label_ex10'] = $reparto->label_ex10;
                DB::table('progetti')->insert($dati);


                $utenti = DB::select('select * from utenti where id_tipologia = 0 or id_reparto =' . $id_reparto);
                if (sizeof($utenti) > 0) {

                    $titolo = 'Nuovo Progetto - ' . $dati['titolo'];
                    $descrizione = $dati['descrizione'];

                    $ids = array();

                    foreach ($utenti as $u) {
                        array_push($ids, $u->onesignal_token);
                        array_push($ids, $u->onesignal_token_mobile);
                    }

                    $headings = array("en" => $titolo);
                    $content = array("en" => $descrizione);
                    $fields = array(
                        'app_id' => "7981e39d-e1a4-4b4b-8f84-f3007aaf15ee",
                        'include_player_ids' => $ids,
                        'url' => "https://crm.ingenia.cloud",
                        'contents' => $content,
                        'headings' => $headings
                    );

                    $fields = json_encode($fields);

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Authorization: Basic YWE5N2ViZTQtNjg4Mi00NGU1LThiMjUtZjBkMjQxZjA3Y2Ey'));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                    curl_setopt($ch, CURLOPT_HEADER, FALSE);
                    curl_setopt($ch, CURLOPT_POST, TRUE);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    $response = curl_exec($ch);
                    curl_close($ch);

                }

                return Redirect::to('admin/progetti/' . $id_reparto);

            }
        }

        if(isset($dati['stampa_report'])){
            unset($dati['stampa_report']);

            $progetti = DB::select('SELECT p.*,DATEDIFF(p.timestamp_prossimo_sal,NOW()) as scadenza, u.ragione_sociale AS cliente,r.descrizione AS reparto,CONCAT(u2.nome," ",u2.cognome) AS operatore,CONCAT(u3.nome," ",u3.cognome) AS assegnatario from progetti p
            JOIN utenti u ON u.id = p.id_utente
            JOIN reparti r ON r.id = p.id_reparto
            LEFT JOIN utenti u2 ON u2.id = p.id_operatore_ultimo_sal 
            LEFT JOIN utenti u3 ON u3.id = p.id_assegnatario where p.id_reparto = '.$dati['id_reparto'].' and p.archiviato = 0
            order BY p.timestamp_prossimo_sal asc');

            $reparti = DB::select('SELECT * from reparti where id = '.$dati['id_reparto']);

            $utenti = DB::select('SELECT * from utenti where id_reparto ='.$dati['id_reparto']);

            if(sizeof($reparti) > 0) {

                $reparto = $reparti[0];

                $html = View::make('stampa.report_progetti', compact('progetti','dati','reparto','utenti'));
                $mpdf = new \Mpdf\Mpdf(['format' => 'A4-L', 'mode' => 'utf-8', 'margin_left' => 5, 'margin_right' => 5, 'margin_top' => 40, 'margin_bottom' => 10, 'margin_header' => 0, 'margin_footer' => 0]);
                $mpdf->SetTitle('Report Reparto ' . $reparto->descrizione);

                $mpdf->SetHTMLHeader('
                    <table width="100%" style="font-weight: bold;">
                        <tr>
                            <td width="33%">                
                                <img src="/logo.png" style="height:120px;">
                            </td>
                            <td width="66%" align="right" style="font-size:25px;">
                                Report Reparto '.$reparto->descrizione.' del '.date('d/m/Y',strtotime($dati['data'])).'<br>
                            </td>
                        </tr>
                    </table>
                    ');

                $mpdf->WriteHTML($html);
                $mpdf->Output('report_' . $reparto->descrizione . '_'.$dati['data'].'.pdf', 'I');

            }
            exit();

        }

        if(isset($dati['aggiungi_sal'])) {
            unset($dati['aggiungi_sal']);
            $dati['id_operatore_ultimo_sal'] = $utente->id;
            $dati['timestamp_ultimo_sal'] = date('Y-m-d H:i:s');
            unset($dati['allegato']);
            DB::table('progetti')->where('id', $dati['id'])->update($dati);
            $dati['id_progetto'] = $dati['id'];
            unset($dati['id']);
            unset($dati['id_reparto']);

            if($_FILES['allegato']['name'] != ''){

                $pathinfo = pathinfo($_FILES['allegato']['name']);
                $nome = Str::random(20);
                $target = 'allegati_progetti/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['allegato']['tmp_name'], $target);
                $dati['allegato'] = $target;
            }

            DB::table('sal_progetti')->insert($dati);


            $progetti = DB::select('SELECT * from progetti where id =' . $dati['id_progetto']);
            $progetto = $progetti[0];


            $utenti = DB::select('select * from utenti where id_tipologia = 0 or id_reparto ='.$progetto->id_reparto);
            if(sizeof($utenti) > 0) {

                $titolo = 'Aggiornamento Progetto - '.$progetto->titolo;
                $descrizione = $dati['descrizione_ultimo_sal'];

                $ids = array();

                foreach($utenti as $u){
                    if($u->onesignal_token != "")  array_push($ids,strval($u->onesignal_token));
                    if($u->onesignal_token_mobile != "")  array_push($ids,strval($u->onesignal_token_mobile));
                }

                $headings = array("en" =>  $titolo);
                $content = array("en" => $descrizione);
                $fields = array(
                    'app_id' => "7981e39d-e1a4-4b4b-8f84-f3007aaf15ee",
                    'include_player_ids' => $ids,
                    'url' => "https://crm.ingenia.cloud",
                    'contents' => $content,
                    'headings' => $headings
                );

                $fields = json_encode($fields);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Authorization: Basic YWE5N2ViZTQtNjg4Mi00NGU1LThiMjUtZjBkMjQxZjA3Y2Ey'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                $response = curl_exec($ch);


                curl_close($ch);

            }




            return Redirect::to('admin/progetti/'.$id_reparto);
        }

        if(isset($dati['aggiungi_allegato'])) {
            unset($dati['aggiungi_allegato']);


            if($_FILES['allegato']['name'] != ''){

                $pathinfo = pathinfo($_FILES['allegato']['name']);
                $nome = Str::random(20);
                $target = 'allegati_progetti/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['allegato']['tmp_name'], $target);
                $dati['allegato'] = $target;
            }

            $id_utente = $dati['id_utente'];
            unset($dati['id_utente']);
            DB::table('progetti_allegati')->insert($dati);

            $dati['id_utente'] = $id_utente;
            $id_progetto = $dati['id_progetto'];
            unset($dati['id_progetto']);

            DB::table('utenti_allegati')->insert($dati);


            $progetti = DB::select('SELECT * from progetti where id =' . $id_progetto);
            $progetto = $progetti[0];


            $utenti = DB::select('select * from utenti where id_tipologia = 0 or id_reparto ='.$progetto->id_reparto);
            if(sizeof($utenti) > 0) {

                $titolo = 'Aggiunto Allegato Al Progetto - '.$progetto->titolo;
                $descrizione = $dati['nome_allegato'];

                $ids = array();

                foreach($utenti as $u){
                    if($u->onesignal_token != "")  array_push($ids,strval($u->onesignal_token));
                    if($u->onesignal_token_mobile != "")  array_push($ids,strval($u->onesignal_token_mobile));
                }

                $headings = array("en" =>  $titolo);
                $content = array("en" => $descrizione);
                $fields = array(
                    'app_id' => "7981e39d-e1a4-4b4b-8f84-f3007aaf15ee",
                    'include_player_ids' => $ids,
                    'url' => "https://crm.ingenia.cloud",
                    'contents' => $content,
                    'headings' => $headings
                );

                $fields = json_encode($fields);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Authorization: Basic YWE5N2ViZTQtNjg4Mi00NGU1LThiMjUtZjBkMjQxZjA3Y2Ey'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                $response = curl_exec($ch);


                curl_close($ch);

            }

            return Redirect::to('admin/progetti/'.$id_reparto);
        }

        if(isset($dati['elimina_allegato'])){
            unset($dati['elimina_allegato']);

            DB::table('progetti_allegati')->where('id',$dati['id'])->delete();

            return Redirect::to('admin/progetti/'.$id_reparto);
        }


        if(isset($dati['modifica'])){
            unset($dati['modifica']);
            DB::table('progetti')->where('id',$dati['id'])->update($dati);
            return Redirect::to('admin/progetti/'.$id_reparto);
        }

        if(isset($dati['archivia'])){
            unset($dati['archivia']);
            $update['archiviato'] = 1;
            DB::table('progetti')->where('id',$dati['id'])->update($update);

            $progetti = DB::select('SELECT * from progetti where id =' . $dati['id']);
            $progetto = $progetti[0];

            $utenti = DB::select('select * from utenti where id_tipologia = 0 or id_reparto ='.$progetto->id_reparto);
            if(sizeof($utenti) > 0) {

                $titolo = 'Archiviato Progetto - '.$progetto->titolo;
                $descrizione = 'Il Progetto è stato Archiviato: Ultimo  sal'.$progetto->descrizione_ultimo_sal;

                $ids = array();

                foreach($utenti as $u){
                    array_push($ids,$u->onesignal_token);
                    array_push($ids,$u->onesignal_token_mobile);

                }
                $headings = array("en" =>  $titolo);
                $content = array("en" => $descrizione);
                $fields = array(
                    'app_id' => "7981e39d-e1a4-4b4b-8f84-f3007aaf15ee",

                    'include_player_ids' => $ids,
                    'url' => "https://crm.ingenia.cloud",
                    'contents' => $content,
                    'headings' => $headings
                );

                $fields = json_encode($fields);

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json; charset=utf-8', 'Authorization: Basic YWE5N2ViZTQtNjg4Mi00NGU1LThiMjUtZjBkMjQxZjA3Y2Ey'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_HEADER, FALSE);
                curl_setopt($ch, CURLOPT_POST, TRUE);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                $response = curl_exec($ch);
                curl_close($ch);

            }



            return Redirect::to('admin/progetti/'.$id_reparto);
        }


        $reparto_attuale = DB::select('select * from reparti where id ='.$id_reparto);
        if(sizeof($reparto_attuale) > 0) {

            $reparto_attuale = $reparto_attuale[0];

            $page = 'index';
            $progetti = DB::select('SELECT p.*,DATEDIFF(p.timestamp_prossimo_sal,NOW()) as scadenza,u.ragione_sociale AS cliente,r.descrizione AS reparto,CONCAT(u2.nome," ",u2.cognome) AS operatore,CONCAT(u3.nome," ",u3.cognome) AS assegnatario from progetti p
            JOIN utenti u ON u.id = p.id_utente
            JOIN reparti r ON r.id = p.id_reparto
            LEFT JOIN utenti u2 ON u2.id = p.id_operatore_ultimo_sal 
            LEFT JOIN utenti u3 ON u3.id = p.id_assegnatario where p.archiviato = 0 and p.id_reparto = '.$id_reparto.'
            order BY p.timestamp_prossimo_sal asc');

            foreach($progetti as $p){
                $p->storico = DB::select('SELECT sp.*,CONCAT(u2.nome," ",u2.cognome) AS operatore from sal_progetti sp LEFT JOIN utenti u2 ON u2.id = sp.id_operatore_ultimo_sal where sp.id_progetto = '.$p->id.' order by sp.timestamp_prossimo_sal desc');
                $p->allegati = DB::select('SELECT * from progetti_allegati where id_progetto = '.$p->id.' order by timestamp ASC');
            }

            $clienti = DB::select('SELECT * FROM utenti WHERE id_tipologia = 2 order by ragione_sociale DESC');
            $reparti = DB::select('SELECT * FROM reparti order by descrizione DESC');

            $operatori = DB::select('SELECT * from utenti where id_tipologia = 3');

            return View::make('default.progetti', compact('page', 'utente', 'progetti', 'clienti', 'reparti', 'operatori','reparto_attuale'));

        }

    }

    public function dipendenti(Request $request) {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if (isset($dati['invia_credenziali'])) {
            unset($dati['invia_credenziali']);
            $utenti = DB::select('SELECT * FROM utenti WHERE id = ? AND id_azienda = ?', [$dati['id_per_credenziali'], $utente->id_azienda]);
            if (sizeof($utenti) > 0) {
                $usr = $utenti[0];
                $mail = new PHPMailer(true);
                // ... configurazione della mail
                $mail->send();
                return Redirect::to('admin/dipendenti');
            }
        }


        if (isset($dati['aggiungi_reparto'])) {
            unset($dati['aggiungi_reparto']);

            $dati['id_azienda'] = $utente->id_azienda; // Aggiungi l'azienda associata




            DB::table('reparti')->insert($dati);
            return Redirect::to('admin/dipendenti');
        }

        if (isset($dati['aggiungi'])) {
            unset($dati['aggiungi']);
            $dati['id_tipologia'] = 3;
            $dati['id_azienda'] = $utente->id_azienda; // Aggiungi l'azienda associata
            $dati['admin_azienda'] = 2;

            if ($_FILES['immagine']['name'] != '') {
                $pathinfo = pathinfo($_FILES['immagine']['name']);
                $nome = Str::random(20);
                $target = 'immagini/dipendenti/' . $nome . '.' . $pathinfo['extension'];
                move_uploaded_file($_FILES['immagine']['tmp_name'], $target);
                $dati['immagine'] = $target;
            }

            DB::table('utenti')->insert($dati);
            return Redirect::to('admin/dipendenti');
        }

        if (isset($dati['modifica'])) {
            unset($dati['modifica']);
            $dati['id_azienda'] = $utente->id_azienda; // Aggiungi l'azienda associata

            if ($_FILES['immagine']['name'] != '') {
                $path_da_eliminare = DB::table('utenti')->where('id_tipologia', 3)->where('id', $dati['id'])->where('id_azienda', $utente->id_azienda)->first();
                if (file_exists($path_da_eliminare->immagine)) {
                    unlink($path_da_eliminare->immagine);
                }

                $pathinfo = pathinfo($_FILES['immagine']['name']);
                $nome = Str::random(20);
                $target = 'immagini/dipendenti/' . $nome . '.' . $pathinfo['extension'];
                move_uploaded_file($_FILES['immagine']['tmp_name'], $target);
                $dati['immagine'] = $target;
            }

            DB::table('utenti')->where('id', $dati['id'])->where('id_azienda', $utente->id_azienda)->update($dati);
            return Redirect::to('admin/dipendenti');
        }

        if (isset($dati['elimina'])) {
            unset($dati['elimina']);
            $path_da_eliminare = DB::table('utenti')->where('id_tipologia', 3)->where('id', $dati['id'])->where('id_azienda', $utente->id_azienda)->first();
            if (file_exists($path_da_eliminare->immagine)) {
                unlink($path_da_eliminare->immagine);
            }

            DB::table('utenti')->where('id', $dati['id'])->where('id_azienda', $utente->id_azienda)->delete();
            return Redirect::to('admin/dipendenti');
        }

        $page = 'dipendenti';
        $dipendenti = DB::table('utenti')->where('id_tipologia', 3)->where('id_azienda', $utente->id_azienda)->get();
        $reparti = DB::table('reparti')->where('id_azienda', $utente->id_azienda)->get();

        return View::make('default.dipendenti', compact('page', 'utente', 'dipendenti', 'reparti'));
    }
    public function evadiTuttoInFattura(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Recupera tutti i contratti per l'azienda corrente con dati cliente
        $contratti = DB::table('contratti')
            ->join('clienti', 'contratti.cliente_id', '=', 'clienti.id')
            ->where('contratti.id_azienda', $utente->id_azienda)
            ->select('contratti.*', 'clienti.ragione_sociale', 'clienti.piva', 'clienti.indirizzo', 'clienti.cap',
                'clienti.comune', 'clienti.provincia', 'clienti.nazione', 'clienti.sdi', 'clienti.pec')
            ->get();

        if ($contratti->isEmpty()) {
            return redirect()->back()->with('error', 'Nessun contratto disponibile per l\'evasione.');
        }

        foreach ($contratti as $contratto) {
            // Calcolo numero fattura incrementale per ogni contratto
            $ultimoNumeroDoc = DB::table('dotes')
                ->where('id_azienda', $utente->id_azienda)
                ->orderBy('id', 'desc')
                ->value('numero_doc');

            $numeroIncrementale = $ultimoNumeroDoc ? (int)substr($ultimoNumeroDoc, 2) + 1 : 1;
            $numeroFattura = 'FT' . str_pad($numeroIncrementale, 6, '0', STR_PAD_LEFT);

            // Calcolo numero progressivo (campo 'numero')
            $num_fattura = DB::select('SELECT IFNULL(MAX(numero) + 1, 1) AS numero FROM dotes WHERE anno = YEAR(NOW()) AND id_azienda = ?', [$utente->id_azienda])[0]->numero;

            // Calcolo dei valori
            $pu = (float)$contratto->prezzo; // Prezzo Unitario (già comprensivo di IVA)
            $ivaPercentuale = (float)$contratto->iva;

            // Calcolo imponibile e imposta
            $imponibile = round($pu / (1 + ($ivaPercentuale / 100)), 2); // Prezzo senza IVA
            $imposta = round($pu - $imponibile, 2); // Differenza tra prezzo totale e imponibile

            // Prezzo Totale (comprensivo di IVA)
            $prezzoTotale = $pu; // Già comprensivo di IVA

            // **Creazione del documento dotes (Testata)**
            $idDotes = DB::table('dotes')->insertGetId([
                'id_utente' => $utente->id,
                'cd_do' => 'CTR',
                'anno' => now()->year,
                'tipo_documento' => 'fattura',
                'tipologia_documento' => 'TD01',
                'data_doc' => now(),
                'data' => now(),
                'id_azienda' => $utente->id_azienda,
                'numero_doc' => $numeroFattura,
                'numero' => $num_fattura, // Numero progressivo della fattura
                'imponibile' => $imponibile, // Totale imponibile
                'imposta' => $imposta,       // Totale IVA
                'totale' => $prezzoTotale, // Totale imponibile + IVA
                'esigibilita_iva' => 'I',
                'nominativo' => $contratto->ragione_sociale,
                'cf' => $contratto->piva,
                'piva' => $contratto->piva,
                'indirizzo' => $contratto->indirizzo,
                'cap' => $contratto->cap,
                'citta' => $contratto->comune,
                'provincia' => $contratto->provincia,
                'nazione' => $contratto->nazione,
                'sdi' => $contratto->sdi,
                'pec' => $contratto->pec,
                'condizioni_pagamento' => 'TP02',
                'tipologia_pagamento' => 'MP05',
                'stato' => 0,
            ]);

            // **Creazione della riga in dorig (Dettaglio)**
            DB::table('dorig')->insert([
                'id_dotes' => $idDotes,
                'id_testata' => $idDotes,
                'descrizione' => $contratto->descrizione,
                'qta' => 1,
                'prezzo_unitario' => $imponibile, // Prezzo netto (senza IVA)
                'pu' => $pu, // Prezzo Unitario con IVA
                'pt' => $prezzoTotale, // Prezzo Totale (con IVA)
                'imposta' => $imposta, // IVA
                'imponibile' => $imponibile, // Prezzo senza IVA
                'id_utente' => $utente->id,
                'id_azienda' => $utente->id_azienda,
                'iva' => $ivaPercentuale,
                'fattura' => 1,
                'rif_normativo' => null, // Può essere valorizzato se necessario
            ]);
        }





        return redirect()->back()->with('success', 'Tutti i contratti sono stati evasi in fattura.');
    }




    public function creaFatturaEvasa(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

$idDotesOriginale = $dati['id_dotes_originale'];
        // Filtra i campi da inserire nella tabella 'dotes'
        $campiTestata = [
            'tipologia_documento',
            'numero',
            'data',
            'esigibilita_iva',
            'nominativo',
            'cf',
            'piva',
            'indirizzo',
            'cap',
            'citta',
            'provincia',
            'nazione',
            'sdi',
            'pec',
            'condizioni_pagamento',
            'tipologia_pagamento',
            'stato',

        ];

        $dotesOriginale = DB::table('dotes')
            ->where('id', $idDotesOriginale)
                ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
                ->first();
        // Prepara i dati per la testata
        $datiTestata = [];
        foreach ($campiTestata as $campo) {
            if (isset($dati[$campo]) && !is_array($dati[$campo])) {
                $datiTestata[$campo] = $dati[$campo];
            }
        }
        if (isset($dati['id_dotes_originale']) && !is_array($dati['id_dotes_originale'])) {
            $datiTestata['id_dotes_evade'] = $dati['id_dotes_originale'];
        }


        if (isset($dati['id_dotes_originale']) && !is_array($dati['id_dotes_originale'])) {
            $datiTestata['cd_cf'] = $dotesOriginale->cd_cf;
        }

        if (isset($dati['id_dotes_originale']) && !is_array($dati['id_dotes_originale'])) {
            $datiTestata['pec'] = $dotesOriginale->pec;
        }
        if (isset($dati['id_dotes_originale']) && !is_array($dati['id_dotes_originale'])) {
            $datiTestata['sdi'] = $dotesOriginale->sdi;
        }

        $datiTestata['anno'] = date('Y');
        $datiTestata['data'] = date('Y-m-d', strtotime(str_replace('/', '-', $dati['data'])));
        $datiTestata['id_utente'] = $utente->id;
        $datiTestata['id_azienda'] = $utente->id_azienda;


        // Inserisce la testata nella tabella 'dotes'
        $idTestata = DB::table('dotes')->insertGetId($datiTestata);

        // Se esistono righe da aggiungere, inserisce le righe di dettaglio nella tabella 'dorig'
        if (isset($dati['quantita_evasa'])) {
            foreach ($dati['quantita_evasa'] as $idProdotto => $quantita) {
                if ($quantita > 0) {
                    // Recupera il prodotto dal documento evaso per ottenere il nome del prodotto
                    $dorigOriginale = DB::table('dorig')
                        ->where('id', $idProdotto)
                        ->where('id_azienda', $utente->id_azienda)
                        ->first();



                    $prezzoUnitario = $dorigOriginale->prezzo_unitario;
                    $iva = $dorigOriginale->iva; // Assumo che sia la percentuale di IVA (es. 22 per il 22%)

// Calcola l'IVA
                    $prezzoNetto = $prezzoUnitario * $quantita;
                    $importoIva = $prezzoNetto * ($iva / 100);

// Calcola il prezzo totale comprensivo di IVA

                    if ($dorigOriginale) {
                        $nomeProdotto = $dorigOriginale->nome_prodotto;

                        $datiRiga = [
                            'id_dotes' => $idTestata,
                            'id_testata' => $idTestata,
                            'descrizione' => $nomeProdotto,
                            'qta' => $quantita,
                            'prezzo_unitario' => $dorigOriginale->prezzo_unitario,
                            'pu' => $dorigOriginale->prezzo_unitario,
                            'pt' => $prezzoNetto,
                            'imposta' => $quantita * $dorigOriginale->prezzo_unitario - ($quantita * $dorigOriginale->prezzo_unitario / (1 + ($dorigOriginale->iva / 100))),
                            'imponibile' => $quantita * $dorigOriginale->prezzo_unitario - ($quantita * $dorigOriginale->prezzo_unitario - ($quantita * $dorigOriginale->prezzo_unitario / (1 + ($dorigOriginale->iva / 100)))),
                            'id_utente' => $utente->id,
                            'id_azienda' => $utente->id_azienda,
                            'iva' => $dorigOriginale->iva,
                            'fattura' => 1,
                            'codice_iva' => $dati['codice_iva'][$idProdotto] ?? null,
                            'rif_normativo' => $dati['rif_normativo'][$idProdotto] ?? null,
                        ];

                        // Inserisce la riga nella tabella 'dorig'
                        DB::table('dorig')->insert($datiRiga);
                    }
                }
            }

            // Aggiorna la testata con il totale delle righe inserite
            DB::update(
                'UPDATE dotes SET imponibile = (SELECT IFNULL(SUM(imponibile), 0) FROM dorig WHERE id_dotes = ? AND fattura = 1), imposta = (SELECT IFNULL(SUM(imposta), 0) FROM dorig WHERE id_dotes = ? AND fattura = 1), totale = imponibile + imposta WHERE id = ? AND id_azienda = ?',
                [$idTestata, $idTestata, $idTestata, $utente->id_azienda]
            );
        }

        // Reindirizza alla pagina delle fatture dell'anno corrente
        return Redirect::to('admin/fatture/' . $datiTestata['anno']);
    }





    public function fatture($anno, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();
        if (isset($dati['aggiungi_testata'])) {
            unset($dati['aggiungi_testata']);
            if (isset($dati['id_pratica'])) unset($dati['id_pratica']);
            $dati['anno'] = $anno;
            $dati['data'] = date('Y-m-d', strtotime(str_replace('/', '-', $dati['data'])));
            $dati['id_utente'] = $utente->id;
            $dati['id_azienda'] = $utente->id_azienda;

            DB::table('dotes')->insert($dati);
            return Redirect::to('admin/fatture/' . $anno);
        }

        if (isset($dati['esporta_fatture'])) {
            $data_inizio = date('Y-m-d', strtotime(str_replace('/', '-', $dati['data_inizio'])));
            $data_fine = date('Y-m-d', strtotime(str_replace('/', '-', $dati['data_fine'])));

            $fatture = DB::select('SELECT * FROM dotes WHERE data >= ? AND data <= ? AND id_azienda = ?', [$data_inizio, $data_fine, $utente->id_azienda]);

            $zip_file = 'fatture_' . str_replace('-', '', $data_inizio) . '_' . str_replace('-', '', $data_fine) . '.zip';

            $zip = new \ZipArchive();
            $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

            foreach ($fatture as $f) {
                $testata = $f;
                $righe = DB::select('SELECT * FROM dorig WHERE id_dotes = ? AND fattura = 1', [$f->id]);
                $dati_riepilogo = DB::select('SELECT SUM(imponibile) AS imponibile, SUM(imposta) AS imposta, iva, codice_iva, rif_normativo FROM dorig WHERE id_dotes = ? AND fattura = 1 GROUP BY codice_iva', [$f->id]);

                $xml = View::make('fatturazione.xml', compact('testata', 'righe', 'dati_riepilogo'));
                $invoice_file = 'fatture_inviate/IT02292500648_' . str_pad($f->id, 5, '0', STR_PAD_LEFT) . '.xml';
                file_put_contents($invoice_file, $xml);
                $zip->addFile($invoice_file, str_replace('fatture_inviate/', '', $invoice_file));
            }

            $zip->close();

            return response()->download($zip_file);
        }



        if (isset($dati['aggiungi_allegato'])) {
            unset($dati['aggiungi_allegato']);
            if (isset($_FILES['allegato']['name']) && $_FILES['allegato']['name'] != '') {
                $name = Str::random(20);
                $ext = pathinfo($_FILES['allegato']['name'], PATHINFO_EXTENSION);
                $path = 'allegati_fatture/' . $name . '.' . $ext;
                move_uploaded_file($_FILES['allegato']['tmp_name'], $path);
                $dati['allegato'] = $path;
                $dati['nome_allegato'] = strtolower(str_replace(' ', '_', $dati['nome_allegato'])) . '.' . $ext;
            }

            DB::table('dotes')->where('id', $dati['id'])->where('id_azienda', $utente->id_azienda)->update($dati);
            return Redirect::to('admin/fatture/' . $anno);
        }

        if (isset($dati['aggiungi_allegato2'])) {
            unset($dati['aggiungi_allegato2']);
            if (isset($_FILES['allegato2']['name']) && $_FILES['allegato2']['name'] != '') {
                $name = Str::random(20);
                $ext = pathinfo($_FILES['allegato2']['name'], PATHINFO_EXTENSION);
                $path = 'allegati_fatture/' . $name . '.' . $ext;
                move_uploaded_file($_FILES['allegato2']['tmp_name'], $path);
                $dati['allegato2'] = $path;
                $dati['nome_allegato2'] = strtolower(str_replace(' ', '_', $dati['nome_allegato2'])) . '.' . $ext;
            }

            DB::table('dotes')->where('id', $dati['id'])->where('id_azienda', $utente->id_azienda)->update($dati);
            return Redirect::to('admin/fatture/' . $anno);
        }

        if (isset($dati['modifica_testata'])) {
            unset($dati['modifica_testata']);
            if (isset($dati['id_pratica'])) unset($dati['id_pratica']);
            $dati['anno'] = $anno;
            $dati['data'] = date('Y-m-d', strtotime(str_replace('/', '-', $dati['data'])));
            $dati['id_utente'] = $utente->id;

            DB::table('dotes')->where('id', $dati['id'])->where('id_azienda', $utente->id_azienda)->update($dati);
            return Redirect::to('admin/fatture/' . $anno);
        }

        if (isset($dati['duplica'])) {
            if (isset($dati['id_pratica'])) unset($dati['id_pratica']);
            unset($dati['duplica']);

            $testata = DB::select('SELECT * FROM dotes WHERE id = ? AND id_azienda = ?', [$dati['id'], $utente->id_azienda]);
            if (sizeof($testata) > 0) {
                $testata = (array)$testata[0];
                $num_fattura = DB::select('SELECT IFNULL(MAX(numero) + 1, 1) AS numero FROM dotes WHERE anno = YEAR(NOW()) AND id_azienda = ?', [$utente->id_azienda])[0]->numero;

                $testata['numero'] = $num_fattura;
                unset($testata['id']);
                unset($testata['allegato']);
                unset($testata['allegato2']);
                unset($testata['nome_allegato']);
                unset($testata['nome_allegato2']);
                $testata['stato'] = 0;
                $testata['id_utente'] = $utente->id;

                $id_testata = DB::table('dotes')->insertGetId($testata);


                $righe = DB::select('SELECT * FROM dorig WHERE id_dotes = ? AND fattura = 1', [$dati['id']]);
                foreach ($righe as $r) {
                    $r = (array)$r;
                    unset($r['id']);
                    $r['id_dotes'] = $id_testata;
                    $r['id_utente'] = $utente->id;
                    $r['fattura'] = 1;

                    DB::table('dorig')->insert($r);
                }
                return Redirect::to('admin/fatture/' . $anno);
            }

            return Redirect::to('admin/fatture/' . $anno);
        }

        if (isset($dati['crea_nota_credito'])) {
            if (isset($dati['id_pratica'])) unset($dati['id_pratica']);
            unset($dati['duplica']);

            $testata = DB::select('SELECT * FROM dotes WHERE id = ? AND id_azienda = ?', [$dati['id'], $utente->id_azienda]);
            if (sizeof($testata) > 0) {
                $testata = (array)$testata[0];
                $num_fattura = DB::select('SELECT IFNULL(MAX(numero) + 1, 1) AS numero FROM dotes WHERE tipologia_documento = "TD04" AND anno = YEAR(NOW()) AND id_azienda = ?', [$utente->id_azienda])[0]->numero;

                $testata['numero'] = $num_fattura;
                $testata['tipologia_documento'] = 'TD04';
                unset($testata['id']);
                unset($testata['allegato']);
                unset($testata['allegato2']);
                unset($testata['nome_allegato']);
                unset($testata['nome_allegato2']);
                $testata['stato'] = 0;
                $testata['id_utente'] = $utente->id;

                $id_testata = DB::table('dotes')->insertGetId($testata);

                $r['id_dotes'] = $id_testata;
                $r['descrizione'] = $dati['descrizione'];
                $r['qta'] = 1;
                $r['um'] = 'NR';
                $r['pu'] = $testata['totale'];
                $r['pt'] = $testata['totale'];
                $r['iva'] = $testata['iva'];
                $r['imposta'] = $testata['imposta'];
                $r['imponibile'] = $testata['imponibile'];
                $r['id_utente'] = $utente->id;
                $r['fattura'] = 1;

                DB::table('dorig')->insert($r);

                return Redirect::to('admin/fatture/' . $anno);
            }

            return Redirect::to('admin/fatture/' . $anno);
        }

        if (isset($dati['invia_sdi'])) {
            $fatture = DB::select('SELECT * FROM dotes WHERE id = ? AND (stato = 0 OR stato = 2) AND id_azienda = ?', [$dati['id'], $utente->id_azienda]);


                if (sizeof($fatture) > 0) {
                    $fattura = $fatture[0];

                    // Recupera testata, righe e riepilogo per la creazione dell'XML
                    $testata = DB::select('SELECT * FROM dotes WHERE id = ? AND id_azienda = ?', [$dati['id'], $utente->id_azienda]);
                    $righe = DB::select('SELECT * FROM dorig WHERE id_dotes = ? AND fattura = 1', [$dati['id']]);
                    $dati_riepilogo = DB::select('SELECT SUM(imponibile) AS imponibile, SUM(imposta) AS imposta, iva, codice_iva, rif_normativo FROM dorig WHERE id_dotes = ? AND fattura = 1 GROUP BY codice_iva', [$dati['id']]);
                    $azienda = DB::table('aziende')->where('id', $utente->id_azienda)->first();

                    if (sizeof($righe) > 0) {

                        $testata = $testata[0];
                        // Crea il contenuto XML con una view
                        $xml = View::make('fatturazione.xml', compact('testata', 'righe', 'dati_riepilogo', 'azienda'));
                        $fileName = 'IT02292500648_' . str_pad(base_convert($testata->id, 10, 36), 4, '0', STR_PAD_LEFT) . '.xml';
                        $filePath = 'fatture_inviate/' . $fileName;

                        // Controlla se la directory esiste, se no la crea
                        $directory = 'fatture_inviate';
                        if (!file_exists($directory)) {
                            mkdir($directory, 0775, true);
                        }

                        // Salva il file XML sul server
                        file_put_contents($filePath, $xml);

                        // Invia il file XML via FTP
                        try {
                            if ($this->inviaFileXmlSftp($filePath,$testata->numero)) {
                                // Se l'invio ha successo, aggiorna lo stato della fattura
                                DB::update('UPDATE dotes SET stato = 1,nome_file_fattura="'.$fileName.'" WHERE id = ? AND id_azienda = ?', [$dati['id'], $utente->id_azienda]);
                            }
                        } catch (Exception $e) {
                            return Redirect::to('admin/fatture/' . $anno)->with('error', 'Errore durante l\'invio dell\'XML: ' . $e->getMessage());
                        }
                    }
                }

            //questa è la pec di invio fattura sbloccare solo quando è finito tutto e inserire i dati dinamici per l'invio
            /*if (sizeof($fatture) > 0) {
                $fattura = $fatture[0];

                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtps.pec.aruba.it';
                $mail->SMTPAuth = true;
                $mail->Username = 'orr@pec.officinariparazionirotabili.com';
                $mail->Password = 'Luisa2022?';
                $mail->SMTPSecure = 'ssl';
                $mail->CharSet = 'utf-8';
                $mail->Port = 465;
                $mail->setFrom('orr@pec.officinariparazionirotabili.com');
                $mail->addAddress('sdi24@pec.fatturapa.it');
                $mail->isHTML(true);
                $mail->Subject = 'Fattura Elettronica ' . $fattura->numero . ' del ' . $fattura->data;
                $mail->Body = 'Fattura Elettronica ' . $fattura->numero . ' del ' . $fattura->data;

                $testata = DB::select('SELECT * FROM dotes WHERE id = ? AND id_azienda = ?', [$dati['id'], $utente->id_azienda]);
                $righe = DB::select('SELECT * FROM dorig WHERE id_dotes = ? AND fattura = 1', [$dati['id']]);
                $dati_riepilogo = DB::select('SELECT SUM(imponibile) AS imponibile, SUM(imposta) AS imposta, iva, codice_iva, rif_normativo FROM dorig WHERE id_dotes = ? AND fattura = 1 GROUP BY codice_iva', [$dati['id']]);

                DB::update('UPDATE dotes SET stato = 1 WHERE id = ? AND id_azienda = ?', [$dati['id'], $utente->id_azienda]);

                if (sizeof($righe) > 0) {
                    $testata = $testata[0];
                    $xml = View::make('fatturazione.xml', compact('testata', 'righe', 'dati_riepilogo'));

                    file_put_contents('fatture_inviate/IT02292500648_' . str_pad(base_convert($testata->id, 10, 36), 4, '0', STR_PAD_LEFT) . '.xml', $xml);
                    $mail->AddAttachment('fatture_inviate/IT02292500648_' . str_pad(base_convert($testata->id, 10, 36), 4, '0', STR_PAD_LEFT) . '.xml');
                    $mail->send();
                }
            }*/
        }

        if (isset($dati['elimina'])) {
            DB::table('dotes')->where('id', $dati['id'])->where('id_azienda', $utente->id_azienda)->delete();
            DB::table('dorig')->where('id_dotes', $dati['id'])->where('fattura', 1)->delete();
           /* DB::update('UPDATE preventivi_testata SET id_fattura_testata = 0 WHERE id_fattura_testata = ?', [$dati['id']]);*/
            return Redirect::to('admin/fatture/' . $anno);
        }

        if (isset($dati['elimina_allegato'])) {
            DB::update('UPDATE dotes SET allegato = NULL, nome_allegato = NULL WHERE id = ? AND id_azienda = ?', [$dati['id'], $utente->id_azienda]);
            return Redirect::to('admin/fatture/' . $anno);
        }

        if (isset($dati['elimina_allegato2'])) {
            DB::update('UPDATE dotes SET allegato2 = NULL, nome_allegato2 = NULL WHERE id = ? AND id_azienda = ?', [$dati['id'], $utente->id_azienda]);
            return Redirect::to('admin/fatture/' . $anno);
        }

        if (isset($dati['aggiungi_riga'])) {
            unset($dati['aggiungi_riga']);
            if (isset($dati['id_pratica'])) unset($dati['id_pratica']);
            $dati['imposta'] = $dati['pt'] - ($dati['pt'] / (1 + ($dati['iva'] / 100)));
            $dati['imponibile'] = $dati['pt'] - $dati['imposta'];
            $dati['id_utente'] = $utente->id;
            $dati['id_azienda'] = $utente->id_azienda;
            $dati['fattura'] = 1;
            $dati['id_dotes'] = $dati['id_testata'];
            DB::table('dorig')->insert($dati);
            DB::update(
                'UPDATE dotes SET imponibile = (SELECT IFNULL(SUM(imponibile), 0) FROM dorig WHERE id_dotes = ? AND fattura = 1), imposta = (SELECT IFNULL(SUM(imposta), 0) FROM dorig WHERE id_dotes = ? AND fattura = 1), totale = imponibile + imposta WHERE id = ? AND id_azienda = ?',
                [$dati['id_testata'], $dati['id_testata'], $dati['id_testata'], $utente->id_azienda]
            );
            return Redirect::to('admin/fatture/' . $anno);
        }

        if (isset($dati['modifica_riga'])) {
            unset($dati['modifica_riga']);
            if (isset($dati['id_pratica'])) unset($dati['id_pratica']);
            $dati['imposta'] = $dati['pt'] - ($dati['pt'] / (1 + ($dati['iva'] / 100)));
            $dati['imponibile'] = $dati['pt'] - $dati['imposta'];
            $dati['id_utente'] = $utente->id;

            DB::table('dorig')->where('id', $dati['id'])->where('fattura', 1)->update($dati);
            DB::update(
                'UPDATE dotes SET imponibile = (SELECT IFNULL(SUM(imponibile), 0) FROM dorig WHERE id_dotes = ? AND fattura = 1), imposta = (SELECT IFNULL(SUM(imposta), 0) FROM dorig WHERE id_dotes = ? AND fattura = 1), totale = imponibile + imposta WHERE id = ? AND id_azienda = ?',
                [$dati['id_testata'], $dati['id_testata'], $dati['id_testata'], $utente->id_azienda]
            );
            return Redirect::to('admin/fatture/' . $anno);
        }

        if (isset($dati['elimina_riga'])) {
            unset($dati['elimina_riga']);
            if (isset($dati['id_pratica'])) unset($dati['id_pratica']);
            DB::table('dorig')->where('id', $dati['id'])->where('fattura', 1)->delete();
            DB::update(
                'UPDATE dotes SET imponibile = (SELECT IFNULL(SUM(imponibile), 0) FROM dorig WHERE id_dotes = ? AND fattura = 1), imposta = (SELECT IFNULL(SUM(imposta), 0) FROM dorig WHERE id_dotes = ? AND fattura = 1), totale = imponibile + imposta WHERE id = ? AND id_azienda = ?',
                [$dati['id_testata'], $dati['id_testata'], $dati['id_testata'], $utente->id_azienda]
            );
            return Redirect::to('admin/fatture/' . $anno);
        }

        $num_fattura = DB::select('SELECT IFNULL(MAX(numero) + 1, 1) AS numero FROM dotes WHERE anno = YEAR(NOW()) AND id_azienda = ?', [$utente->id_azienda])[0]->numero;
        $fatture = DB::select('SELECT * FROM dotes WHERE tipologia_documento LIKE "TD%" AND id_azienda = ? ORDER BY numero DESC, anno DESC, data DESC', [$utente->id_azienda]);

        foreach ($fatture as $f) {
            $f->righe = DB::select('SELECT * FROM dorig WHERE id_dotes = ? AND fattura = 1', [$f->id]);
        }

        $clienti = DB::table('clienti')->where('id_azienda', $utente->id_azienda)->get();

        $page = 'fatture';
        return View::make('default.fatture', compact('page', 'utente', 'fatture', 'num_fattura', 'anno', 'clienti'));
    }


    public function dettaglio_fattura(Request $request, $id)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Recupero della testata e delle righe
        $testata = DB::table('dotes')->where('id', $id)->where('id_azienda', $utente->id_azienda)->first();
        $righe = DB::select('SELECT * FROM dorig WHERE id_dotes = ? AND id_azienda = ?', [$id, $utente->id_azienda]);
        $dati_riepilogo = DB::select('SELECT sum(imponibile) imponibile, sum(imposta) imposta, iva, codice_iva, rif_normativo FROM dorig WHERE id_dotes = ? GROUP BY codice_iva', [$id]);

        // Recupero dei pagamenti effettuati per questa fattura
        $pagamenti = DB::select('SELECT * FROM pagamenti WHERE id_fattura = ? AND id_azienda = ?', [$id, $utente->id_azienda]);

        // Recupero delle informazioni sulle rate
        $importo_rate = json_decode($testata->importo_rate, true) ?? [];
        $scadenze_rate = json_decode($testata->scadenze_rate, true) ?? [];
        $status_rate = json_decode($testata->status_pagamento, true) ?? [];

        // Calcolo dello stato del pagamento
        $totale_pagato = array_sum(array_column($pagamenti, 'importo'));
        $numero_rate = count($pagamenti);
        $status_pagamento = 'Non ancora pagato';

        if ($totale_pagato > 0) {
            $status_pagamento = $totale_pagato >= $testata->totale ? "Pagato" : "Pagato in $numero_rate rate";
        }

        // Recupero dei dati dell'azienda
        $azienda = DB::table('aziende')->where('id', $utente->id_azienda)->first();

        // Passiamo i dati alla vista
        return view('default.dettaglio_fattura', compact(
            'testata', 'righe', 'dati_riepilogo', 'azienda', 'utente', 'pagamenti',
            'status_pagamento', 'totale_pagato', 'importo_rate', 'scadenze_rate', 'status_rate'
        ));
    }







    public function inserisci_pagamento(Request $request, $id)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Valida i dati
        $request->validate([
            'importo' => 'required|numeric',
            'data_pagamento' => 'required|date',
        ]);

        // Recupera la tipologia della fattura
        $tipologia_fattura = DB::table('dotes')->where('id', $id)->where('id_azienda', $utente->id_azienda)->value('tipologia_documento');

        // Determina se il pagamento è un'entrata o un'uscita
        $natura = 'entrata'; // Default se la fattura è in uscita
        if ($tipologia_fattura == 'fattura in ingresso') {
            $natura = 'uscita'; // Se la tipologia è "fattura in ingresso"
        }

        // Inserisci il pagamento nel database
        DB::table('pagamenti')->insert([
            'id_utente' => $utente->id,
            'id_fattura' => $id,
            'importo' => $request->input('importo'),
            'data_pagamento' => $request->input('data_pagamento'),
            'stato' => 'Pagato', // Imposta lo stato come "Pagato"
            'id_azienda' => $utente->id_azienda,
            'natura' => $natura,  // Assegna la natura del pagamento
        ]);

        // Passa i dati alla vista e reindirizza alla pagina di dettaglio
        return redirect()->route('fattura.dettaglio', ['id' => $id])->with('success', 'Pagamento registrato con successo.');
    }



    // In controller: inserisci_pagamento
    public function aggiorna_rate(Request $request, $id)
    {
        $this->is_loggato();
        $utente = session('utente');

        $testata = DB::table('dotes')->where('id', $id)->where('id_azienda', $utente->id_azienda)->first();

        // Raccogli nuovi dati dal form
        $importo_rate = $request->input('rate', []);
        $scadenze_rate = $request->input('scadenza', []);
        $status_rate = $request->input('status', []);

        // Recupera i dati attuali dal database
        $current_status_rate = json_decode($testata->status_pagamento, true) ?? [];
        $current_importo_rate = json_decode($testata->pagamento_rate, true) ?? [];

        // Loop per verificare cambiamenti nello stato
        foreach ($status_rate as $index => $new_status) {
            // Se lo stato cambia da "da_saldare" a "saldato", inserisci un nuovo pagamento
            if (
                isset($current_status_rate[$index]) &&
                $current_status_rate[$index] === 'da_saldare' &&
                $new_status === 'saldato'
            ) {
                DB::table('pagamenti')->insert([
                    'id_utente' => $utente->id,
                    'id_fattura' => $id,
                    'importo' => $current_importo_rate[$index] ?? 0, // Usa l'importo della rata
                    'data_pagamento' => now(), // Usa la data attuale
                    'stato' => 'Pagato', // Stato del pagamento
                    'id_azienda' => $utente->id_azienda,
                    'natura' => $testata->tipologia_documento === 'fattura in ingresso' ? 'uscita' : 'entrata',
                ]);
            }

            // Se lo stato cambia da "saldato" a "da saldare", rimuovi il pagamento
            if (
                isset($current_status_rate[$index]) &&
                $current_status_rate[$index] === 'saldato' &&
                $new_status === 'da_saldare'
            ) {
                DB::table('pagamenti')
                    ->where('id_fattura', $id)
                    ->where('id_azienda', $utente->id_azienda)
                    ->where('importo', $current_importo_rate[$index] ?? 0)
                    ->delete();
            }
        }

        // Aggiorna il database con i nuovi dati
        DB::table('dotes')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->update([
                'importo_rate' => json_encode($importo_rate),
                'pagamento_rate' => json_encode($importo_rate),
                'scadenze_rate' => json_encode($scadenze_rate),
                'status_pagamento' => json_encode($status_rate),
            ]);

        // Controlla se tutte le rate sono saldate
        if (collect($status_rate)->every(function ($status) {
            return $status === 'saldato';
        })) {

            DB::table('dotes')
                ->where('id', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->update(['saldata' => 1]);
        }

        return redirect()->route('fattura.dettaglio', ['id' => $id])->with('success', 'Rate aggiornate con successo.');
    }

    public function marcaSaldata(Request $request, $id)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Trova la fattura per l'utente attuale
        $fattura = DB::table('dotes')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$fattura) {
            return response()->json(['success' => false, 'message' => 'Fattura non trovata.']);
        }

        // Aggiorna la colonna saldata
        DB::table('dotes')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->update(['saldata' => 1]);

        return response()->json(['success' => true]);
    }





    public function eliminaRata(Request $request, $id)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Recupera l'indice della rata da eliminare
        $rateIndex = $request->input('rate_index');

        // Recupera la fattura
        $testata = DB::table('dotes')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        // Decodifica i dati delle rate
        $importo_rate = json_decode($testata->pagamento_rate, true);
        $scadenze_rate = json_decode($testata->scadenze_rate, true);
        $status_rate = json_decode($testata->status_pagamento, true);

        if (isset($importo_rate[$rateIndex])) {
            // Rimuovi l'elemento dai dati delle rate
            array_splice($importo_rate, $rateIndex, 1);
            array_splice($scadenze_rate, $rateIndex, 1);
            array_splice($status_rate, $rateIndex, 1);

            // Aggiorna il database
            DB::table('dotes')
                ->where('id', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->update([
                    'importo_rate' => json_encode($importo_rate),
                    'pagamento_rate' => json_encode($importo_rate),
                    'scadenze_rate' => json_encode($scadenze_rate),
                    'status_pagamento' => json_encode($status_rate),
                ]);

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false, 'message' => 'Rata non trovata']);
    }






    public function configura_pagamento_rate(Request $request, $id)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Recupera i dati da input (rate, scadenze, status)
        $importi_rate = $request->input('importo');
        $scadenze_rate = $request->input('scadenza');
        $status_rate = $request->input('status');

        // Crea un array associativo con le informazioni delle rate
        $rate_data = [];
        foreach ($importi_rate as $index => $importo) {
            $rate_data[] = [
                'importo' => $importo,
                'scadenza' => $scadenze_rate[$index],
                'status' => $status_rate[$index],
            ];
        }

        // Salva le informazioni nel database
        DB::table('dotes')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->update([
                'importo_rate' => json_encode(array_column($rate_data, 'importo')),
                'numero_rate' => count($rate_data),  // Numero totale delle rate
                'pagamento_rate' => json_encode(array_column($rate_data, 'importo')),
                'scadenze_rate' => json_encode(array_column($rate_data, 'scadenza')),
                'status_pagamento' => json_encode(array_column($rate_data, 'status')),
                'split_payment' => 1
            ]);

        // Redirigi al dettaglio della fattura con messaggio di successo
        return redirect()->route('fattura.dettaglio', ['id' => $id])->with('success', 'Pagamento rateizzato configurato con successo.');
    }





    public function riepilogoDocumenti() {
        $this->is_loggato();
        $utente = session()->get('utente');


        $anno = ('2024');
        $mese = ('12');

        $query = DB::table('dotes')
            ->whereYear('data_doc', $anno)
            ->whereMonth('data_doc', $mese)
            ->where('id_azienda', $utente->id_azienda);



        // Recupera i tipi di documento unici e il loro conteggio
        $tipi_documento = DB::table('dotes')
            ->select('tipo_documento', DB::raw('COUNT(*) as count'))
            ->whereYear('data_doc', $anno)
            ->whereMonth('data_doc', $mese)
            ->where('id_azienda', $utente->id_azienda)
            ->groupBy('tipo_documento')
            ->get();


        return view('default.riepilogo_documenti', compact('utente'));
    }

    public function getDocumentiPerMese(Request $request)
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









    public function riepilogo($anno) {
        $this->is_loggato();
        $utente = session()->get('utente');

        // Query per il fatturato mensile
        $fatturato_mensile = DB::select('
        SELECT 
            MONTH(data) AS mese, 
            SUM(totale) AS totale_tot
        FROM 
            dotes
        WHERE 
            tipologia_documento LIKE "TD%" 
            AND contabilizzata = 1
            AND YEAR(data) = ? 
            AND id_azienda = ?
        GROUP BY MONTH(data)
        ORDER BY MONTH(data)
    ', [$anno, $utente->id_azienda]);

        // Calcolo del fatturato totale
        $fatturato_totale = DB::table('dotes')
            ->where('tipologia_documento', 'LIKE', 'TD%')
            ->whereYear('data', $anno)
            ->where('id_azienda', $utente->id_azienda)
            ->sum('totale');

        // Totale annuale per le entrate e uscite
        $totale_entrate = DB::table('pagamenti')
            ->where('natura', 'entrata')
            ->whereYear('data_pagamento', $anno)
            ->where('id_azienda', $utente->id_azienda)
            ->sum('importo');

        $totale_uscite = DB::table('pagamenti')
            ->where('natura', 'uscita')
            ->whereYear('data_pagamento', $anno)
            ->where('id_azienda', $utente->id_azienda)
            ->sum('importo');

        // Query per tutte le fatture
        $fatture = DB::table('dotes')
            ->leftJoin('clienti', 'dotes.cd_cf', '=', 'clienti.cd_cf')
            ->where('dotes.id_azienda', $utente->id_azienda)
            ->where('dotes.tipologia_documento', 'LIKE', 'TD%')
            ->whereYear('dotes.data_doc', $anno)
            ->select('dotes.*', 'clienti.ragione_sociale AS cliente')
            ->get();

        return view('default.riepilogo', compact(
            'fatturato_mensile',
            'fatturato_totale',
            'totale_entrate',
            'totale_uscite',
            'fatture',
            'anno',
            'utente'
        ));
    }






    private function creaMovimentoContabile($fattura)
    {
        $importo = $fattura->totale; // Usa il totale della fattura per il movimento contabile

        // Crea il movimento contabile
        DB::table('movimenti')->insert([
            'codice_contabile' => 'FATTURA_' . $fattura->numero,  // Codice contabile generato dinamicamente
            'descrizione' => 'Pagamento della fattura numero ' . $fattura->numero,
            'importo' => $importo,
            'tipo' => 'entrata', // O 'uscita' se la fattura è un'uscita
            'data_movimento' => now(),
        ]);
    }


    /**
     * Invia il file XML tramite FTP
     *
     * @param string $xmlFile Percorso del file XML da inviare
     * @return bool Esito dell'operazione
     */



    private function inviaFileXmlSftp($xmlFile,$numero_doc) {
        // Configurazione SFTP
        $sftpHost = '135.125.180.188';
        $sftpPort = 22; // Porta SFTP standard
        $sftpUser = 'sftp_user';
        $ppk_path = 'CERTS/ingenia_private.ppk';
        $remotePath = 'DatiVersoSdITest/';

        date_default_timezone_set('UTC');

        $year = date("Y");

        // Calcola il giorno giuliano (1-366)
        $julianDay = date("z") + 1; // date("z") ritorna 0-365, aggiungiamo 1

        // Formatta il giorno giuliano con padding di zeri
        $julianDay = str_pad($julianDay, 3, "0", STR_PAD_LEFT);

        // Ora corrente nel formato HHMM
        $time = date("Hi");

        // Crea il nome del file ZIP
        $zipFileName = sprintf('FI.02883500643.%s%s.%s.900.zip',
            $year,      // anno completo (es. 2025)
            $julianDay, // giorno giuliano (es. 008)
            $time       // ora e minuti (es. 1430)
        );

        // Crea un nuovo oggetto ZipArchive
        $zip = new \ZipArchive();

        // Apri/crea il file ZIP
        if ($zip->open($zipFileName, \ZipArchive::CREATE) === TRUE) {
            // Aggiungi il file all'archivio

            $zip->addFile($xmlFile, basename($xmlFile));


            $xml = new \DOMDocument('1.0', 'UTF-8');
            $xml->formatOutput = true;
            $root = $xml->createElementNS('http://www.fatturapa.it/sdi/ftp/v2.0', 'ns2:FileQuadraturaFTP');
            $root->setAttribute('versione', '2.0');
            $xml->appendChild($root);
            $idNodo = $xml->createElement('IdentificativoNodo', '02883500643');
            $root->appendChild($idNodo);
            $dataOra = $xml->createElement('DataOraCreazione', date('Y-m-d\TH:i:s.000\Z'));
            $root->appendChild($dataOra);
            $nomeSupporto = $xml->createElement('NomeSupporto',$zipFileName);
            $root->appendChild($nomeSupporto);
            $numeroFile = $xml->createElement('NumeroFile');
            $file = $xml->createElement('File');
            $tipo = $xml->createElement('Tipo', 'FA');
            $numero = $xml->createElement('Numero',1);
            $file->appendChild($tipo);
            $file->appendChild($numero);
            $numeroFile->appendChild($file);
            $root->appendChild($numeroFile);
            $xml->xmlStandalone = true;

            $nome_file_quadratura = str_replace('.zip','.xml',$zipFileName);

            $xml->save($nome_file_quadratura);

            $zip->addFile($nome_file_quadratura, basename($nome_file_quadratura));


            $zip->close();

        }

            // Carica la chiave privata
        $key = PublicKeyLoader::load(file_get_contents($ppk_path));

        // Crea connessione SFTP
        $sftp = new SFTP($sftpHost);

        // Login con la chiave
        if (!$sftp->login($sftpUser, $key)) {
            throw new Exception('Login fallito');
        }

        // Verifica esistenza file
        if (!file_exists($zipFileName)) {
            throw new \Exception("File XML non trovato: " . $zipFileName);
        }


        $signerCert = 'CERTS/FIRMA.PEM';
        $password = 'Kj7eH3ga';
        $password_pem = '123456';
        $certificateFile = 'CERTS/sogeiunicocifra.pem';

        // Costruisci il comando OpenSSL
        $command = sprintf(
            'openssl smime -sign -in %s -outform der -binary -nodetach -out %s -signer %s -passin pass:%s 2>&1',
            escapeshellarg($zipFileName),
            escapeshellarg($zipFileName.'.p7m'),
            escapeshellarg($signerCert),
            escapeshellarg($password_pem)
        );

        $return = shell_exec($command);

        
        $command = sprintf(
            'openssl smime -encrypt -in %s -outform der -binary -aes256 -out %s %s',
            escapeshellarg($zipFileName.'.p7m'),
            escapeshellarg($zipFileName.'.p7m.enc'),
            escapeshellarg($certificateFile)
        );

        $return = shell_exec($command);


        unlink($zipFileName);
        unlink($zipFileName.'.p7m');
        rename($zipFileName.'.p7m.enc',$zipFileName);

        // Ottieni il nome del file dal percorso completo
        $fileName = basename($zipFileName);

        // Percorso completo nel server remoto
        $remoteFile = $remotePath . $fileName;

        // Carica il file sul server remoto
        $fileContents = file_get_contents($zipFileName);
        if ($fileContents === false) {
            throw new \Exception("Impossibile leggere il file locale: " . $zipFileName);
        }

        // Upload del file
        if ($sftp->put($remoteFile, $fileContents)) {



            unlink($zipFileName);
            unlink(str_replace('.zip','.xml',$zipFileName));
            unlink($xmlFile);


        } else {

            throw new \Exception("Errore durante l'upload del file: " . $fileName);
        }

        return true;
    }









    /*public function fatture($anno,Request $request){

        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if(isset($dati['aggiungi_testata'])){
            unset($dati['aggiungi_testata']);
            if(isset($dati['id_pratica'])) unset($dati['id_pratica']);
            $dati['anno'] = $anno;
            $dati['data'] = date('Y-m-d', strtotime(str_replace('/', '-', $dati['data'])));
            $dati['id_utente'] = $utente->id;

            dd($dati);
            DB::table('fatture_testata')->insert($dati);
            return Redirect::to('admin/fatture/'.$anno);
        }

        if(isset($dati['esporta_fatture'])){

            $data_inizio = date('Y-m-d', strtotime(str_replace('/', '-', $dati['data_inizio'])));
            $data_fine = date('Y-m-d', strtotime(str_replace('/', '-', $dati['data_fine'])));

            $fatture = DB::select('SELECT * from fatture_testata where data >= "'.$data_inizio.'" and data <= "'.$data_fine.'"');

            $zip_file = 'fatture_'.str_replace('-','',$data_inizio).'_'.str_replace('-','',$data_fine).'.zip'; // Name of our archive to download

            $zip = new \ZipArchive();
            $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);


            foreach($fatture as $f){

                $testata = $f;
                $righe = DB::select('SELECT * from fatture_righe where id_testata = '.$f->id);
                $dati_riepilogo = DB::select('SELECT sum(imponibile) imponibile,sum(imposta) imposta,iva,codice_iva,rif_normativo from fatture_righe where id_testata = '.$f->id.' group by codice_iva');

                $xml = View::make('fatturazione.xml', compact('testata', 'righe', 'dati_riepilogo'));
                $invoice_file = 'fatture_inviate/IT02292500648_' . str_pad($f->id, 5, '0', STR_PAD_LEFT) . '.xml';
                file_put_contents($invoice_file, $xml);
                $zip->addFile($invoice_file, str_replace('fatture_inviate/','',$invoice_file));

            }

            $zip->close();

            return response()->download($zip_file);
        }

        if(isset($dati['aggiungi_allegato'])){
            unset($dati['aggiungi_allegato']);
            if(isset($_FILES['allegato']['name']) && $_FILES['allegato']['name'] != ''){

                $name = Str::random(20);
                $ext  = pathinfo($_FILES['allegato']['name'], PATHINFO_EXTENSION);
                $path = 'allegati_fatture/'.$name.'.'.$ext;
                move_uploaded_file($_FILES['allegato']['tmp_name'],$path);
                $dati['allegato'] = $path;

                $dati['nome_allegato'] = $dati['nome_allegato'].'.'.$ext;

                $dati['nome_allegato'] = strtolower(str_replace(' ','_',$dati['nome_allegato']));
            }

            DB::table('fatture_testata')->where('id',$dati['id'])->update($dati);
            return Redirect::to('admin/fatture/'.$anno);
        }

        if(isset($dati['aggiungi_allegato2'])){
            unset($dati['aggiungi_allegato2']);
            if(isset($_FILES['allegato2']['name']) && $_FILES['allegato2']['name'] != ''){

                $name = Str::random(20);
                $ext  = pathinfo($_FILES['allegato2']['name'], PATHINFO_EXTENSION);
                $path = 'allegati_fatture/'.$name.'.'.$ext;
                move_uploaded_file($_FILES['allegato2']['tmp_name'],$path);
                $dati['allegato2'] = $path;

                $dati['nome_allegato2'] = $dati['nome_allegato2'].'.'.$ext;
                $dati['nome_allegato2'] = strtolower(str_replace(' ','_',$dati['nome_allegato2']));
            }

            DB::table('fatture_testata')->where('id',$dati['id'])->update($dati);
            return Redirect::to('admin/fatture/'.$anno);
        }

        if(isset($dati['modifica_testata'])){
            unset($dati['modifica_testata']);
            if(isset($dati['id_pratica'])) unset($dati['id_pratica']);
            $dati['anno'] = $anno;
            $dati['data'] = date('Y-m-d', strtotime(str_replace('/', '-', $dati['data'])));
            $dati['id_utente'] = $utente->id;

            DB::table('fatture_testata')->where('id',$dati['id'])->update($dati);
            return Redirect::to('admin/fatture/'.$anno);
        }

        if(isset($dati['duplica'])){
            if(isset($dati['id_pratica'])) unset($dati['id_pratica']);
            unset($dati['duplica']);

            $testata = DB::select('SELECT * from fatture_testata where id='.$dati['id']);
            if(sizeof($testata) > 0) {
                $testata = (array) $testata[0];
                $num_fattura = DB::select('SELECT IFNULL(max(numero)+1,1) as numero from fatture_testata where anno = YEAR(NOW())')[0]->numero;

                $testata['numero'] = $num_fattura;
                unset($testata['id']);
                unset($testata['allegato']);
                unset($testata['allegato2']);
                unset($testata['nome_allegato']);
                unset($testata['nome_allegato2']);
                $testata['stato'] = 0;
                $testata['id_utente'] = $utente->id;

                $id_testata = DB::table('fatture_testata')->insertGetId($testata);

                $righe = DB::select('SELECT * from fatture_righe where id_testata = '.$dati['id']);
                foreach($righe as $r){
                    $r = (array) $r;
                    unset($r['id']);
                    $r['id_testata'] = $id_testata;
                    $r['id_utente'] = $utente->id;

                    DB::table('fatture_righe')->insert($r);
                }
                return Redirect::to('admin/fatture/'.$anno);
            }


            return Redirect::to('lista_fatture2/'.$anno);
        }

        if(isset($dati['crea_nota_credito'])){
            if(isset($dati['id_pratica'])) unset($dati['id_pratica']);
            unset($dati['duplica']);

            $testata = DB::select('SELECT * from fatture_testata where id='.$dati['id']);
            if(sizeof($testata) > 0) {
                $testata = (array) $testata[0];
                $num_fattura = DB::select('SELECT IFNULL(max(numero)+1,1) as numero from fatture_testata where tipologia_documento = "TD04" and anno = YEAR(NOW())')[0]->numero;

                $testata['numero'] = $num_fattura;
                $testata['tipologia_documento'] = 'TD04';
                unset($testata['id']);
                unset($testata['id']);
                unset($testata['allegato']);
                unset($testata['allegato2']);
                unset($testata['nome_allegato']);
                unset($testata['nome_allegato2']);
                $testata['stato'] = 0;
                $testata['id_utente'] = $utente->id;

                $id_testata = DB::table('fatture_testata')->insertGetId($testata);

                $r['id_testata'] = $id_testata;
                $r['descrizione'] = $dati['descrizione'];
                $r['qta'] = 1;
                $r['um'] = 'NR';
                $r['pu'] = $testata['totale'];
                $r['pt'] = $testata['totale'];
                $r['iva'] = $testata['iva'];
                $r['imposta'] = $testata['imposta'];
                $r['imponibile'] = $testata['imponibile'];
                $r['id_utente'] = $utente->id;

                DB::table('fatture_righe')->insert($r);

                return Redirect::to('admin/fatture/'.$anno);
            }

            return Redirect::to('lista_fatture2/'.$anno);
        }

        if(isset($dati['invia_sdi'])){

            $fatture = DB::select('SELECT * from fatture_testata where id='.$dati['id'].' and (stato = 0 or stato = 2)');
            if(sizeof($fatture) > 0) {

                $fattura = $fatture[0];

                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = ' smtps.pec.aruba.it';
                $mail->SMTPAuth = true;
                $mail->Username = 'orr@pec.officinariparazionirotabili.com';
                $mail->Password = 'Luisa2022?';
                $mail->SMTPSecure = 'ssl';
                $mail->CharSet = 'utf-8';
                $mail->Port = 465;
                $mail->setFrom('orr@pec.officinariparazionirotabili.com');
                $mail->addAddress('sdi24@pec.fatturapa.it');
                $mail->isHTML(true);
                $mail->Subject = 'Fattura Elettronica '.$fattura->numero.' del '.$fattura->data;
                $mail->Body = 'Fattura Elettronica '.$fattura->numero.' del '.$fattura->data;

                $testata = DB::select('SELECT * from fatture_testata where id = '.$dati['id']);
                $righe = DB::select('SELECT * from fatture_righe where id_testata = '.$dati['id']);
                $dati_riepilogo = DB::select('SELECT sum(imponibile) imponibile,sum(imposta) imposta,iva,codice_iva,rif_normativo from fatture_righe where id_testata = '.$dati['id'].' group by codice_iva');

                DB::update('update fatture_testata set stato = 1 where id='.$dati['id']);
                if(sizeof($righe) > 0) {
                    $testata = $testata[0];
                    $xml = View::make('fatturazione.xml', compact('testata', 'righe', 'dati_riepilogo'));

                    file_put_contents('fatture_inviate/IT02292500648_' . str_pad(base_convert($testata->id,10,36), 4, '0', STR_PAD_LEFT) . '.xml', $xml);
                    $mail->AddAttachment('fatture_inviate/IT02292500648_' . str_pad(base_convert($testata->id,10,36), 4, '0', STR_PAD_LEFT) . '.xml');
                    $mail->send();



                }
            }
        }

        if(isset($dati['elimina'])){

            DB::table('fatture_testata')->where('id',$dati['id'])->delete();
            DB::table('fatture_righe')->where('id_testata',$dati['id'])->delete();
            DB::update('update preventivi_testata set id_fattura_testata = 0 where id_fattura_testata ='.$dati['id']);
            return Redirect::to('admin/fatture/'.$anno);
        }

        if(isset($dati['elimina_allegato'])){
            DB::update('update fatture_testata set allegato = NULL,nome_allegato= NULL where id='.$dati['id']);
            return Redirect::to('admin/fatture/'.$anno);
        }

        if(isset($dati['elimina_allegato2'])){
            DB::update('update fatture_testata set allegato2 = NULL,nome_allegato2= NULL where id='.$dati['id']);
            return Redirect::to('admin/fatture/'.$anno);
        }

        if(isset($dati['aggiungi_riga'])){
            unset($dati['aggiungi_riga']);
            if(isset($dati['id_pratica'])) unset($dati['id_pratica']);
            $dati['imposta'] = $dati['pt'] - ($dati['pt']/(1+($dati['iva']/100)));
            $dati['imponibile'] = $dati['pt']-$dati['imposta'];
            $dati['id_utente'] = $utente->id;

            DB::table('fatture_righe')->insert($dati);
            DB::update('update fatture_testata set imponibile = (SELECT ifnull(SUM(imponibile),0) as imponibile from fatture_righe where id_testata = '.$dati['id_testata'].'), imposta = (SELECT ifnull(SUM(imposta),0) as imposta  from fatture_righe where id_testata = '.$dati['id_testata'].'),totale = imponibile+imposta   where id='.$dati['id_testata']);
            return Redirect::to('admin/fatture/'.$anno);
        }

        if(isset($dati['modifica_riga'])){
            unset($dati['modifica_riga']);
            if(isset($dati['id_pratica'])) unset($dati['id_pratica']);
            $dati['imposta'] = $dati['pt'] - ($dati['pt']/(1+($dati['iva']/100)));
            $dati['imponibile'] = $dati['pt']-$dati['imposta'];
            $dati['id_utente'] = $utente->id;

            DB::table('fatture_righe')->where('id',$dati['id'])->update($dati);
            DB::update('update fatture_testata set imponibile = (SELECT ifnull(SUM(imponibile),0) as imponibile from fatture_righe where id_testata = '.$dati['id_testata'].'), imposta = (SELECT ifnull(SUM(imposta),0) as imposta  from fatture_righe where id_testata = '.$dati['id_testata'].'),totale = imponibile+imposta   where id='.$dati['id_testata']);
            return Redirect::to('admin/fatture/'.$anno);
        }

        if(isset($dati['elimina_riga'])){
            unset($dati['elimina_riga']);
            if(isset($dati['id_pratica'])) unset($dati['id_pratica']);
            DB::table('fatture_righe')->where('id',$dati['id'])->delete();
            DB::update('update fatture_testata set imponibile = (SELECT ifnull(SUM(imponibile),0) as imponibile from fatture_righe where id_testata = '.$dati['id_testata'].'), imposta = (SELECT ifnull(SUM(imposta),0) as imposta  from fatture_righe where id_testata = '.$dati['id_testata'].'),totale = imponibile+imposta   where id='.$dati['id_testata']);
            return Redirect::to('admin/fatture/'.$anno);
        }

        $num_fattura = DB::select('SELECT IFNULL(max(numero)+1,1) as numero from fatture_testata where anno = YEAR(NOW())')[0]->numero;
        $fatture = DB::select('SELECT * from fatture_testata order by numero DESC, anno DESC, data DESC');
        foreach($fatture as $f){
            $f->righe = DB::select('SELECT * from fatture_righe where id_testata = '.$f->id);
        }
        $clienti = DB::table('clienti')->where('id_azienda', $utente->id_azienda)->get();


        $page = 'fatture';
        return View::make('default.fatture',compact('page', 'utente','fatture','num_fattura','anno', 'clienti'));
    }*/

    public function scarica_xml($id)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Recupero della testata e delle righe
        $testata = DB::select('SELECT * FROM dotes WHERE id = ? AND id_azienda = ?', [$id, $utente->id_azienda]);
        $righe = DB::select('SELECT * FROM dorig WHERE id_dotes = ? AND id_azienda = ?', [$id, $utente->id_azienda]);
        $dati_riepilogo = DB::select('SELECT sum(imponibile) imponibile, sum(imposta) imposta, iva, codice_iva, rif_normativo FROM dorig WHERE id_dotes = ? GROUP BY codice_iva', [$id]);

        // Recupero dei dati dell'azienda
        $azienda = DB::table('aziende')->where('id', $utente->id_azienda)->first();
        if (sizeof($righe) > 0) {
            $testata = $testata[0];
            $xml = View::make('fatturazione.xml', compact('testata', 'righe', 'dati_riepilogo', 'azienda'));

            $response = Response::create($xml, 200);
            $response->header('Content-Type', 'text/xml');
            $response->header('Cache-Control', 'public');
            $response->header('Content-Description', 'File Transfer');
            $response->header('Content-Disposition', 'attachment; filename=IT08949141215_' . str_pad(base_convert($testata->id, 10, 36), 5, '0', STR_PAD_LEFT) . '.xml');
            $response->header('Content-Transfer-Encoding', 'binary');
            return $response;
        }
    }

    public function visualizza_xml($id)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Recupero della testata e delle righe
        $testata = DB::select('SELECT * FROM dotes WHERE id = ? AND id_azienda = ?', [$id, $utente->id_azienda]);
        $righe = DB::select('SELECT * FROM dorig WHERE id_dotes = ? AND id_azienda = ?', [$id, $utente->id_azienda]);
        $dati_riepilogo = DB::select('SELECT sum(imponibile) imponibile, sum(imposta) imposta, iva, codice_iva, rif_normativo FROM dorig WHERE id_dotes = ? GROUP BY codice_iva', [$id]);

        // Recupero dei dati dell'azienda
        $azienda = DB::table('aziende')->where('id', $utente->id_azienda)->first();

        if (sizeof($righe) > 0) {
            $testata = $testata[0];
            $xml = View::make('fatturazione.xml_stampa', compact('testata', 'righe', 'dati_riepilogo', 'azienda'));

            $response = Response::create($xml, 200);
            $response->header('Content-Type', 'text/xml');
            return $response;
        }
    }


    /*public function dettaglio_fattura($id)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Recupero della testata e delle righe
        $testata = DB::select('SELECT * FROM dotes WHERE id = ? AND id_azienda = ?', [$id, $utente->id_azienda]);
        $righe = DB::select('SELECT * FROM dorig WHERE id_dotes = ? AND id_azienda = ?', [$id, $utente->id_azienda]);
        $dati_riepilogo = DB::select('SELECT sum(imponibile) imponibile, sum(imposta) imposta, iva, codice_iva, rif_normativo FROM dorig WHERE id_dotes = ? GROUP BY codice_iva', [$id]);

        // Recupero dei dati dell'azienda
        $azienda = DB::table('aziende')->where('id', $utente->id_azienda)->first();

        // Recupero dei pagamenti ricevuti per la fattura
        $pagamenti = DB::select('SELECT * FROM pagamenti WHERE id_fattura = ?', [$id]);

        if (sizeof($righe) > 0) {
            $testata = $testata[0];
            return view('default.dettaglio_fattura', compact('testata', 'righe', 'dati_riepilogo', 'azienda', 'pagamenti'));
        } else {
            return redirect()->back()->with('error', 'Fattura non trovata.');
        }
    }*/





    public function commesse_in_lavorazione(Request $request){

        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();


        if(isset($dati['aggiungi_lavoro'])){
            unset($dati['aggiungi_lavoro']);

            $dati['stato'] = 1;
            $dati['created_at'] = now();


            DB::table('lavori')->insert($dati);
            return Redirect::to('admin/commesse_in_lavorazione');
        }

        if(isset($dati['chiudi_lavoro'])){
            unset($dati['chiudi_lavoro']);



            DB::table('lavori')->where('id', $dati['id_chiusura'])->update(['stato' => 0, 'data_chiusura' => now()]);
            return Redirect::to('admin/commesse_in_lavorazione');
        }

        if(isset($dati['archivia_lavoro'])){
            unset($dati['archivia_lavoro']);



            DB::table('lavori')->where('id', $dati['id_archiviazione'])->update(['stato' => 2, 'data_archiviazione' => now()]);
            return Redirect::to('admin/commesse_in_lavorazione');
        }


        if(isset($dati['aggiungi_task'])){
            unset($dati['aggiungi_task']);

            $dati['stato'] = 1;
            $dati['created_at'] = now();

            DB::table('task')->insert($dati);
            return Redirect::to('admin/commesse_in_lavorazione');
        }

        if(isset($dati['modifica_task'])){
            unset($dati['modifica_task']);


            DB::table('task')->where('id', $dati['id'])->update($dati);

            return Redirect::to('admin/commesse_in_lavorazione');
        }

        if(isset($dati['elimina_task'])){
            unset($dati['elimina_task']);


            DB::table('task')->where('id', $dati['id'])->delete();

            return Redirect::to('admin/commesse_in_lavorazione');
        }

        if(isset($dati['assegna_task'])){
            unset($dati['assegna_task']);


            DB::table('task')->where('id', $dati['id'])->update(['id_dipendente' => $dati['id_dipendente']]);
            return Redirect::to('admin/commesse_in_lavorazione');
        }

        if(isset($dati['chiudi_task'])){
            unset($dati['chiudi_task']);


            DB::table('task')->where('id', $dati['id_chiusura_task'])->update(['stato' => 0 , 'data_chiusura' => now(), 'note_chiusura' => $dati['note']]);


            return Redirect::to('admin/commesse_in_lavorazione');
        }

        if(isset($dati['sospendi_task'])){
            unset($dati['sospendi_task']);


            DB::table('task')->where('id', $dati['id_sospensione_task'])->update(['stato' => 2 , 'data_sospensione' => now(), 'note_sospensione' => $dati['note']]);


            return Redirect::to('admin/commesse_in_lavorazione');
        }

        if(isset($dati['riapri_task'])){
            unset($dati['riapri_task']);


            DB::table('task')->where('id', $dati['id_task'])->update(['data_sospensione' => null, 'note_sospensione' => null, 'stato' => 1]);

            return Redirect::to('admin/commesse_in_lavorazione');
        }

        $page = 'commesse_in_lavorazione';
        $clienti = DB::table('utenti')->where('id_tipologia', '<', 3)->where('id_tipologia', '>', 0)->orderBy('ragione_sociale', 'asc')->get();
        $dipendenti = DB::table('utenti')->where('id_tipologia', 3)->get();
        $lavori = DB::table('lavori')->where('stato', 1)->get();

        return View::make('default.commesse_in_lavorazione', compact('page', 'lavori', 'utente', 'clienti', 'dipendenti'));

    }

    public function task_assegnati(Request $request){

        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();



        if(isset($dati['chiudi_task'])){
            unset($dati['chiudi_task']);


            DB::table('task')->where('id', $dati['id_chiusura_task'])->update(['stato' => 0 , 'data_chiusura' => now(), 'note_chiusura' => $dati['note']]);


            return Redirect::to('admin/task_assegnati');
        }

        if(isset($dati['sospendi_task'])){
            unset($dati['sospendi_task']);


            DB::table('task')->where('id', $dati['id_sospensione_task'])->update(['stato' => 2 , 'data_sospensione' => now(), 'note_sospensione' => $dati['note']]);


            return Redirect::to('admin/task_assegnati');
        }

        if(isset($dati['aggiungi_task'])){
            unset($dati['aggiungi_task']);

            $dati['stato'] = 1;
            $dati['created_at'] = now();

            DB::table('task')->insert($dati);
            return Redirect::to('admin/task_assegnati');
        }

        if(isset($dati['modifica_task'])){
            unset($dati['modifica_task']);


            DB::table('task')->where('id', $dati['id'])->update($dati);

            return Redirect::to('admin/task_assegnati');
        }

        if(isset($dati['elimina_task'])){
            unset($dati['elimina_task']);


            DB::table('task')->where('id', $dati['id'])->delete();

            return Redirect::to('admin/task_assegnati');
        }

        if(isset($dati['assegna_task'])){
            unset($dati['assegna_task']);


            DB::table('task')->where('id', $dati['id'])->update(['id_dipendente' => $dati['id_dipendente']]);
            return Redirect::to('admin/task_assegnati');
        }


        $page = 'task_assegnati';
        $clienti = DB::table('utenti')->where('id_tipologia', '<', 3)->where('id_tipologia', '>', 0)->orderBy('ragione_sociale', 'asc')->get();
        $userId = $utente->id;
        $lavori = DB::table('lavori')
            ->where('stato', 1)
            ->whereExists(function ($query) use ($userId) {
                $query->select(DB::raw(1))
                    ->from('task')
                    ->whereRaw('task.id_lavoro = lavori.id')
                    ->where('task.id_dipendente', $userId)
                    ->where('task.stato', 1);
            })
            ->get();

        return View::make('default.task_assegnati', compact('page', 'lavori', 'utente', 'clienti'));

    }

    public function task_sospesi(Request $request){

        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();


        if(isset($dati['riapri_task'])){
            unset($dati['riapri_task']);


            DB::table('task')->where('id', $dati['id_task'])->update(['data_sospensione' => null, 'note_sospensione' => null, 'stato' => 1]);

            return Redirect::to('admin/task_sospesi');
        }

        if(isset($dati['modifica_task'])){
            unset($dati['modifica_task']);


            DB::table('task')->where('id', $dati['id'])->update($dati);

            return Redirect::to('admin/task_sospesi');
        }

        if(isset($dati['elimina_task'])){
            unset($dati['elimina_task']);


            DB::table('task')->where('id', $dati['id'])->delete();

            return Redirect::to('admin/task_sospesi');
        }


        $page = 'task_sospesi';
        $clienti = DB::table('utenti')->where('id_tipologia', '<', 3)->where('id_tipologia', '>', 0)->orderBy('ragione_sociale', 'asc')->get();
        $userId = $utente->id;
        $lavori = DB::table('lavori')
            ->where('stato', 1)
            ->whereExists(function ($query) use ($userId) {
                $query->select(DB::raw(1))
                    ->from('task')
                    ->whereRaw('task.id_lavoro = lavori.id')
                    ->where('task.id_dipendente', $userId)
                    ->where('task.stato', 2);
            })
            ->get();

        return View::make('default.task_sospesi', compact('page', 'lavori', 'utente', 'clienti'));

    }

    public function task_chiusi(Request $request){

        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();


        if(isset($dati['riapri_task'])){
            unset($dati['riapri_task']);


            DB::table('task')->where('id', $dati['id_task'])->update(['data_chiusura' => null, 'note_chiusura' => null, 'stato' => 1]);

            return Redirect::to('admin/task_chiusi');
        }

        if(isset($dati['modifica_task'])){
            unset($dati['modifica_task']);


            DB::table('task')->where('id', $dati['id'])->update($dati);

            return Redirect::to('admin/task_chiusi');
        }

        if(isset($dati['elimina_task'])){
            unset($dati['elimina_task']);


            DB::table('task')->where('id', $dati['id'])->delete();

            return Redirect::to('admin/task_chiusi');
        }


        $page = 'task_chiusi';
        $clienti = DB::table('utenti')->where('id_tipologia', '<', 3)->where('id_tipologia', '>', 0)->orderBy('ragione_sociale', 'asc')->get();
        $userId = $utente->id;
        $lavori = DB::table('lavori')
            ->where('stato', 1)
            ->whereExists(function ($query) use ($userId) {
                $query->select(DB::raw(1))
                    ->from('task')
                    ->whereRaw('task.id_lavoro = lavori.id')
                    ->where('task.id_dipendente', $userId)
                    ->where('task.stato', 0);
            })
            ->get();

        return View::make('default.task_chiusi', compact('page', 'lavori', 'utente', 'clienti'));

    }

    public function commesse_chiuse(Request $request){

        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();



        $page = 'commesse_chiuse';
        $clienti = DB::table('utenti')->where('id_tipologia', '<', 3)->where('id_tipologia', '>', 0)->orderBy('ragione_sociale', 'asc')->get();
        $dipendenti = DB::table('utenti')->where('id_tipologia', 3)->get();
        $lavori = DB::table('lavori')->where('stato', 0)->get();

        return View::make('default.commesse_chiuse', compact('page', 'lavori', 'utente', 'clienti', 'dipendenti'));

    }

    public function commesse_archiviate(Request $request){

        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();


        if(isset($dati['riapri_commessa'])){
            unset($dati['riapri_commessa']);

            DB::table('lavori')->where('id', $dati['id_riapertura_commessa'])->update(['stato' => 1, 'data_archiviazione' => null]);

            return Redirect::to('admin/commesse_archiviate');
        }

        $page = 'commesse_archiviate';
        $clienti = DB::table('utenti')->where('id_tipologia', '<', 3)->where('id_tipologia', '>', 0)->orderBy('ragione_sociale', 'asc')->get();
        $dipendenti = DB::table('utenti')->where('id_tipologia', 3)->get();
        $lavori = DB::table('lavori')->where('stato', 2)->get();

        return View::make('default.commesse_archiviate', compact('page', 'lavori', 'utente', 'clienti', 'dipendenti'));

    }

    public function bandi(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if(isset($dati['aggiungi'])){
            unset($dati['aggiungi']);

            if($_FILES['decreto']['name'] != ''){

                $pathinfo = pathinfo($_FILES['decreto']['name']);
                $nome = Str::random(20);
                $target = 'allegati_bandi_decreti/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['decreto']['tmp_name'], $target);
                $dati['decreto'] = $target;
            }



            DB::table('bandi')->insert($dati);
            return Redirect::to('admin/bandi');
        }

        if(isset($dati['modifica'])){
            unset($dati['modifica']);

            // Creiamo un array di numeri da 1 a 10
            $numbersArray = range(1, $dati['n_preventivi']);

            // Convertiamo l'array in una stringa separata da virgole
            $numberString = implode(',', $numbersArray);

            $dati['n_preventivi'] = $numberString;


            if($_FILES['allegati']['name'] != ''){

                $path_da_eliminare = DB::table('bandi')->where('id',$dati['id'])->first();


                if (file_exists($path_da_eliminare->allegati)) {
                    unlink($path_da_eliminare->allegati);
                }

                $pathinfo = pathinfo($_FILES['allegati']['name']);
                $nome = Str::random(20);
                $target = 'allegati_bandi/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['allegati']['tmp_name'], $target);
                $dati['allegati'] = $target;
            }

            if($_FILES['decreto']['name'] != ''){

                $path_da_eliminare = DB::table('bandi')->where('id',$dati['id'])->first();


                if (file_exists($path_da_eliminare->decreto)) {
                    unlink($path_da_eliminare->decreto);
                }

                $pathinfo = pathinfo($_FILES['decreto']['name']);
                $nome = Str::random(20);
                $target = 'allegati_bandi_decreti/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['decreto']['tmp_name'], $target);
                $dati['decreto'] = $target;
            }

            if($_FILES['immagine_bando']['name'] != ''){

                $path_da_eliminare = DB::table('bandi')->where('id',$dati['id'])->first();


                if (file_exists($path_da_eliminare->immagine_bando)) {
                    unlink($path_da_eliminare->immagine_bando);
                }

                $pathinfo = pathinfo($_FILES['immagine_bando']['name']);
                $nome = Str::random(20);
                $target = 'allegati_bandi_immagine_bando/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['immagine_bando']['tmp_name'], $target);
                $dati['immagine_bando'] = $target;
            }


            DB::table('bandi')->where('id',$dati['id'])->update($dati);
            return Redirect::to('admin/bandi');
        }

        if(isset($dati['aggiungi_utenti'])){
            unset($dati['aggiungi_utenti']);

            if(isset($dati['array_utenti'])){
                $stringa_utenti = implode(',', $dati['array_utenti']);
            } else{
                $stringa_utenti = '';
            }


            DB::table('bandi')->where('id',$dati['id'])->update(['id_clienti' => $stringa_utenti]);
            return Redirect::to('admin/bandi');
        }

        if(isset($dati['aggiungi_allegati'])){
            unset($dati['aggiungi_allegati']);

            if(isset($dati['array_allegati'])){
                $stringa_allegati = implode(',', $dati['array_allegati']);
            } else{
                $stringa_allegati = '';
            }


            DB::table('bandi')->where('id',$dati['id'])->update(['id_allegati' => $stringa_allegati]);
            return Redirect::to('admin/bandi');
        }

        if(isset($dati['invia_mail'])){
            unset($dati['invia_mail']);

            $array_utenti_request = $dati['array_clienti_per_mail'];

            $utenti_per_mail = DB::table('utenti')->whereIn('id', $array_utenti_request)->get();

            $bando_per_mail = DB::table('bandi')->where('id', $dati['info_bando'])->first();
            $array_bando_mail = explode(',', $bando_per_mail->id_allegati);

            $bando_allegati_per_mail = DB::table('bandi_allegati')->whereIn('id', $array_bando_mail)->get();


            foreach ($utenti_per_mail as $utente_m){

                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'p.picariello@ingenia.cloud';
                $mail->Password = 'hugonbeepqcfqldx';
                $mail->SMTPSecure = 'tls';
                $mail->CharSet = 'utf-8';
                $mail->Port = 587;


                $mail->setFrom('p.picariello@ingenia.cloud', 'CRM Ingenia SRL');
                $mail->addAddress($utente_m->email);
                /*$mail->addBCC('giovinefabio@gmail.com');*/

                $mail->isHTML(true);
                $mail->Subject = 'Ciao ' . $utente_m->nome.' '.$utente_m->cognome. ' Ingenia ti dà il benvenuto al Bando ' . $bando_per_mail->titolo;

                $documenti = '';
                foreach ($bando_allegati_per_mail as $bapm) {
                    $file_caricato = DB::table('bandi_allegati_utenti')->where('id_bando', $bando_per_mail->id)->where('id_allegato', $bapm->id)->where('id_cliente', $utente_m->id)->first();
                    if (!$file_caricato){
                        $documenti .= '<li>' . $bapm->descrizione . '</li>';
                    }
                }


                $mail->Body = '
                                Benvenuto '.$utente_m->nome.' '.$utente_m->cognome.', dopo una attenta verifica abbiamo verificato che hai 
                                tutti i requisiti per partecipare al bando: <b>'.$bando_per_mail->titolo.'</b> <br><br>
                                Per poter continuare dovrai fornirci i seguenti documenti:<br>
                                <ul>'.$documenti.'</ul><br>
                                
                                Di seguito il link per caricare i documenti restanti nella nostra area:<br>
                                '.asset('bandi/'. $utente_m->token_utente_per_bando . '/' . $bando_per_mail->token_bando).'<br><br><br>


                                <img style="width:300px;" src="https://crm.ingenia.cloud/logo.png"><br>
                                ';

                $mail->send();

                $mail_inviate = DB::table('bandi')->where('id', $dati['info_bando'])->value('mail_inviate');

                // Verifica se l'ID è già presente nella stringa
                if ($mail_inviate === null || !in_array($utente_m->id, explode(',', $mail_inviate))) {
                    // Se l'ID non è presente, aggiorna la colonna aggiungendo il nuovo ID
                    DB::table('bandi')->where('id', $dati['info_bando'])->update([
                        'mail_inviate' => DB::raw("CONCAT(IFNULL(mail_inviate, ''), IF(mail_inviate IS NOT NULL AND mail_inviate != '', ',', ''), '" . $utente_m->id . "')")
                    ]);
                }

            }

            return Redirect::to('admin/bandi');
        }

        if(isset($dati['archivia'])){
            unset($dati['archivia']);

            DB::table('bandi')->where('id',$dati['id'])->update(['archiviato' => 1]);
            return Redirect::to('admin/bandi');
        }

        $page = 'bandi';
        $bandi = DB::table('bandi')->where('id_utente', $utente->id)->where('archiviato', 0)->get();

        return View::make('default.bandi', compact('page', 'utente','bandi'));
    }


    public function bandi_old(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if(isset($dati['aggiungi'])){
            unset($dati['aggiungi']);
            $dati['id_utente'] = $utente->id;

            $caratteri_permessi = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $lunghezza_stringa = 20;
            $stringa_random = '';

            for ($i = 0; $i < $lunghezza_stringa; $i++) {
                $stringa_random .= $caratteri_permessi[rand(0, strlen($caratteri_permessi) - 1)];
            }

            // Creiamo un array di numeri da 1 a 10
            $numbersArray = range(1, $dati['n_preventivi']);

            // Convertiamo l'array in una stringa separata da virgole
            $numberString = implode(',', $numbersArray);

            $dati['n_preventivi'] = $numberString;

            $dati['token_bando'] = $stringa_random;

            if($_FILES['allegati']['name'] != ''){

                $pathinfo = pathinfo($_FILES['allegati']['name']);
                $nome = Str::random(20);
                $target = 'allegati_bandi/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['allegati']['tmp_name'], $target);
                $dati['allegati'] = $target;
            }

            if($_FILES['decreto']['name'] != ''){

                $pathinfo = pathinfo($_FILES['decreto']['name']);
                $nome = Str::random(20);
                $target = 'allegati_bandi_decreti/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['decreto']['tmp_name'], $target);
                $dati['decreto'] = $target;
            }

            if($_FILES['immagine_bando']['name'] != ''){

                $pathinfo = pathinfo($_FILES['immagine_bando']['name']);
                $nome = Str::random(20);
                $target = 'allegati_bandi_immagine_bando/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['immagine_bando']['tmp_name'], $target);
                $dati['immagine_bando'] = $target;
            }


            DB::table('bandi')->insert($dati);
            return Redirect::to('admin/bandi');
        }

        if(isset($dati['modifica'])){
            unset($dati['modifica']);

            // Creiamo un array di numeri da 1 a 10
            $numbersArray = range(1, $dati['n_preventivi']);

            // Convertiamo l'array in una stringa separata da virgole
            $numberString = implode(',', $numbersArray);

            $dati['n_preventivi'] = $numberString;


            if($_FILES['allegati']['name'] != ''){

                $path_da_eliminare = DB::table('bandi')->where('id',$dati['id'])->first();


                if (file_exists($path_da_eliminare->allegati)) {
                    unlink($path_da_eliminare->allegati);
                }

                $pathinfo = pathinfo($_FILES['allegati']['name']);
                $nome = Str::random(20);
                $target = 'allegati_bandi/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['allegati']['tmp_name'], $target);
                $dati['allegati'] = $target;
            }

            if($_FILES['decreto']['name'] != ''){

                $path_da_eliminare = DB::table('bandi')->where('id',$dati['id'])->first();


                if (file_exists($path_da_eliminare->decreto)) {
                    unlink($path_da_eliminare->decreto);
                }

                $pathinfo = pathinfo($_FILES['decreto']['name']);
                $nome = Str::random(20);
                $target = 'allegati_bandi_decreti/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['decreto']['tmp_name'], $target);
                $dati['decreto'] = $target;
            }

            if($_FILES['immagine_bando']['name'] != ''){

                $path_da_eliminare = DB::table('bandi')->where('id',$dati['id'])->first();


                if (file_exists($path_da_eliminare->immagine_bando)) {
                    unlink($path_da_eliminare->immagine_bando);
                }

                $pathinfo = pathinfo($_FILES['immagine_bando']['name']);
                $nome = Str::random(20);
                $target = 'allegati_bandi_immagine_bando/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['immagine_bando']['tmp_name'], $target);
                $dati['immagine_bando'] = $target;
            }


            DB::table('bandi')->where('id',$dati['id'])->update($dati);
            return Redirect::to('admin/bandi');
        }

        if(isset($dati['aggiungi_utenti'])){
            unset($dati['aggiungi_utenti']);

            if(isset($dati['array_utenti'])){
                $stringa_utenti = implode(',', $dati['array_utenti']);
            } else{
                $stringa_utenti = '';
            }


            DB::table('bandi')->where('id',$dati['id'])->update(['id_clienti' => $stringa_utenti]);
            return Redirect::to('admin/bandi');
        }

        if(isset($dati['aggiungi_allegati'])){
            unset($dati['aggiungi_allegati']);

            if(isset($dati['array_allegati'])){
                $stringa_allegati = implode(',', $dati['array_allegati']);
            } else{
                $stringa_allegati = '';
            }


            DB::table('bandi')->where('id',$dati['id'])->update(['id_allegati' => $stringa_allegati]);
            return Redirect::to('admin/bandi');
        }

        if(isset($dati['invia_mail'])){
            unset($dati['invia_mail']);

            $array_utenti_request = $dati['array_clienti_per_mail'];

            $utenti_per_mail = DB::table('utenti')->whereIn('id', $array_utenti_request)->get();

            $bando_per_mail = DB::table('bandi')->where('id', $dati['info_bando'])->first();
            $array_bando_mail = explode(',', $bando_per_mail->id_allegati);

            $bando_allegati_per_mail = DB::table('bandi_allegati')->whereIn('id', $array_bando_mail)->get();


            foreach ($utenti_per_mail as $utente_m){

                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'p.picariello@ingenia.cloud';
                $mail->Password = 'hugonbeepqcfqldx';
                $mail->SMTPSecure = 'tls';
                $mail->CharSet = 'utf-8';
                $mail->Port = 587;


                $mail->setFrom('p.picariello@ingenia.cloud', 'CRM Ingenia SRL');
                $mail->addAddress($utente_m->email);
                /*$mail->addBCC('giovinefabio@gmail.com');*/

                $mail->isHTML(true);
                $mail->Subject = 'Ciao ' . $utente_m->nome.' '.$utente_m->cognome. ' Ingenia ti dà il benvenuto al Bando ' . $bando_per_mail->titolo;

                $documenti = '';
                foreach ($bando_allegati_per_mail as $bapm) {
                    $file_caricato = DB::table('bandi_allegati_utenti')->where('id_bando', $bando_per_mail->id)->where('id_allegato', $bapm->id)->where('id_cliente', $utente_m->id)->first();
                    if (!$file_caricato){
                        $documenti .= '<li>' . $bapm->descrizione . '</li>';
                    }
                }


                $mail->Body = '
                                Benvenuto '.$utente_m->nome.' '.$utente_m->cognome.', dopo una attenta verifica abbiamo verificato che hai 
                                tutti i requisiti per partecipare al bando: <b>'.$bando_per_mail->titolo.'</b> <br><br>
                                Per poter continuare dovrai fornirci i seguenti documenti:<br>
                                <ul>'.$documenti.'</ul><br>
                                
                                Di seguito il link per caricare i documenti restanti nella nostra area:<br>
                                '.asset('bandi/'. $utente_m->token_utente_per_bando . '/' . $bando_per_mail->token_bando).'<br><br><br>


                                <img style="width:300px;" src="https://crm.ingenia.cloud/logo.png"><br>
                                ';

                $mail->send();

                $mail_inviate = DB::table('bandi')->where('id', $dati['info_bando'])->value('mail_inviate');

                // Verifica se l'ID è già presente nella stringa
                if ($mail_inviate === null || !in_array($utente_m->id, explode(',', $mail_inviate))) {
                    // Se l'ID non è presente, aggiorna la colonna aggiungendo il nuovo ID
                    DB::table('bandi')->where('id', $dati['info_bando'])->update([
                        'mail_inviate' => DB::raw("CONCAT(IFNULL(mail_inviate, ''), IF(mail_inviate IS NOT NULL AND mail_inviate != '', ',', ''), '" . $utente_m->id . "')")
                    ]);
                }

            }

            return Redirect::to('admin/bandi');
        }

        if(isset($dati['archivia'])){
            unset($dati['archivia']);

            DB::table('bandi')->where('id',$dati['id'])->update(['archiviato' => 1]);
            return Redirect::to('admin/bandi');
        }

        $page = 'bandi';
        $bandi = DB::table('bandi')->where('id_utente', $utente->id)->where('archiviato', 0)->get();

        return View::make('default.bandi', compact('page', 'utente','bandi'));
    }

    public function bandi_archiviati(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if(isset($dati['estrai'])){
            unset($dati['estrai']);

            DB::table('bandi')->where('id',$dati['id'])->update(['archiviato' => 0]);
            return Redirect::to('admin/bandi_archiviati');
        }

        $page = 'bandi_archiviati';
        $bandi_archiviati = DB::table('bandi')->where('id_utente', $utente->id)->where('archiviato', 1)->get();

        return View::make('default.bandi_archiviati', compact('page', 'utente','bandi_archiviati'));
    }

    public function bandi_allegati(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if(isset($dati['aggiungi'])){
            unset($dati['aggiungi']);
            $dati['id_utente'] = $utente->id;

            if(isset($dati['valore_si_no'])){
                $dati['valore_si_no'] = 1;
            } else{
                $dati['valore_si_no'] = 0;
            }

            if(isset($dati['formati'])){
                $stringa_formati = implode(',', $dati['formati']);
                $dati['formati'] = $stringa_formati;
            } else{
                $dati['formati'] = '';
            }

            DB::table('bandi_allegati')->insert($dati);
            return Redirect::to('admin/bandi_allegati');
        }

        if(isset($dati['modifica'])){
            unset($dati['modifica']);

            if(isset($dati['valore_si_no'])){
                $dati['valore_si_no'] = 1;
            } else{
                $dati['valore_si_no'] = 0;
            }

            if(isset($dati['formati'])){
                $stringa_formati = implode(',', $dati['formati']);
                $dati['formati'] = $stringa_formati;
            } else{
                $dati['formati'] = '';
            }


            DB::table('bandi_allegati')->where('id',$dati['id'])->update($dati);
            return Redirect::to('admin/bandi_allegati');
        }

        if(isset($dati['elimina'])){
            unset($dati['elimina']);
            DB::table('bandi_allegati')->where('id',$dati['id'])->delete();
            return Redirect::to('admin/bandi_allegati');
        }

        $page = 'bandi_allegati';
        $bandi_allegati = DB::table('bandi_allegati')->where('id_utente', $utente->id)->get();

        return View::make('default.bandi_allegati', compact('page', 'utente','bandi_allegati'));
    }

    public function invio_dati(Request $request, $token_utente, $token_bando){

        $dati = $request->all();

        $bando = DB::table('bandi')->where('token_bando', $token_bando)->first();
        $utente = DB::table('utenti')->where('token_utente_per_bando', $token_utente)->first();

        $array_allegati_bandi = explode(',', $bando->id_allegati);

        $bandi_allegati = DB::table('bandi_allegati')->whereIn('id', $array_allegati_bandi)->get();


        if(isset($dati['invio_dati_effettuato'])){
            unset($dati['invio_dati_effettuato']);

            if($_FILES['path_allegato']['name'] != ''){

                $ragione_sociale = str_replace(' ', '_', strtolower($utente->ragione_sociale));
                $bando_directory = str_replace(' ', '_', strtolower($bando->titolo));
                $targetDirectory = 'allegati_clienti/' . $ragione_sociale . '/' . $bando_directory . '/';

                // Creazione della cartella se non esiste
                if (!file_exists($targetDirectory)) {
                    mkdir($targetDirectory, 0777, true);
                }

                $pathinfo = pathinfo($_FILES['path_allegato']['name']);
                $nome = Str::random(20);
                $target = $targetDirectory .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['path_allegato']['tmp_name'], $target);
                $dati['path_allegato'] = $target;
                $dati['id_cliente'] = $utente->id;
                $dati['id_bando'] = $bando->id;
                $dati['preventivo'] = 0;


                DB::table('bandi_allegati_utenti')->insert($dati);


            }

            return Redirect::to('bandi/'. $token_utente . '/' . $token_bando);
        }

        if(isset($dati['invio_preventivo'])){
            unset($dati['invio_preventivo']);

            if($_FILES['preventivo']['name'] != ''){


                $ragione_sociale = str_replace(' ', '_', strtolower($utente->ragione_sociale));
                $bando_directory = str_replace(' ', '_', strtolower($bando->titolo));
                $targetDirectory = 'allegati_clienti/' . $ragione_sociale . '/' . $bando_directory . '/preventivi/';

                // Creazione della cartella se non esiste
                if (!file_exists($targetDirectory)) {
                    mkdir($targetDirectory, 0777, true);
                }

                $pathinfo = pathinfo($_FILES['preventivo']['name']);
                $nome = Str::random(20);
                $target = $targetDirectory .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['preventivo']['tmp_name'], $target);
                $dati['path_allegato'] = $target;
                $dati['id_cliente'] = $utente->id;
                $dati['id_bando'] = $bando->id;
                $dati['preventivo'] = 1;


                DB::table('bandi_allegati_utenti')->insert($dati);


            }

            return Redirect::to('bandi/'. $token_utente . '/' . $token_bando);
        }

        if(isset($dati['modifica_valore_allegato'])){
            unset($dati['modifica_valore_allegato']);


            DB::table('bandi_allegati_utenti')->where('id', $dati['id_modifica_valore'])->update(['valore' => $dati['valore']]);


            return Redirect::to('bandi/'. $token_utente . '/' . $token_bando);
        }

        if(isset($dati['modifica_valore_preventivo'])){
            unset($dati['modifica_valore_allegato']);

            DB::table('bandi_allegati_utenti')->where('id', $dati['id_modifica_valore_preventivo'])->update(['valore' => $dati['valore']]);


            return Redirect::to('bandi/'. $token_utente . '/' . $token_bando);
        }


        if(isset($dati['elimina_file_caricato'])){
            unset($dati['elimina_file_caricato']);

            $path_da_eliminare = DB::table('bandi_allegati_utenti')->where('id', $dati['id_eliminazione_allegato'])->first();

            if (file_exists($path_da_eliminare->path_allegato)) {
                unlink($path_da_eliminare->path_allegato);
            }

            DB::table('bandi_allegati_utenti')->where('id', $dati['id_eliminazione_allegato'])->delete();


            return Redirect::to('bandi/'. $token_utente . '/' . $token_bando);
        }

        if(isset($dati['elimina_preventivo_caricato'])){
            unset($dati['elimina_preventivo_caricato']);

            $path_da_eliminare = DB::table('bandi_allegati_utenti')->where('id', $dati['id_eliminazione_preventivo'])->first();

            if (file_exists($path_da_eliminare->path_allegato)) {
                unlink($path_da_eliminare->path_allegato);
            }

            DB::table('bandi_allegati_utenti')->where('id', $dati['id_eliminazione_preventivo'])->delete();


            return Redirect::to('bandi/'. $token_utente . '/' . $token_bando);
        }

        return View::make('default.ajax.invio_dati', compact('utente', 'bandi_allegati', 'bando'));
    }

    public function form_frc(Request $request){

        $dati = $request->all();

        if(isset($dati['invio_dati'])){
            unset($dati['invio_dati']);


            return Redirect::to('bandi/form_frc');
        }


        return View::make('frontend.form_frc');
    }


    public function dettaglio_utente($id,Request $request){

        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();


        if(isset($dati['aggiungi_preventivo'])){
            unset($dati['aggiungi_preventivo']);
            $dati['id_utente'] = $id;

            if($_FILES['allegato']['name'] != ''){

                $pathinfo = pathinfo($_FILES['allegato']['name']);
                $nome = Str::random(20);
                $target = 'allegati_preventivi/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['allegato']['tmp_name'], $target);
                $dati['allegato'] = $target;
            }

            DB::table('preventivi')->insert($dati);
            return Redirect::to('admin/dettaglio_utente/'.$id);
        }

        if(isset($dati['modifica_preventivo'])){
            unset($dati['modifica_preventivo']);
            DB::table('preventivi')->where('id',$dati['id'])->update($dati);
            return Redirect::to('admin/dettaglio_utente/'.$id);
        }

        if(isset($dati['elimina_preventivo'])){
            unset($dati['elimina_preventivo']);
            $dati['id_utente'] = $id;
            DB::table('preventivi')->where('id',$dati['id'])->delete();
            return Redirect::to('admin/dettaglio_utente/'.$id);
        }

        if(isset($dati['aggiungi_attivita'])){
            unset($dati['aggiungi_attivita']);
            $dati['id_utente'] = $id;
            DB::table('scadenziario')->insert($dati);
            return Redirect::to('admin/dettaglio_utente/'.$id);
        }

        if(isset($dati['modifica_attivita'])){
            unset($dati['modifica_attivita']);
            $dati['id_utente'] = $id;
            DB::table('scadenziario')->where('id',$dati['id'])->update($dati);
            return Redirect::to('admin/dettaglio_utente/'.$id);
        }

        if(isset($dati['elimina_attivita'])){
            unset($dati['elimina_attivita']);
            $dati['id_utente'] = $id;
            DB::table('scadenziario')->where('id',$dati['id'])->delete();
            return Redirect::to('admin/dettaglio_utente/'.$id);
        }

        if(isset($dati['aggiungi_lead'])){
            unset($dati['aggiungi_lead']);

            DB::table('leads')->insert($dati);
            return Redirect::to('admin/dettaglio_utente/'.$id);
        }

        if(isset($dati['modifica_lead'])){
            unset($dati['modifica_lead']);
            DB::table('leads')->where('id',$dati['id'])->update($dati);
            return Redirect::to('admin/dettaglio_utente/'.$id);
        }

        if(isset($dati['elimina_lead'])){
            unset($dati['elimina_lead']);
            DB::table('leads')->where('id',$dati['id'])->delete();
            return Redirect::to('admin/dettaglio_utente/'.$id);
        }

        $page = 'index';
        $utenti = DB::select('SELECT *,(select ifnull(sum(provvigione) - sum(pagato),0) FROM preventivi WHERE status < 4 and status > 0 and id_utente IN (select id from utenti where id_agente='.$id.')) as budget from utenti where id='.$id);
        if(sizeof($utenti) > 0) {
            $user = $utenti[0];
            if($user->id_tipologia == 1) {
                $preventivi = DB::select('SELECT p.*,u.ragione_sociale from preventivi p JOIN utenti u ON u.id = p.id_utente where p.status < 4 and p.id_utente IN (select id from utenti where id_agente='.$id.')');

                $clienti = DB::select('SELECT *,(SELECT sum(totale) from preventivi where id_utente = utenti.id) as preventivato,(SELECT sum(incassato) from preventivi where id_utente = utenti.id) as incassato from utenti where id_tipologia = 2 and id_agente = '.$id);
                $leads = DB::select('SELECT l.*,u.ragione_sociale,u.mail_leads,u.telefono_referente,u.referente from leads l JOIN utenti u ON u.id = l.id_utente and l.id_utente IN (select id from utenti where id_agente='.$id.') order by data desc');
                $leads_assegnate = DB::select('SELECT l.*,u.ragione_sociale,u.mail_leads,u.telefono_referente,u.referente from leads l JOIN utenti u ON u.id = l.id_utente and l.id_assegnazione = '.$id.' order by data desc');

                return View::make('default.dettaglio_agente', compact('page', 'utente', 'user','preventivi','clienti','leads','leads_assegnate'));
            }

            if($user->id_tipologia == 2) {
                $preventivi = DB::select('SELECT * from preventivi where id_utente ='.$id);
                $leads = DB::select('SELECT l.*,u.ragione_sociale,u.mail_leads,u.telefono_referente,u.referente from leads l JOIN utenti u ON u.id = l.id_utente and l.id_utente = '.$id.' order by data desc');
                return View::make('default.dettaglio_cliente', compact('page', 'utente', 'user','preventivi','leads'));
            }
        }

    }

    public function leads($status,Request $request){

        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if(isset($dati['aggiungi'])){
            unset($dati['aggiungi']);

            DB::table('leads')->insert($dati);
            return Redirect::to('admin/leads/'.$status);
        }

        if(isset($dati['modifica'])){
            unset($dati['modifica']);

            DB::table('leads')->where('id',$dati['id'])->update($dati);


            return Redirect::to('admin/leads/'.$status);
        }

        if(isset($dati['assegna_lead'])){
            unset($dati['assegna_lead']);

            $leads = DB::select('SELECT * from leads where id ='.$dati['id']);
            if(sizeof($leads) > 0) {

                $lead = $leads[0];

                $utenti = DB::select('select * from utenti where id =' . $lead->id_assegnazione);
                if (sizeof($utenti) > 0) {

                    $operatore = $utenti[0];

                    $utenti = DB::select('select * from utenti where id =' . $lead->id_utente);
                    if (sizeof($utenti) > 0) {

                        $usr = $utenti[0];

                        $mail = new PHPMailer(true);
                        $mail->isSMTP();
                        $mail->Host = 'smtps.aruba.it;smtps.aruba.it';
                        $mail->SMTPAuth = true;
                        $mail->Username = 'crm@ingenia.cloud';
                        $mail->Password = '8_gg86eEtShU?6D';
                        $mail->SMTPSecure = 'ssl';
                        $mail->CharSet = 'utf-8';
                        $mail->Port = 465;
                        $mail->setFrom('crm@ingenia.cloud', 'CRM Ingenia SRL');
                        $mail->addAddress($operatore->email);
                        $mail->addBCC('giovinefabio@gmail.com');

                        $mail->isHTML(true);
                        $mail->Subject = 'Lead Assegnata - ' . $lead->descrizione . ' - ' . $usr->ragione_sociale;

                        $mail->Body = '
                        Salve ' . $operatore->nome . ' ' . $operatore->cognome . '<br>
                        Ti è stata assegnata una Lead<br><br>
                        Descrizione: ' . $lead->descrizione . '<br>
                        Cliente: ' . $usr->ragione_sociale . '<br>
                        Note: ' . $lead->note . '<br><br>
                        Per Poter entrare nella tua area riservata clicca su questo link <br>
                        <a href="' . URL::asset('admin/index') . '">' . URL::asset('admin/index') . '</a> <br><br>
                        
                        <img style="width:300px;" src="https://crm.ingenia.cloud/logo.png"><br>
                        ';

                        $mail->send();
                    }
                }

            }


        }

        if(isset($dati['elimina'])){
            unset($dati['elimina']);
            DB::table('leads')->where('id',$dati['id'])->delete();
            return Redirect::to('admin/leads/'.$status);
        }


        if(isset($dati['aggiungi_preventivo'])){
            unset($dati['aggiungi_preventivo']);

            if($_FILES['allegato']['name'] != ''){

                $pathinfo = pathinfo($_FILES['allegato']['name']);
                $nome = Str::random(20);
                $target = 'allegati_preventivi/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['allegato']['tmp_name'], $target);
                $dati['allegato'] = $target;
            }

            DB::table('preventivi')->insert($dati);
            return Redirect::to('admin/preventivi/0');
        }


        $page = 'index';
        $leads = DB::select('SELECT l.*,u.ragione_sociale,u.mail_leads,u.telefono_referente,u.referente,u2.ragione_sociale as operatore from leads l JOIN utenti u ON u.id = l.id_utente and l.status ='.$status.' LEFT JOIN utenti u2 ON u2.id = l.id_assegnazione order by data desc');
        $clienti = DB::select('SELECT * from utenti where id_tipologia = 2 order by ragione_sociale asc');
        $operatori = DB::select('SELECT * from utenti where id_tipologia = 1 order by ragione_sociale asc');

        return View::make('default.leads', compact('page', 'utente','leads','clienti','status','operatori'));

    }

    public function preventivi($status,Request $request){

        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if(isset($dati['aggiungi_preventivo'])){
            unset($dati['aggiungi_preventivo']);

            if($_FILES['allegato']['name'] != ''){

                $pathinfo = pathinfo($_FILES['allegato']['name']);
                $nome = Str::random(20);
                $target = 'allegati_preventivi/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['allegato']['tmp_name'], $target);
                $dati['allegato'] = $target;
            }

            DB::table('preventivi')->insert($dati);
            return Redirect::to('admin/preventivi/'.$status);
        }

        if(isset($dati['modifica_preventivo'])){
            unset($dati['modifica_preventivo']);


            if($_FILES['allegato']['name'] != ''){

                $pathinfo = pathinfo($_FILES['allegato']['name']);
                $nome = Str::random(20);
                $target = 'allegati_preventivi/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['allegato']['tmp_name'], $target);
                $dati['allegato'] = $target;
            }

            DB::table('preventivi')->where('id',$dati['id'])->update($dati);
            return Redirect::to('admin/preventivi/'.$status);
        }

        if(isset($dati['elimina_preventivo'])){
            unset($dati['elimina_preventivo']);
            DB::table('preventivi')->where('id',$dati['id'])->delete();
            return Redirect::to('admin/preventivi/'.$status);
        }

        if(isset($dati['aggiungi_cashflow'])){
            unset($dati['aggiungi_cashflow']);

            if($_FILES['allegato']['name'] != ''){

                $pathinfo = pathinfo($_FILES['allegato']['name']);
                $nome = Str::random(20);
                $target = 'allegati_cashflow/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['allegato']['tmp_name'], $target);
                $dati['allegato'] = $target;
            }

            DB::table('cashflow')->insert($dati);
            DB::update('UPDATE preventivi SET incassato = (SELECT SUM(valore) AS totale_incassato FROM cashflow WHERE id_preventivo = '.$dati['id_preventivo'].' AND incassato = 1) WHERE id = '.$dati['id_preventivo']);
            return Redirect::to('admin/cashflow/0');
        }

        $page = 'index';
        $preventivi = DB::select('SELECT p.*,u.ragione_sociale from preventivi p JOIN utenti u ON u.id = p.id_utente and p.status ='.$status);
        $clienti = DB::select('SELECT * from utenti where id_tipologia = 2 order by ragione_sociale asc');

        return View::make('default.preventivi', compact('page', 'utente','preventivi','clienti','status'));

    }

    public function cashflow($status,Request $request){

        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if(isset($dati['aggiungi'])){
            unset($dati['aggiungi']);

            if($_FILES['allegato']['name'] != ''){

                $pathinfo = pathinfo($_FILES['allegato']['name']);
                $nome = Str::random(20);
                $target = 'allegati_cashflow/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['allegato']['tmp_name'], $target);
                $dati['allegato'] = $target;
            }

            DB::table('cashflow')->insert($dati);
            return Redirect::to('admin/cashflow/'.$status);
        }

        if(isset($dati['modifica'])){
            unset($dati['modifica']);

            if($_FILES['allegato']['name'] != ''){

                $pathinfo = pathinfo($_FILES['allegato']['name']);
                $nome = Str::random(20);
                $target = 'allegati_cashflow/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['allegato']['tmp_name'], $target);
                $dati['allegato'] = $target;
            }

            DB::table('cashflow')->where('id',$dati['id'])->update($dati);

            DB::update('UPDATE preventivi SET incassato = (SELECT SUM(valore) AS totale_incassato FROM cashflow WHERE id_preventivo = '.$dati['id_preventivo'].' AND incassato = 1) WHERE id = '.$dati['id_preventivo']);

            return Redirect::to('admin/cashflow/'.$status);
        }

        if(isset($dati['elimina'])){
            unset($dati['elimina']);
            DB::table('cashflow')->where('id',$dati['id'])->delete();
            return Redirect::to('admin/cashflow/'.$status);
        }

        $page = 'index';
        $cashflow = DB::select('

            SELECT c.*,p.descrizione as preventivo,cli.ragione_sociale as cliente,cli.id as id_cliente from cashflow c 
            LEFT JOIN preventivi p ON p.id = c.id_preventivo 
            LEFT JOIN utenti cli ON cli.id = p.id_utente                                       
            where c.incassato = '.$status.' order by c.data desc');

        return View::make('default.cashflow', compact('page', 'utente','cashflow','status'));

    }


    public function canoni(Request $request){

        $this->is_loggato();
        $utente = session('utente');

        $page = 'index';
        $preventivi = DB::select('SELECT p.*,u.ragione_sociale from preventivi p JOIN utenti u ON u.id = p.id_utente and canone > 0 and status > 0 order by data_canone ASC');

        return View::make('default.canoni', compact('page', 'utente','preventivi'));

    }


    public function logout(){

        session()->flush();
        return Redirect::to('admin/login');
    }

    public function is_loggato(){

        if (!session()->has('utente')) return Redirect::to('admin/login')->send();

    }

}

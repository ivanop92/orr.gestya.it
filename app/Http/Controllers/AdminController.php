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
class AdminController extends Controller{

    public function login(Request $request){

        $dati = $request->all();
        $error = '';

        if (isset($dati['login'])) {

            $utenti = DB::select('SELECT * from utenti where abilitato = 1 and email = "' . htmlentities($dati['email'], 3, 'UTF-8' . '') . '" and password = "' . htmlentities($dati['password'], 3, 'UTF-8' . '') . '"');

            if (sizeof($utenti) > 0) {

                $utente = $utenti[0];
                session(['utente' => $utente]);
                session(['anno' => date("Y")]);
                session()->save();
                // Manutentore: dashboard responsive dedicata (smartphone/tablet)
                if(!empty($utente->manutentore)) return Redirect::to('manutentore/dashboard');
                if($utente->admin_azienda == 1) return Redirect::to('utente/index');
                if($utente->admin_azienda == 2) return Redirect::to('utente/index');
                if($utente->id_tipologia == 1) return Redirect::to('utente/visualizza_provvigioni');
                else return Redirect::to('admin/index');
            } else $error = 'Inserisci username e password corretti';

        }

        return View::make('default.login', compact('error'));
    }

    public function index(Request $request){

        $this->is_loggato();
        $utente = session('utente');
        $page = 'index';

        return View::make('admin.index', compact('page', 'utente'));

    }
    public function aziende(request $request) {

        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if (isset($dati['effettua_login'])) {
            unset($dati['effettua_login']);

            $utenti = DB::select('SELECT * from utenti where id = '.$dati['id_utente']);
            if(sizeof($utenti) > 0) {
                $id_admin = $utente->id;
                $utente = $utenti[0];
                $utente->torna_super_admin = $id_admin;
                session(['utente' => $utente]);
                session(['anno' =>date("Y")]);
                session()->save();
                return Redirect::to('utente/index');
            }
        }

        if(isset($dati['aggiungi'])) {
            unset($dati['aggiungi']);

            $dati['immagine'] = '/placehold_immagine.png';

            $dati['token_azienda'] = Str::random(30);


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
                'token_azienda' => $dati['token_azienda']

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
            // Setup automatico dei magazzini standard (PF/MP/Commerciali, uno default per tipologia)
            UtenteController::inserisci_magazzini_standard($id_azienda_inserita);

            return Redirect::to('admin/aziende');


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

            DB::table('aziende')->where('id', $dati['id_azienda'])->delete();
            DB::table('utenti')->where('id_azienda', $dati['id_azienda'])->delete();

            return Redirect::to('admin/aziende');

        }


        $aziende = DB::select('SELECT a.*,u.nome,u.cognome,u.id as id_utente from aziende a JOIN utenti u ON a.id = u.id_azienda and u.admin_azienda = 1');
        return View::make('admin.aziende', compact( 'utente', 'aziende'));
    }

    public function utenti(request $request) {

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

        return View::make('admin.utenti', compact( 'utente', 'utenti'));
    }

    public function logout(){

        session()->flush();
        return Redirect::to('admin/login');
    }

    public function is_loggato(){

        if (!session()->has('utente')) return Redirect::to('admin/login')->send();

    }

}

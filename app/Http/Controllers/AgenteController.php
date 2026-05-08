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
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TariffeImport;
use App\Http\Controllers\URL;


class AgenteController extends Controller{

    public function index(Request $request){

        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        $id = $utente->id;


        if(isset($dati['aggiungi_cliente'])){
            unset($dati['aggiungi_cliente']);
            $dati['id_tipologia'] = 2;

            if($_FILES['immagine']['name'] != ''){

                $pathinfo = pathinfo($_FILES['immagine']['name']);
                $nome = Str::random(20);
                $target = 'immagini/clienti/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['immagine']['tmp_name'], $target);
                $dati['immagine'] = $target;
            }


            DB::table('utenti')->insert($dati);
            return Redirect::to('agente/index');
        }

        if(isset($dati['effettua_login'])) {
            unset($dati['effettua_login']);

            $utenti = DB::select('SELECT * from utenti where id = '.$dati['id']);

            if (sizeof($utenti) > 0) {

                $utente = $utenti[0];
                $utente->torna_admin = $id;
                session(['utente' => $utente]);
                session()->save();
                if ($utente->id_tipologia == 0)  return Redirect::to('admin/index');
                if ($utente->id_tipologia == 1)  return Redirect::to('agente/index');
                if ($utente->id_tipologia == 2)  return Redirect::to('cliente/index');
            }
        }

        if(isset($dati['modifica_cliente'])){
            unset($dati['modifica_cliente']);
            $dati['id_tipologia'] = 2;

            if($_FILES['immagine']['name'] != ''){

                $pathinfo = pathinfo($_FILES['immagine']['name']);
                $nome = Str::random(20);
                $target = 'immagini/clienti/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['immagine']['tmp_name'], $target);
                $dati['immagine'] = $target;
            }


            DB::table('utenti')->where('id',$dati['id'])->update($dati);
            return Redirect::to('agente/index');
        }


        if(isset($dati['aggiungi_lead'])){
            unset($dati['aggiungi_lead']);

            DB::table('leads')->insert($dati);
            return Redirect::to('agente/index');
        }

        if(isset($dati['modifica_lead'])){
            unset($dati['modifica_lead']);
            DB::table('leads')->where('id',$dati['id'])->update($dati);

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
                        $mail->addAddress('giovinefabio@gmail.com');

                        $mail->isHTML(true);
                        $mail->Subject =  $operatore->ragione_sociale . ' Modifica Lead - ' . $lead->descrizione.' - '.$usr->ragione_sociale;

                        $mail->Body = '
                        ' . $operatore->nome . ' ' . $operatore->cognome . ' Ha Aggiornato la Lead<br>
                        Note: ' . $lead->note;

                        $mail->send();
                    }
                }

            }

            return Redirect::to('agente/index');
        }

        if(isset($dati['elimina_lead'])){
            unset($dati['elimina_lead']);
            DB::table('leads')->where('id',$dati['id'])->delete();
            return Redirect::to('agente/index');
        }

        $page = 'index';
        $utenti = DB::select('SELECT *,(select ifnull(sum(provvigione) - sum(pagato),0) FROM preventivi WHERE status >= 1 and id_utente IN (select id from utenti where id_agente='.$id.')) as budget from utenti where id='.$id);
        if(sizeof($utenti) > 0) {
            $user = $utenti[0];
            $preventivi = DB::select('SELECT p.*,u.ragione_sociale from preventivi p JOIN utenti u ON u.id = p.id_utente where p.id_utente IN (select id from utenti where id_agente='.$id.')');
            $clienti = DB::select('SELECT *,(SELECT sum(totale) from preventivi where id_utente = utenti.id) as preventivato,(SELECT sum(incassato) from preventivi where id_utente = utenti.id) as incassato from utenti where id_tipologia = 2 and id_agente = '.$id);
            $leads = DB::select('SELECT l.*,u.ragione_sociale,u.mail_leads,u.telefono_referente,u.referente from leads l JOIN utenti u ON u.id = l.id_utente and l.id_utente IN (select id from utenti where id_agente='.$id.') and status < 3 order by data desc');
            $leads_assegnate = DB::select('SELECT l.*,u.ragione_sociale,u.mail_leads,u.telefono_referente,u.referente from leads l JOIN utenti u ON u.id = l.id_utente and l.id_assegnazione = '.$id.' order by data desc');
            return View::make('default.dettaglio_agente', compact('page', 'utente', 'user','preventivi','clienti','leads','leads_assegnate'));

        }

    }

    public function logout(){

        session()->flush();
        return Redirect::to('admin/login');
    }

    public function is_loggato(){

        if (!session()->has('utente'))  return Redirect::to('admin/login')->send();

    }


}

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


class AjaxAdminController extends Controller
{

    public function delete_oggetto($id)
    {
        $this->is_loggato();
        DB::SELECT('DELETE FROM oggetti_preferenza where id = ' . $id);
    }

    public function delete_consiglio($id)
    {
        $this->is_loggato();
        DB::SELECT('DELETE FROM consigli where id = ' . $id);
    }

    public function delete_viaggio($id)
    {
        $this->is_loggato();
        DB::SELECT('DELETE FROM viaggi where id = ' . $id);
    }

    public function delete_road($id)
    {
        $this->is_loggato();
        DB::SELECT('DELETE FROM roadmap where id = ' . $id);
    }

    public function search_partecipante($like)
    {
        $this->is_loggato();
        $simili = DB::SELECT('SELECT * FROM info_partecipanti where nome like \'%' . $like . '%\' or cognome \'%' . $like . '%\' ');
        foreach ($simili as $s){

            echo '<option value="'.$s->id_utente.'">'.$s->nome.' '.$s->cognome.'</option>';

        }

    }

    public function elimina_partecipante($id_utente, $id_viaggio)
    {
        $this->is_loggato();
        DB::SELECT('DELETE FROM partecipanti_viaggio where id_viaggio = ' . $id_viaggio . ' and id_utente = ' . $id_utente);

    }

    public function update_road($id, $content, $data_inizio, $data_fine, $id_tipo_roadmap, $indirizzo)
    {
        $this->is_loggato();
        DB::SELECT('UPDATE roadmap SET content =\'' . $content . '\',data_inizio = \'' . $data_inizio . '\',data_fine = \'' . $data_fine . '\' ,id_tipo_roadmap = \'' . $id_tipo_roadmap . '\',indirizzo = \'' . $indirizzo . '\' where id = ' . $id);
    }

    public function update_oggetto($id, $content)
    {
        $this->is_loggato();
        DB::SELECT('UPDATE oggetti_preferenza set content = \'' . $content . '\' where id = ' . $id);
    }


    public function aggiungi_oggetto($id, $content)
    {
        $this->is_loggato();
        DB::SELECT('INSERT INTO oggetti_preferenza (id_preferenza,content) VALUES(' . $id . ' ,\'' . $content . '\')');
    }

    public function aggiungi_consiglio()
    {
        $this->is_loggato();
        DB::SELECT('INSERT INTO consigli (title,content) VALUES(\'' . $_GET['title'] . '\' ,\'' . $_GET['content'] . '\')');
    }

    public function aggiungi_group_consiglio()
    {
        $this->is_loggato();
        DB::SELECT('INSERT INTO consigli (id_tipo_roadmap,title,content) VALUES('.$_GET['id_tipo_roadmap'].',\'' . $_GET['title'] . '\' ,\'' . $_GET['content'] . '\')');
    }

    public function aggiungi_preferenza($type, $title)
    {
        $this->is_loggato();
        DB::SELECT('INSERT INTO preferenza (title,id_type) VALUES(\'' . str_replace('\'', '\\\'', $title) . '\' ,\'' . $type . '\')');
    }

    public function aggiungi_tutorial($descrizione, $img)
    {
        $this->is_loggato();
        DB::SELECT('INSERT INTO tutorial (descrizione,img) VALUES(\'' . str_replace('\'', '\\\'', $descrizione) . '\' ,\'' . $img . '\')');
    }

    public function aggiungi_viaggio($localita, $descrizione, $andata, $ritorno,$partenza,$arrivo,$max_partecipanti)
    {
        $this->is_loggato();
        DB::SELECT('INSERT INTO viaggi (localita,descrizione,data_andata,data_ritorno,stato,partenza,arrivo,max_partecipanti) VALUES(\'' . str_replace('\'', '\\\'', $localita) . '\' ,\'' . str_replace('\'', '\\\'', $descrizione) . '\',\'' . $andata . '\',\'' . $ritorno . '\',1,\'' . str_replace('\'', '\\\'', $partenza) . '\' ,\'' . str_replace('\'', '\\\'', $arrivo) . '\', '.$max_partecipanti.')');
    }

    public function delete_descrizione_tutorial($id)
    {
        $this->is_loggato();
        DB::SELECT('UPDATE tutorial SET descrizione = \'\' where id = ' . $id);
    }

    public function delete_img_tutorial($id)
    {
        $this->is_loggato();
        DB::SELECT('UPDATE tutorial SET img = \'\' where id = ' . $id);
    }

    public function delete_tutorial($id)
    {
        $this->is_loggato();
        DB::SELECT('DELETE FROM tutorial where id = ' . $id);
    }

    public function delete_oggetto_viaggio($id)
    {
        $this->is_loggato();
        DB::SELECT('DELETE FROM oggetti_filtri_viaggio where id = ' . $id);
    }

    public function update_oggetto_viaggio($id, $content)
    {
        $this->is_loggato();
        DB::SELECT('UPDATE oggetti_filtri_viaggio set content = \'' . $content . '\' where id = ' . $id);
    }


    public function aggiungi_oggetto_viaggio($id, $content)
    {
        $this->is_loggato();
        DB::SELECT('INSERT INTO oggetti_filtri_viaggio (id_filtro,content) VALUES(' . $id . ' ,\'' . $content . '\')');
    }

    public function aggiungi_filtri_viaggio($type, $title)
    {
        $this->is_loggato();
        DB::SELECT('INSERT INTO filtri_viaggio (title,id_type) VALUES(\'' . str_replace('\'', '\\\'', $title) . '\' ,\'' . $type . '\')');
    }

    /**
     * Verifica se l'utente è loggato
     * @return \Illuminate\Http\RedirectResponse
     */
    public function is_loggato()
    {
        if (!session()->has('utente')) return Redirect::to('admin/login')->send();
    }

}

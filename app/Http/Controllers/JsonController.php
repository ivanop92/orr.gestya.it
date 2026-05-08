<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Mpdf\Mpdf;


class JsonController extends Controller{

    public function riepilogo_documenti() {

        $this->is_loggato();
        $anno = session('anno');
        $utente = session('utente');

        $ordering = ' order by data DESC,numero DESC, anno DESC';

        $where = array();
        $where_string = '';

        if (isset($_GET['iSortCol_0'])) {

            if ($_GET['iSortCol_0'] == 1) $ordering = 'ORDER BY id ' . $_GET['sSortDir_0'];
            /*if ($_GET['iSortCol_0'] == 2) $ordering = 'ORDER BY p.data_prima_notte ' . $_GET['sSortDir_0'];
            if ($_GET['iSortCol_0'] == 3) $ordering = 'ORDER BY p.data_ultima_notte ' . $_GET['sSortDir_0'];
            if ($_GET['iSortCol_0'] == 4) $ordering = 'ORDER BY p.nominativo ' . $_GET['sSortDir_0'];
            */

        }


        array_push($where, 'dotes.tipologia_documento = "'.$_GET['td'].'"');

        array_push($where, '1 = 1');
        array_push($where, 'id_azienda = '.$utente->id_azienda);
        array_push($where, 'YEAR(data_doc) = '.$anno);

        if(is_numeric($_GET['sSearch'])) {
            if ($_GET['sSearch'] != '') array_push($where, ' (numero like  "%' . $_GET['sSearch'] . '%" or nominativo like  "%' . $_GET['sSearch'] . '%" or id IN (SELECT id_testata from dorig where id_pratica = ' . $_GET['sSearch'] . '))');
        } else {

            if ($_GET['sSearch'] != '') array_push($where, ' (numero like  "%' . $_GET['sSearch'] . '%" or nominativo like  "%' . $_GET['sSearch'] . '%" )');
        }

        if (sizeof($where) > 0) {
            $i = 0;
            foreach ($where as $condition) {
                if ($i == 0)
                    $where_string .= ' where ';
                else
                    $where_string.= ' and ';

                $where_string.= $condition;

                $i++;
            }
        }

        $limit = '';
        if($_GET['iDisplayLength']  > 0) $limit = ' limit ' . $_GET['iDisplayStart'] . ',' . $_GET['iDisplayLength'] ;

        $documenti = DB::select('SELECT * from dotes  '.$where_string.' ' . $ordering . ' '.$limit);
        $numero = DB::select('SELECT count(id) as numero from dotes '.$where_string)[0]->numero;

        $jsonoutput = array(
            "iTotalRecords" => intval($numero),
            "iTotalDisplayRecords" => intval($numero),
            "aaData" => array()
        );

        foreach ($fatture as $f) {

            $f->righe = DB::select('SELECT * from dorig where id_testata = '.$f->id);


            $tipologia = 'Fattura';
            if ($f->tipologia_documento == 'TD04') {
                $tipologia = 'Nota di Credito';
            }




            if($f->stato == 0) $background = 'rgba(231, 76, 60,0.2)';
            if($f->stato == 1) $background = 'rgba(243, 156, 18,0.2)';
            if($f->stato == 2) $background = 'rgba(231, 76, 60,0.2)';
            if($f->stato == 3) $background = 'rgba(39, 174, 96,0.2)';

            $object = array();
            $object['DT_RowId'] = $f->id;
            $object['0'] = $f->numero . '/' . $f->anno . '<br>' . $tipologia;


            $object['1'] = date('d/m/Y',strtotime($f->data));
            $object['2'] = $f->nominativo.'<br>CF:'.$f->cf.'<br>P.IVA: '.$f->piva.'<br>'.$f->indirizzo.'<br>'.$f->cap.' '.$f->citta.' ('.$f->provincia.') '.$f->nazione;



            if($f->stato == 2){

                $object['2'] .= '<br><br><b>Errore: '.$f->ns_codice.'<br>'.$f->ns_descrizione.'<br>'.$f->ns_suggerimento.'</b>';

            }

            $object['3'] = 'SDI: '.$f->sdi.'<br>PEC: '.$f->pec;
            $object['4'] = 'Imponibile: &euro;'.$f->imponibile.'<br>Imposta: &euro;'.$f->imposta.'<br>Totale: &euro; '.($f->totale);

            $object['5'] = '
                <a style="float:left" onclick="modifica(' . $f->id . ')" class="btn btn-primary btn-sm">MODIFICA</a>
                <a style="float:left;margin-left:5px;" onclick="aggiungi_riga(' . $f->id . ')"  class="btn btn-success btn-sm">+ RIGA</a>
              
                <form method="post" onsubmit="return confirm(\'vuoi duplicare questa fattura ?\')" style="float:left;margin-left:5px;">
                    <input type="hidden" name="id" value="' . $f->id . '">
                    <input type="submit" name="duplica" value="DUPLICA" class="btn btn-primary btn-sm">
                </form> 
                
                 <a style="float:left;margin-left:5px;" onclick="nota_credito(' . $f->id . ')" class="btn btn-primary btn-sm">Crea Nota Credito</a>
              
              
                <a style="float:left;margin-left:5px;" onclick="mostra_righe('.$f->id.')" class="btn btn-primary btn-sm">ESPANDI</a>';

            if(sizeof($f->righe) > 0){
                $object['5'] .='
                <a style="float:left;margin-left:5px;" target="_blank" href="'.URL::asset('admin/scarica_xml/'.$f->id).'" class="btn btn-primary btn-sm">SCARICA</a>
                <a style="float:left;margin-left:5px;" target="_blank" href="'.URL::asset('admin/visualizza_xml/'.$f->id).'" class="btn btn-primary btn-sm">STAMPA XML</a>
                <a style="float:left;margin-left:5px;" target="_blank" href="'.URL::asset('admin/stampa/proforma_fattura/'.$f->id).'" class="btn btn-primary btn-sm">STAMPA</a>';


            }

            if($f->stato == 0 || $f->stato == 2) {

                $object['5'] .='    <form method="post" onsubmit="return confirm(\'vuoi inviare allo sdi ?\')" style="float:left;margin-left:5px;">
                    <input type="hidden" name="id" value="' . $f->id . '">
                    <input type="submit" name="invia_sdi" value="Invia SDI" class="btn btn-primary btn-sm">
                </form> ';

                $object['5'] .= '<form method="post" onsubmit="return confirm(\'vuoi eliminare questa fattura ?\')" style="float:left;margin-left:5px;">
                        <input type="hidden" name="id" value="' . $f->id . '">
                        <input type="submit" name="elimina" value="ELIMINA" class="btn btn-danger btn-sm">
                    </form>';

            }

            // Se la fattura non è contabilizzata, aggiungi il pulsante "Contabilizza"
            // Se la fattura non è contabilizzata, aggiungi il pulsante "Contabilizza"
            if($f->contabilizzata == 0) {
                $object['5'] .= '
        <form method="POST" action="' . route('fattura.dettaglio', $f->id) . '" style="float:left;margin-left:5px;">
            
            <input type="submit" class="btn btn-success btn-sm" value="Contabilizza">
        </form>';
            } else {
                $object['5'] .= '<span class="badge badge-success" style="float:left;margin-left:5px;">Contabilizzata</span>';
            }



            if($f->allegato != ''){
                $object['5'] .='<div style="clear:both"></div>
                <a style="width:70%;margin-top:5px;float:left;" target="_blank" href="'.URL::asset($f->allegato).'" class="btn btn-primary btn-sm">'.$f->nome_allegato.'</a>';

                $object['5'] .= '<form method="post" onsubmit="return confirm(\'vuoi eliminare questo Allegato ?\')" >
                    <input type="hidden" name="id" value="' . $f->id . '">
                    <input style="width:30%;float:left;margin-top:5px;" type="submit" name="elimina_allegato" value="Elimina" class="btn btn-danger btn-sm">
                </form>';

            } else {
                $object['5'] .= '<a style="width:100%;margin-top:5px;" onclick="aggiungi_allegato(' . $f->id . ')"  class="btn btn-success btn-sm">Allegato 1</a>';
            }

            if($f->allegato2 != ''){
                $object['5'] .='
                <a style="width:70%;margin-top:5px;float:left;" target="_blank" href="'.URL::asset($f->allegato2).'" class="btn btn-primary btn-sm">'.$f->nome_allegato2.'</a>';

                $object['5'] .= '<form method="post" onsubmit="return confirm(\'vuoi eliminare questo Allegato ?\')">
                    <input type="hidden" name="id" value="' . $f->id . '">
                    <input style="width:30%;float:left;margin-top:5px;" type="submit" name="elimina_allegato2" value="Elimina" class="btn btn-danger btn-sm">
                </form>';

            } else {
                $object['5'] .= '<br><a style="width:100%;margin-top:5px;" onclick="aggiungi_allegato2(' . $f->id . ')"  class="btn btn-success btn-sm">Allegato2</a>';
            }


            $object['6'] = $background;

            array_push($jsonoutput['aaData'], $object);

        }

        echo json_encode($jsonoutput);

    }


    public function lista_fatture($anno) {

        $this->is_loggato();
        $utente = session()->get('utente');

        $ordering = ' order by data DESC,numero DESC, anno DESC';

        $where = array();
        $where_string = '';

        if (isset($_GET['iSortCol_0'])) {

            if ($_GET['iSortCol_0'] == 1) $ordering = 'ORDER BY id ' . $_GET['sSortDir_0'];
            /*if ($_GET['iSortCol_0'] == 2) $ordering = 'ORDER BY p.data_prima_notte ' . $_GET['sSortDir_0'];
            if ($_GET['iSortCol_0'] == 3) $ordering = 'ORDER BY p.data_ultima_notte ' . $_GET['sSortDir_0'];
            if ($_GET['iSortCol_0'] == 4) $ordering = 'ORDER BY p.nominativo ' . $_GET['sSortDir_0'];
            */

        }


        array_push($where, 'dotes.tipologia_documento = "'.$_GET['td'].'"');

        array_push($where, '1 = 1');
        array_push($where, 'id_utente = '.$utente->id);
        array_push($where, 'YEAR(data) = '.$anno);

        if(is_numeric($_GET['sSearch'])) {
            if ($_GET['sSearch'] != '') array_push($where, ' (numero like  "%' . $_GET['sSearch'] . '%" or nominativo like  "%' . $_GET['sSearch'] . '%" or id IN (SELECT id_testata from dorig where id_pratica = ' . $_GET['sSearch'] . '))');
        } else {

            if ($_GET['sSearch'] != '') array_push($where, ' (numero like  "%' . $_GET['sSearch'] . '%" or nominativo like  "%' . $_GET['sSearch'] . '%" )');
        }

        if (sizeof($where) > 0) {
            $i = 0;
            foreach ($where as $condition) {
                if ($i == 0)
                    $where_string .= ' where ';
                else
                    $where_string.= ' and ';

                $where_string.= $condition;

                $i++;
            }
        }

        $limit = '';
        if($_GET['iDisplayLength']  > 0) $limit = ' limit ' . $_GET['iDisplayStart'] . ',' . $_GET['iDisplayLength'] ;

        $fatture = DB::select('SELECT * from dotes  '.$where_string.' ' . $ordering . ' '.$limit);
        $numero = DB::select('SELECT count(id) as numero from dotes '.$where_string)[0]->numero;

        $jsonoutput = array(
            "iTotalRecords" => intval($numero),
            "iTotalDisplayRecords" => intval($numero),
            "aaData" => array()
        );

        foreach ($fatture as $f) {

            $f->righe = DB::select('SELECT * from dorig where id_testata = '.$f->id);


            $tipologia = 'Fattura';
            if ($f->tipologia_documento == 'TD04') {
                $tipologia = 'Nota di Credito';
            }

        


            if($f->stato == 0) $background = 'rgba(231, 76, 60,0.2)';
            if($f->stato == 1) $background = 'rgba(243, 156, 18,0.2)';
            if($f->stato == 2) $background = 'rgba(231, 76, 60,0.2)';
            if($f->stato == 3) $background = 'rgba(39, 174, 96,0.2)';

            $object = array();
            $object['DT_RowId'] = $f->id;
            $object['0'] = $f->numero . '/' . $f->anno . '<br>' . $tipologia;


            $object['1'] = date('d/m/Y',strtotime($f->data));
            $object['2'] = $f->nominativo.'<br>CF:'.$f->cf.'<br>P.IVA: '.$f->piva.'<br>'.$f->indirizzo.'<br>'.$f->cap.' '.$f->citta.' ('.$f->provincia.') '.$f->nazione;



            if($f->stato == 2){

                $object['2'] .= '<br><br><b>Errore: '.$f->ns_codice.'<br>'.$f->ns_descrizione.'<br>'.$f->ns_suggerimento.'</b>';

            }

            $object['3'] = 'SDI: '.$f->sdi.'<br>PEC: '.$f->pec;
            $object['4'] = 'Imponibile: &euro;'.$f->imponibile.'<br>Imposta: &euro;'.$f->imposta.'<br>Totale: &euro; '.($f->totale);

            $object['5'] = '
                <a style="float:left" onclick="modifica(' . $f->id . ')" class="btn btn-primary btn-sm">MODIFICA</a>
                <a style="float:left;margin-left:5px;" onclick="aggiungi_riga(' . $f->id . ')"  class="btn btn-success btn-sm">+ RIGA</a>
              
                <form method="post" onsubmit="return confirm(\'vuoi duplicare questa fattura ?\')" style="float:left;margin-left:5px;">
                    <input type="hidden" name="id" value="' . $f->id . '">
                    <input type="submit" name="duplica" value="DUPLICA" class="btn btn-primary btn-sm">
                </form> 
                
                 <a style="float:left;margin-left:5px;" onclick="nota_credito(' . $f->id . ')" class="btn btn-primary btn-sm">Crea Nota Credito</a>
              
              
                <a style="float:left;margin-left:5px;" onclick="mostra_righe('.$f->id.')" class="btn btn-primary btn-sm">ESPANDI</a>';

            if(sizeof($f->righe) > 0){
                $object['5'] .='
                <a style="float:left;margin-left:5px;" target="_blank" href="'.URL::asset('admin/scarica_xml/'.$f->id).'" class="btn btn-primary btn-sm">SCARICA</a>
                <a style="float:left;margin-left:5px;" target="_blank" href="'.URL::asset('admin/visualizza_xml/'.$f->id).'" class="btn btn-primary btn-sm">STAMPA XML</a>
                <a style="float:left;margin-left:5px;" target="_blank" href="'.URL::asset('admin/stampa/proforma_fattura/'.$f->id).'" class="btn btn-primary btn-sm">STAMPA</a>';


            }

            if($f->stato == 0 || $f->stato == 2) {

                $object['5'] .='    <form method="post" onsubmit="return confirm(\'vuoi inviare allo sdi ?\')" style="float:left;margin-left:5px;">
                    <input type="hidden" name="id" value="' . $f->id . '">
                    <input type="submit" name="invia_sdi" value="Invia SDI" class="btn btn-primary btn-sm">
                </form> ';

                $object['5'] .= '<form method="post" onsubmit="return confirm(\'vuoi eliminare questa fattura ?\')" style="float:left;margin-left:5px;">
                        <input type="hidden" name="id" value="' . $f->id . '">
                        <input type="submit" name="elimina" value="ELIMINA" class="btn btn-danger btn-sm">
                    </form>';

            }

            // Se la fattura non è contabilizzata, aggiungi il pulsante "Contabilizza"
            // Se la fattura non è contabilizzata, aggiungi il pulsante "Contabilizza"
            if($f->contabilizzata == 0) {
                $object['5'] .= '
        <form method="POST" action="' . route('fattura.dettaglio', $f->id) . '" style="float:left;margin-left:5px;">
            
            <input type="submit" class="btn btn-success btn-sm" value="Contabilizza">
        </form>';
            } else {
                $object['5'] .= '<span class="badge badge-success" style="float:left;margin-left:5px;">Contabilizzata</span>';
            }



            if($f->allegato != ''){
                $object['5'] .='<div style="clear:both"></div>
                <a style="width:70%;margin-top:5px;float:left;" target="_blank" href="'.URL::asset($f->allegato).'" class="btn btn-primary btn-sm">'.$f->nome_allegato.'</a>';

                $object['5'] .= '<form method="post" onsubmit="return confirm(\'vuoi eliminare questo Allegato ?\')" >
                    <input type="hidden" name="id" value="' . $f->id . '">
                    <input style="width:30%;float:left;margin-top:5px;" type="submit" name="elimina_allegato" value="Elimina" class="btn btn-danger btn-sm">
                </form>';

            } else {
                $object['5'] .= '<a style="width:100%;margin-top:5px;" onclick="aggiungi_allegato(' . $f->id . ')"  class="btn btn-success btn-sm">Allegato 1</a>';
            }

            if($f->allegato2 != ''){
                $object['5'] .='
                <a style="width:70%;margin-top:5px;float:left;" target="_blank" href="'.URL::asset($f->allegato2).'" class="btn btn-primary btn-sm">'.$f->nome_allegato2.'</a>';

                $object['5'] .= '<form method="post" onsubmit="return confirm(\'vuoi eliminare questo Allegato ?\')">
                    <input type="hidden" name="id" value="' . $f->id . '">
                    <input style="width:30%;float:left;margin-top:5px;" type="submit" name="elimina_allegato2" value="Elimina" class="btn btn-danger btn-sm">
                </form>';

            } else {
                $object['5'] .= '<br><a style="width:100%;margin-top:5px;" onclick="aggiungi_allegato2(' . $f->id . ')"  class="btn btn-success btn-sm">Allegato2</a>';
            }


            $object['6'] = $background;

            array_push($jsonoutput['aaData'], $object);

        }

        echo json_encode($jsonoutput);

    }






    /* public function lista_fatture($anno) {

        $this->is_loggato();
       $utente = session()->get('utente');

        $ordering = ' order by data DESC,numero DESC, anno DESC';

        $where = array();
        $where_string = '';

        if (isset($_GET['iSortCol_0'])) {

            if ($_GET['iSortCol_0'] == 1) $ordering = 'ORDER BY id ' . $_GET['sSortDir_0'];
            //if ($_GET['iSortCol_0'] == 2) $ordering = 'ORDER BY p.data_prima_notte ' . $_GET['sSortDir_0'];
            //if ($_GET['iSortCol_0'] == 3) $ordering = 'ORDER BY p.data_ultima_notte ' . $_GET['sSortDir_0'];
            //if ($_GET['iSortCol_0'] == 4) $ordering = 'ORDER BY p.nominativo ' . $_GET['sSortDir_0'];


        }


        array_push($where, 'fatture_testata.tipologia_documento = "'.$_GET['td'].'"');

        array_push($where, '1 = 1');
        array_push($where, 'id_utente = '.$utente->id);
        array_push($where, 'YEAR(data) = '.$anno);

        if(is_numeric($_GET['sSearch'])) {
            if ($_GET['sSearch'] != '') array_push($where, ' (numero like  "%' . $_GET['sSearch'] . '%" or nominativo like  "%' . $_GET['sSearch'] . '%" or id IN (SELECT id_testata from fatture_righe where id_pratica = ' . $_GET['sSearch'] . '))');
        } else {

            if ($_GET['sSearch'] != '') array_push($where, ' (numero like  "%' . $_GET['sSearch'] . '%" or nominativo like  "%' . $_GET['sSearch'] . '%" )');
        }

        if (sizeof($where) > 0) {
            $i = 0;
            foreach ($where as $condition) {
                if ($i == 0)
                    $where_string .= ' where ';
                else
                    $where_string.= ' and ';

                $where_string.= $condition;

                $i++;
            }
        }

        $limit = '';
        if($_GET['iDisplayLength']  > 0) $limit = ' limit ' . $_GET['iDisplayStart'] . ',' . $_GET['iDisplayLength'] ;

        $fatture = DB::select('SELECT * from fatture_testata  '.$where_string.' ' . $ordering . ' '.$limit);
        $numero = DB::select('SELECT count(id) as numero from fatture_testata '.$where_string)[0]->numero;

        $jsonoutput = array(
            "iTotalRecords" => intval($numero),
            "iTotalDisplayRecords" => intval($numero),
            "aaData" => array()
        );

        foreach ($fatture as $f) {

            $f->righe = DB::select('SELECT * from fatture_righe where id_testata = '.$f->id);


            $tipologia = 'Fattura';
            if ($f->tipologia_documento == 'TD04') {
                $tipologia = 'Nota di Credito';
            }


            if($f->stato == 0) $background = 'rgba(231, 76, 60,0.2)';
            if($f->stato == 1) $background = 'rgba(243, 156, 18,0.2)';
            if($f->stato == 2) $background = 'rgba(231, 76, 60,0.2)';

            $object = array();
            $object['DT_RowId'] = $f->id;
            $object['0'] = $f->numero . '/' . $f->anno . '<br>' . $tipologia;


            $object['1'] = date('d/m/Y',strtotime($f->data));
            $object['2'] = $f->nominativo.'<br>CF:'.$f->cf.'<br>P.IVA: '.$f->piva.'<br>'.$f->indirizzo.'<br>'.$f->cap.' '.$f->citta.' ('.$f->provincia.') '.$f->nazione;



            if($f->stato == 2){

                $object['2'] .= '<br><br><b>'.$f->errore_testata.'</b>';

            }

            $object['3'] = 'SDI: '.$f->sdi.'<br>PEC: '.$f->pec;
            $object['4'] = 'Imponibile: &euro;'.$f->imponibile.'<br>Imposta: &euro;'.$f->imposta.'<br>Totale: &euro; '.($f->totale);

            $object['5'] = '
                <a style="float:left" onclick="modifica(' . $f->id . ')" class="btn btn-primary btn-sm">MODIFICA</a>
                <a style="float:left;margin-left:5px;" onclick="aggiungi_riga(' . $f->id . ')"  class="btn btn-success btn-sm">+ RIGA</a>
              
                <form method="post" onsubmit="return confirm(\'vuoi duplicare questa fattura ?\')" style="float:left;margin-left:5px;">
                    <input type="hidden" name="id" value="' . $f->id . '">
                    <input type="submit" name="duplica" value="DUPLICA" class="btn btn-primary btn-sm">
                </form> 
                
                 <a style="float:left;margin-left:5px;" onclick="nota_credito(' . $f->id . ')" class="btn btn-primary btn-sm">Crea Nota Credito</a>
              
              
                <a style="float:left;margin-left:5px;" onclick="mostra_righe('.$f->id.')" class="btn btn-primary btn-sm">ESPANDI</a>';

            if(sizeof($f->righe) > 0){
                $object['5'] .='
                <a style="float:left;margin-left:5px;" target="_blank" href="'.URL::asset('admin/scarica_xml/'.$f->id).'" class="btn btn-primary btn-sm">SCARICA</a>
                <a style="float:left;margin-left:5px;" target="_blank" href="'.URL::asset('admin/visualizza_xml/'.$f->id).'" class="btn btn-primary btn-sm">STAMPA XML</a>
                <a style="float:left;margin-left:5px;" target="_blank" href="'.URL::asset('admin/stampa/proforma_fattura/'.$f->id).'" class="btn btn-primary btn-sm">STAMPA</a>';


            }

            if($f->stato == 0 || $f->stato == 2) {

                $object['5'] .='    <form method="post" onsubmit="return confirm(\'vuoi inviare allo sdi ?\')" style="float:left;margin-left:5px;">
                    <input type="hidden" name="id" value="' . $f->id . '">
                    <input type="submit" name="invia_sdi" value="Invia SDI" class="btn btn-primary btn-sm">
                </form> ';

                $object['5'] .= '<form method="post" onsubmit="return confirm(\'vuoi eliminare questa fattura ?\')" style="float:left;margin-left:5px;">
                        <input type="hidden" name="id" value="' . $f->id . '">
                        <input type="submit" name="elimina" value="ELIMINA" class="btn btn-danger btn-sm">
                    </form>';

            }


            if($f->allegato != ''){
                $object['5'] .='<div style="clear:both"></div>
                <a style="width:70%;margin-top:5px;float:left;" target="_blank" href="'.URL::asset($f->allegato).'" class="btn btn-primary btn-sm">'.$f->nome_allegato.'</a>';

                $object['5'] .= '<form method="post" onsubmit="return confirm(\'vuoi eliminare questo Allegato ?\')" >
                    <input type="hidden" name="id" value="' . $f->id . '">
                    <input style="width:30%;float:left;margin-top:5px;" type="submit" name="elimina_allegato" value="Elimina" class="btn btn-danger btn-sm">
                </form>';

            } else {
                $object['5'] .= '<a style="width:100%;margin-top:5px;" onclick="aggiungi_allegato(' . $f->id . ')"  class="btn btn-success btn-sm">Allegato 1</a>';
            }

            if($f->allegato2 != ''){
                $object['5'] .='
                <a style="width:70%;margin-top:5px;float:left;" target="_blank" href="'.URL::asset($f->allegato2).'" class="btn btn-primary btn-sm">'.$f->nome_allegato2.'</a>';

                $object['5'] .= '<form method="post" onsubmit="return confirm(\'vuoi eliminare questo Allegato ?\')">
                    <input type="hidden" name="id" value="' . $f->id . '">
                    <input style="width:30%;float:left;margin-top:5px;" type="submit" name="elimina_allegato2" value="Elimina" class="btn btn-danger btn-sm">
                </form>';

            } else {
                $object['5'] .= '<br><a style="width:100%;margin-top:5px;" onclick="aggiungi_allegato2(' . $f->id . ')"  class="btn btn-success btn-sm">Allegato2</a>';
            }


            $object['6'] = $background;

            array_push($jsonoutput['aaData'], $object);

        }

        echo json_encode($jsonoutput);

    }*/

    
    /**
     * Verifica se l'utente è loggato
     * @return \Illuminate\Http\RedirectResponse
     */
    public function is_loggato(){
        if(!session()->has('utente')) return Redirect::to('admin/login')->send();
    }



    public function check_login(){

        if(session()->has('utente')) return true;
        else return false;
    }
}


<?php

namespace App\Http\Controllers;

use App\Imports\ArticoliImport;
use App\Imports\MagazzinoImport;
use App\Imports\BPImport;
use App\Imports\StoricoImport;
use App\Imports\VenditeImport;
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


class AnalisiController extends Controller {


    public function tipo_cliente($tipo_cliente,$mese,$anno,Request $request){
        $page = 'vendite';
        $descrizione = 'Vendite '.$tipo_cliente.' '.$mese.'/'.$anno;
        $vendite = DB::select('SELECT v.*,a.id as id_articolo,bp.id as id_cliente from vendite v LEFT JOIN articoli a ON a.codice = v.codice_articolo LEFT JOIN bp ON bp.codice = v.codice_bp where v.mese_emv = '.$mese.' and v.anno_emv = '.$anno.' and v.tipo_cliente = "'.$tipo_cliente.'"');
        return View::make('admin.index',compact('page','vendite','descrizione'));
    }


}

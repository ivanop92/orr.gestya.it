<?php

namespace App\Http\Controllers;

use App\Exports\SearchResultExport;

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
use Illuminate\Support\Facades\URL;


class TecnopackController extends Controller {


    public function magazzino($token,Request $request){

        if($token == 'sajklhdalskjdklsa'){


            return View::make('tecnopack.magazzino');

        }

    }

    public function gridhandler(Request $request){

        $dati = $request->all();

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'GET' && $_GET['action'] === 'load') {
            // Carica tutti i dati della griglia
            try {


                $celle = DB::select('SELECT row_index, col_index, cell_text, cell_color FROM celle_tecnopack');
                echo json_encode($celle);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Errore nel caricamento dei dati']);
            }
        } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Decodifica i dati JSON ricevuti
            $data = json_decode(file_get_contents('php://input'), true);

            if ($dati['action'] === 'update') {
                try {

                    unset($dati['action']);

                    DB::table('celle_tecnopack')->where('row_index',$dati['row_index'])->where('col_index',$dati['col_index'])->update($dati);
                    echo json_encode(['success' => true]);
                } catch (PDOException $e) {
                    http_response_code(500);
                    echo json_encode(['error' => 'Errore nel salvataggio dei dati']);
                }
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Richiesta non valida']);
        }
    }


}

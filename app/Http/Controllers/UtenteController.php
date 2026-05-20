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
use App\Imports\ScadenziarioImport;

class UtenteController extends Controller{


    public function index(Request $request){

        $this->is_loggato();
        $utente = session('utente');
        $anno = session('anno');
        $page = 'index';


        $fatture_da_registrare = DB::table('dotes')->where('id_azienda', $utente->id_azienda)->where('fattura_in_ingresso', 1)->where('da_registrare', 1)->count();

        $fatture_scartate = DB::table('dotes')->where('id_azienda', $utente->id_azienda)->where('cd_do','FTV')->where('stato',2)->get();

        $pagamenti_da_effettuare = DB::select("
    SELECT s.*, 
           d.cd_do, d.numero_doc, d.data_doc, d.ragione_sociale_fatturazione,
           DATEDIFF(CURDATE(), s.data_scadenza) as giorni_scaduto,
           a.nome as agente_nome, a.cognome as agente_cognome
    FROM scadenziario s
    LEFT JOIN dotes d ON d.id = s.id_dotes
    LEFT JOIN utenti a ON a.id = s.id_agente
    WHERE s.id_azienda = ?
    AND s.tipo_movimento = 'uscita'
    AND s.importo_pagato < s.importo
    ORDER BY s.data_scadenza ASC ", [$utente->id_azienda]);

        $incassi_da_ricevere = DB::select("SELECT s.*, d.cd_do, d.numero_doc, d.data_doc, d.ragione_sociale_fatturazione, DATEDIFF(CURDATE(), s.data_scadenza) as giorni_scaduto FROM scadenziario s LEFT JOIN dotes d ON d.id = s.id_dotes WHERE s.id_azienda = ? AND s.tipo_movimento = 'entrata' AND s.importo_pagato < s.importo ORDER BY s.data_scadenza ASC ", [$utente->id_azienda]);
        $fatturato_mensile = DB::select('SELECT MONTH(data_doc) AS mese, SUM(totale) AS totale_tot FROM dotes WHERE cd_do = "FTV" AND YEAR(data_doc) = ? AND id_azienda = ? GROUP BY MONTH(data_doc) ORDER BY MONTH(data_doc)', [$anno, $utente->id_azienda]);
        $fatturato_totale = DB::table('dotes')->where('cd_do', 'FTV')->whereYear('data_doc', $anno)->where('id_azienda', $utente->id_azienda)->sum('totale');
        $note_credito_totale = DB::table('dotes')->where('cd_do', 'NC')->whereYear('data_doc', $anno)->where('id_azienda', $utente->id_azienda)->sum('totale');
        $fatturato_totale = $fatturato_totale - $note_credito_totale;
        $totale_costi = DB::table('dotes')->where('cd_do', 'FTI')->whereYear('data_doc', $anno)->where('id_azienda', $utente->id_azienda)->sum('totale');
        $utile_impresa = $fatturato_totale - $totale_costi;
        $indice_redditivita = $fatturato_totale > 0 ? ($utile_impresa / $fatturato_totale) * 100 : 0;
        $totale_da_pagare = DB::select('SELECT ifnull(SUM(importo - importo_pagato),0) AS totale FROM scadenziario WHERE id_azienda = '.$utente->id_azienda.' and tipo_movimento = "uscita" AND importo_pagato < importo')[0]->totale;
        $totale_da_incassare = DB::select('SELECT ifnull(SUM(importo - importo_pagato),0) AS totale FROM scadenziario WHERE id_azienda = '.$utente->id_azienda.' and tipo_movimento = "entrata" AND importo_pagato < importo')[0]->totale;
        $crediti_clienti = $totale_da_incassare;
        $debiti_fornitori = $totale_da_pagare;
        $anno_precedente = $anno - 1;
        $fatturato_anno_precedente = DB::table('dotes')->where('cd_do', 'FTV')->whereYear('data_doc', $anno_precedente)->where('id_azienda', $utente->id_azienda)->sum('totale');
        $variazione_fatturato_perc = $fatturato_anno_precedente > 0 ? (($fatturato_totale - $fatturato_anno_precedente) / $fatturato_anno_precedente) * 100 : 0;
        $costi_anno_precedente = DB::table('dotes')->where('cd_do', 'FTI')->whereYear('data', $anno_precedente)->where('id_azienda', $utente->id_azienda)->sum('totale');
        $variazione_costi_perc = $costi_anno_precedente > 0 ? (($totale_costi - $costi_anno_precedente) / $costi_anno_precedente) * 100 : 0;

        $utile_anno_precedente = $fatturato_anno_precedente - $costi_anno_precedente;$variazione_utile_perc = $utile_anno_precedente > 0 ? (($utile_impresa - $utile_anno_precedente) / $utile_anno_precedente) * 100 : 0;
        return View::make('utente.index', compact('page', 'utente','fatture_da_registrare','fatture_scartate','pagamenti_da_effettuare', 'incassi_da_ricevere', 'totale_da_pagare', 'totale_da_incassare',  'fatturato_mensile', 'fatturato_totale', 'totale_costi', 'utile_impresa', 'indice_redditivita', 'crediti_clienti', 'debiti_fornitori', 'anno', 'variazione_fatturato_perc', 'variazione_costi_perc', 'variazione_utile_perc'));

    }

    public function clienti(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if(isset($dati['aggiungi'])){
            unset($dati['aggiungi']);
            $dati['id_tipologia'] = 2;
            $dati['id_azienda'] = $utente->id_azienda;
            $dati['esportatore_abituale'] = isset($dati['esportatore_abituale']) ? 1 : 0;

            // Se l'utente è un agente (id_tipologia = 1), imposta automaticamente l'id_agente
            if($utente->id_tipologia == 1) {
                $dati['id_agente'] = $utente->id;
            }

            // Gestione dell'immagine
            if($_FILES['immagine']['name'] != '') {
                $pathinfo = pathinfo($_FILES['immagine']['name']);
                $nome = Str::random(20);
                $target = 'immagini/clienti/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['immagine']['tmp_name'], $target);
                $dati['immagine'] = $target;
            } else {
                // Immagine di default se non viene caricata
                $dati['immagine'] = '/default/assets/images/users/user-dummy-img.jpg';
            }

            // Generazione codice cliente progressivo
            $lastCounter = DB::table('clienti')->where('id_azienda', $utente->id_azienda)->max('cd_cf');

            // Estrai solo il numero dal codice cliente (rimuovendo 'C')
            if ($lastCounter) {
                $counterCliente = (int) substr($lastCounter, 1);
            } else {
                $counterCliente = 0; // Inizia da zero se non esiste alcun cliente
            }

            $counterCliente++;
            $counterCliente = str_pad($counterCliente, 7, '0', STR_PAD_LEFT);
            $dati['cd_cf'] = 'C'.$counterCliente;

            // Genera token casuale per l'area bandi
            $dati['token_utente_per_bando'] = Str::random(20);

            // Gestione del codice ATECO e sezione
            $dati['id_sezione'] = 0;
            if (isset($dati['ateco_codice']) && !empty($dati['ateco_codice'])) {
                $ateco = DB::select('SELECT * from ateco_codici where codice = ?', [$dati['ateco_codice']]);
                if(sizeof($ateco) > 0){
                    $dati['id_sezione'] = $ateco[0]->id_sezione;
                }
            }

            // Inserimento del cliente nel database
            DB::table('clienti')->insert($dati);
            return Redirect::to('utente/clienti')->with('success', 'Cliente aggiunto con successo');
        }

        if(isset($dati['modifica'])){
            unset($dati['modifica']);
            $dati['esportatore_abituale'] = isset($dati['esportatore_abituale']) ? 1 : 0;

            // Gestione dell'immagine
            if($_FILES['immagine']['name'] != ''){
                $pathinfo = pathinfo($_FILES['immagine']['name']);
                $nome = Str::random(20);
                $target = 'immagini/clienti/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['immagine']['tmp_name'], $target);
                $dati['immagine'] = $target;
            }

            // Gestione del codice ATECO e sezione
            $dati['id_sezione'] = 0;
            if (isset($dati['ateco_codice']) && !empty($dati['ateco_codice'])) {
                $ateco = DB::select('SELECT * from ateco_codici where codice = ?', [$dati['ateco_codice']]);
                if(sizeof($ateco) > 0){
                    $dati['id_sezione'] = $ateco[0]->id_sezione;
                }
            }

            // Aggiorna il cliente nel database
            DB::table('clienti')
                ->where('id', $dati['id'])
                ->where('id_azienda', $utente->id_azienda) // Filtra per l'azienda corrente
                ->update($dati);

            return Redirect::to('utente/clienti')->with('success', 'Cliente modificato con successo');
        }

        if(isset($dati['elimina'])){
            unset($dati['elimina']);

            // Elimina il cliente dal database
            DB::table('clienti')
                ->where('id', $dati['id'])
                ->where('id_azienda', $utente->id_azienda) // Filtra per l'azienda corrente
                ->delete();

            return Redirect::to('utente/clienti')->with('success', 'Cliente eliminato con successo');
        }

        if(isset($dati['import_excel'])) {
            // Qui gestiresti l'importazione da Excel se necessario
            // Da implementare in base alle tue esigenze
        }

        $page = 'clienti';

        // Query per recuperare i clienti con le loro informazioni
        $clienti = DB::select('
        SELECT c.*, a.descrizione as sezione 
        FROM clienti c 
        LEFT JOIN ateco_sezioni a ON a.id = c.id_sezione 
        WHERE c.id_tipologia = 2 
        AND c.id_azienda = ? 
        ORDER BY c.ragione_sociale ASC',
            [$utente->id_azienda]
        );

        // Se l'utente è un agente, mostra solo i suoi clienti
        if($utente->id_tipologia == 1) {
            $clienti = DB::select('
            SELECT c.*, a.descrizione as sezione 
            FROM clienti c 
            LEFT JOIN ateco_sezioni a ON a.id = c.id_sezione 
            WHERE c.id_tipologia = 2 
            AND c.id_azienda = ? 
            AND c.id_agente = ?
            ORDER BY c.ragione_sociale ASC',
                [$utente->id_azienda, $utente->id]
            );
        }

        // Recupera gli agenti per il dropdown nel form
        $agenti = DB::select('
        SELECT id, nome, cognome 
        FROM utenti 
        WHERE id_tipologia = 1 
        AND id_azienda = ? 
        ORDER BY cognome, nome',
            [$utente->id_azienda]
        );

        return View::make('utente.clienti', compact('page', 'utente', 'clienti', 'agenti'));
    }

    public function estrattoCliente(Request $request, $id)
    {
        $this->is_loggato();
        $utente = session('utente');

        $documenti = $request->get('documenti', 0);
        $scadenze = $request->get('scadenze', 0);
        $dataDa = $request->get('data_da');
        $dataA = $request->get('data_a');

        // Verifica che il cliente appartenga all'azienda
        $cliente = DB::table('clienti')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if(!$cliente) {
            return redirect()->back()->with('error', 'Cliente non trovato');
        }

        // Prepara i dati per l'Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheetIndex = 0;

        // --- FOGLIO DOCUMENTI ---
        if($documenti) {
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Documenti');

            // Query documenti - CORRETTO: data_doc invece di data
            $queryDocumenti = "
            SELECT d.*, do.descrizione as tipo_doc_desc
            FROM dotes d 
            LEFT JOIN do ON do.cd_do = d.cd_do AND do.id_azienda = d.id_azienda
            WHERE d.id_cliente = ? 
            AND d.id_azienda = ?
        ";
            $params = [$id, $utente->id_azienda];

            if($dataDa) {
                $queryDocumenti .= " AND d.data_doc >= ?";
                $params[] = $dataDa;
            }
            if($dataA) {
                $queryDocumenti .= " AND d.data_doc <= ?";
                $params[] = $dataA;
            }

            $queryDocumenti .= " ORDER BY d.data_doc DESC";
            $documentiData = DB::select($queryDocumenti, $params);

            // Intestazioni
            $headers = ['ID', 'Tipo Documento', 'Codice Doc', 'Numero Doc', 'Data', 'Imponibile', 'Imposta', 'Totale', 'Stato'];
            $col = 'A';
            foreach($headers as $header) {
                $sheet->setCellValue($col.'1', $header);
                $sheet->getStyle($col.'1')->getFont()->setBold(true);
                $sheet->getStyle($col.'1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
                $sheet->getStyle($col.'1')->getFont()->getColor()->setRGB('FFFFFF');
                $col++;
            }

            // Dati
            $row = 2;
            foreach($documentiData as $doc) {
                $sheet->setCellValue('A'.$row, $doc->id);
                $sheet->setCellValue('B'.$row, $doc->tipo_doc_desc ?? '');
                $sheet->setCellValue('C'.$row, $doc->cd_do ?? '');
                $sheet->setCellValue('D'.$row, $doc->numero_doc ?? '');
                $sheet->setCellValue('E'.$row, $doc->data_doc ?? '');
                $sheet->setCellValue('F'.$row, $doc->imponibile ?? 0);
                $sheet->setCellValue('G'.$row, $doc->imposta ?? 0);
                $sheet->setCellValue('H'.$row, $doc->totale ?? 0);
                $sheet->setCellValue('I'.$row, $doc->stato ?? '');
                $row++;
            }

            // Auto-dimensiona le colonne
            foreach(range('A','I') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }

            $sheetIndex++;
        }

        // --- FOGLIO SCADENZE ---
        if($scadenze) {
            if($sheetIndex > 0) {
                $sheet = $spreadsheet->createSheet();
            } else {
                $sheet = $spreadsheet->getActiveSheet();
            }
            $sheet->setTitle('Scadenze');

            // Query scadenze
            $queryScadenze = "
            SELECT s.*, d.numero_doc, d.data_doc, d.cd_do
            FROM scadenziario s 
            LEFT JOIN dotes d ON d.id = s.id_dotes
            WHERE s.id_cliente = ? 
            AND s.id_azienda = ?
        ";
            $params = [$id, $utente->id_azienda];

            if($dataDa) {
                $queryScadenze .= " AND s.data_scadenza >= ?";
                $params[] = $dataDa;
            }
            if($dataA) {
                $queryScadenze .= " AND s.data_scadenza <= ?";
                $params[] = $dataA;
            }

            $queryScadenze .= " ORDER BY s.data_scadenza ASC";
            $scadenzeData = DB::select($queryScadenze, $params);

            // Intestazioni
            $headers = ['ID', 'Data Scadenza', 'Importo', 'Importo Pagato', 'Residuo', 'Tipo Movimento', 'Cod. Documento', 'Num. Documento', 'Data Documento'];
            $col = 'A';
            foreach($headers as $header) {
                $sheet->setCellValue($col.'1', $header);
                $sheet->getStyle($col.'1')->getFont()->setBold(true);
                $sheet->getStyle($col.'1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('ED7D31');
                $sheet->getStyle($col.'1')->getFont()->getColor()->setRGB('FFFFFF');
                $col++;
            }

            // Dati
            $row = 2;
            foreach($scadenzeData as $scad) {
                $importo = $scad->importo ?? 0;
                $importoPagato = $scad->importo_pagato ?? 0;
                $residuo = $importo - $importoPagato;

                $sheet->setCellValue('A'.$row, $scad->id);
                $sheet->setCellValue('B'.$row, $scad->data_scadenza ?? '');
                $sheet->setCellValue('C'.$row, $importo);
                $sheet->setCellValue('D'.$row, $importoPagato);
                $sheet->setCellValue('E'.$row, $residuo);
                $sheet->setCellValue('F'.$row, $scad->tipo_movimento ?? '');
                $sheet->setCellValue('G'.$row, $scad->cd_do ?? '');
                $sheet->setCellValue('H'.$row, $scad->numero_doc ?? '');
                $sheet->setCellValue('I'.$row, $scad->data_doc ?? '');
                $row++;
            }

            // Auto-dimensiona le colonne
            foreach(range('A','I') as $columnID) {
                $sheet->getColumnDimension($columnID)->setAutoSize(true);
            }
        }

        // Imposta il primo foglio come attivo
        $spreadsheet->setActiveSheetIndex(0);

        // Genera il file Excel
        $nomeFile = 'Estratto_' . Str::slug($cliente->ragione_sociale) . '_' . date('Y-m-d') . '.xlsx';

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$nomeFile.'"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }

    public function esporta_clienti()
    {
        $this->is_loggato();
        $utente = session('utente');

        // Query per recuperare i clienti
        $query = 'SELECT c.*, a.descrizione as sezione_descrizione 
              FROM clienti c 
              LEFT JOIN ateco_sezioni a ON a.id = c.id_sezione 
              WHERE c.id_tipologia = 2 AND c.id_azienda = ?';
        $params = [$utente->id_azienda];

        // Se l'utente è un agente, mostra solo i suoi clienti
        if($utente->id_tipologia == 1) {
            $query .= ' AND c.id_agente = ?';
            $params[] = $utente->id;
        }

        $query .= ' ORDER BY c.ragione_sociale ASC';
        $clienti = DB::select($query, $params);

        // Recupera gli agenti per mappare i nomi
        $agenti = DB::select('SELECT id, nome, cognome FROM utenti WHERE id_tipologia = 1 AND id_azienda = ?', [$utente->id_azienda]);
        $agentiMap = [];
        foreach($agenti as $a) {
            $agentiMap[$a->id] = $a->nome . ' ' . $a->cognome;
        }

        // Crea il file Excel con PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Clienti');

        // Header
        $headers = [
            'A1' => 'Codice Cliente',
            'B1' => 'Ragione Sociale',
            'C1' => 'Partita IVA',
            'D1' => 'Codice Fiscale',
            'E1' => 'CCIAA',
            'F1' => 'REA',
            'G1' => 'Indirizzo',
            'H1' => 'CAP',
            'I1' => 'Comune',
            'J1' => 'Provincia',
            'K1' => 'Regione',
            'L1' => 'Ateco Codice',
            'M1' => 'Ateco Descrizione',
            'N1' => 'Sezione',
            'O1' => 'Grandezza Azienda',
            'P1' => 'Fatturato',
            'Q1' => 'Dipendenti',
            'R1' => 'Email',
            'S1' => 'Telefono',
            'T1' => 'PEC',
            'U1' => 'SDI',
            'V1' => 'Mail Fatture',
            'W1' => 'Mail Lead',
            'X1' => 'Referente',
            'Y1' => 'Telefono Referente',
            'Z1' => 'Esigibilità IVA',
            'AA1' => 'Agente',
        ];

        foreach($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }

        // Stile header
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4CAF50']],
        ];
        $sheet->getStyle('A1:AA1')->applyFromArray($headerStyle);

        // Dati
        $row = 2;
        foreach($clienti as $c) {
            $grandezza = '';
            switch($c->grandezza_azienda) {
                case 0: $grandezza = 'MICRO'; break;
                case 1: $grandezza = 'PICCOLA'; break;
                case 2: $grandezza = 'MEDIA'; break;
                case 3: $grandezza = 'GRANDE'; break;
            }

            $esigibilita = '';
            switch($c->esigibilita_iva ?? '') {
                case 'I': $esigibilita = 'Immediata'; break;
                case 'D': $esigibilita = 'Differita'; break;
                case 'S': $esigibilita = 'Split Payment'; break;
            }

            $sheet->setCellValue('A' . $row, $c->cd_cf ?? '');
            $sheet->setCellValue('B' . $row, $c->ragione_sociale ?? '');
            $sheet->setCellValue('C' . $row, $c->piva ?? '');
            $sheet->setCellValue('D' . $row, $c->cf ?? '');
            $sheet->setCellValue('E' . $row, $c->cciaa ?? '');
            $sheet->setCellValue('F' . $row, $c->rea ?? '');
            $sheet->setCellValue('G' . $row, $c->indirizzo ?? '');
            $sheet->setCellValue('H' . $row, $c->cap ?? '');
            $sheet->setCellValue('I' . $row, $c->comune ?? '');
            $sheet->setCellValue('J' . $row, $c->provincia ?? '');
            $sheet->setCellValue('K' . $row, $c->regione ?? '');
            $sheet->setCellValue('L' . $row, $c->ateco_codice ?? '');
            $sheet->setCellValue('M' . $row, $c->ateco_descrizione ?? '');
            $sheet->setCellValue('N' . $row, $c->sezione_descrizione ?? '');
            $sheet->setCellValue('O' . $row, $grandezza);
            $sheet->setCellValue('P' . $row, $c->fatturato ?? '');
            $sheet->setCellValue('Q' . $row, $c->dipendenti ?? '');
            $sheet->setCellValue('R' . $row, $c->email ?? '');
            $sheet->setCellValue('S' . $row, $c->telefono ?? '');
            $sheet->setCellValue('T' . $row, $c->pec ?? '');
            $sheet->setCellValue('U' . $row, $c->sdi ?? '');
            $sheet->setCellValue('V' . $row, $c->mail_recapito ?? '');
            $sheet->setCellValue('W' . $row, $c->mail_leads ?? '');
            $sheet->setCellValue('X' . $row, $c->referente ?? '');
            $sheet->setCellValue('Y' . $row, $c->telefono_referente ?? '');
            $sheet->setCellValue('Z' . $row, $esigibilita);
            $sheet->setCellValue('AA' . $row, $agentiMap[$c->id_agente] ?? '');

            $row++;
        }

        // Auto-size colonne
        foreach(range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        $sheet->getColumnDimension('AA')->setAutoSize(true);

        // Output
        $filename = 'clienti_' . date('Y-m-d_His') . '.xlsx';

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function dettaglio_cliente($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Verifica che il cliente esista e sia associato all'azienda dell'utente
        $cliente = DB::table('clienti')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$cliente) {
            return redirect()->to('utente/clienti')->with('error', 'Cliente non trovato');
        }

        // Se l'utente è un agente, verifica che il cliente sia associato a lui
        if ($utente->id_tipologia == 1 && $cliente->id_agente != $utente->id) {
            return redirect()->to('utente/clienti')->with('error', 'Non hai i permessi per visualizzare questo cliente');
        }

        // Recupera le informazioni aggiuntive
        $sezione = DB::table('ateco_sezioni')
            ->where('id', $cliente->id_sezione)
            ->first();

        $agente = null;
        if ($cliente->id_agente) {
            $agente = DB::table('utenti')
                ->where('id', $cliente->id_agente)
                ->first();
        }

        // Recupera i documenti del cliente
        $documenti = DB::table('dotes')
            ->where('id_azienda', $utente->id_azienda)
            ->where('cd_cf', $cliente->cd_cf)  // Usa il codice cliente per trovare i documenti
            ->orderBy('data_doc', 'desc')
            ->get();

        // Calcola il fatturato del cliente
        $fatturato_totale = DB::table('dotes')
            ->where('id_azienda', $utente->id_azienda)
            ->where('cd_cf', $cliente->cd_cf)
            ->where('cd_do', 'FTV')  // Solo fatture
            ->sum('totale');

        // Recupera le scadenze del cliente
        $scadenze = DB::table('scadenziario')
            ->where('id_azienda', $utente->id_azienda)
            ->where('id_cliente', $cliente->id)
            ->orderBy('data_scadenza', 'desc')
            ->get();

        // Calcola le scadenze scadute e non pagate
        $scadenze_scadute = DB::table('scadenziario')
            ->where('id_azienda', $utente->id_azienda)
            ->where('id_cliente', $cliente->id)
            ->where('data_scadenza', '<', now())
            ->where('stato', '!=', 'pagato')
            ->sum('importo');

        // Recupera gli ordini aperti
        $ordini_aperti = DB::table('dotes')
            ->where('id_azienda', $utente->id_azienda)
            ->where('cd_cf', $cliente->cd_cf)
            ->where('cd_do', 'ORD')  // Solo ordini
            ->where('stato', 0)  // Ordini aperti
            ->get();

        // Gestione sedi
        if (isset($dati['aggiungi_sede'])) {
            DB::table('sedi')->insert([
                'id_azienda' => $utente->id_azienda,
                'tipo' => 'cliente',
                'id_riferimento' => $id,
                'nome' => $dati['nome_sede'],
                'indirizzo' => $dati['indirizzo_sede'] ?? null,
                'cap' => $dati['cap_sede'] ?? null,
                'comune' => $dati['comune_sede'] ?? null,
                'provincia' => $dati['provincia_sede'] ?? null,
                'telefono' => $dati['telefono_sede'] ?? null,
                'email' => $dati['email_sede'] ?? null,
                'note' => $dati['note_sede'] ?? null,
                'created_at' => now(),
            ]);
            return redirect()->back()->with('success', 'Sede aggiunta con successo');
        }

        if (isset($dati['modifica_sede'])) {
            DB::table('sedi')
                ->where('id', $dati['id_sede'])
                ->where('id_azienda', $utente->id_azienda)
                ->update([
                    'nome' => $dati['nome_sede'],
                    'indirizzo' => $dati['indirizzo_sede'] ?? null,
                    'cap' => $dati['cap_sede'] ?? null,
                    'comune' => $dati['comune_sede'] ?? null,
                    'provincia' => $dati['provincia_sede'] ?? null,
                    'telefono' => $dati['telefono_sede'] ?? null,
                    'email' => $dati['email_sede'] ?? null,
                    'note' => $dati['note_sede'] ?? null,
                    'updated_at' => now(),
                ]);
            return redirect()->back()->with('success', 'Sede aggiornata con successo');
        }

        if (isset($dati['elimina_sede'])) {
            DB::table('sedi')
                ->where('id', $dati['id_sede'])
                ->where('id_azienda', $utente->id_azienda)
                ->delete();
            return redirect()->back()->with('success', 'Sede eliminata');
        }

        // Gestione salvataggio note
        if (isset($dati['salva_note'])) {
            DB::table('clienti')
                ->where('id', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->update(['note' => $dati['note']]);

            return redirect()->back()->with('success', 'Note salvate con successo');
        }

        // Gestione creazione attività
        if (isset($dati['crea_attivita'])) {
            $attivita = [
                'id_cliente' => $id,
                'id_azienda' => $utente->id_azienda,
                'id_utente' => $utente->id,
                'titolo' => $dati['titolo'],
                'descrizione' => $dati['descrizione'],
                'data_scadenza' => $dati['data_scadenza'],
                'priorita' => $dati['priorita'],
                'created_at' => now()
            ];

            DB::table('attivita')->insert($attivita);

            return redirect()->back()->with('success', 'Attività creata con successo');
        }

        // Recupera le attività del cliente


        // Recupera gli ordini recenti
        $ordini_recenti = DB::table('dotes')
            ->where('id_azienda', $utente->id_azienda)
            ->where('cd_cf', $cliente->cd_cf)
            ->where('cd_do', 'ORD')
            ->orderBy('data_doc', 'desc')
            ->limit(5)
            ->get();

        // Recupera le fatture recenti
        $fatture_recenti = DB::table('dotes')
            ->where('id_azienda', $utente->id_azienda)
            ->where('cd_cf', $cliente->cd_cf)
            ->where('cd_do', 'FTV')
            ->orderBy('data_doc', 'desc')
            ->limit(5)
            ->get();

        // Calcola il fatturato per anno
        $fatturato_per_anno = DB::select('
        SELECT YEAR(data_doc) as anno, SUM(totale) as totale
        FROM dotes
        WHERE id_azienda = ?
        AND cd_cf = ?
        AND cd_do = "FTV"
        GROUP BY YEAR(data_doc)
        ORDER BY anno DESC
    ', [$utente->id_azienda, $cliente->cd_cf]);

        $page = 'clienti';

        // Carica le sedi del cliente
        $sedi = DB::table('sedi')
            ->where('id_azienda', $utente->id_azienda)
            ->where('tipo', 'cliente')
            ->where('id_riferimento', $id)
            ->orderBy('nome')
            ->get();

        return view('utente.dettaglio_cliente', compact(
            'page',
            'utente',
            'cliente',
            'sezione',
            'agente',
            'documenti',
            'fatturato_totale',
            'scadenze',
            'scadenze_scadute',
            'ordini_aperti',
            'ordini_recenti',
            'fatture_recenti',
            'fatturato_per_anno',
            'sedi'
        ));
    }



    public function commesse_attivita_dettaglio($id_commessa, $id_attivita, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Verifica che la commessa esista e appartenga all'azienda
        $commessa = DB::table('commesse')
            ->where('id', $id_commessa)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$commessa) {
            return Redirect::to('utente/commesse')->with('error', 'Commessa non trovata');
        }

        // Recupera l'attività specifica
        $attivita = DB::table('commesse_attivita')
            ->where('id', $id_attivita)
            ->where('id_commessa', $id_commessa)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$attivita) {
            return Redirect::to('utente/commesse/'.$id_commessa.'/attivita')->with('error', 'Attività non trovata');
        }

        // GESTIONE DELLE AZIONI

        // Avvio dell'attività
        if (isset($dati['start_attivita'])) {
            unset($dati['start_attivita']);

            DB::table('commesse_attivita')
                ->where('id', $id_attivita)
                ->where('id_azienda', $utente->id_azienda)
                ->update([
                    'data_inizio_effettiva' => now(),
                    'stato' => 'in_corso',
                    'updated_at' => now()
                ]);

            return Redirect::to('utente/commesse/'.$id_commessa.'/attivita/'.$id_attivita)
                ->with('success', 'Attività avviata con successo');
        }

        // Completamento dell'attività
        if (isset($dati['fine_attivita'])) {
            unset($dati['fine_attivita']);

            $update_data = [
                'data_fine_effettiva' => now(),
                'stato' => 'completata',
                'completamento' => 100,
                'updated_at' => now()
            ];

            // Aggiorna le note se fornite
            if (isset($dati['note']) && !empty($dati['note'])) {
                $update_data['note'] = $dati['note'];
            }

            DB::table('commesse_attivita')
                ->where('id', $id_attivita)
                ->where('id_azienda', $utente->id_azienda)
                ->update($update_data);

            return Redirect::to('utente/commesse/'.$id_commessa.'/attivita/'.$id_attivita)
                ->with('success', 'Attività completata con successo');
        }

        // Aggiornamento avanzamento attività
        if (isset($dati['aggiorna_attivita'])) {
            unset($dati['aggiorna_attivita']);

            $update_data = [
                'updated_at' => now()
            ];

            // Valida e aggiorna completamento
            if (isset($dati['completamento'])) {
                $completamento = max(0, min(99, (int)$dati['completamento'])); // Limita tra 0 e 99
                $update_data['completamento'] = $completamento;
            }

            // Aggiorna le note se fornite
            if (isset($dati['note'])) {
                $update_data['note'] = $dati['note'];
            }

            DB::table('commesse_attivita')
                ->where('id', $id_attivita)
                ->where('id_azienda', $utente->id_azienda)
                ->update($update_data);

            return Redirect::to('utente/commesse/'.$id_commessa.'/attivita/'.$id_attivita)
                ->with('success', 'Avanzamento attività aggiornato con successo');
        }

        // Modifica note attività (azione dedicata)
        if (isset($dati['modifica_note'])) {
            unset($dati['modifica_note']);

            DB::table('commesse_attivita')
                ->where('id', $id_attivita)
                ->where('id_azienda', $utente->id_azienda)
                ->update([
                    'note' => $dati['note'] ?? '',
                    'updated_at' => now()
                ]);

            return Redirect::to('utente/commesse/'.$id_commessa.'/attivita/'.$id_attivita)
                ->with('success', 'Note attività aggiornate con successo');
        }

        // Caricamento allegato
        if (isset($dati['carica_allegato'])) {
            unset($dati['carica_allegato']);

            if ($request->hasFile('allegato')) {
                $file = $request->file('allegato');
                $nome_originale = $file->getClientOriginalName();
                $estensione = $file->getClientOriginalExtension();
                $nome_file = time() . '_' . Str::random(10) . '.' . $estensione;
                $path = 'allegati/commesse_attivita/' . $nome_file;

                // Sposta il file
                $file->move(public_path('allegati/commesse_attivita'), $nome_file);

                // Salva nel database
                DB::table('commesse_attivita_allegati')->insert([
                    'id_attivita' => $id_attivita,
                    'id_azienda' => $utente->id_azienda,
                    'id_utente' => $utente->id,
                    'nome_originale' => $nome_originale,
                    'nome_file' => $nome_file,
                    'path_file' => $path,
                    'tipo_file' => $file->getMimeType(),
                    'dimensione' => $file->getSize(),
                    'tipo' => $dati['tipo'] ?? 'documento',
                    'descrizione' => $dati['descrizione'] ?? '',
                    'created_at' => now()
                ]);

                // Aggiorna le note dell'attività se fornite
                if (!empty($dati['nota_allegato'])) {
                    $note_attuali = $attivita->note ?? '';
                    $nuova_nota = $note_attuali . "\n\n[" . date('d/m/Y H:i') . "] Allegato caricato: " . $dati['nota_allegato'];

                    DB::table('commesse_attivita')
                        ->where('id', $id_attivita)
                        ->where('id_azienda', $utente->id_azienda)
                        ->update([
                            'note' => $nuova_nota,
                            'updated_at' => now()
                        ]);
                }

                return Redirect::to('utente/commesse/'.$id_commessa.'/attivita/'.$id_attivita)
                    ->with('success', 'Allegato caricato con successo');
            }

            return Redirect::to('utente/commesse/'.$id_commessa.'/attivita/'.$id_attivita)
                ->with('error', 'Errore nel caricamento dell\'allegato');
        }

        // Elimina allegato
        if (isset($dati['elimina_allegato'])) {
            $id_allegato = $dati['id_allegato'];

            $allegato = DB::table('commesse_attivita_allegati')
                ->where('id', $id_allegato)
                ->where('id_azienda', $utente->id_azienda)
                ->first();

            if ($allegato) {
                // Elimina il file fisico
                if (file_exists(public_path($allegato->path_file))) {
                    unlink(public_path($allegato->path_file));
                }

                // Elimina dal database
                DB::table('commesse_attivita_allegati')
                    ->where('id', $id_allegato)
                    ->where('id_azienda', $utente->id_azienda)
                    ->delete();

                return Redirect::to('utente/commesse/'.$id_commessa.'/attivita/'.$id_attivita)
                    ->with('success', 'Allegato eliminato con successo');
            }

            return Redirect::to('utente/commesse/'.$id_commessa.'/attivita/'.$id_attivita)
                ->with('error', 'Allegato non trovato');
        }

        // RECUPERO DATI PER LA VIEW

        // Ricarica l'attività aggiornata
        $attivita = DB::table('commesse_attivita')
            ->where('id', $id_attivita)
            ->where('id_commessa', $id_commessa)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        // Recupera il responsabile dell'attività, se presente
        $responsabile = null;
        if ($attivita->id_responsabile) {
            $responsabile = DB::table('utenti')
                ->where('id', $attivita->id_responsabile)
                ->where('id_azienda', $utente->id_azienda)
                ->first();
        }

        // Recupera gli allegati dell'attività con info sull'operatore
        $allegati = DB::select('
        SELECT 
            a.*, 
            u.nome as nome_operatore, 
            u.cognome as cognome_operatore 
        FROM commesse_attivita_allegati a
        LEFT JOIN utenti u ON u.id = a.id_utente
        WHERE a.id_attivita = ? 
        AND a.id_azienda = ?
        ORDER BY a.created_at DESC',
            [$id_attivita, $utente->id_azienda]
        );

        // Converti il risultato in una collezione per poter usare il metodo where()
        $allegati = collect($allegati);

        // Recupera le attività collegate (altre attività della stessa commessa)
        $attivita_collegate = DB::table('commesse_attivita')
            ->where('id_commessa', $id_commessa)
            ->where('id_azienda', $utente->id_azienda)
            ->where('id', '!=', $id_attivita) // Escludi l'attività corrente
            ->orderBy('data_inizio', 'asc')
            ->limit(5) // Limita a 5 attività collegate
            ->get();

        // Aggiungi informazioni sulla commessa all'attività per la view
        $attivita->codice_commessa = $commessa->codice_commessa;
        $attivita->commessa_descrizione = $commessa->descrizione;

        // Aggiungi informazioni sul responsabile all'attività
        if ($responsabile) {
            $attivita->nome_responsabile = $responsabile->nome;
            $attivita->cognome_responsabile = $responsabile->cognome;
        }

        return View::make('utente.commesse_attivita_dettaglio', compact(
            'utente', 'commessa', 'attivita', 'responsabile', 'allegati', 'attivita_collegate'
        ));
    }
    public function carica_allegato_attivita($id_commessa, $id_attivita, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Controlla se l'attività esiste
        $attivita = DB::table('commesse_attivita')
            ->where('id', $id_attivita)
            ->where('id_commessa', $id_commessa)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$attivita) {
            return redirect()->back()->with('error', 'Attività non trovata');
        }

        // Gestione upload file con il metodo che usi già
        if($_FILES['allegato']['name'] != ''){

            // Percorso di salvataggio
            $path_destinazione = 'uploads/commesse/attivita/' . $id_attivita . '/';

            // Crea la directory se non esiste
            if (!file_exists($path_destinazione)) {
                mkdir($path_destinazione, 0755, true);
            }

            $pathinfo = pathinfo($_FILES['allegato']['name']);
            $nome = Str::random(20);
            $nome_file = $nome . '.' . $pathinfo['extension'];
            $target = $path_destinazione . $nome_file;

            // Sposta il file
            if(move_uploaded_file($_FILES['allegato']['tmp_name'], $target)) {

                $tipo = isset($dati['tipo']) ? $dati['tipo'] : 'documento';
                $descrizione = isset($dati['descrizione']) ? $dati['descrizione'] : '';
                $nota_allegato = isset($dati['nota_allegato']) ? $dati['nota_allegato'] : '';

                // Salva nel database
                DB::table('commesse_attivita_allegati')->insert([
                    'id_attivita' => $id_attivita,
                    'id_azienda' => $utente->id_azienda,
                    'id_utente' => $utente->id,
                    'nome_originale' => $_FILES['allegato']['name'],
                    'nome_file' => $nome_file,
                    'path_file' => $target,
                    'tipo_file' => $_FILES['allegato']['type'],
                    'dimensione' => $_FILES['allegato']['size'],
                    'tipo' => $tipo,
                    'descrizione' => $descrizione,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                // Se c'è una nota da aggiungere all'attività
                if (!empty($nota_allegato)) {
                    $note_attuali = $attivita->note ? $attivita->note . "\n\n" : '';
                    $nuova_nota = $note_attuali . "[" . date('d/m/Y H:i') . "] " . $nota_allegato;

                    DB::table('commesse_attivita')
                        ->where('id', $id_attivita)
                        ->where('id_azienda', $utente->id_azienda)
                        ->update(['note' => $nuova_nota, 'updated_at' => now()]);
                }

                return redirect()->back()->with('success', 'Allegato caricato con successo');
            } else {
                return redirect()->back()->with('error', 'Errore durante il caricamento del file');
            }
        }

        return redirect()->back()->with('error', 'Nessun file selezionato');
    }

    public function elimina_allegato_attivita($id_commessa, $id_attivita, $id)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Trova l'allegato
        $allegato = DB::table('commesse_attivita_allegati')
            ->where('id', $id)
            ->where('id_attivita', $id_attivita)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$allegato) {
            return redirect()->back()->with('error', 'Allegato non trovato');
        }

        // Elimina il file fisico
        if (file_exists(public_path($allegato->path_file))) {
            unlink(public_path($allegato->path_file));
        }

        // Elimina dal database
        DB::table('commesse_attivita_allegati')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->delete();

        return redirect()->back()->with('success', 'Allegato eliminato con successo');
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
                return Redirect::to('utente/dipendenti');
            }
        }

        if (isset($dati['aggiungi_reparto'])) {
            unset($dati['aggiungi_reparto']);
            $dati['id_azienda'] = $utente->id_azienda;
            DB::table('reparti')->insert($dati);
            return Redirect::to('utente/dipendenti');
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
            return Redirect::to('utente/dipendenti');
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
            return Redirect::to('utente/dipendenti');
        }

        if (isset($dati['elimina'])) {
            unset($dati['elimina']);
            $path_da_eliminare = DB::table('utenti')->where('id_tipologia', 3)->where('id', $dati['id'])->where('id_azienda', $utente->id_azienda)->first();
            if (file_exists($path_da_eliminare->immagine)) {
                unlink($path_da_eliminare->immagine);
            }

            DB::table('utenti')->where('id', $dati['id'])->where('id_azienda', $utente->id_azienda)->delete();
            return Redirect::to('utente/dipendenti');
        }

        $page = 'dipendenti';
        $dipendenti = DB::table('utenti')->where('id_tipologia', 3)->where('id_azienda', $utente->id_azienda)->get();
        $reparti = DB::table('reparti')->where('id_azienda', $utente->id_azienda)->get();

        return View::make('utente.dipendenti', compact('page', 'utente', 'dipendenti', 'reparti'));
    }

    public function fornitori(Request $request) {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Determina la tabella da usare
        $usaClientTable = ($utente->id_azienda == 14);
        $tabella = $usaClientTable ? 'clienti' : 'fornitori';

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

            DB::table($tabella)->insert($dati);
            return Redirect::to('utente/fornitori');
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

            DB::table($tabella)->where('id', $dati['id'])->where('id_azienda', $utente->id_azienda)->update($dati);
            return Redirect::to('utente/fornitori');
        }

        if (isset($dati['elimina'])) {
            unset($dati['elimina']);
            DB::table('clienti')->where('id', $dati['id'])->where('id_azienda', $utente->id_azienda)->delete();
            return Redirect::to('utente/fornitori');
        }

        $page = 'index';
        // Filtra i fornitori per azienda
        $fornitori = DB::table($tabella)->where('id_tipologia', 1)->where('id_azienda', $utente->id_azienda)->get();

        return View::make('utente.fornitori', compact('page', 'utente', 'fornitori'));
    }
    public function dettaglio_fornitore($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Determina la tabella da usare
        $usaClientTable = ($utente->id_azienda == 14);
        $tabella = $usaClientTable ? 'clienti' : 'fornitori';

        $fornitore = DB::table($tabella)
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$fornitore) {
            return redirect()->to('utente/fornitori')->with('error', 'Fornitore non trovato');
        }

        // Gestione sedi
        if (isset($dati['aggiungi_sede'])) {
            DB::table('sedi')->insert([
                'id_azienda' => $utente->id_azienda,
                'tipo' => 'fornitore',
                'id_riferimento' => $id,
                'nome' => $dati['nome_sede'],
                'indirizzo' => $dati['indirizzo_sede'] ?? null,
                'cap' => $dati['cap_sede'] ?? null,
                'comune' => $dati['comune_sede'] ?? null,
                'provincia' => $dati['provincia_sede'] ?? null,
                'telefono' => $dati['telefono_sede'] ?? null,
                'email' => $dati['email_sede'] ?? null,
                'note' => $dati['note_sede'] ?? null,
                'created_at' => now(),
            ]);
            return redirect()->back()->with('success', 'Sede aggiunta con successo');
        }

        if (isset($dati['modifica_sede'])) {
            DB::table('sedi')
                ->where('id', $dati['id_sede'])
                ->where('id_azienda', $utente->id_azienda)
                ->update([
                    'nome' => $dati['nome_sede'],
                    'indirizzo' => $dati['indirizzo_sede'] ?? null,
                    'cap' => $dati['cap_sede'] ?? null,
                    'comune' => $dati['comune_sede'] ?? null,
                    'provincia' => $dati['provincia_sede'] ?? null,
                    'telefono' => $dati['telefono_sede'] ?? null,
                    'email' => $dati['email_sede'] ?? null,
                    'note' => $dati['note_sede'] ?? null,
                    'updated_at' => now(),
                ]);
            return redirect()->back()->with('success', 'Sede aggiornata con successo');
        }

        if (isset($dati['elimina_sede'])) {
            DB::table('sedi')
                ->where('id', $dati['id_sede'])
                ->where('id_azienda', $utente->id_azienda)
                ->delete();
            return redirect()->back()->with('success', 'Sede eliminata');
        }

        // Documenti del fornitore
        $documenti = DB::table('dotes')
            ->where('id_azienda', $utente->id_azienda)
            ->where('cd_cf', $fornitore->cd_cf ?? '')
            ->orderBy('data_doc', 'desc')
            ->get();

        // Sedi del fornitore
        $sedi = DB::table('sedi')
            ->where('id_azienda', $utente->id_azienda)
            ->where('tipo', 'fornitore')
            ->where('id_riferimento', $id)
            ->orderBy('nome')
            ->get();

        $page = 'fornitori';

        return view('utente.dettaglio_fornitore', compact(
            'page', 'utente', 'fornitore', 'documenti', 'sedi'
        ));
    }

    public function exportFornitoriCsv()
    {
        $this->is_loggato();
        $utente = session('utente');

        // Determina la tabella da usare
        $usaClientTable = ($utente->id_azienda == 14);
        $tabella = $usaClientTable ? 'clienti' : 'fornitori';

        // Recupera i fornitori
        $fornitori = DB::table($tabella)
            ->where('id_tipologia', 1)
            ->where('id_azienda', $utente->id_azienda)
            ->get();

        // Nome del file
        $filename = 'fornitori_' . date('Y-m-d_His') . '.csv';

        // Headers per il download
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        // Callback per generare il CSV
        $callback = function() use ($fornitori) {
            $file = fopen('php://output', 'w');

            // BOM per UTF-8 (per Excel)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Intestazioni colonne
            fputcsv($file, [
                'ID',
                'Ragione Sociale',
                'Nome',
                'Cognome',
                'P.IVA',
                'Codice Fiscale',
                'Email',
                'PEC',
                'Telefono',
                'Indirizzo',
                'CAP',
                'Comune',
                'Provincia',
                'Regione',
                'SDI',
                'CCIAA',
                'REA'
            ], ';'); // Uso punto e virgola per compatibilità Excel italiano

            // Dati
            foreach ($fornitori as $f) {
                fputcsv($file, [
                    $f->id ?? '',
                    $f->ragione_sociale ?? '',
                    $f->nome ?? '',
                    $f->cognome ?? '',
                    $f->piva ?? '',
                    $f->cf ?? '',
                    $f->email ?? '',
                    $f->pec ?? '',
                    $f->telefono ?? '',
                    $f->indirizzo ?? '',
                    $f->cap ?? '',
                    $f->comune ?? '',
                    $f->provincia ?? '',
                    $f->regione ?? '',
                    $f->sdi ?? '',
                    $f->cciaa ?? '',
                    $f->rea ?? ''
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function categorie_articoli(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // AGGIUNGI
        if (isset($dati['aggiungi'])) {
            unset($dati['aggiungi']);
            $dati['id_azienda'] = $utente->id_azienda;
            $dati['data_creazione'] = now();

            DB::table('categorie_articoli')->insert($dati);
            return Redirect::to('utente/categorie_articoli')->with('success', 'Categoria aggiunta con successo');
        }

        // MODIFICA
        if (isset($dati['modifica'])) {
            unset($dati['modifica']);
            $id = $dati['id'];
            unset($dati['id']);

            DB::table('categorie_articoli')
                ->where('id', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->update($dati);

            return Redirect::to('utente/categorie_articoli')->with('success', 'Categoria modificata con successo');
        }

        // ELIMINA
        if (isset($dati['elimina'])) {
            $id = $dati['id'];

            // Controlla se ci sono articoli associati
            $articoli_associati = DB::table('articoli')
                ->where('id_categoria', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->count();

            if ($articoli_associati > 0) {
                return Redirect::to('utente/categorie_articoli')->with('error', 'Impossibile eliminare: ci sono ' . $articoli_associati . ' articoli associati a questa categoria');
            }

            DB::table('categorie_articoli')
                ->where('id', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->delete();

            return Redirect::to('utente/categorie_articoli')->with('success', 'Categoria eliminata con successo');
        }

        $page = 'categorie_articoli';
        $categorie = DB::table('categorie_articoli')
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('nome')
            ->get();

        // Conta articoli per ogni categoria
        foreach ($categorie as $cat) {
            $cat->num_articoli = DB::table('articoli')
                ->where('id_categoria', $cat->id)
                ->where('id_azienda', $utente->id_azienda)
                ->count();
        }

        return View::make('utente.categorie_articoli', compact('page', 'utente', 'categorie'));
    }

    public function articoli(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if (isset($dati['upload_immagine_articolo'])) {
            if ($request->hasFile('immagine_articolo')) {
                $file = $request->file('immagine_articolo');
                $nome = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $target = 'immagini/articoli/' . $nome;
                $file->move(public_path('immagini/articoli'), $nome);

                // Elimina la vecchia immagine se esiste
                $vecchia = DB::table('articoli')->where('id', $dati['id_articolo'])->value('immagine');
                if ($vecchia && file_exists(public_path($vecchia))) {
                    unlink(public_path($vecchia));
                }

                DB::table('articoli')
                    ->where('id', $dati['id_articolo'])
                    ->where('id_azienda', $utente->id_azienda)
                    ->update(['immagine' => $target]);
            }
            return redirect()->back();
        }

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

            $dati['id_azienda'] = $utente->id_azienda;

            // Recupera il GTIN
            $gtin = str_pad($dati['codice_articolo'], 12, '0', STR_PAD_LEFT);
            $lotto = isset($dati['lotto']) ? str_pad($dati['lotto'], 5, '0', STR_PAD_LEFT) : '00000';
            $data_creazione = now()->format('ymd');
            $barcode = $gtin . $lotto . $data_creazione;

            $dati['data_creazione'] = now();
            $dati['barcode'] = $barcode;

            // NUOVO: Genera un token univoco per il QR code (solo per prodotti finiti)
            if ($dati['tipologia'] == 0) {
                $dati['qr_token'] = Str::random(32);
            }

            // Rimuoviamo le fasi dai dati prima di inserirli
            $datiPerInsert = array_filter($dati, function ($key) {
                return $key !== 'fasi';
            }, ARRAY_FILTER_USE_KEY);

            // Inserimento dell'articolo
            $id_articolo = DB::table('articoli')->insertGetId($datiPerInsert);

            // Inserimento delle fasi
            if (isset($dati['fasi']) && is_array($dati['fasi'])) {
                foreach ($dati['fasi'] as $id_fase) {
                    DB::table('fasi_articoli')->insert([
                        'id_azienda' => $utente->id_azienda,
                        'id_utente' => $utente->id,
                        'id_fase' => $id_fase,
                        'id_articolo' => $id_articolo,
                        'tempo_medio_minuti' => 0
                    ]);
                }
            }

            return redirect()->back();
        }

        if (isset($dati['modifica'])) {
            unset($dati['modifica']);
            unset($dati['tipo']);
            unset($dati['page']);
            unset($dati['search']);

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

            // Gestione delle fasi
            if (isset($dati['fasi'])) {
                // Ottieni le fasi attualmente associate
                $fasi_attuali = DB::table('fasi_articoli')
                    ->where('id_articolo', $dati['id'])
                    ->pluck('id_fase')
                    ->toArray();

                // Converti l'array delle nuove fasi in array se non lo è già
                $nuove_fasi = is_array($dati['fasi']) ? $dati['fasi'] : [$dati['fasi']];

                // Trova le fasi da aggiungere (presenti nelle nuove ma non nelle attuali)
                $fasi_da_aggiungere = array_diff($nuove_fasi, $fasi_attuali);

                // Trova le fasi da rimuovere (presenti nelle attuali ma non nelle nuove)
                $fasi_da_rimuovere = array_diff($fasi_attuali, $nuove_fasi);

                // Aggiungi le nuove fasi
                foreach ($fasi_da_aggiungere as $id_fase) {
                    DB::table('fasi_articoli')->insert([
                        'id_azienda' => $utente->id_azienda,
                        'id_utente' => $utente->id,
                        'id_fase' => $id_fase,
                        'id_articolo' => $dati['id'],
                        'tempo_medio_minuti' => 0
                    ]);
                }

                // Rimuovi le associazioni non più necessarie
                if (!empty($fasi_da_rimuovere)) {
                    // Prima rimuovi i materiali dalla distinta base per le fasi disassociate
                    DB::table('distinta_base')
                        ->where('id_articolo', $dati['id'])
                        ->whereIn('id_fase_articolo', $fasi_da_rimuovere)
                        ->delete();

                    // Poi rimuovi l'associazione fase-articolo
                    DB::table('fasi_articoli')
                        ->where('id_articolo', $dati['id'])
                        ->whereIn('id_fase', $fasi_da_rimuovere)
                        ->delete();
                }
            } else {
                // Se non ci sono fasi selezionate, rimuovi tutte le associazioni
                DB::table('distinta_base')
                    ->where('id_articolo', $dati['id'])
                    ->delete();

                DB::table('fasi_articoli')
                    ->where('id_articolo', $dati['id'])
                    ->delete();
            }

            return redirect()->back()->with('success', 'Articolo aggiornato con successo');
        }

        if (isset($dati['carica_materiale'])) {
            unset($dati['carica_materiale']);
            unset($dati['tipo']);

            $dati['id_azienda'] = $utente->id_azienda;
            DB::table('mgmov')->insertGetId($dati);
            DB::update('update articoli set giacenza = (select sum(qta) as giacenza from mgmov where id_articolo = ' . $dati['id_articolo'] . ') where id =' . $dati['id_articolo']);

            return redirect()->back();
        }

        if (isset($dati['scarica_materiale'])) {
            unset($dati['scarica_materiale']);
            unset($dati['tipo']);

            $dati['id_azienda'] = $utente->id_azienda;
            $dati['qta'] = $dati['qta'] * -1;
            $dati['sca'] = 1;
            DB::table('mgmov')->insertGetId($dati);

            DB::update('update articoli set giacenza = (select sum(qta) as giacenza from mgmov where id_articolo = ' . $dati['id_articolo'] . ') where id =' . $dati['id_articolo']);

            return redirect()->back();
        }

        if (isset($dati['rettifica_materiale'])) {
            unset($dati['rettifica_materiale']);
            unset($dati['tipo']);

            $giacenza = DB::select('select sum(qta) as giacenza from mgmov where id_mg = '.$dati['id_mg'].' and id_articolo = ' . $dati['id_articolo'])[0]->giacenza;

            $dati['id_azienda'] = $utente->id_azienda;
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

            $dati['id_azienda'] = $utente->id_azienda;
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
                            'id_fornitore' => !empty($dati['fornitore_db'][$id_fase][$posizione]) ? $dati['fornitore_db'][$id_fase][$posizione] : null,
                        ];

                        $insert_materiale['id_azienda'] = $utente->id_azienda;
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

        if (isset($dati['associa_cliente'])) {
            unset($dati['associa_cliente']);
            unset($dati['tipo']);

            DB::table('articoli')
                ->where('id', $dati['id_articolo'])
                ->where('id_azienda', $utente->id_azienda)
                ->update(['id_cliente' => $dati['id_cliente'] ?: null]);

            return redirect()->back()->with('success', 'Cliente associato con successo');
        }



        $page = 'index';

        // Parametri di paginazione e ricerca
        $per_page = 10; // Numero di elementi per pagina
        $current_page = $request->get('page', 1);
        $search = $request->get('search', '');
        $tipo = $request->query('tipo', 'prodotto_finito');

        // Determina la tipologia in base al tipo richiesto
        $tipologia_filter = 0; // Default: prodotto finito
        if ($tipo === 'materia_prima') {
            $tipologia_filter = 1;
        } elseif ($tipo === 'commerciale') {
            $tipologia_filter = 2;
        } elseif ($tipo === 'semilavorato') {
            $tipologia_filter = 3;
        }

        // Query base
        $query = DB::table('articoli')->where('articoli.id_azienda', $utente->id_azienda);

        // Filtro per tipologia
        $query->where('articoli.tipologia', $tipologia_filter);

        // Filtro di ricerca se presente
        if (!empty($search)) {
            $query->leftJoin('clienti', 'clienti.id', '=', 'articoli.id_cliente');
            $query->where(function($q) use ($search) {
                $q->where('articoli.codice_articolo', 'LIKE', '%' . $search . '%')
                    ->orWhere('articoli.titolo', 'LIKE', '%' . $search . '%')
                    ->orWhere('articoli.descrizione', 'LIKE', '%' . $search . '%')
                    ->orWhere('clienti.ragione_sociale', 'LIKE', '%' . $search . '%');
            });
            $query->select('articoli.*');
        }

        // Conta totale elementi per la paginazione
        $total_items = $query->count();
        $total_pages = ceil($total_items / $per_page);

        // Applica offset e limit per la paginazione
        $offset = ($current_page - 1) * $per_page;
        $articoli = $query->offset($offset)->limit($per_page)->get();

        foreach ($articoli as $a) {
            // Prima recuperiamo le righe della distinta base
            $distinta_items = DB::select('
            SELECT db.*, m.titolo as materiale, m.um, m.prezzo 
            FROM distinta_base db
            JOIN articoli m ON m.id = db.id_materiale
            WHERE db.id_articolo = ? 
            ORDER BY db.id_fase_articolo ASC',
                [$a->id]
            );

            // Inizializziamo l'array della distinta base
            $a->distinta_base = [];

            // Raggruppiamo per fase
            foreach($distinta_items as $item) {
                if(!isset($a->distinta_base[$item->id_fase_articolo])) {
                    $a->distinta_base[$item->id_fase_articolo] = [];
                }
                $a->distinta_base[$item->id_fase_articolo][] = $item;
            }

            // Le fasi associate rimangono come sono
            $a->fasi_associate = DB::table('fasi_articoli')
                ->where('id_articolo', $a->id)
                ->pluck('id_fase')
                ->toArray();
        }

        // Creiamo un array associativo per le fasi associate agli articoli
        $fasi_associate = [];
        foreach ($articoli as $articolo) {
            $fasi_associate[$articolo->id] = DB::table('fasi_articoli')
                ->join('fasi', 'fasi_articoli.id_fase', '=', 'fasi.id')
                ->where('fasi_articoli.id_articolo', $articolo->id)
                ->select('fasi.id', 'fasi.descrizione')
                ->get();

            // Per ogni fase, attacca l'array dei materiali di distinta base associati a quella fase
            foreach ($fasi_associate[$articolo->id] as $fase_obj) {
                $fase_obj->distinta_base = isset($articolo->distinta_base[$fase_obj->id])
                    ? array_values($articolo->distinta_base[$fase_obj->id])
                    : [];
            }
        }

        foreach ($articoli as $a) {
            $a->fasi_associate = DB::table('fasi_articoli')
                ->where('id_articolo', $a->id)
                ->pluck('id_fase')
                ->toArray();
        }

        $materiali = DB::table('articoli')->whereIn('tipologia', [1, 2, 3])->where('id_azienda', $utente->id_azienda)->orderBy('tipologia')->orderBy('titolo')->get();
        $magazzini = DB::table('mg')->where('id_azienda', $utente->id_azienda)->get();
        $fasi = DB::table('fasi')->where('id_azienda', $utente->id_azienda)->get();

        // Dati per la paginazione
        $pagination = [
            'current_page' => $current_page,
            'total_pages' => $total_pages,
            'total_items' => $total_items,
            'per_page' => $per_page,
            'has_prev' => $current_page > 1,
            'has_next' => $current_page < $total_pages,
            'prev_page' => $current_page > 1 ? $current_page - 1 : null,
            'next_page' => $current_page < $total_pages ? $current_page + 1 : null,
            'search' => $search
        ];

        $clienti = DB::table('clienti')->where('id_azienda', $utente->id_azienda)->get();
        $clienti_map = $clienti->keyBy('id');


        // Calcola il prossimo codice articolo per azienda 19
        $prossimo_codice_articolo = null;
        if ($utente->id_azienda == 19) {
            $ultimo_codice = DB::table('articoli')
                ->where('id_azienda', 19)
                ->where('codice_articolo', 'LIKE', 'ITS%')
                ->whereRaw('SUBSTRING(codice_articolo, 4) REGEXP "^[0-9]+$"')
                ->orderByRaw('CAST(SUBSTRING(codice_articolo, 4) AS UNSIGNED) DESC')
                ->value('codice_articolo');

            if ($ultimo_codice) {
                // Estrae la parte numerica e incrementa, mantenendo il padding con zeri
                $parte_numerica = substr($ultimo_codice, 3);
                $numero = (int) $parte_numerica;
                $prossimo_codice_articolo = 'ITS' . str_pad($numero + 1, strlen($parte_numerica), '0', STR_PAD_LEFT);
            } else {
                // Default se non ci sono articoli con prefisso ITS
                $prossimo_codice_articolo = 'ITS1407';
            }
        }

        // Fornitori per assegnazione lavorazioni esterne sui semilavorati in distinta base
        $usaClientTable = ($utente->id_azienda == 14);
        $tabella_fornitori = $usaClientTable ? 'clienti' : 'fornitori';
        $fornitori_db = DB::table($tabella_fornitori)->where('id_azienda', $utente->id_azienda)->where('id_tipologia', 1)->orderBy('ragione_sociale')->get();

        return View::make('utente.articoli', compact('page', 'utente', 'clienti', 'clienti_map', 'articoli', 'materiali', 'magazzini', 'fasi', 'fasi_associate', 'tipo', 'pagination', 'prossimo_codice_articolo', 'fornitori_db'));
    }


    /**
     * METODO DA AGGIUNGERE/SOSTITUIRE IN UtenteController.php
     *
     * Gestisce export per:
     * - FTV (Fatture in Uscita)
     * - FTI (Fatture in Ingresso)
     * - FTI con filtro TD04 (Note di Credito)
     */

    public function exportFattureDettaglio(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        $mese = $request->input('mese');
        $anno = $request->input('anno', session('anno'));
        $cd_do = $request->input('cd_do', 'FTV'); // FTV o FTI
        $filtro_nc = $request->input('filtro_nc', ''); // 'TD04' per note di credito

        if (empty($anno)) {
            $anno = date('Y');
        }

        // Query per le fatture
        $query = "SELECT d.* FROM dotes d 
              WHERE d.cd_do = ? 
              AND d.id_azienda = ? 
              AND d.da_registrare = 0";

        $params = [$cd_do, $utente->id_azienda];

        if ($mese && $mese != 0) {
            $query .= " AND MONTH(d.data_doc) = ?";
            $params[] = $mese;
        }

        if ($anno) {
            $query .= " AND YEAR(d.data_doc) = ?";
            $params[] = $anno;
        }

        // Filtro per Note di Credito (solo per FTI)
        if ($cd_do == 'FTI' && $filtro_nc == 'TD04') {
            $query .= " AND d.tipologia_documento = 'TD04'";
        } elseif ($cd_do == 'FTI' && $filtro_nc == 'fatture') {
            $query .= " AND (d.tipologia_documento IS NULL OR d.tipologia_documento != 'TD04')";
        }

        // Filtro agente se l'utente è un agente
        if ($utente->id_tipologia == 1) {
            $query .= " AND d.id_agente = ?";
            $params[] = $utente->id;
        }

        $query .= " ORDER BY d.data_doc DESC, d.numero_doc DESC";

        $fatture = DB::select($query, $params);

        // Crea il file Excel con PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Titolo foglio in base al tipo
        if ($cd_do == 'FTV') {
            $sheet->setTitle('Fatture Uscita');
            $tipoLabel = 'FATTURA USCITA';
        } elseif ($filtro_nc == 'TD04') {
            $sheet->setTitle('Note di Credito');
            $tipoLabel = 'NOTA DI CREDITO';
        } else {
            $sheet->setTitle('Fatture Ingresso');
            $tipoLabel = 'FATTURA INGRESSO';
        }

        // Stili
        $headerStyleDotes = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $cd_do == 'FTV' ? '4472C4' : ($filtro_nc == 'TD04' ? 'ED7D31' : '7030A0')]],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];

        $headerStyleDorig = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '70AD47']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];

        $rigaDotes = [
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $cd_do == 'FTV' ? 'D6DCE5' : ($filtro_nc == 'TD04' ? 'FCE4D6' : 'E2D1F0')]]
        ];

        $rigaDorig = [
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2EFDA']]
        ];

        $currentRow = 1;

        // Header principale DOTES
        $headerDotes = ['TIPO', 'ID', 'Numero Doc', 'Data Doc', 'Ragione Sociale', 'P.IVA',
            'Indirizzo', 'Comune', 'CAP', 'Tipologia Doc', 'Imponibile', 'Imposta',
            'Totale', 'Stato', 'Saldata', 'Modalità Pagamento'];

        $col = 1;
        foreach ($headerDotes as $header) {
            $sheet->setCellValue([$col, $currentRow], $header);
            $col++;
        }
        $sheet->getStyle("A{$currentRow}:P{$currentRow}")->applyFromArray($headerStyleDotes);
        $currentRow++;

        foreach ($fatture as $fattura) {
            // Scrivi riga dotes (testata fattura)
            $sheet->setCellValue([1, $currentRow], $tipoLabel);
            $sheet->setCellValue([2, $currentRow], $fattura->id);
            $sheet->setCellValue([3, $currentRow], $fattura->numero_doc);
            $sheet->setCellValue([4, $currentRow], $fattura->data_doc);
            $sheet->setCellValue([5, $currentRow], $fattura->ragione_sociale_fatturazione);
            $sheet->setCellValue([6, $currentRow], $fattura->partita_iva_fatturazione);
            $sheet->setCellValue([7, $currentRow], $fattura->indirizzo_fatturazione);
            $sheet->setCellValue([8, $currentRow], $fattura->comune_fatturazione);
            $sheet->setCellValue([9, $currentRow], $fattura->cap);
            $sheet->setCellValue([10, $currentRow], $fattura->tipologia_documento);
            $sheet->setCellValue([11, $currentRow], $fattura->imponibile);
            $sheet->setCellValue([12, $currentRow], $fattura->imposta);
            $sheet->setCellValue([13, $currentRow], $fattura->totale);
            $sheet->setCellValue([14, $currentRow], $fattura->stato == 1 ? 'Inviata' : ($fattura->stato == 0 ? 'Da inviare' : ($fattura->stato == 2 ? 'Rifiutata' : 'Sconosciuto')));
            $sheet->setCellValue([15, $currentRow], $fattura->saldata == 1 ? 'Sì' : 'No');
            $sheet->setCellValue([16, $currentRow], $fattura->modalita_pagamento);

            $sheet->getStyle("A{$currentRow}:P{$currentRow}")->applyFromArray($rigaDotes);
            $sheet->getStyle("K{$currentRow}:M{$currentRow}")->getNumberFormat()->setFormatCode('#,##0.00');

            $currentRow++;

            // Recupera le righe dorig collegate
            $righe = DB::select("SELECT * FROM dorig WHERE id_dotes = ? AND id_azienda = ? ORDER BY n_riga ASC",
                [$fattura->id, $utente->id_azienda]);

            if (count($righe) > 0) {
                // Header righe dorig
                $headerDorig = ['', 'ID Riga', 'Nome Prodotto', 'Descrizione', 'Qta', 'Prezzo Unitario',
                    'Prezzo Totale', 'IVA %', 'Imponibile', 'Imposta', 'UM', 'N. Riga'];

                $col = 1;
                foreach ($headerDorig as $header) {
                    $sheet->setCellValue([$col, $currentRow], $header);
                    $col++;
                }
                $sheet->getStyle("A{$currentRow}:L{$currentRow}")->applyFromArray($headerStyleDorig);
                $currentRow++;

                // Scrivi ogni riga dorig
                foreach ($righe as $riga) {
                    $sheet->setCellValue([1, $currentRow], '  → RIGA');
                    $sheet->setCellValue([2, $currentRow], $riga->id);
                    $sheet->setCellValue([3, $currentRow], $riga->nome_prodotto);
                    $sheet->setCellValue([4, $currentRow], $riga->descrizione);
                    $sheet->setCellValue([5, $currentRow], $riga->qta);
                    $sheet->setCellValue([6, $currentRow], $riga->prezzo_unitario);
                    $sheet->setCellValue([7, $currentRow], $riga->prezzo_totale);
                    $sheet->setCellValue([8, $currentRow], $riga->iva);
                    $sheet->setCellValue([9, $currentRow], $riga->imponibile);
                    $sheet->setCellValue([10, $currentRow], $riga->imposta);
                    $sheet->setCellValue([11, $currentRow], $riga->um);
                    $sheet->setCellValue([12, $currentRow], $riga->n_riga);

                    $sheet->getStyle("A{$currentRow}:L{$currentRow}")->applyFromArray($rigaDorig);
                    $sheet->getStyle("E{$currentRow}:J{$currentRow}")->getNumberFormat()->setFormatCode('#,##0.00');

                    $currentRow++;
                }
            }

            // Riga vuota tra fatture
            $currentRow++;
        }

        // Auto-dimensiona colonne
        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Nome file in base al tipo
        if ($cd_do == 'FTV') {
            $filePrefix = 'fatture_uscita';
        } elseif ($filtro_nc == 'TD04') {
            $filePrefix = 'note_credito';
        } else {
            $filePrefix = 'fatture_ingresso';
        }

        $fileName = $filePrefix . '_' . ($mese && $mese != 0 ? str_pad($mese, 2, '0', STR_PAD_LEFT) . '_' : '') . $anno . '.xlsx';

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        $tempFile = storage_path('app/' . $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    public function exportFattureUscita(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        $mese = $request->input('mese');
        $anno = $request->input('anno');
        $tipoDocumento = $request->input('tipo_documento', '');

        // Query per le fatture in uscita (FTV)
        $query = "SELECT d.* FROM dotes d 
              WHERE d.cd_do = 'FTV' 
              AND d.id_azienda = ? 
              AND d.da_registrare = 0";

        $params = [$utente->id_azienda];

        if ($mese) {
            $query .= " AND MONTH(d.data_doc) = ?";
            $params[] = $mese;
        }

        if ($anno) {
            $query .= " AND YEAR(d.data_doc) = ?";
            $params[] = $anno;
        }

        if ($tipoDocumento) {
            $query .= " AND d.tipologia_documento = ?";
            $params[] = $tipoDocumento;
        }

        $query .= " ORDER BY d.data_doc DESC, d.numero_doc DESC";

        $fatture = DB::select($query, $params);

        // Crea il file Excel con PhpSpreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Fatture Uscita');

        // Header per DOTES (testata fattura)
        $headerDotes = [
            'TIPO', 'ID', 'Numero Doc', 'Data Doc', 'Ragione Sociale', 'P.IVA',
            'Indirizzo', 'Comune', 'CAP', 'Tipologia Doc', 'Imponibile', 'Imposta',
            'Totale', 'Stato', 'Saldata', 'Modalità Pagamento'
        ];

        // Header per DORIG (righe fattura)
        $headerDorig = [
            'TIPO', 'ID Riga', 'Nome Prodotto', 'Descrizione', 'Qta', 'Prezzo Unitario',
            'Prezzo Totale', 'IVA %', 'Imponibile', 'Imposta', 'UM', 'N. Riga'
        ];

        // Stili
        $headerStyleDotes = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];

        $headerStyleDorig = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '70AD47']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
        ];

        $rowaDotes = [
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D6DCE5']]
        ];

        $rigaDorig = [
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2EFDA']]
        ];

        $currentRow = 1;
        $isFirstHeader = true;

        foreach ($fatture as $fattura) {
            // Scrivi header dotes (solo la prima volta o se preferisci ripeterlo)
            if ($isFirstHeader) {
                $col = 1;
                foreach ($headerDotes as $header) {
                    $sheet->setCellValue([$col, $currentRow], $header);
                    $col++;
                }
                $sheet->getStyle("A{$currentRow}:P{$currentRow}")->applyFromArray($headerStyleDotes);
                $currentRow++;
                $isFirstHeader = false;
            }

            // Scrivi riga dotes (testata fattura)
            $sheet->setCellValue([1, $currentRow], 'FATTURA');
            $sheet->setCellValue([2, $currentRow], $fattura->id);
            $sheet->setCellValue([3, $currentRow], $fattura->numero_doc);
            $sheet->setCellValue([4, $currentRow], $fattura->data_doc);
            $sheet->setCellValue([5, $currentRow], $fattura->ragione_sociale_fatturazione);
            $sheet->setCellValue([6, $currentRow], $fattura->partita_iva_fatturazione);
            $sheet->setCellValue([7, $currentRow], $fattura->indirizzo_fatturazione);
            $sheet->setCellValue([8, $currentRow], $fattura->comune_fatturazione);
            $sheet->setCellValue([9, $currentRow], $fattura->cap);
            $sheet->setCellValue([10, $currentRow], $fattura->tipologia_documento);
            $sheet->setCellValue([11, $currentRow], $fattura->imponibile);
            $sheet->setCellValue([12, $currentRow], $fattura->imposta);
            $sheet->setCellValue([13, $currentRow], $fattura->totale);
            $sheet->setCellValue([14, $currentRow], $fattura->stato == 1 ? 'Inviata' : ($fattura->stato == 0 ? 'Da inviare' : ($fattura->stato == 2 ? 'Rifiutata' : 'Sconosciuto')));
            $sheet->setCellValue([15, $currentRow], $fattura->saldata == 1 ? 'Sì' : 'No');
            $sheet->setCellValue([16, $currentRow], $fattura->modalita_pagamento);

            $sheet->getStyle("A{$currentRow}:P{$currentRow}")->applyFromArray($rowaDotes);

            // Formatta numeri
            $sheet->getStyle("K{$currentRow}:M{$currentRow}")->getNumberFormat()->setFormatCode('#,##0.00');

            $currentRow++;

            // Recupera le righe dorig collegate
            $righe = DB::select("SELECT * FROM dorig WHERE id_dotes = ? AND id_azienda = ? ORDER BY n_riga ASC",
                [$fattura->id, $utente->id_azienda]);

            if (count($righe) > 0) {
                // Header righe dorig
                $col = 1;
                foreach ($headerDorig as $header) {
                    $sheet->setCellValue([$col, $currentRow], $header);
                    $col++;
                }
                $sheet->getStyle("A{$currentRow}:L{$currentRow}")->applyFromArray($headerStyleDorig);
                $currentRow++;

                // Scrivi ogni riga dorig
                foreach ($righe as $riga) {
                    $sheet->setCellValue([1, $currentRow], '  → RIGA');
                    $sheet->setCellValue([2, $currentRow], $riga->id);
                    $sheet->setCellValue([3, $currentRow], $riga->nome_prodotto);
                    $sheet->setCellValue([4, $currentRow], $riga->descrizione);
                    $sheet->setCellValue([5, $currentRow], $riga->qta);
                    $sheet->setCellValue([6, $currentRow], $riga->prezzo_unitario);
                    $sheet->setCellValue([7, $currentRow], $riga->prezzo_totale);
                    $sheet->setCellValue([8, $currentRow], $riga->iva);
                    $sheet->setCellValue([9, $currentRow], $riga->imponibile);
                    $sheet->setCellValue([10, $currentRow], $riga->imposta);
                    $sheet->setCellValue([11, $currentRow], $riga->um);
                    $sheet->setCellValue([12, $currentRow], $riga->n_riga);

                    $sheet->getStyle("A{$currentRow}:L{$currentRow}")->applyFromArray($rigaDorig);

                    // Formatta numeri
                    $sheet->getStyle("E{$currentRow}:J{$currentRow}")->getNumberFormat()->setFormatCode('#,##0.00');

                    $currentRow++;
                }
            }

            // Riga vuota tra fatture
            $currentRow++;
        }

        // Auto-dimensiona colonne
        foreach (range('A', 'P') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Genera il file
        $fileName = 'fatture_uscita_' . ($mese ? str_pad($mese, 2, '0', STR_PAD_LEFT) . '_' : '') . $anno . '.xlsx';

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        // Salva temporaneamente e scarica
        $tempFile = storage_path('app/' . $fileName);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }



    public function articolo_pubblico($token)
    {
        // Recupera l'articolo tramite il token
        $articolo = DB::table('articoli')
            ->where('qr_token', $token)
            ->where('tipologia', 0) // Solo prodotti finiti
            ->first();

        if (!$articolo) {
            abort(404, 'Articolo non trovato');
        }

        // Recupera le fasi associate
        $fasi = DB::table('fasi_articoli')
            ->join('fasi', 'fasi_articoli.id_fase', '=', 'fasi.id')
            ->where('fasi_articoli.id_articolo', $articolo->id)
            ->select('fasi.descrizione')
            ->get();

        return View::make('utente.articolo_pubblico', compact('articolo', 'fasi'));
    }

    public function getDistintaBaseTree($id_articolo) {
        $utente = session('utente');

        // Recupera l'articolo principale
        $articolo = DB::table('articoli')
            ->where('id', $id_articolo)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        // Recupera le fasi dell'articolo
        $fasi = DB::table('fasi_articoli')
            ->join('fasi', 'fasi.id', '=', 'fasi_articoli.id_fase')
            ->where('fasi_articoli.id_articolo', $id_articolo)
            ->where('fasi_articoli.id_azienda', $utente->id_azienda)
            ->select('fasi.id', 'fasi.descrizione')
            ->get();

        // Inizializza il tree con tutte le fasi, anche quelle vuote
        $tree = [];
        foreach($fasi as $fase) {
            $tree[$fase->descrizione] = []; // Inizializza un array vuoto per ogni fase
        }

        // Recupera e aggiungi i materiali per ogni fase
        foreach($fasi as $fase) {
            $materiali = DB::select('
            SELECT 
                db.*,
                m.titolo as materiale,
                m.codice_articolo,
                m.um,
                m.prezzo,
                f.descrizione as fase_descrizione
            FROM distinta_base db
            JOIN articoli m ON m.id = db.id_materiale
            JOIN fasi f ON f.id = db.id_fase_articolo
            WHERE db.id_articolo = ?
            AND db.id_fase_articolo = ?
            AND db.id_azienda = ?
            ORDER BY db.id',
                [$id_articolo, $fase->id, $utente->id_azienda]
            );

            // Aggiorna l'array dei materiali per questa fase
            // Se non ci sono materiali, l'array rimarrà vuoto
            if(!empty($materiali)) {
                $tree[$fase->descrizione] = $materiali;
            }
        }

        return response()->json([
            'success' => true,
            'articolo' => $articolo,
            'tree' => $tree
        ]);
    }
    public function fasi_di_lavorazione(Request $request) {
        $this->is_loggato();
        $dati = $request->all();
        $utente = session('utente');

        if (isset($dati['aggiungi'])) {
            unset($dati['aggiungi']);
            $dati['id_azienda'] = $utente->id_azienda;
            DB::table('fasi')->insert($dati);
            return Redirect::to('utente/fasi_di_lavorazione');
        }

        if (isset($dati['elimina'])) {
            DB::table('fasi')->where('id', $dati['id'])->where('id_azienda',$utente->id_azienda)->delete();
            return Redirect::to('utente/fasi_di_lavorazione');
        }

        // Modifica della fase
        if (isset($dati['modifica_fase'])) {
            unset($dati['modifica_fase']);
            DB::table('fasi')->where('id', $dati['id'])->where('id_azienda',$utente->id_azienda)->update($dati);
            return Redirect::to('utente/fasi_di_lavorazione');
        }

        // Recupera tutte le fasi
        $fasi = DB::table('fasi')->where('id_azienda', $utente->id_azienda)->get();

        return View::make('utente.fasi_di_lavorazione', compact('utente', 'fasi'));
    }

    public function scadenziario(Request $request) {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Se vengono inviate nuove date, salvale in sessione
        if(isset($dati['data_inizio']) && isset($dati['data_fine'])) {
            session(['scadenziario_data_inizio' => $dati['data_inizio']]);
            session(['scadenziario_data_fine' => $dati['data_fine']]);
            return Redirect::to('utente/scadenziario');
        }

        if(isset($dati['importa_scadenze'])) {
            if($_FILES['file_scadenze']['name'] != '') {

                Excel::import(new ScadenziarioImport($utente->id_azienda), request()->file('file_scadenze'));
                return Redirect::to('utente/scadenziario');
            }
            return Redirect::to('utente/scadenziario');
        }

        if(isset($dati['invia_sollecito'])){

            $utente = session('utente');
            $dati = $request->all();




            $azienda = DB::select('SELECT * from aziende where id='.$utente->id_azienda);
            if(sizeof($azienda) > 0) {
                $azienda = $azienda[0];

                $processi_response = '';

                $scadenza = DB::table('scadenziario')->where('id', $dati['id'])->first();

                if (!$scadenza) {
                    return response()->json(['success' => false, 'message' => 'Scadenza non trovata']);
                }


                if(isset($dati['ora_chiamata'])){

                    $azienda = DB::select('SELECT * from aziende where id='.$utente->id_azienda);
                    if(sizeof($azienda) > 0) {

                        $azienda = $azienda[0];

                        $cliente = DB::select('SELECT * FROM clienti WHERE id IN (SELECT id_cliente FROM scadenziario WHERE id = '.$scadenza->id.')');

                        if(sizeof($cliente) > 0) {
                            $cliente = $cliente[0];

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, 'https://processi.cloud/api/invia_chiamata_sollecito');
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
                                'token' => $azienda->token_processi,
                                'piva' => $cliente->piva,
                                'timestamp' => $dati['ora_chiamata'],
                                'importo' => $scadenza->importo
                            ]));
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            $response = curl_exec($ch);
                            $responseData = json_decode($response, true);
                            $processi_response = $responseData['data'];
                            if($responseData['status'] == 'success'){
                                $processi_response = $responseData['data'];
                            }else{
                                echo $responseData['data'];
                                exit;
                            }
                            curl_close($ch);
                        }


                    }
                }

                $tracking_id = Str::random(32);

                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = 'smtps.aruba.it';
                $mail->SMTPAuth = true;
                $mail->Username = 'noreply@gestya.it';
                $mail->Password = 'jwZFTChzg8gp41?c';
                $mail->SMTPSecure = 'ssl';
                $mail->CharSet = 'utf-8';
                $mail->Port = 465;
                $mail->setFrom('noreply@gestya.it');

                // Gestione destinatari
                $destinatari = strpos($request->email_destinatari, ';') !== false ?
                    explode(';', $request->email_destinatari) :
                    [$request->email_destinatari];

                foreach ($destinatari as $email) {
                    $email = trim($email);
                    if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                        $mail->addAddress($email);
                    }
                }

                $mail->addBCC($azienda->email_ricezione_fatture);
                $mail->addReplyTo($azienda->email_ricezione_fatture);

                $mail->isHTML(true);
                $mail->Subject = "Sollecito pagamento Fattura del ".date('d/m/Y',strtotime($scadenza->data_scadenza))." ".$azienda->ragione_sociale;

                // Aggiungi pixel tracking
                $pixel_url = url("/track-email/{$tracking_id}");
                $tracking_pixel = "<img src='{$pixel_url}' width='1' height='1' style='display:none'>";

                $firma = '<br><br><a href="https://gestya.it/firma/click" target="_blank" style="text-decoration:none;"><img src="https://gestya.it/firma/banner" alt="Gestya" width="300" style="max-width:100%;height:auto;border:0;" /></a>';
                $mail->Body = nl2br($request->email_messaggio) . $firma . $tracking_pixel;
                $mail->send();



                DB::table('scadenziario')->where('id',$dati['id'])->update(['processi_response' => $processi_response, 'data_ultimo_sollecito' => now(),'data_apertura_email' => null,'email_aperta' => 0, 'numero_solleciti' => DB::raw('numero_solleciti + 1'), 'tracking_id' => $tracking_id]);


                return Redirect::to('utente/scadenziario');
            }
        }

        if(isset($dati['aggiungi'])){
            unset($dati['aggiungi']);
            $dati['id_azienda'] = $utente->id_azienda;
            DB::table('scadenziario')->insert($dati);
            return Redirect::to('utente/scadenziario');
        }

        if(isset($dati['modifica'])){
            unset($dati['modifica']);

            // Verifica e limita l'importo pagato
            if($dati['importo_pagato'] > $dati['importo']) {
                $dati['importo_pagato'] = $dati['importo'];
            }

            // Recupera la scadenza attuale per informazioni aggiuntive
            $scadenza_attuale = DB::table('scadenziario')->where('id', $dati['id'])->first();
            if (!$scadenza_attuale) {
                return redirect()->back()->with('error', 'Scadenza non trovata');
            }

            // Determina il nuovo stato della scadenza
            $stato_precedente = $scadenza_attuale->stato;
            $nuovo_stato = 'da_pagare';

            if ($dati['importo_pagato'] >= $dati['importo']) {
                $nuovo_stato = 'pagato';
            } elseif ($dati['importo_pagato'] > 0) {
                $nuovo_stato = 'parziale';
            }

            $dati['stato'] = $nuovo_stato;

            // Aggiorna la scadenza
            DB::table('scadenziario')->where('id', $dati['id'])->update($dati);

            // Se la scadenza è stata completamente pagata, verifica se generare provvigione
            // Nota: Usiamo il tipo_movimento dalla scadenza recuperata dal DB, non dai dati POST
            if ($nuovo_stato == 'pagato' && $stato_precedente != 'pagato' && $scadenza_attuale->tipo_movimento == 'entrata') {
                // Non è necessario recuperare nuovamente la scadenza, usiamo quella già recuperata

                if ($scadenza_attuale->id_dotes) {
                    // Verifica se tutte le scadenze della fattura sono state pagate
                    $scadenze_non_pagate = DB::select('
                SELECT COUNT(*) as conteggio FROM scadenziario 
                WHERE id_dotes = ? 
                AND tipo_movimento = "entrata" 
                AND id_azienda = ? 
                AND importo > importo_pagato
                AND id != ?  -- Escludi la scadenza corrente che abbiamo appena aggiornato
            ', [$scadenza_attuale->id_dotes, $utente->id_azienda, $scadenza_attuale->id]);

                    if ($scadenze_non_pagate[0]->conteggio == 0) {
                        // Recupera la fattura
                        $fattura = DB::select('SELECT * FROM dotes WHERE id = ? AND id_azienda = ?',
                            [$scadenza_attuale->id_dotes, $utente->id_azienda]);

                        if (!empty($fattura)) {
                            $fattura = $fattura[0];

                            // Aggiorna lo stato della fattura come saldata
                            DB::table('dotes')
                                ->where('id', $fattura->id)
                                ->update(['saldata' => 1]);

                            // Verifica che sia una fattura di vendita con agente
                            if ($fattura->cd_do == 'FTV' && !empty($fattura->id_agente)) {
                                // Verifica che non ci sia già una provvigione
                                $provvigione_esistente = DB::select('
                            SELECT COUNT(*) as conteggio FROM scadenziario 
                            WHERE id_dotes = ? 
                            AND tipo_movimento = "uscita" 
                            AND id_azienda = ?
                            AND note LIKE ?
                        ', [$fattura->id, $utente->id_azienda, 'Provvigione per fattura%']);

                                if ($provvigione_esistente[0]->conteggio == 0) {
                                    // Recupera configurazione provvigione
                                    $provvigione = DB::select('
                                SELECT * FROM provvigioni_agenti 
                                WHERE id_agente = ? 
                                AND id_azienda = ? 
                                ORDER BY data_creazione DESC LIMIT 1
                            ', [$fattura->id_agente, $utente->id_azienda]);

                                    // Calcola importo
                                    $importo_provvigione = 0;
                                    $tipo_provvigione = 'percentuale';
                                    $valore_provvigione = 10; // Default

                                    if (!empty($provvigione)) {
                                        $provvigione = $provvigione[0];
                                        $tipo_provvigione = $provvigione->tipo_provvigione;
                                        $valore_provvigione = $provvigione->valore;

                                        if ($tipo_provvigione == 'percentuale') {
                                            $importo_provvigione = $fattura->imponibile * ($valore_provvigione / 100);
                                        } else {
                                            $importo_provvigione = $valore_provvigione;
                                        }
                                    } else {
                                        // Default: 10% di provvigione
                                        $importo_provvigione = $fattura->imponibile * 0.1;
                                    }

                                    // Inserisci la scadenza provvigione
                                    $id_scadenziario = DB::table('scadenziario')->insertGetId([
                                        'id_dotes' => $fattura->id,
                                        'id_azienda' => $utente->id_azienda,
                                        'id_cliente' => $fattura->id_cliente,
                                        'id_agente' => $fattura->id_agente,
                                        'data_scadenza' => date('Y-m-d', strtotime('+30 days')),
                                        'importo' => $importo_provvigione,
                                        'importo_pagato' => 0,
                                        'tipo_movimento' => 'uscita',
                                        'modalita_pagamento' => 'bonifico',
                                        'stato' => 'da_pagare',
                                        'note' => 'Provvigione per fattura n.' . $fattura->numero_doc
                                    ]);

                                    // Registra anche nella tabella delle provvigioni fatture
                                    DB::table('provvigioni_fatture')->insert([
                                        'id_azienda' => $utente->id_azienda,
                                        'id_agente' => $fattura->id_agente,
                                        'id_dotes' => $fattura->id,
                                        'id_scadenziario' => $id_scadenziario,
                                        'importo_fattura' => $fattura->totale,
                                        'importo_provvigione' => $importo_provvigione,
                                        'tipo_provvigione' => $tipo_provvigione,
                                        'valore' => $valore_provvigione,
                                        'pagata' => 0,
                                        'data_creazione' => now(),
                                        'riferimento_fattura' => $fattura->numero_doc,
                                        'note' => 'Provvigione generata automaticamente' .
                                            (empty($provvigione) ? ' (default 10%)' : '')
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            return Redirect::to('utente/scadenziario');
        }


        if(isset($dati['elimina'])){

            unset($dati['elimina']);
            DB::table('scadenziario')->where('id',$dati['id'])->delete();
            return Redirect::to('utente/scadenziario');
        }

        // Gestione date di filtro
        $data_inizio = session('scadenziario_data_inizio', date('Y-01-01'));
        $data_fine = session('scadenziario_data_fine', date('Y-12-31'));

        // Query per le scadenze in entrata
        $scadenze_entrata = DB::select('
        SELECT s.*, c.ragione_sociale as cliente,c.mail_recapito as email, d.numero_doc, d.data_doc
        FROM scadenziario s 
        LEFT JOIN clienti c ON c.id = s.id_cliente 
        LEFT JOIN dotes d ON d.id = s.id_dotes 
        WHERE s.id_azienda = ? 
        AND s.tipo_movimento = "entrata"
        AND s.importo_pagato < s.importo
        AND s.data_scadenza BETWEEN ? AND ?
        ORDER BY s.data_scadenza ASC',
            [$utente->id_azienda, $data_inizio, $data_fine]
        );

        $scadenze_uscita = DB::select('
SELECT s.*, 
    c.ragione_sociale as cliente, 
    c.mail_recapito as email, 
    d.numero_doc, 
    d.data_doc,
    a.nome as agente_nome,
    a.cognome as agente_cognome
FROM scadenziario s 
LEFT JOIN clienti c ON c.id = s.id_cliente 
LEFT JOIN dotes d ON d.id = s.id_dotes 
LEFT JOIN utenti a ON a.id = s.id_agente
WHERE s.id_azienda = ? 
AND s.tipo_movimento = "uscita"
AND s.importo_pagato < s.importo
AND s.data_scadenza BETWEEN ? AND ?
ORDER BY s.data_scadenza ASC',
            [$utente->id_azienda, $data_inizio, $data_fine]
        );

        // Calcolo totali
        $totale_entrate = DB::select('
        SELECT COALESCE(SUM(importo - importo_pagato), 0) as totale
        FROM scadenziario 
        WHERE id_azienda = ? 
        AND tipo_movimento = "entrata"
        AND importo_pagato < importo
        AND data_scadenza BETWEEN ? AND ?',
            [$utente->id_azienda, $data_inizio, $data_fine]
        )[0]->totale;

        $totale_uscite = DB::select('
        SELECT COALESCE(SUM(importo - importo_pagato), 0) as totale
        FROM scadenziario 
        WHERE id_azienda = ? 
        AND tipo_movimento = "uscita"
        AND importo_pagato < importo
        AND data_scadenza BETWEEN ? AND ?',
            [$utente->id_azienda, $data_inizio, $data_fine]
        )[0]->totale;

        $utile = $totale_entrate - $totale_uscite;

        $clienti = DB::select('SELECT * FROM clienti WHERE id_azienda = ? ORDER BY ragione_sociale ASC', [$utente->id_azienda]);

        $azienda = DB::select('SELECT * from aziende where id='.$utente->id_azienda);
        if(sizeof($azienda) > 0) {
            $azienda = $azienda[0];

            return View::make('utente.scadenziario', compact(
                'utente',
                'scadenze_entrata',
                'scadenze_uscita',
                'clienti',
                'totale_entrate',
                'totale_uscite',
                'utile',
                'data_inizio',
                'data_fine',
                'azienda'
            ));
        }
    }


    public function trackEmail($tracking_id)
    {
        try {
            DB::table('scadenziario')
                ->where('tracking_id', $tracking_id)
                ->update([
                    'email_aperta' => true,
                    'data_apertura_email' => now(),
                    'tracking_id' => null
                ]);
        } catch (\Exception $e) {
            \Log::error('Errore tracking email: ' . $e->getMessage());
        }

        // Ritorna un'immagine trasparente 1x1 pixel
        return response()->file(public_path('images/pixel.png'))
            ->header('Content-Type', 'image/png');
    }

    public function get_scadenza($id)
    {
        $this->is_loggato();
        $utente = session('utente');

        $scadenza = DB::table('scadenziario')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$scadenza) {
            return response()->json(['error' => 'Scadenza non trovata'], 404);
        }

        return response()->json($scadenza);
    }


    public function registra_pagamento(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        try {
            // Recupera la scadenza
            $scadenza = DB::table('scadenziario')
                ->where('id', $request->id_scadenza)
                ->where('id_azienda', $utente->id_azienda)
                ->first();

            if (!$scadenza) {
                throw new \Exception('Scadenza non trovata');
            }

            // Verifica l'importo
            if ($request->importo_pagato > $scadenza->importo) {
                throw new \Exception('Importo non valido');
            }

            // Aggiorna la scadenza
            $nuovo_importo_pagato = $request->importo_pagato;
            $stato = 'da_pagare';

            if ($nuovo_importo_pagato >= $scadenza->importo) {
                $stato = 'pagato';
            } elseif ($nuovo_importo_pagato > 0) {
                $stato = 'parziale';
            }

            // Aggiorna la scadenza
            DB::table('scadenziario')
                ->where('id', $request->id_scadenza)
                ->update([
                    'importo_pagato' => $nuovo_importo_pagato,
                    'stato' => $stato,
                    'data_pagamento' => $request->data_pagamento,
                    'modalita_pagamento' => $request->modalita_pagamento,
                    'note' => $request->note
                ]);

            // Solo se è un pagamento completo di un'entrata
            if ($stato == 'pagato' && $scadenza->tipo_movimento == 'entrata' && !empty($scadenza->id_dotes)) {
                // Recupera la fattura direttamente
                $fattura = DB::select('SELECT * FROM dotes WHERE id = ? AND id_azienda = ?',
                    [$scadenza->id_dotes, $utente->id_azienda]);

                if (!empty($fattura)) {
                    $fattura = $fattura[0];

                    // Verifica che sia una fattura di vendita con agente
                    if ($fattura->cd_do == 'FTV' && !empty($fattura->id_agente)) {
                        // Verifica tutte le scadenze pagate
                        $scadenze_non_pagate = DB::select('
                    SELECT COUNT(*) as conteggio FROM scadenziario 
                    WHERE id_dotes = ? 
                    AND tipo_movimento = "entrata" 
                    AND id_azienda = ? 
                    AND importo > importo_pagato
                ', [$fattura->id, $utente->id_azienda]);

                        if ($scadenze_non_pagate[0]->conteggio == 0) {
                            // Aggiorna lo stato della fattura come saldata
                            DB::table('dotes')
                                ->where('id', $fattura->id)
                                ->update(['saldata' => 1]);

                            // Verifica che non ci sia già una provvigione
                            $provvigione_esistente = DB::select('
                        SELECT COUNT(*) as conteggio FROM scadenziario 
                        WHERE id_dotes = ? 
                        AND tipo_movimento = "uscita" 
                        AND id_azienda = ?
                        AND note LIKE ?
                    ', [$fattura->id, $utente->id_azienda, 'Provvigione per fattura%']);

                            if ($provvigione_esistente[0]->conteggio == 0) {
                                // Recupera configurazione provvigione
                                $provvigione = DB::select('
                            SELECT * FROM provvigioni_agenti 
                            WHERE id_agente = ? 
                            AND id_azienda = ? 
                            ORDER BY data_creazione DESC LIMIT 1
                        ', [$fattura->id_agente, $utente->id_azienda]);

                                // Se c'è una configurazione, crea la scadenza
                                if (!empty($provvigione)) {
                                    $provvigione = $provvigione[0];

                                    // Calcola importo
                                    $importo_provvigione = 0;
                                    if ($provvigione->tipo_provvigione == 'percentuale') {
                                        $importo_provvigione = $fattura->imponibile * ($provvigione->valore / 100);
                                    } else {
                                        $importo_provvigione = $provvigione->valore;
                                    }

                                    // Inserisci la scadenza provvigione
                                    $id_scadenziario = DB::table('scadenziario')->insertGetId([
                                        'id_dotes' => $fattura->id,
                                        'id_azienda' => $utente->id_azienda,
                                        'id_cliente' => $fattura->id_cliente,
                                        'id_agente' => $fattura->id_agente,
                                        'data_scadenza' => date('Y-m-d', strtotime('+30 days')),
                                        'importo' => $importo_provvigione,
                                        'importo_pagato' => 0,
                                        'tipo_movimento' => 'uscita',
                                        'modalita_pagamento' => 'bonifico',
                                        'stato' => 'da_pagare',
                                        'note' => 'Provvigione per fattura n.' . $fattura->numero_doc
                                    ]);

                                    // Registra anche nella tabella delle provvigioni fatture
                                    DB::table('provvigioni_fatture')->insert([
                                        'id_azienda' => $utente->id_azienda,
                                        'id_agente' => $fattura->id_agente,
                                        'id_dotes' => $fattura->id,
                                        'id_scadenziario' => $id_scadenziario,
                                        'importo_fattura' => $fattura->totale,
                                        'importo_provvigione' => $importo_provvigione,
                                        'tipo_provvigione' => $provvigione->tipo_provvigione,
                                        'valore' => $provvigione->valore,
                                        'pagata' => 0,
                                        'data_creazione' => now(),
                                        'riferimento_fattura' => $fattura->numero_doc,
                                        'note' => 'Provvigione generata automaticamente'
                                    ]);
                                } else {
                                    // Default: 10% di provvigione
                                    $importo_provvigione = $fattura->imponibile * 0.1;

                                    // Inserisci la scadenza provvigione con valore default
                                    $id_scadenziario = DB::table('scadenziario')->insertGetId([
                                        'id_dotes' => $fattura->id,
                                        'id_azienda' => $utente->id_azienda,
                                        'id_cliente' => $fattura->id_cliente,
                                        'id_agente' => $fattura->id_agente,
                                        'data_scadenza' => date('Y-m-d', strtotime('+30 days')),
                                        'importo' => $importo_provvigione,
                                        'importo_pagato' => 0,
                                        'tipo_movimento' => 'uscita',
                                        'modalita_pagamento' => 'bonifico',
                                        'stato' => 'da_pagare',
                                        'note' => 'Provvigione per fattura n.' . $fattura->numero_doc
                                    ]);

                                    // Registra anche nella tabella delle provvigioni fatture con valori default
                                    DB::table('provvigioni_fatture')->insert([
                                        'id_azienda' => $utente->id_azienda,
                                        'id_agente' => $fattura->id_agente,
                                        'id_dotes' => $fattura->id,
                                        'id_scadenziario' => $id_scadenziario,
                                        'importo_fattura' => $fattura->totale,
                                        'importo_provvigione' => $importo_provvigione,
                                        'tipo_provvigione' => 'percentuale',
                                        'valore' => 10,
                                        'pagata' => 0,
                                        'data_creazione' => now(),
                                        'riferimento_fattura' => $fattura->numero_doc,
                                        'note' => 'Provvigione generata automaticamente (default 10%)'
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            return redirect()->back()->with('success',
                $scadenza->tipo_movimento === 'entrata' ? 'Incasso registrato con successo' : 'Pagamento registrato con successo');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Errore: ' . $e->getMessage());
        }
    }



    public function odl(Request $request){

        $this->is_loggato();
        $dati = $request->all();
        $utente = session('utente');
        $reparto = session('reparto');


        if(isset($dati['aggiungi'])){
            unset($dati['aggiungi']);
            $dati['data'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-',$dati['data'])));
            $dati['id_utente'] = $utente->id;
            $dati['id_azienda'] = $utente->id_azienda;

            $dati_odl = $dati;
            unset($dati_odl['id_dorig']);
            unset($dati_odl['_token']);

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
                $insert_riga['id_azienda'] = $utente->id_azienda;
                $insert_riga['id_fase'] = $fase->id_fase;
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


            return Redirect::to('utente/dettaglio_odl/'.$insert_riga['id_odl']);
        }


        if(isset($dati['modifica'])){
            unset($dati['modifica']);
            $dati['id_utente'] = $utente->id;
            $dati['data'] = date('Y-m-d H:i:s', strtotime(str_replace('/', '-',$dati['data'])));
            DB::table('odl')->where('id',$dati['id'])->update($dati);
            return Redirect::to('utente/odl');

        }

        if(isset($dati['elimina'])){
            unset($dati['elimina']);
            $dati['id_utente'] = $utente->id;
            DB::table('odl')->where('id',$dati['id'])->delete();
            DB::table('odl_righe')->where('Id_odl',$dati['id'])->delete();
            return Redirect::to('utente/odl');
        }

        if(isset($dati['modifica_commessa'])){
            unset($dati['modifica_commessa']);
            DB::table('utenti')->where('id',$utente->id)->update($dati);
            $utente->commessa_attuale = $dati['commessa_attuale'];

            session(['utente' => $utente]);
            session()->save();
            return Redirect::to('utente/odl');
        }

        $num_odl = DB::select('SELECT ifnull(max(numero)+1,1) as numero from odl where id_utente = ? AND id_azienda = ?', [$utente->id, $utente->id_azienda])[0]->numero;

        $odl = DB::select('
            SELECT o.*, a.titolo as articolo, a.codice_articolo, c.ragione_sociale as cliente_ragione_sociale
            FROM odl o
            LEFT JOIN articoli a ON a.id = o.id_articolo
            LEFT JOIN dotes d ON d.id = o.id_dotes
            LEFT JOIN clienti c ON c.id = d.id_cliente
            where  o.id_azienda = '.$utente->id_azienda.'
            ORDER BY data DESC, id DESC'
        );


        $articoli = DB::select('SELECT * from articoli where tipologia = 0  AND id_azienda = ?', [$utente->id_azienda]);

        return View::make('utente.odl',compact('utente','odl','articoli','num_odl'));


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

                    DB::update('update odl_righe set inizio = NOW() where id = ? and id_azienda = ?', [$dati['id'], $utente->id_azienda]);
                }
            }

            return Redirect::to('utente/dettaglio_odl/' . $id_odl);
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
                    $dati['scadenza_lotto'] ?? null,  // Passa il lotto solo se esiste, altrimenti null
                    $utente,
                    $riga->id_odl,
                    $dati['id_dorig'],
                    '',
                    $dati['qta_materiale'] ?? null,
                    $dati['id_materiale'] ?? null
                );

                // Completa automaticamente i semilavorati associati a questa fase
                DB::table('odl_semilavorati')
                    ->where('id_odl', $riga->id_odl)
                    ->where('id_azienda', $utente->id_azienda)
                    ->where('id_fase', $dati['id_fase'])
                    ->where('stato', 0)
                    ->update([
                        'stato' => 1,
                        'id_operatore' => $utente->id,
                        'completato_at' => now(),
                    ]);

                return Redirect::to('utente/dettaglio_odl/' . $id_odl);
            }
        }

        if (isset($dati['assegna_operatore'])) {
            DB::table('odl_righe')
                ->where('id', $dati['id_riga'])
                ->update(['id_operatore_assegnato' => $dati['id_operatore_assegnato'] ?: null]);

            return redirect()->back()->with('success', 'Operatore assegnato con successo');
        }

        if (isset($dati['toggle_semilavorato'])) {
            $semi = DB::table('odl_semilavorati')
                ->where('id', $dati['id_semilavorato'])
                ->where('id_azienda', $utente->id_azienda)
                ->first();
            if ($semi) {
                $nuovo_stato = $semi->stato == 1 ? 0 : 1;
                DB::table('odl_semilavorati')
                    ->where('id', $semi->id)
                    ->update([
                        'stato' => $nuovo_stato,
                        'completato_at' => $nuovo_stato == 1 ? now() : null,
                    ]);
            }
            return redirect()->back();
        }

        if (isset($dati['assegna_fornitore'])) {
            DB::table('odl_righe')
                ->where('id', $dati['id_riga'])
                ->where('id_azienda', $utente->id_azienda)
                ->update(['id_fornitore' => !empty($dati['id_fornitore']) ? $dati['id_fornitore'] : null]);

            return redirect()->back()->with('success', 'Fornitore assegnato con successo');
        }

        if (isset($dati['modifica_orari'])) {
            $update = [];
            if (!empty($dati['inizio'])) {
                $update['inizio'] = date('Y-m-d H:i:s', strtotime($dati['inizio']));
            }
            if (!empty($dati['fine'])) {
                $update['fine'] = date('Y-m-d H:i:s', strtotime($dati['fine']));
            }
            if (!empty($update)) {
                DB::table('odl_righe')
                    ->where('id', $dati['id_riga'])
                    ->where('id_azienda', $utente->id_azienda)
                    ->update($update);
            }
            return redirect()->back()->with('success', 'Orari aggiornati con successo');
        }


        $odl = DB::select('SELECT o.*, a.titolo as articolo, a.codice_articolo, c.ragione_sociale as cliente_ragione_sociale, c.cd_cf as cliente_cd_cf from odl o LEFT JOIN articoli a ON a.id = o.id_articolo LEFT JOIN dotes d ON d.id = o.id_dotes LEFT JOIN clienti c ON c.id = d.id_cliente where o.id = ? and o.id_azienda = ?', [$id_odl, $utente->id_azienda]);

        if (sizeof($odl) == 0) {
            return redirect('utente/odl')->with('error', 'ODL non trovato (id: ' . $id_odl . ')');
        }

        $odl = $odl[0];
            $odl_righe = DB::select('
    SELECT o.*, f.descrizione as nome_fase 
    FROM odl_righe o 
    LEFT JOIN fasi f ON f.id = o.id_fase 
    WHERE o.id_odl = ? AND o.id_azienda = ?', [$id_odl, $utente->id_azienda]);


            $operatori = DB::select('SELECT * from utenti where id_tipologia = 3 and id_azienda = ?', [$utente->id_azienda]);

            // Recupera i fornitori per lavorazioni esterne
            $usaClientTable = ($utente->id_azienda == 14);
            $tabella_fornitori = $usaClientTable ? 'clienti' : 'fornitori';
            $fornitori_odl = DB::table($tabella_fornitori)->where('id_azienda', $utente->id_azienda)->where('id_tipologia', 1)->orderBy('ragione_sociale')->get();

            // Recupera l'articolo associato all'ODL
            $articoli = DB::select('SELECT * FROM articoli WHERE id = ? and id_azienda = ?', [$odl->id_articolo, $utente->id_azienda]);

            if (count($articoli) == 0) {
                return redirect('utente/odl')->with('error', 'L\'articolo collegato all\'ODL #' . $odl->numero . ' non esiste o e\' stato eliminato dall\'anagrafica.');
            }

            $articolo = $articoli[0];

                // Aggiungi i materiali e allegati per ogni riga dell'ODL
                foreach ($odl_righe as &$riga) {
                    // Recupera i materiali associati alla fase specifica dalla distinta base
                    $riga->materiali = DB::select('
                    SELECT a.titolo, a.id, a.tipologia, db.qta, db.id_fase_articolo
                    FROM articoli a
                    LEFT JOIN distinta_base db ON db.id_materiale = a.id
                    WHERE db.id_articolo = ? AND db.id_fase_articolo = ? AND a.id_azienda = ?',
                        [$articolo->id, $riga->id_fase, $utente->id_azienda]
                    );

                    // Per ogni materiale, recupera i lotti disponibili
                    foreach ($riga->materiali as $materiale) {
                        $materiale->lotti_disponibili = DB::select('
                        SELECT lotto, scadenza_lotto, SUM(qta) as giacenza
                        FROM mgmov
                        WHERE id_articolo = ?
                        AND id_azienda = ?
                        AND lotto IS NOT NULL
                        GROUP BY lotto, scadenza_lotto
                        HAVING SUM(qta) > 0
                        ORDER BY lotto ASC',
                            [$materiale->id, $utente->id_azienda]
                        );
                    }

                    // Recupera gli allegati per questa fase
                    $riga->allegati = DB::table('odl_righe_allegati')
                        ->where('id_odl_riga', $riga->id)
                        ->where('id_azienda', $utente->id_azienda)
                        ->orderBy('created_at', 'desc')
                        ->get();
                }


                // Carica semilavorati dalla distinta base del prodotto finito (con la fase a cui sono associati)
                $semi_in_db = DB::select('
                    SELECT a.id, a.titolo, a.codice_articolo, db.qta, db.id_fase_articolo, db.id_fornitore
                    FROM distinta_base db
                    JOIN articoli a ON a.id = db.id_materiale
                    WHERE db.id_articolo = ? AND a.tipologia = 3 AND a.id_azienda = ?',
                    [$articolo->id, $utente->id_azienda]
                );

                // Auto-popola odl_semilavorati se non già fatto, propagando il fornitore dalla distinta base
                foreach ($semi_in_db as $s) {
                    $esiste = DB::table('odl_semilavorati')
                        ->where('id_odl', $id_odl)
                        ->where('id_articolo', $s->id)
                        ->where('id_fase', $s->id_fase_articolo)
                        ->first();
                    if (!$esiste) {
                        DB::table('odl_semilavorati')->insert([
                            'id_odl'     => $id_odl,
                            'id_azienda' => $utente->id_azienda,
                            'id_articolo'=> $s->id,
                            'id_fase'    => $s->id_fase_articolo,
                            'id_fornitore' => $s->id_fornitore,
                            'qta'        => $s->qta,
                            'stato'      => 0,
                            'created_at' => now(),
                        ]);
                    } elseif (empty($esiste->id_fornitore) && !empty($s->id_fornitore)) {
                        // Aggiorna il fornitore se è stato impostato in distinta base dopo la creazione dell'ODL
                        DB::table('odl_semilavorati')->where('id', $esiste->id)->update(['id_fornitore' => $s->id_fornitore]);
                    }
                }

                // Carica semilavorati ODL con info articolo, fase, operatore e fornitore
                $tabella_fornitori_join = ($utente->id_azienda == 14) ? 'clienti' : 'fornitori';
                $odl_semilavorati = DB::select('
                    SELECT os.*, a.titolo, a.codice_articolo, f.descrizione as nome_fase,
                           u.nome as operatore_nome, u.cognome as operatore_cognome,
                           fo.ragione_sociale as fornitore_nome
                    FROM odl_semilavorati os
                    JOIN articoli a ON a.id = os.id_articolo
                    LEFT JOIN fasi f ON f.id = os.id_fase
                    LEFT JOIN utenti u ON u.id = os.id_operatore
                    LEFT JOIN ' . $tabella_fornitori_join . ' fo ON fo.id = os.id_fornitore
                    WHERE os.id_odl = ? AND os.id_azienda = ?
                    ORDER BY os.id_fase ASC, a.titolo ASC',
                    [$id_odl, $utente->id_azienda]
                );

                // Per ogni semilavorato carica le sue fasi di produzione e i materiali della sua distinta base
                foreach ($odl_semilavorati as $semi) {
                    $semi->fasi = DB::select('
                        SELECT f.descrizione
                        FROM fasi_articoli fa
                        JOIN fasi f ON f.id = fa.id_fase
                        WHERE fa.id_articolo = ? AND fa.id_azienda = ?
                        ORDER BY fa.id ASC',
                        [$semi->id_articolo, $utente->id_azienda]
                    );

                    $semi->materiali = DB::select('
                        SELECT a.titolo, a.codice_articolo, a.tipologia, a.um, db.qta
                        FROM distinta_base db
                        JOIN articoli a ON a.id = db.id_materiale
                        WHERE db.id_articolo = ? AND db.id_azienda = ?
                        ORDER BY a.tipologia ASC, a.titolo ASC',
                        [$semi->id_articolo, $utente->id_azienda]
                    );
                }

            $azienda = DB::table('aziende')->where('id', $utente->id_azienda)->first();
            return View::make('utente.dettaglio_odl', compact('utente', 'odl', 'odl_righe', 'articolo', 'operatori', 'odl_semilavorati', 'fornitori_odl', 'azienda'));
    }


    public function odl_carica_allegato($id_riga, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        if (!$request->hasFile('allegato')) {
            return redirect()->back()->with('error', 'Nessun file selezionato');
        }

        $riga = DB::table('odl_righe')->where('id', $id_riga)->where('id_azienda', $utente->id_azienda)->first();
        if (!$riga) {
            return redirect()->back()->with('error', 'Fase non trovata');
        }

        $file = $request->file('allegato');
        $descrizione = $request->input('descrizione', '');
        $nome_originale = $file->getClientOriginalName();
        $tipo_file = $file->getClientMimeType();
        $dimensione = $file->getSize();
        $nome_file = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path_destinazione = 'uploads/odl/' . $riga->id_odl . '/fase/' . $id_riga . '/';

        if (!file_exists(public_path($path_destinazione))) {
            mkdir(public_path($path_destinazione), 0755, true);
        }

        if ($file->move(public_path($path_destinazione), $nome_file)) {
            DB::table('odl_righe_allegati')->insert([
                'id_odl_riga' => $id_riga,
                'id_odl' => $riga->id_odl,
                'id_azienda' => $utente->id_azienda,
                'id_utente' => $utente->id,
                'nome_originale' => $nome_originale,
                'nome_file' => $nome_file,
                'path_file' => $path_destinazione . $nome_file,
                'tipo_file' => $tipo_file,
                'dimensione' => $dimensione,
                'descrizione' => $descrizione,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            return redirect()->back()->with('success', 'Allegato caricato con successo');
        }

        return redirect()->back()->with('error', 'Errore durante il caricamento del file');
    }

    public function odl_elimina_allegato($id)
    {
        $this->is_loggato();
        $utente = session('utente');

        $allegato = DB::table('odl_righe_allegati')->where('id', $id)->where('id_azienda', $utente->id_azienda)->first();
        if (!$allegato) {
            return redirect()->back()->with('error', 'Allegato non trovato');
        }

        if (file_exists(public_path($allegato->path_file))) {
            unlink(public_path($allegato->path_file));
        }

        DB::table('odl_righe_allegati')->where('id', $id)->where('id_azienda', $utente->id_azienda)->delete();
        return redirect()->back()->with('success', 'Allegato eliminato con successo');
    }

    public function programmazione(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        $flash_msg = $request->query('msg');
        $flash_type = $request->query('msg_type');

        if(isset($dati['elimina_odl'])) {
            $id_odl = $dati['id_odl'];
            $id_dorig = $dati['id_dorig'];

            try {
                DB::beginTransaction();

                // Elimina le righe dell'ODL

                DB::table('odl_righe')->where('id_odl', $id_odl)->where('id_azienda', $utente->id_azienda)->delete();
                DB::table('odl')->where('id', $id_odl)->where('id_azienda', $utente->id_azienda)->delete();

                // Ripristina lo stato della riga ordine
                DB::table('dorig')->where('id', $id_dorig)->where('id_azienda', $utente->id_azienda)->update(['stato_prod' => 0]);
                DB::table('mgmov')->where('id_odl', $id_odl)->where('id_azienda', $utente->id_azienda)->delete();


                DB::commit();

                return redirect('utente/programmazione?msg_type=success&msg=' . urlencode('Ordine di lavoro eliminato con successo'));
            } catch (\Exception $e) {
                DB::rollback();
                return redirect('utente/programmazione?msg_type=error&msg=' . urlencode('Errore durante l\'eliminazione dell\'ordine di lavoro'));
            }
        }




        // Recupera gli ordini non ancora processati in produzione
        $ordini = DB::select("
    SELECT 
        d.*,
        c.ragione_sociale as cliente,
        dr.nome_prodotto,
        dr.qta,
        dr.id as id_dorig,
        a.titolo as articolo
    FROM dotes d
    LEFT JOIN clienti c ON c.id = d.id_cliente
    LEFT JOIN dorig dr ON dr.id_dotes = d.id  
    LEFT JOIN articoli a ON a.id = dr.id_articolo
    WHERE d.cd_do = 'ORD' 
    AND d.id_azienda = ?
    AND d.data_consegna IS NOT NULL
    AND dr.stato_prod = 0
    ORDER BY d.data_consegna DESC",
            [$utente->id_azienda]
        );

        $ordini_in_lavorazione = DB::select("
    SELECT 
        d.*,
        c.ragione_sociale as cliente,
        o.stato as stato_odl,
        o.id as id_odl,
        dr.nome_prodotto,
        dr.qta,
        dr.id as id_dorig,
        a.titolo as articolo
    FROM dotes d
    LEFT JOIN clienti c ON c.id = d.id_cliente
    LEFT JOIN odl o ON o.id_dotes = d.id
    LEFT JOIN dorig dr ON dr.id_dotes = d.id
    LEFT JOIN articoli a ON a.id = dr.id_articolo
    WHERE d.cd_do = 'ORD'
    AND d.id_azienda = ? 
    AND d.data_consegna IS NOT NULL
    AND dr.stato_prod = 1
    ORDER BY d.data_consegna DESC",
            [$utente->id_azienda]
        );

        if(isset($dati['crea_odl'])) {

            $id_dotes = $dati['id_dotes'];
            $id_dorig = $dati['id_dorig'];


            $riga = DB::table('dorig')
                ->where('id', $id_dorig)
                ->where('id_azienda', $utente->id_azienda)
                ->first();

            if(!$riga) {
                return redirect('utente/programmazione?msg_type=error&msg=' . urlencode('Riga ordine non trovata'));
            }

            if(empty($riga->id_articolo)) {
                return redirect('utente/programmazione?msg_type=error&msg=' . urlencode('La riga ordine non ha un articolo collegato dall\'anagrafica: impossibile creare l\'ODL. Modifica l\'ordine e seleziona un articolo.'));
            }

            $fasi_articolo = DB::select("
                SELECT fa.*, f.descrizione
                FROM fasi_articoli fa
                JOIN fasi f ON f.id = fa.id_fase
                WHERE fa.id_articolo = ?
                AND fa.id_azienda = ?",
                [$riga->id_articolo, $utente->id_azienda]
            );

            if(count($fasi_articolo) == 0) {
                return redirect('utente/programmazione?msg_type=error&msg=' . urlencode('L\'articolo non ha fasi di lavorazione configurate: impossibile creare l\'ODL. Configura le fasi nell\'anagrafica articolo e riprova.'));
            }

            $id_odl = DB::table('odl')->insertGetId([
                'id_dotes' => $id_dotes,
                'id_dorig' => $id_dorig,
                'id_utente' => $utente->id,
                'id_azienda' => $utente->id_azienda,
                'numero' => DB::select('SELECT ifnull(max(numero)+1,1) as numero from odl where id_utente = ?', [$utente->id])[0]->numero,
                'id_articolo' => $riga->id_articolo,
                'qta' => $riga->qta,
                'data' => now(),
                'stato' => 0
            ]);

            foreach($fasi_articolo as $fase) {
                DB::table('odl_righe')->insert([
                    'id_azienda' => $utente->id_azienda,
                    'id_odl' => $id_odl,
                    'id_dorig' => $id_dorig,
                    'id_fase' => $fase->id_fase,
                    'qta' => $riga->qta,
                ]);
            }

            DB::table('dorig')
                ->where('id', $id_dorig)
                ->update(['stato_prod' => 1]);

            return redirect('utente/programmazione?msg_type=success&msg=' . urlencode('Ordine di lavoro creato con successo'));
        }

        return View::make('utente.programmazione', compact('utente', 'ordini', 'ordini_in_lavorazione', 'flash_msg', 'flash_type'));
    }


    /**
     * Inserisce le tipologie documento standard mancanti per un'azienda.
     * Non tocca i record esistenti: aggiunge solo i cd_do non presenti.
     * Restituisce l'array dei codici effettivamente inseriti.
     * Chiamata sia al momento della creazione azienda (AdminController/ClienteController)
     * sia "lazy" alla prima apertura della pagina Gestione Documenti.
     */
    public static function inserisci_tipologie_standard($id_azienda, $id_utente = 0)
    {
        $standard = config('tipologie_documenti');
        $esistenti = DB::table('do')
            ->where('id_azienda', $id_azienda)
            ->pluck('cd_do')
            ->toArray();

        $inseriti = [];
        foreach ($standard as $tipo) {
            if (in_array($tipo['cd_do'], $esistenti, true)) {
                continue;
            }
            DB::table('do')->insert(array_merge($tipo, [
                'id_azienda' => $id_azienda,
                'id_utente' => $id_utente,
                'ordinamento' => 0,
                'id_mg_p' => 0,
                'id_mg_a' => 0,
                'scan_code' => 0,
            ]));
            $inseriti[] = $tipo['cd_do'];
        }
        return $inseriti;
    }

    /**
     * Inserisce i magazzini standard mancanti per un'azienda (uno per tipologia,
     * marcato is_default=1). Non tocca i record gia' presenti con quel codice.
     * Restituisce i codici inseriti.
     */
    public static function inserisci_magazzini_standard($id_azienda)
    {
        $standard = config('magazzini_tipologie.standard');
        $esistenti_codici = DB::table('mg')
            ->where('id_azienda', $id_azienda)
            ->pluck('codice_magazzino')
            ->toArray();

        $inseriti = [];
        foreach ($standard as $mag) {
            if (in_array($mag['codice_magazzino'], $esistenti_codici, true)) {
                continue;
            }

            // Se esiste gia' un magazzino default per questa tipologia (creato a mano dall'utente),
            // non forziamo is_default=1 sul nuovo: lo lasciamo a 0 per non avere ambiguita'.
            $esiste_default_per_tipologia = DB::table('mg')
                ->where('id_azienda', $id_azienda)
                ->where('tipologia', $mag['tipologia'])
                ->where('is_default', 1)
                ->exists();

            if ($esiste_default_per_tipologia) {
                $mag['is_default'] = 0;
                $mag['produzione'] = 0;
            }

            DB::table('mg')->insert(array_merge($mag, [
                'id_azienda' => $id_azienda,
            ]));
            $inseriti[] = $mag['codice_magazzino'];
        }
        return $inseriti;
    }

    public function gestione_documenti(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if(isset($dati['ripristina_standard'])) {
            $inseriti = self::inserisci_tipologie_standard($utente->id_azienda, $utente->id);
            if (count($inseriti) > 0) {
                return redirect()->back()->with('success', 'Tipologie standard ripristinate: ' . implode(', ', $inseriti));
            }
            return redirect()->back()->with('success', 'Tutte le tipologie standard erano gia\' presenti.');
        }

        if(isset($dati['salva_flusso_documentale'])){
            unset($dati['salva_flusso_documentale']);

            $dati['flusso'] = isset($dati['flusso']) ? implode(',', $dati['flusso']) : '';

            DB::table('do')
                ->where('id', $dati['id'])
                ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
                ->update($dati);

        }

        if (isset($dati['aggiungi'])) {
            DB::table('do')
                ->insert([
                    'id_azienda' => $utente->id_azienda,
                    'cd_do' => $dati['edit_cd_do'],
                    'descrizione' => $dati['edit_descrizione'],
                    'attivo' => $request->has('edit_attivo') ? 1 : 0,
                    'passivo' => $request->has('edit_passivo') ? 1 : 0,
                    'scarico' => $request->has('edit_scarico') ? 1 : 0,
                    'carico' => $request->has('edit_carico') ? 1 : 0,
                    'id_mg_a' => $dati['edit_id_mg_a'],
                    'id_mg_p' => $dati['edit_id_mg_p'],
                    'trasferimento' => $request->has('edit_trasferimento') ? 1 : 0,
                    'fatturazione_ingresso' => $request->has('edit_fatturazione_ingresso') ? 1 : 0,
                    'fatturazione_uscita' => $request->has('edit_fatturazione_uscita') ? 1 : 0,
                    'ordine' => $request->has('edit_ordine') ? 1 : 0,
                    'scan_code' => $request->has('edit_scan_code') ? 1 : 0,
                ]);

            return redirect()->back()->with('success', 'Documento Aggiunto con successo!');
        }


        if (isset($dati['elimina'])) {
            $codici_standard = array_column(config('tipologie_documenti'), 'cd_do');
            $documento_da_eliminare = DB::table('do')
                ->where('id', $dati['id_documento'])
                ->where('id_azienda', $utente->id_azienda)
                ->first();
            if ($documento_da_eliminare && in_array($documento_da_eliminare->cd_do, $codici_standard, true)) {
                return redirect()->back()->with('error', 'Impossibile eliminare la tipologia standard "' . $documento_da_eliminare->cd_do . '". Se non la usi, lasciala disattivata.');
            }
            DB::table('do')
                ->where('id', $dati['id_documento'])
                ->where('id_azienda', $utente->id_azienda)
                ->delete();

            return redirect()->back()->with('success', 'Documento eliminato con successo!');
        }

        if (isset($dati['modifica_documento'])) {
            // Per le tipologie standard non permettiamo cambio del cd_do
            $codici_standard = array_column(config('tipologie_documenti'), 'cd_do');
            $documento_attuale = DB::table('do')
                ->where('id', $dati['id_documento'])
                ->where('id_azienda', $utente->id_azienda)
                ->first();
            if ($documento_attuale && in_array($documento_attuale->cd_do, $codici_standard, true)) {
                $dati['edit_cd_do'] = $documento_attuale->cd_do; // forza il codice originale
            }

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
                    'id_mg_a' => $dati['edit_id_mg_a'],
                    'id_mg_p' => $dati['edit_id_mg_p'],
                    'trasferimento' => $request->has('edit_trasferimento') ? 1 : 0,
                    'fatturazione_ingresso' => $request->has('edit_fatturazione_ingresso') ? 1 : 0,
                    'fatturazione_uscita' => $request->has('edit_fatturazione_uscita') ? 1 : 0,
                    'ordine' => $request->has('edit_ordine') ? 1 : 0,
                    'scan_code' => $request->has('edit_scan_code') ? 1 : 0,
                ]);

            return redirect()->back()->with('success', 'Documento modificato con successo!');
        }

        $documenti = DB::table('do')
            ->where('id_azienda', $utente->id_azienda) // Filtra per azienda
            ->get();

        if(sizeof($documenti) == 0){
            // Setup automatico (fallback) per aziende preesistenti senza tipologie
            self::inserisci_tipologie_standard($utente->id_azienda, $utente->id);
            $documenti = DB::table('do')
                ->where('id_azienda', $utente->id_azienda)
                ->get();
        }


        $magazzini = DB::table('mg')
            ->where('id_azienda', $utente->id_azienda)
            ->get();

        $codici_standard = array_column(config('tipologie_documenti'), 'cd_do');

        return View::make('utente.gestione_documenti', compact('utente', 'documenti','magazzini','codici_standard'));
    }

    public function documenti_da_registrare(Request $request) {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();


        // Registrazione con piano conti
        if(isset($dati['registra_contabile'])){
            return $this->registra_con_piano_conti($dati, $utente);
        }

        // Registrazione semplice (vecchia)
        if(isset($dati['registra'])){
            $update['da_registrare'] = 0;
            DB::table('dotes')->where('id',$dati['id'])->update($update);
            return Redirect::to('utente/documenti_da_registrare');
        }

        // Query RAW per i documenti da registrare
        $documenti = DB::select("SELECT d.*, c.nome as cliente_nome, c.cognome as cliente_cognome, c.ragione_sociale as cliente_ragione_sociale FROM dotes d LEFT JOIN clienti c ON d.cd_cf = c.cd_cf WHERE d.id_azienda = ? AND d.fattura_in_ingresso = 1 and d.da_registrare = 1 ORDER BY d.data_doc DESC", [$utente->id_azienda]);

        // Prendi i conti per la modal
        $conti = DB::table('piano_conti')
            ->where('id_azienda', $utente->id_azienda)
            ->where('attivo', 1)
            ->orderBy('codice_conto')
            ->get();

        return view('utente.documenti_da_registrare', compact('documenti', 'conti'));
    }

    private function registra_con_piano_conti($dati, $utente) {
        $documento = DB::table('dotes')
            ->where('id', $dati['id_documento'])
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$documento) {
            return redirect()->back()->with('error', 'Documento non trovato');
        }

        // Validazione
        $totale_dare = array_sum($dati['importo_dare'] ?? []);
        $totale_avere = array_sum($dati['importo_avere'] ?? []);

        if (abs($totale_dare - $totale_avere) > 0.01) {
            return redirect()->back()->with('error', 'Totale DARE deve essere uguale al totale AVERE');
        }

        try {
            DB::beginTransaction();

            // Genera numero registrazione
            $numero = $this->genera_numero_registrazione($utente->id_azienda);

            // Crea registrazione contabile
            $registrazione_id = DB::table('registrazioni_contabili')->insertGetId([
                'id_azienda' => $utente->id_azienda,
                'id_utente' => $utente->id,
                'numero_registrazione' => $numero,
                'data_registrazione' => $documento->data_doc,
                'data_documento' => $documento->data_doc,
                'numero_documento' => $documento->numero_doc,
                'tipo_documento' => $documento->cd_do,
                'descrizione' => 'Registrazione ' . $documento->cd_do . ' n.' . $documento->numero_doc,
                'totale_dare' => $totale_dare,
                'totale_avere' => $totale_avere,
                'quadrata' => 1,
                'id_fattura_origine' => $documento->id,
                'automatica' => 0,
                'created_at' => now()
            ]);

            // Salva righe contabili
            $riga_numero = 1;
            for ($i = 0; $i < count($dati['id_conto'] ?? []); $i++) {
                if (!empty($dati['id_conto'][$i]) &&
                    (!empty($dati['importo_dare'][$i]) || !empty($dati['importo_avere'][$i]))) {

                    $conto = DB::table('piano_conti')->where('id', $dati['id_conto'][$i])->first();

                    DB::table('righe_contabili')->insert([
                        'id_registrazione' => $registrazione_id,
                        'id_azienda' => $utente->id_azienda,
                        'riga_numero' => $riga_numero,
                        'id_conto' => $dati['id_conto'][$i],
                        'codice_conto' => $conto->codice_conto,
                        'descrizione' => $dati['descrizione_riga'][$i] ?? '',
                        'importo_dare' => $dati['importo_dare'][$i] ?? 0,
                        'importo_avere' => $dati['importo_avere'][$i] ?? 0
                    ]);

                    $riga_numero++;
                }
            }

            // Segna documento come registrato
            DB::table('dotes')
                ->where('id', $dati['id_documento'])
                ->update(['da_registrare' => 0]);

            DB::commit();

            return redirect()->back()->with('success', 'Documento registrato con n.' . $numero);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()->with('error', 'Errore: ' . $e->getMessage());
        }
    }

    private function genera_numero_registrazione($id_azienda) {
        $ultimo = DB::table('registrazioni_contabili')
            ->where('id_azienda', $id_azienda)
            ->whereYear('data_registrazione', date('Y'))
            ->max('numero_registrazione') ?? 0;

        return $ultimo + 1;
    }

    public function riepilogo_documenti(Request $request, $cd_do, $mese = 0) {
        $this->is_loggato();
        $utente = session('utente');
        $anno = session('anno');
        if (empty($anno)) {
            $anno = date('Y');
        }
        $dati = $request->all();

        if (isset($dati['elimina'])) {
            $righe_da_eliminare = DB::table('dorig')
                ->where('id_dotes', $dati['id'])
                ->where('id_azienda', $utente->id_azienda)
                ->get();

            // Per ogni riga, controlliamo se ha un documento originale da aggiornare
            foreach($righe_da_eliminare as $riga) {
                if($riga->id_dorig_evade) {
                    // Aggiorniamo la riga originale sottraendo la quantità evasa
                    DB::table('dorig')
                        ->where('id', $riga->id_dorig_evade)
                        ->where('id_azienda', $utente->id_azienda)
                        ->update([
                            'qta_evasa' => DB::raw('qta_evasa - ' . $riga->qta),
                        ]);
                }

                DB::delete('delete from mgmov where id_dorig ='.$riga->id);

                $mg = new MagazzinoController();
                $mg->ricalcolaGiacenze();
            }

            DB::table('dotes')->where('id', $dati['id'])->where('id_azienda', $utente->id_azienda)->delete();
            DB::table('dorig')->where('id_dotes', $dati['id'])->where('id_azienda', $utente->id_azienda)->delete();
            DB::table('scadenziario')->where('id_dotes', $dati['id'])->where('id_azienda', $utente->id_azienda)->delete();

            return Redirect::to('utente/riepilogo_documenti/'.$cd_do.'/'.$mese);
        }

        // Verifica se l'utente è un agente
        $is_agente = $utente->id_tipologia == 1;

        // Query base per il riepilogo anno
        $base_query_riepilogo = "
        WITH Mesi AS (
            SELECT 1 AS mese_numero, 'Gennaio' AS nome_mese
            UNION ALL SELECT 2, 'Febbraio'
            UNION ALL SELECT 3, 'Marzo'
            UNION ALL SELECT 4, 'Aprile'
            UNION ALL SELECT 5, 'Maggio'
            UNION ALL SELECT 6, 'Giugno'
            UNION ALL SELECT 7, 'Luglio'
            UNION ALL SELECT 8, 'Agosto'
            UNION ALL SELECT 9, 'Settembre'
            UNION ALL SELECT 10, 'Ottobre'
            UNION ALL SELECT 11, 'Novembre'
            UNION ALL SELECT 12, 'Dicembre'
        )
        
        SELECT 
            m.mese_numero,
            m.nome_mese,
            COALESCE(v.imponibile, 0) as imponibile,
            COALESCE(v.numero_documenti, 0) as numero_documenti
        FROM Mesi m
        LEFT JOIN (
            SELECT MONTH(data_doc) as mese, SUM(imponibile) AS imponibile, COUNT(id) AS numero_documenti 
            FROM dotes 
            WHERE da_registrare = 0 
            AND YEAR(data_doc) = " . $anno . " 
            AND id_azienda = " . $utente->id_azienda . " 
            AND cd_do = '" . $cd_do . "'";

        // Aggiungi filtro per l'agente se necessario
        if ($is_agente) {
            $base_query_riepilogo .= " AND id_agente = " . $utente->id;
        }

        $base_query_riepilogo .= " GROUP BY MONTH(data_doc)
        ) v ON m.mese_numero = v.mese
        ORDER BY m.mese_numero";

        $riepilogo_anno = DB::select($base_query_riepilogo);

        // Query base per i documenti
        $base_query_docs = '
        SELECT 
            d.*,
            (SELECT COUNT(*) FROM dorig WHERE id_dotes = d.id) as num_righe_totale,
            (SELECT COUNT(*) FROM dorig WHERE id_dotes = d.id AND qta_evasa >= qta) as num_righe_evase
        FROM dotes d 
        WHERE d.cd_do = "' . htmlentities($cd_do, 3, 'UTF-8', '') . '"
        AND d.da_registrare = 0 
        AND YEAR(d.data_doc) = ' . $anno . ' 
        AND d.id_azienda = ' . $utente->id_azienda;

        // Aggiungi filtro per l'agente se necessario
        if ($is_agente) {
            $base_query_docs .= ' AND d.id_agente = ' . $utente->id;
        }

        // Aggiungi filtro per mese se specificato
        if ($mese != 0) {
            $base_query_docs .= ' AND MONTH(d.data_doc) = ' . $mese;
        }

        $base_query_docs .= ' ORDER BY d.numero DESC, d.data_doc DESC';

        $dotes = DB::select($base_query_docs);

        return view('utente.riepilogo_documenti', compact('utente', 'riepilogo_anno', 'cd_do', 'dotes', 'mese'));
    }



    public function esporta_documenti($cd_do, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $anno = session('anno');
        if (empty($anno)) {
            $anno = date('Y');
        }

        $filtro = $request->get('filtro', 'tutti');
        $mese = $request->get('mese', 0);

        // Query base
        $query = DB::table('dotes')
            ->where('cd_do', $cd_do)
            ->where('da_registrare', 0)
            ->whereYear('data_doc', $anno)
            ->where('id_azienda', $utente->id_azienda);

        // Filtro per mese se specificato
        if ($mese != 0) {
            $query->whereMonth('data_doc', $mese);
        }

        // Filtro per agente se necessario
        if ($utente->id_tipologia == 1) {
            $query->where('id_agente', $utente->id);
        }

        // Filtro per tipologia documento (solo per FTI)
        if ($cd_do == 'FTI') {
            if ($filtro == 'TD04') {
                $query->where('tipologia_documento', 'TD04');
                $tipoExport = 'Note_di_Credito_Ingresso';
            } elseif ($filtro == 'fatture') {
                $query->where(function($q) {
                    $q->where('tipologia_documento', '!=', 'TD04')
                        ->orWhereNull('tipologia_documento');
                });
                $tipoExport = 'Fatture_Ingresso';
            } else {
                $tipoExport = 'FTI_Tutti';
            }
        } else {
            // Nomi per altri tipi di documento
            $nomiDocumenti = [
                'FTE' => 'Fatture_Uscita',
                'DDT' => 'DDT',
                'PRE' => 'Preventivi',
                'ORC' => 'Ordini_Cliente',
                'ORF' => 'Ordini_Fornitore',
            ];
            $tipoExport = $nomiDocumenti[$cd_do] ?? $cd_do;
        }

        $dotes = $query->orderBy('data_doc', 'desc')->get();

        // Crea il file Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Documenti');

        // Intestazioni
        if ($cd_do == 'FTI') {
            $headers = ['Numero', 'Data', 'Fornitore', 'Partita IVA', 'Tipologia', 'Imponibile', 'Imposta', 'Totale'];
            $colPartitaIva = 'D';
            $colImponibile = 'F';
            $colImposta = 'G';
            $colTotale = 'H';
            $lastCol = 'H';
        } else {
            $headers = ['Numero', 'Data', 'Cliente', 'Partita IVA', 'Imponibile', 'Imposta', 'Totale'];
            $colPartitaIva = 'D';
            $colImponibile = 'E';
            $colImposta = 'F';
            $colTotale = 'G';
            $lastCol = 'G';
        }

        // Scrivi intestazioni
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->getFont()->setBold(true);
            $sheet->getStyle($col . '1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('E9ECEF');
            $col++;
        }

        // Formatta colonna Partita IVA come testo
        $sheet->getStyle($colPartitaIva . ':' . $colPartitaIva)
            ->getNumberFormat()
            ->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_TEXT);

        // Dati
        $row = 2;
        foreach ($dotes as $d) {
            $imponibile = $d->imponibile - ($d->importo_cassa ?? 0);
            $imposta = $d->imposta ?? 0;
            $totale = $imponibile + $imposta;
            $partitaIva = $d->partita_iva_fatturazione ?? '';

            $sheet->setCellValue('A' . $row, $d->numero_doc);
            $sheet->setCellValue('B' . $row, date('d/m/Y', strtotime($d->data_doc)));
            $sheet->setCellValue('C' . $row, $d->ragione_sociale_fatturazione);

            // Imposta la partita IVA come testo esplicito
            $sheet->setCellValueExplicit($colPartitaIva . $row, $partitaIva, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

            if ($cd_do == 'FTI') {
                $tipologia = ($d->tipologia_documento == 'TD04') ? 'Nota di Credito' : 'Fattura';
                $sheet->setCellValue('E' . $row, $tipologia);
                $sheet->setCellValue($colImponibile . $row, $imponibile);
                $sheet->setCellValue($colImposta . $row, $imposta);
                $sheet->setCellValue($colTotale . $row, $totale);
            } else {
                $sheet->setCellValue($colImponibile . $row, $imponibile);
                $sheet->setCellValue($colImposta . $row, $imposta);
                $sheet->setCellValue($colTotale . $row, $totale);
            }

            $row++;
        }

        $lastRow = $row - 1;

        // Formatta colonne numeriche come valuta
        $sheet->getStyle($colImponibile . '2:' . $colImponibile . $lastRow)
            ->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle($colImposta . '2:' . $colImposta . $lastRow)
            ->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle($colTotale . '2:' . $colTotale . $lastRow)
            ->getNumberFormat()->setFormatCode('#,##0.00');

        // Auto-dimensiona le colonne
        foreach (range('A', $lastCol) as $colLetter) {
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }

        // Nome file
        $filename = 'Export_' . $tipoExport . '_' . $anno . ($mese ? '_Mese'.$mese : '') . '_' . date('Ymd_His') . '.xlsx';

        // Output
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function crea_documento(Request $request, $cd_do) {
        $this->is_loggato();
        $utente = session('utente');
        $anno = session('anno') ?? date('Y');
        $dati = $request->all();

        // Recupera i clienti solo per l'azienda dell'utente loggato
        // Recupera clienti e fornitori
        $clienti = DB::table('clienti')->where('id_tipologia', 2)->where('id_azienda', $utente->id_azienda)->get();
        $fornitori = DB::table('fornitori')->where('id_azienda', $utente->id_azienda)->get();  // AGGIUNGI QUESTA

        // Dati per il flusso manutenzione (gated lato view dai flag aziendali)
        $vagoni = DB::table('vagoni')
            ->where('id_azienda', $utente->id_azienda)
            ->where('attivo', 1)
            ->orderBy('codice')
            ->get();
        $lavorazioni_disponibili = DB::table('lavorazioni')
            ->where('id_azienda', $utente->id_azienda)
            ->where('attivo', 1)
            ->orderBy('descrizione')
            ->get();

        // Carica i dati degli agenti associati ai clienti
        $clienti_agenti = DB::table('clienti')
            ->where('id_azienda', $utente->id_azienda)
            ->whereNotNull('id_agente')
            ->where('id_agente', '!=', 0)
            ->pluck('id_agente', 'id')
            ->toArray();

        $documento = DB::table('do')->where('cd_do', $cd_do)->where('id_azienda', $utente->id_azienda)->first();
        $scanBarcodeEnabled = $documento->scan_code ?? 0;
        $productId = $request->input('id_articolo');

        // Controlla se è stato inviato il form per aggiungere un documento
        if (isset($dati['aggiungi_dotes'])) {
            unset($dati['aggiungi_dotes']);

            /*evita l'inserimento di queste cose*/
            $datiDotes = array_filter($dati, function($key) {
                return $key !== 'products' && $key !== 'product_id' && $key !== 'product_name' && $key !== 'scadenziario' && $key !== 'deleted_rows' && $key !== 'lavorazioni_applicate' && $key !== 'righe_lavorazione' && $key !== '_token';
            }, ARRAY_FILTER_USE_KEY);

            $datiDotes['da_registrare'] = 0;
            $datiDotes['cd_do'] = $cd_do;
            $datiDotes['id_azienda'] = $utente->id_azienda; // Aggiungi l'azienda
            $datiDotes['imponibile'] = (float)str_replace(['€', ' '], '', $datiDotes['imponibile']);
            $datiDotes['imposta'] = (float)str_replace(['€', ' '], '', $datiDotes['imposta']);
            $datiDotes['totale'] = (float)str_replace(['€', ' '], '', $datiDotes['totale']);

            // Normalizza id_vagone (vuoto/0 -> null)
            if (isset($datiDotes['id_vagone']) && ($datiDotes['id_vagone'] === '' || (int)$datiDotes['id_vagone'] === 0)) {
                $datiDotes['id_vagone'] = null;
            }

            $idDotes = DB::table('dotes')->insertGetId($datiDotes);

            // Applica lavorazioni del catalogo (multi-select dal form manutenzione - legacy)
            if (!empty($dati['lavorazioni_applicate']) && is_array($dati['lavorazioni_applicate'])) {
                \App\Services\ApplicaLavorazione::applicaA(
                    (int) $idDotes,
                    (int) $utente->id_azienda,
                    $dati['lavorazioni_applicate'],
                    (int) $utente->id
                );
            }

            // Inserisci righe lavorazione direttamente dal form manutenzione (nuova UI)
            if (!empty($dati['righe_lavorazione']) && is_array($dati['righe_lavorazione'])) {
                $nRiga = 0;
                foreach ($dati['righe_lavorazione'] as $rl) {
                    if (!is_array($rl)) continue;
                    $descr = trim($rl['descrizione'] ?? '');
                    $codiceR = trim($rl['codice'] ?? '');
                    if ($descr === '' && $codiceR === '') continue; // skippa righe completamente vuote

                    $nRiga++;
                    $qta       = (float) str_replace(',', '.', $rl['qta'] ?? 0);
                    $minuti    = (float) str_replace(',', '.', $rl['minuti'] ?? 0);
                    $pu        = (float) str_replace(',', '.', $rl['pu'] ?? 0);
                    $attRaw    = (float) str_replace(',', '.', $rl['attivita'] ?? 1);
                    $att       = $attRaw > 0 ? $attRaw : 1;
                    $aliquota  = (int)   ($rl['aliquota'] ?? 22);
                    $materiale = (float) str_replace(',', '.', $rl['materiale'] ?? 0);

                    if ($minuti > 0) {
                        $pt = round($pu * $minuti / 60, 2);
                    } else {
                        $pt = round($pu * $att * $qta, 2);
                    }
                    $imposta = round($pt * $aliquota / 100, 2);
                    $totale  = $pt + $imposta;
                    $isOrario = $minuti > 0;

                    DB::table('dorig')->insert([
                        'id_azienda'            => $utente->id_azienda,
                        'id_utente'             => $utente->id,
                        'id_cliente'            => $datiDotes['id_cliente'] ?? null,
                        'id_dotes'              => $idDotes,
                        'id_testata'            => $idDotes,
                        'cd_do'                 => $cd_do,
                        'numero_doc'            => $datiDotes['numero_doc'] ?? null,
                        'data_doc'              => $datiDotes['data_doc'] ?? null,
                        'cd_ar'                 => $codiceR !== '' ? $codiceR : null,
                        'n_riga'                => $nRiga,
                        'descrizione'           => $descr !== '' ? $descr : null,
                        'qta'                   => $isOrario ? round($minuti / 60, 3) : round($qta * $att, 3),
                        'um'                    => $isOrario ? 'H' : 'PZ',
                        'pu'                    => $pu,
                        'pt'                    => $pt,
                        'prezzo_unitario'       => $pu,
                        'prezzo_totale'         => $pt,
                        'prezzo_totale_iva'     => $totale,
                        'iva'                   => $aliquota,
                        'imponibile'            => $pt,
                        'imposta'               => $imposta,
                        'totale'                => $totale,
                        'servizio'              => trim($rl['servizio'] ?? '') !== '' ? $rl['servizio'] : null,
                        'setup_tank'            => isset($rl['setup_tank']) && (int) $rl['setup_tank'] === 1 ? 1 : 0,
                        'attivita'              => $att,
                        'minuti'                => $minuti,
                        'materiale'             => $materiale,
                        'descrizione_materiale' => trim($rl['descrizione_materiale'] ?? '') !== '' ? $rl['descrizione_materiale'] : null,
                    ]);
                }

                // Ricalcola aggregati testata dalla somma delle righe effettivamente inserite
                \App\Services\ApplicaLavorazione::ricalcolaAggregatiDotes((int) $idDotes, (int) $utente->id_azienda);
            }

            // Gestione dei prodotti
            if (isset($dati['products'])) {
                $nRigaCounter = 0;
                foreach ($dati['products'] as $i => $product) {
                    $nRigaCounter++;
                    // Genera il numero progressivo della riga
                    $nRiga = str_pad($nRigaCounter, 2, '0', STR_PAD_LEFT);

                    // Riga di tipo nota separatrice
                    if (isset($product['is_nota']) && $product['is_nota'] == '1') {
                        DB::table('dorig')->insert([
                            'cd_do' => $datiDotes['cd_do'],
                            'id_cliente' => $dati['id_cliente'],
                            'numero_doc' => $datiDotes['numero_doc'],
                            'data_doc' => date('Y-m-d'),
                            'descrizione' => $product['descrizione'],
                            'qta' => 0,
                            'id_articolo' => 0,
                            'prezzo_unitario' => 0,
                            'prezzo_totale' => 0,
                            'imponibile' => 0,
                            'imposta' => 0,
                            'totale' => 0,
                            'iva' => 0,
                            'id_dotes' => $idDotes,
                            'n_riga' => $nRiga,
                            'id_azienda' => $utente->id_azienda,
                            'nota_riga' => 'NOTA_SEPARATORE'
                        ]);
                        continue;
                    }

                    $anno = date('Y');
                    $barcodeData = $anno . 'C' . $datiDotes['numero_doc'] . '  ' . $nRiga;

                    $prodotto = DB::table('articoli')->where('id', $product['id_articolo'])->first();

                    $sconto_perc_riga = isset($product['sconto_perc']) ? floatval($product['sconto_perc']) : 0;
                    $prezzo_unitario_riga = (float)str_replace(['€', ' '], '', $product['prezzo_unitario']);
                    $prezzo_totale_scontato = $prezzo_unitario_riga * $product['qta'] * (1 - $sconto_perc_riga / 100);

                    DB::table('dorig')->insert([
                        'cd_do' => $datiDotes['cd_do'],
                        'id_cliente' => $dati['id_cliente'],
                        'numero_doc' => $datiDotes['numero_doc'],
                        'data_doc' => date('Y-m-d'),
                        'descrizione' => $product['descrizione'],
                        'qta' => $product['qta'],
                        'qta_evadibile_prod' => $product['qta'],
                        'um' => $product['um'],
                        'id_articolo' => $product['id_articolo'],
                        'prezzo_unitario' => $prezzo_unitario_riga,
                        'sconto_perc' => $sconto_perc_riga,
                        'prezzo_totale' => $prezzo_totale_scontato,
                        'imponibile' => (float)str_replace(['€', ' '], '', $product['imponibile']),
                        'imposta' => (float)str_replace(['€', ' '], '', $product['imposta']),
                        'totale' => (float)str_replace(['€', ' '], '', (float)str_replace(['€', ' '], '', $product['prezzo_unitario'])+(float)str_replace(['€', ' '], '', $product['imposta'])),
                        'lotto' => $product['lotto'],
                        'natura' => isset($product['natura']) ? $product['natura'] : null,
                        'iva' => $product['iva'],
                        'rif_normativo' => isset($product['rif_normativo']) ? $product['rif_normativo'] : null,
                        'rif_normativo_pdf' => isset($product['rif_normativo_pdf']) ? $product['rif_normativo_pdf'] : null,
                        'id_dotes' => $idDotes,
                        'n_riga' => $nRiga,
                        'barcode' => isset($prodotto) && isset($prodotto->barcode) ? $prodotto->barcode : null,
                        'id_azienda' => $utente->id_azienda,
                        'nota_riga' => isset($product['nota_riga']) ? $product['nota_riga'] : null
                    ]);
                }
            }

            if(in_array($cd_do, ['FTV', 'NC', 'PRV'])) {
                if (isset($dati['scadenziario'])) {
                    foreach ($dati['scadenziario'] as $scadenziario) {
                        $scadenziario['id_dotes'] = $idDotes;
                        $scadenziario['id_azienda'] = $utente->id_azienda;
                        $scadenziario['tipo_movimento'] = 'entrata';
                        $scadenziario['id_cliente'] = $dati['id_cliente'];

                        DB::table('scadenziario')->insert($scadenziario);
                    }
                }
            }

            $documento = DB::table('do')->where('cd_do', $datiDotes['cd_do'])->where('id_azienda', $utente->id_azienda)->first();
            if($documento && ($documento->carico == 1 || $documento->scarico == 1 || $documento->trasferimento == 1)) {
                $magazzinoController = new MagazzinoController();
                $magazzinoController->generaMovimentiMancanti($idDotes);
            }

            return Redirect::to('/utente/modifica_documento/'.$idDotes);
        }

        $prodotti_finiti = DB::select('SELECT * from articoli where id_azienda ='.$utente->id_azienda.' and tipologia = 0 order by descrizione DESC');
        $materie_prime = DB::select('SELECT * from articoli where id_azienda ='.$utente->id_azienda.' and tipologia = 1 order by descrizione DESC');
        $commerciali = DB::select('SELECT * from articoli where id_azienda ='.$utente->id_azienda.' and tipologia = 2 order by descrizione DESC');
        $numero_doc = DB::select('SELECT IFNULL(max(numero_doc + 1),1) as numero_doc from dotes where cd_do = "'.htmlentities($cd_do,3,'UTF-8','').'" and YEAR(data_doc) = '.$anno.' and id_azienda = '.$utente->id_azienda)[0]->numero_doc;
        $modalita = 'crea';
        $azienda = DB::select('SELECT * from aziende where id ='.$utente->id_azienda);

        $agenti = DB::table('utenti')
            ->where('id_tipologia', 1)
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('cognome')
            ->orderBy('nome')
            ->get();

        if(sizeof($azienda) > 0) {
            $azienda = $azienda[0];
            // Branch: per il flusso manutenzione (PRE/ORD con flag azienda attivo) usa la view dedicata
            if (!empty($azienda->manut_workflow_accettazione_multistep) && in_array($cd_do, ['PRE', 'ORD'])) {
                return View::make('utente.crea_documento_manutenzione', compact('utente', 'fornitori', 'azienda','agenti','clienti', 'cd_do', 'documento', 'scanBarcodeEnabled', 'prodotti_finiti', 'materie_prime', 'commerciali', 'numero_doc', 'modalita', 'clienti_agenti', 'vagoni', 'lavorazioni_disponibili'));
            }

            return View::make('utente.crea_documento', compact('utente', 'fornitori', 'azienda','agenti','clienti', 'cd_do', 'documento', 'scanBarcodeEnabled', 'prodotti_finiti', 'materie_prime', 'commerciali', 'numero_doc', 'modalita', 'clienti_agenti', 'vagoni', 'lavorazioni_disponibili'));
        }
    }

    public function modifica_documento(Request $request, $id) {
        $this->is_loggato();
        $utente = session('utente');
        $anno = session('anno');
        $dati = $request->all();

        if(isset($dati['evadi'])){
            unset($dati['evadi']);

            $vecchio_dotes = DB::select('SELECT * from dotes where id_azienda ='.$utente->id_azienda.' and id ='.$dati['id_dotes_originale']);
            if(sizeof($vecchio_dotes) > 0) {
                $vecchio_dotes = $vecchio_dotes[0];

                $numero_doc = DB::select('SELECT IFNULL(max(numero_doc + 1),1) as numero_doc from dotes where cd_do = "'.htmlentities($dati['cd_do'],3,'UTF-8','').'" and YEAR(data_doc) = '.$anno.' and id_azienda = '.$utente->id_azienda)[0]->numero_doc;

                $dotes_da_evadere = DB::select('SELECT * from dotes where id ='.$id);
                if(sizeof($dotes_da_evadere) > 0) {
                    $dotes_da_evadere = (array) $dotes_da_evadere[0];
                    unset($dotes_da_evadere['id']);
                    $dotes_da_evadere['cd_do'] = $dati['cd_do'];
                    $dotes_da_evadere['id_dotes_evade'] = $dati['id_dotes_originale'];
                    $dotes_da_evadere['data_doc'] = now();
                    $dotes_da_evadere['numero_doc'] = $numero_doc;
                    $dotes_da_evadere['da_registrare'] = 0;
                    $dotes_da_evadere['stato'] = 0;

                    $nuovoDotesId = DB::table('dotes')->insertGetId($dotes_da_evadere);

                    foreach ($dati['quantita_evasa'] as $id => $qtaEvasa) {

                        $dorig_da_evadere = DB::table('dorig')->where('id', $id)->where('id_azienda', $utente->id_azienda)->first();
                        $dorig_da_evadere = (array) $dorig_da_evadere;
                        unset($dorig_da_evadere['id']);
                        $dorig_da_evadere['id_dotes'] = $nuovoDotesId;
                        $dorig_da_evadere['id_dorig_evade'] = $id;
                        $dorig_da_evadere['qta'] = $qtaEvasa;
                        $dorig_da_evadere['qta_evadibile_prod'] = $qtaEvasa;
                        $dorig_da_evadere['qta_evasa'] = 0;
                        $dorig_da_evadere['cd_do'] = $dati['cd_do'];
                        DB::table('dorig')->insert($dorig_da_evadere);
                        DB::table('dorig')->where('id', $id)->update(['qta_evasa' => $dorig_da_evadere['qta_evasa'] + $qtaEvasa]);

                        // aggiorna_status_documento
                    }


                    $documento = DB::table('do')->where('cd_do', $dati['cd_do'])->where('id_azienda', $utente->id_azienda)->first();
                    if($documento && ($documento->carico == 1 || $documento->scarico == 1 || $documento->trasferimento == 1)) {
                        $magazzinoController = new MagazzinoController();
                        $magazzinoController->generaMovimentiMancanti($nuovoDotesId);
                    }

                    return Redirect::to('/utente/modifica_documento/' . $nuovoDotesId);

                }

            }
        }

        if (isset($dati['invia_mail'])) {

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtps.aruba.it';
            $mail->SMTPAuth = true;
            $mail->Username = 'noreply@gestya.it';
            $mail->Password = 'jwZFTChzg8gp41?c';
            $mail->SMTPSecure = 'ssl';
            $mail->CharSet = 'utf-8';
            $mail->Port = 465;
            $mail->setFrom('noreply@gestya.it');

            $destinatari = strpos($dati['email_destinatari'], ';') !== false ? explode(';', $dati['email_destinatari']) : [$dati['email_destinatari']];

            foreach($destinatari as $email) {
                $email = trim($email);
                if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $mail->addAddress($email);
                }
            }


            $dotes = DB::select('SELECT * from dotes where id = '.$id.' and id_azienda = '.$utente->id_azienda);
            if(sizeof($dotes) > 0){
                $dotes = $dotes[0];
                $do = DB::select('SELECT * from do where cd_do = "'.$dotes->cd_do.'" and id_azienda = '.$utente->id_azienda);
                if(sizeof($do) > 0){
                    $do = $do[0];
                    $dorig = DB::select('SELECT * from dorig where id_dotes ='.$dotes->id);
                    $azienda = DB::select('SELECT * from aziende where id ='.$dotes->id_azienda);
                    $scadenze = DB::select('SELECT * from scadenziario where id_dotes ='.$dotes->id);

                    if(sizeof($azienda) > 0){


                        $azienda = $azienda[0];
                        // Aggiungi il Reply-To se presente nel database
                        if (!empty($azienda->email_replyto) && filter_var($azienda->email_replyto, FILTER_VALIDATE_EMAIL)) {
                            $mail->addReplyTo($azienda->email_replyto, 'Supporto Gestya');
                        }
                        $html = View::make('stampa.documento',compact('dotes','dorig','do','utente','azienda','scadenze'));
                        $mpdf = new \Mpdf\Mpdf(['format' => 'A4', 'mode' => 'utf-8', 'margin_left' => 5, 'margin_right' => 5, 'margin_top' => 5, 'margin_bottom' => 5, 'margin_header' => 0, 'margin_footer' => 0]);
                        $mpdf->SetTitle($do->descrizione.' N. '.$dotes->numero_doc.' del '.$dotes->data_doc);
                        $mpdf->WriteHTML($html);
                        $mpdf->Output('stampa_documento' . $id . '.pdf', 'F');
                        $mail->AddAttachment('stampa_documento' . $id . '.pdf');
                        $mail->isHTML(true);
                        $mail->Subject = $dati['oggetto'];
                        $firma = '<br><br><a href="https://gestya.it/firma/click" target="_blank" style="text-decoration:none;"><img src="https://gestya.it/firma/banner" alt="Gestya" width="300" style="max-width:100%;height:auto;border:0;" /></a>';
                        $mail->Body = nl2br($dati['corpo']) . $firma;
                        $mail->send();

                        unlink('stampa_documento' . $id . '.pdf');

                        DB::table('clienti')
                            ->where('id', $dotes->id_cliente)
                            ->where('id_azienda', $utente->id_azienda)
                            ->update(['mail_recapito' => $dati['email_destinatari']]);


                    }


                }
            }

        }

        if (isset($dati['invia_sdi'])) {


            $fatture = DB::select('SELECT * FROM dotes WHERE id = ? AND (stato = 0 OR stato = 2) AND id_azienda = ?', [$dati['id'], $utente->id_azienda]);


            if (sizeof($fatture) > 0) {
                $testata = $fatture[0];

                $righe = DB::select('SELECT * FROM dorig WHERE id_dotes = ? ', [$dati['id']]);
                $dati_riepilogo = DB::select('SELECT SUM(imponibile) AS imponibile, SUM(imposta) AS imposta, iva, codice_iva, rif_normativo, rif_normativo_pdf FROM dorig WHERE id_dotes = ? GROUP BY codice_iva', [$dati['id']]);
                $azienda = DB::table('aziende')->where('id', $utente->id_azienda)->first();
                $scadenziario = DB::select('SELECT * from scadenziario where id_dotes ='.$dati['id']);

                if (sizeof($righe) > 0) {

                    // Crea il contenuto XML con una view
                    $xml = View::make('fatturazione.xml', compact('testata', 'righe', 'dati_riepilogo', 'azienda','scadenziario'));
                    $fileName = 'IT'.$azienda->partita_iva.'_' . str_pad(base_convert($testata->id, 10, 36), 4, '0', STR_PAD_LEFT) . '.xml';
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
                        if ($this->inviaFileXmlSftp($filePath,$azienda)) {
                            // Se l'invio ha successo, aggiorna lo stato della fattura
                            DB::update('UPDATE dotes SET stato = 1,nome_file_fattura="'.$fileName.'" WHERE id = ? AND id_azienda = ?', [$dati['id'], $utente->id_azienda]);

                            return Redirect::to('/utente/modifica_documento/' . $id);

                        }
                    } catch (Exception $e) {
                        return Redirect::to('/utente/modifica_documento/' . $id);
                    }
                }
            }

        }


        if (isset($dati['modifica_dotes'])) {
            unset($dati['modifica_dotes']);
            /*evita l'inserimento di queste cose*/

            if (!empty($dati['deleted_rows'])) {
                $deletedRowIds = explode(',', $dati['deleted_rows']);
                if (!empty($deletedRowIds)) {
                    DB::table('dorig')
                        ->whereIn('id', $deletedRowIds)
                        ->where('id_dotes', $id)  // Verifica che le righe appartengano al documento corrente
                        ->where('id_azienda', $utente->id_azienda)
                        ->delete();
                }
            }

            $datiDotes = array_filter($dati, function($key) {
                return $key !== 'products' && $key !== 'product_id' && $key !== 'product_name' && $key !== 'scadenziario' && $key !== 'deleted_rows';
            }, ARRAY_FILTER_USE_KEY);


            $datiDotes['id_azienda'] = $utente->id_azienda; // Aggiungi l'azienda
            $datiDotes['imponibile'] = (float)str_replace(['€', ' '], '', $datiDotes['imponibile']);
            $datiDotes['imposta'] = (float)str_replace(['€', ' '], '', $datiDotes['imposta']);
            $datiDotes['totale'] = (float)str_replace(['€', ' '], '', $datiDotes['totale']);

            // Gestione id_commessa: se è vuoto, imposta NULL
            if (isset($datiDotes['id_commessa']) && empty($datiDotes['id_commessa'])) {
                $datiDotes['id_commessa'] = null;
            }

            DB::table('dotes')->where('id', $id)->where('id_azienda', $utente->id_azienda)->update($datiDotes);



            // Gestione dei prodotti
            if (isset($dati['products'])) {
                $nRigaCounter = 0;
                foreach ($dati['products'] as $i => $product) {
                    $nRigaCounter++;
                    // Genera il numero progressivo della riga
                    $nRiga = str_pad($nRigaCounter, 2, '0', STR_PAD_LEFT);
                    $anno = date('Y');
                    $barcodeData = $anno . 'C' . $datiDotes['numero_doc'] . '  ' . $nRiga;

                    // Riga di tipo nota separatrice
                    if (isset($product['is_nota']) && $product['is_nota'] == '1') {
                        if($product['tipologia'] == 'update') {
                            DB::table('dorig')->where('id', $i)->update([
                                'descrizione' => $product['descrizione'],
                                'qta' => 0,
                                'id_articolo' => 0,
                                'prezzo_unitario' => 0,
                                'prezzo_totale' => 0,
                                'imponibile' => 0,
                                'imposta' => 0,
                                'totale' => 0,
                                'iva' => 0,
                                'n_riga' => $nRiga,
                                'nota_riga' => 'NOTA_SEPARATORE'
                            ]);
                        } else {
                            DB::table('dorig')->insert([
                                'id_cliente' => $datiDotes['id_cliente'],
                                'numero_doc' => $datiDotes['numero_doc'],
                                'data_doc' => date('Y-m-d'),
                                'descrizione' => $product['descrizione'],
                                'qta' => 0,
                                'id_articolo' => 0,
                                'prezzo_unitario' => 0,
                                'prezzo_totale' => 0,
                                'imponibile' => 0,
                                'imposta' => 0,
                                'totale' => 0,
                                'iva' => 0,
                                'id_dotes' => $id,
                                'n_riga' => $nRiga,
                                'id_azienda' => $utente->id_azienda,
                                'nota_riga' => 'NOTA_SEPARATORE'
                            ]);
                        }
                        continue;
                    }

                    if($product['tipologia'] == 'update') {
                        DB::table('dorig')->where('id', $i)->update([
                            'id_cliente' => $datiDotes['id_cliente'],
                            'numero_doc' => $datiDotes['numero_doc'],
                            'data_doc' => date('Y-m-d'),
                            'id_articolo' => $product['id_articolo'],
                            'descrizione' => $product['descrizione'],
                            'qta' => $product['qta'],
                            'qta_evadibile_prod' => $product['qta'],
                            'um' => $product['um'],
                            'prezzo_unitario' => (float)str_replace(['€', ' '], '', $product['prezzo_unitario']),
                            'prezzo_totale' => (float)str_replace(['€', ' '], '', $product['prezzo_unitario'] * $product['qta']),
                            'imponibile' => (float)str_replace(['€', ' '], '', $product['imponibile']),
                            'imposta' => (float)str_replace(['€', ' '], '', $product['imposta']),
                            'totale' => (float)str_replace(['€', ' '], '', (float)str_replace(['€', ' '], '', $product['prezzo_unitario']) + (float)str_replace(['€', ' '], '', $product['imposta'])),
                            'lotto' => $product['lotto'],
                            'scadenza_lotto' => isset($product['scadenza_lotto']) ? $product['scadenza_lotto'] : null,
                            'natura' => isset($product['natura']) ? $product['natura'] : null,
                            'iva' => $product['iva'],
                            'rif_normativo' => isset($product['rif_normativo']) ? $product['rif_normativo'] : null,
                            'rif_normativo_pdf' => isset($product['rif_normativo_pdf']) ? $product['rif_normativo_pdf'] : null,
                            'id_dotes' => $id,
                            'n_riga' => $nRiga,
                            'barcode' => isset($prodotto) && isset($prodotto->barcode) ? $barcodeData : null,
                            'id_azienda' => $utente->id_azienda,
                            'nota_riga' => isset($product['nota_riga']) ? $product['nota_riga'] : null

                        ]);

                    } else {

                        DB::table('dorig')->insert([
                            'id_cliente' => $datiDotes['id_cliente'],
                            'numero_doc' => $datiDotes['numero_doc'],
                            'data_doc' => date('Y-m-d'),
                            'id_articolo' => $product['id_articolo'],
                            'descrizione' => $product['descrizione'],
                            'qta' => $product['qta'],
                            'um' => $product['um'],
                            'prezzo_unitario' => (float)str_replace(['€', ' '], '', $product['prezzo_unitario']),
                            'prezzo_totale' => (float)str_replace(['€', ' '], '', $product['prezzo_unitario'] * $product['qta']),
                            'imponibile' => (float)str_replace(['€', ' '], '', $product['imponibile']),
                            'imposta' => (float)str_replace(['€', ' '], '', $product['imposta']),
                            'totale' => (float)str_replace(['€', ' '], '', (float)str_replace(['€', ' '], '', $product['prezzo_unitario']) + (float)str_replace(['€', ' '], '', $product['imposta'])),
                            'lotto' => $product['lotto'],
                            'scadenza_lotto' => isset($product['scadenza_lotto']) ? $product['scadenza_lotto'] : null,
                            'iva' => $product['iva'],
                            'id_dotes' => $id,
                            'n_riga' => $nRiga,
                            'barcode' => isset($prodotto) && isset($prodotto->barcode) ? $barcodeData : null,
                            'id_azienda' => $utente->id_azienda,
                            'nota_riga' => isset($product['nota_riga']) ? $product['nota_riga'] : null

                        ]);
                    }
                }
            }


            // Recupera l'ordine da modificare, filtrando per azienda
            $dotes = DB::table('dotes')->where('id', $id)->where('id_azienda', $utente->id_azienda)->first();
            if(in_array($dotes->cd_do, ['FTV', 'NC', 'PRV'])) {

                DB::delete('DELETE from scadenziario where id_dotes =' . $id);

                if (isset($dati['scadenziario'])) {
                    foreach ($dati['scadenziario'] as $scadenziario) {

                        $scadenziario['id_dotes'] = $id;
                        $scadenziario['id_azienda'] = $utente->id_azienda;
                        $scadenziario['tipo_movimento'] = 'entrata';
                        $scadenziario['id_cliente'] = $datiDotes['id_cliente'];

                        DB::table('scadenziario')->insert($scadenziario);
                    }
                }

            }


            $documento = DB::table('do')->where('cd_do', $dotes->cd_do)->where('id_azienda', $utente->id_azienda)->first();
            if($documento && ($documento->carico == 1 || $documento->scarico == 1 || $documento->trasferimento == 1)) {
                $magazzinoController = new MagazzinoController();

                $magazzinoController->generaMovimentiMancanti($id);
            }

            return Redirect::to('/utente/modifica_documento/' . $id);
        }

        // Recupera l'ordine da modificare, filtrando per azienda
        $dotes = DB::table('dotes')->where('id', $id)->where('id_azienda', $utente->id_azienda)->first();

        if (!$dotes) {
            return redirect()->back()->with('error', 'Documento non trovato o non autorizzato.');
        }

        $do = DB::table('do')->where('cd_do', $dotes->cd_do)->where('id_azienda', $utente->id_azienda)->first();
        $clienti = DB::table('clienti')->where('id_azienda', $utente->id_azienda)->get();
        $fornitori = DB::table('fornitori')->where('id_azienda', $utente->id_azienda)->get();  // AGGIUNGI QUESTA
        $clienteDotes = DB::table('clienti')->where('id', $dotes->id_cliente)->where('id_azienda', $utente->id_azienda)->first();
        $dorig = DB::table('dorig')->where('id_dotes', $dotes->id)->where('id_azienda', $utente->id_azienda)->get();
        $documento = DB::table('do')->where('cd_do', $dotes->cd_do)->first();
        $scanBarcodeEnabled = $documento->scan_code ?? 0;
        $scadenziario = DB::select('SELECT * from scadenziario where id_dotes ='.$dotes->id);

        // Se non ci sono scadenze e il documento è una fattura, crea una scadenza per il totale
        if(empty($scadenziario) && $dotes->cd_do == 'FTV'){
            // Crea una scadenza a 30 giorni con l'importo totale della fattura
            $nuova_scadenza = [
                'id_dotes' => $dotes->id,
                'id_azienda' => $utente->id_azienda,
                'id_cliente' => $dotes->id_cliente,
                'data_scadenza' => date('Y-m-d', strtotime($dotes->data_doc . ' +30 days')),
                'importo' => $dotes->totale,
                'importo_pagato' => 0,
                'tipo_movimento' => 'entrata',
                'modalita_pagamento' => $dotes->modalita_pagamento ?? 'MP05',
                'iban' => $dotes->iban,
                'stato' => 'da_pagare'
            ];

            DB::table('scadenziario')->insert($nuova_scadenza);

            // Ricarica le scadenze dopo l'inserimento
            $scadenziario = DB::select('SELECT * from scadenziario where id_dotes ='.$dotes->id);
        }

        $prodotti_finiti = DB::select('SELECT * from articoli where id_azienda ='.$utente->id_azienda.' and tipologia = 0 order by descrizione DESC');
        $materie_prime = DB::select('SELECT * from articoli where id_azienda ='.$utente->id_azienda.' and tipologia = 1 order by descrizione DESC');
        $commerciali = DB::select('SELECT * from articoli where id_azienda ='.$utente->id_azienda.' and tipologia = 2 order by descrizione DESC');
        $azienda = DB::table('aziende')->where('id', $utente->id_azienda)->first();
        $modalita = 'modifica';
        $cd_do = $dotes->cd_do;
        $agenti = DB::table('utenti')
            ->where('id_tipologia', 1)
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('cognome')
            ->orderBy('nome')
            ->get();

        return View::make('utente.crea_documento', compact('utente', 'dotes', 'fornitori','agenti', 'clienti','scanBarcodeEnabled', 'clienteDotes', 'dorig', 'documento', 'prodotti_finiti','materie_prime','commerciali','modalita','azienda','scadenziario','do','cd_do'));
    }

    public function ecommerce(){
        $this->is_loggato();
        $utente = session('utente');
        return View::make('utente.ecommerce', compact('utente'));
    }


    public function show_gantt($id_preventivo, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Recupero del preventivo
        $preventivo = DB::table('dotes')
            ->where('id', $id_preventivo)
            ->where('cd_do', 'PRE')
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$preventivo) {
            return redirect()->back()->with('error', 'Preventivo non trovato');
        }

        // Recupero righe del preventivo
        $righe = DB::table('dorig')
            ->where('id_dotes', $id_preventivo)
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('n_riga', 'asc')
            ->get();

        // Calcola la durata in giorni tra data_inizio e data_consegna
        $data_inizio = $preventivo->data_inizio ?? $preventivo->data_doc;
        $data_consegna = $preventivo->data_consegna ?? date('Y-m-d', strtotime('+1 month', strtotime($data_inizio)));

        // Calcola la durata totale in giorni
        $durata_totale = round((strtotime($data_consegna) - strtotime($data_inizio)) / (60 * 60 * 24));

        // Prepara i dati per il Gantt
        $gantt_data = [];

        // Task principale del preventivo
        $gantt_data[] = [
            'id' => 'task_0',
            'text' => 'Preventivo #' . $preventivo->numero_doc . ' - ' . $preventivo->oggetto_visibile,
            'start_date' => date('Y-m-d', strtotime($data_inizio)),
            'end_date' => date('Y-m-d', strtotime($data_consegna)),
            'progress' => 0,
            'open' => true
        ];

        // Dividi il preventivo in fasi basate sulle righe
        $giorni_per_riga = max(1, floor($durata_totale / count($righe)));
        $data_corrente = strtotime($data_inizio);

        foreach ($righe as $index => $riga) {
            // Calcola data fine per questa riga
            $data_fine_riga = $index == count($righe) - 1
                ? strtotime($data_consegna)
                : strtotime("+{$giorni_per_riga} days", $data_corrente);

            $gantt_data[] = [
                'id' => 'task_' . ($index + 1),
                'text' => $riga->descrizione,
                'start_date' => date('Y-m-d', $data_corrente),
                'end_date' => date('Y-m-d', $data_fine_riga),
                'progress' => 0,
                'parent' => 'task_0'
            ];

            // Aggiorna la data corrente per la prossima riga
            $data_corrente = strtotime("+1 day", $data_fine_riga);
        }

        // Informazioni aggiuntive per la vista
        $info = [
            'data_inizio' => date('d/m/Y', strtotime($data_inizio)),
            'data_consegna' => date('d/m/Y', strtotime($data_consegna)),
            'durata_giorni' => $durata_totale
        ];

        return View::make('utente.gantt_preventivo', compact('utente', 'preventivo', 'righe', 'gantt_data', 'info'));
    }


    public function agenti(Request $request){
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if(isset($dati['aggiungi'])){
            unset($dati['aggiungi']);
            $dati['id_tipologia'] = 1; // Tipologia 1 per gli agenti
            $dati['id_azienda'] = $utente->id_azienda;

            if($_FILES['immagine']['name'] != ''){
                $pathinfo = pathinfo($_FILES['immagine']['name']);
                $nome = Str::random(20);
                $target = 'immagini/agenti/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['immagine']['tmp_name'], $target);
                $dati['immagine'] = $target;
            } else {
                $dati['immagine'] = '/default/assets/images/users/user-dummy-img.jpg';
            }

            DB::table('utenti')->insert($dati);
            return Redirect::to('utente/agenti');
        }

        if(isset($dati['modifica'])){
            unset($dati['modifica']);

            if($_FILES['immagine']['name'] != ''){
                $path_da_eliminare = DB::table('utenti')->where('id_tipologia', 1)->where('id', $dati['id'])->where('id_azienda', $utente->id_azienda)->first();
                if (file_exists($path_da_eliminare->immagine)) {
                    unlink($path_da_eliminare->immagine);
                }

                $pathinfo = pathinfo($_FILES['immagine']['name']);
                $nome = Str::random(20);
                $target = 'immagini/agenti/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['immagine']['tmp_name'], $target);
                $dati['immagine'] = $target;
            }

            DB::table('utenti')->where('id', $dati['id'])->where('id_azienda', $utente->id_azienda)->update($dati);
            return Redirect::to('utente/agenti');
        }

        if(isset($dati['elimina'])){
            unset($dati['elimina']);
            $path_da_eliminare = DB::table('utenti')->where('id_tipologia', 1)->where('id', $dati['id'])->where('id_azienda', $utente->id_azienda)->first();
            if (file_exists($path_da_eliminare->immagine)) {
                unlink($path_da_eliminare->immagine);
            }

            DB::table('utenti')->where('id', $dati['id'])->where('id_azienda', $utente->id_azienda)->delete();
            DB::table('provvigioni_agenti')->where('id_agente', $dati['id'])->where('id_azienda', $utente->id_azienda)->delete();
            return Redirect::to('utente/agenti');
        }

        $page = 'agenti';
        $agenti = DB::table('clienti')->where('id_tipologia', 0)->where('id_azienda', $utente->id_azienda)->get();

        return View::make('utente.agenti', compact('page', 'utente', 'agenti'));
    }

    public function provvigioni_agente($id_agente, Request $request){
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Verifica che l'agente esista e appartenga all'azienda
        $agente = DB::table('utenti')
            ->where('id', $id_agente)
            ->where('id_tipologia', 1)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if(!$agente) {
            return Redirect::to('utente/agenti')->with('error', 'Agente non trovato');
        }

        if(isset($dati['aggiungi_provvigione'])){
            unset($dati['aggiungi_provvigione']);
            $dati['id_agente'] = $id_agente;
            $dati['id_azienda'] = $utente->id_azienda;

            DB::table('provvigioni_agenti')->insert($dati);
            return Redirect::to('utente/provvigioni_agente/' . $id_agente);
        }

        if(isset($dati['modifica_provvigione'])){
            unset($dati['modifica_provvigione']);

            DB::table('provvigioni_agenti')
                ->where('id', $dati['id'])
                ->where('id_azienda', $utente->id_azienda)
                ->update([
                    'tipo_provvigione' => $dati['tipo_provvigione'],
                    'valore' => $dati['valore'],
                    'descrizione' => $dati['descrizione']
                ]);

            return Redirect::to('utente/provvigioni_agente/' . $id_agente);
        }

        if(isset($dati['elimina_provvigione'])){
            unset($dati['elimina_provvigione']);

            DB::table('provvigioni_agenti')
                ->where('id', $dati['id'])
                ->where('id_azienda', $utente->id_azienda)
                ->delete();

            return Redirect::to('utente/provvigioni_agente/' . $id_agente);
        }

        // Recupera tutte le provvigioni dell'agente
        $provvigioni = DB::table('provvigioni_agenti')
            ->where('id_agente', $id_agente)
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('data_creazione', 'desc')
            ->get();
        $page = 'agenti';

        return View::make('utente.provvigioni_agente', compact('page', 'utente', 'agente', 'provvigioni'));
    }


    public function visualizza_provvigioni(Request $request) {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Impostazioni filtri
        $filtro_agente = isset($dati['filtro_agente']) ? $dati['filtro_agente'] : '';
        $filtro_stato = isset($dati['filtro_stato']) ? $dati['filtro_stato'] : 'tutte';
        $filtro_data_inizio = isset($dati['filtro_data_inizio']) ? $dati['filtro_data_inizio'] : date('Y-01-01');
        $filtro_data_fine = isset($dati['filtro_data_fine']) ? $dati['filtro_data_fine'] : date('Y-12-31');

        // Costruisci la query base
        $query = DB::table('provvigioni_fatture as pf')
            ->leftJoin('dotes as d', function($join) {
                $join->on('pf.id_dotes', '=', 'd.id');
            })
            ->leftJoin('clienti as c', function($join) {
                $join->on('d.id_cliente', '=', 'c.id')
                    ->whereNotNull('d.id_cliente');
            })
            ->leftJoin('utenti as u', 'pf.id_agente', '=', 'u.id')
            ->whereBetween('pf.data_creazione', [$filtro_data_inizio, $filtro_data_fine]);

// Se l'utente è un agente (id_tipologia = 1), mostra SOLO le sue provvigioni
        if ($utente->id_tipologia == 1) {
            $query->where('pf.id_agente', $utente->id);
            // Forziamo il filtro_agente all'ID dell'agente corrente
            $filtro_agente = $utente->id;
        } else {
            // Se l'utente è un amministratore dell'azienda, mostra le provvigioni di tutti gli agenti dell'azienda
            $query->where('pf.id_azienda', $utente->id_azienda);

            // Applica filtro per agente se specificato
            if (!empty($filtro_agente)) {
                $query->where('pf.id_agente', $filtro_agente);
            }
        }

// Filtro per stato pagamento
        if ($filtro_stato == 'pagate') {
            $query->where('pf.pagata', 1);
        } elseif ($filtro_stato == 'da_pagare') {
            $query->where('pf.pagata', 0);
        }

// Ottieni i risultati
        $provvigioni = $query->select(
            'pf.*',
            'd.numero_doc',
            'd.data_doc',
            'c.ragione_sociale',
            'u.nome as agente_nome',
            'u.cognome as agente_cognome'
        )->orderBy('pf.data_creazione', 'desc')->get();

        // Calcola i totali
        $totali = [
            'importo_fatturato' => $provvigioni->sum('importo_fattura'),
            'importo_provvigioni' => $provvigioni->sum('importo_provvigione'),
            'importo_pagate' => $provvigioni->where('pagata', 1)->sum('importo_provvigione'),
            'importo_da_pagare' => $provvigioni->where('pagata', 0)->sum('importo_provvigione')
        ];

        // Se l'utente è un amministratore dell'azienda, recupera gli agenti per il filtro
        $agenti = [];
        if ($utente->admin_azienda == 1) {
            $agenti = DB::table('utenti')
                ->where('id_tipologia', 1)
                ->where('id_azienda', $utente->id_azienda)
                ->get();
        }

        // Recupera i tipi di provvigioni configurati per l'agente corrente o per tutti gli agenti
        $query_tipi = DB::table('provvigioni_agenti as pa')
            ->join('utenti as u', 'pa.id_agente', '=', 'u.id')
            ->where('pa.id_azienda', $utente->id_azienda);

        // Se l'utente è un agente, mostra solo le sue configurazioni
        if ($utente->id_tipologia == 1) {
            $query_tipi->where('pa.id_agente', $utente->id);
        } elseif (!empty($filtro_agente)) {
            // Se c'è un filtro per agente, mostra solo le configurazioni di quell'agente
            $query_tipi->where('pa.id_agente', $filtro_agente);
        }

        $tipi_provvigioni = $query_tipi
            ->select('pa.*', 'u.nome', 'u.cognome')
            ->orderBy('pa.data_creazione', 'desc')
            ->get();

        // Gestione pagamento provvigione (solo per amministratori)
        if (isset($dati['paga_provvigione']) && $utente->admin_azienda == 1 && $utente->id_tipologia != 1) {
            unset($dati['paga_provvigione']);

            DB::table('provvigioni_fatture')
                ->where('id', $dati['id'])
                ->where('id_azienda', $utente->id_azienda)  // Aggiungiamo un ulteriore controllo di sicurezza
                ->update([
                    'pagata' => 1,
                    'data_pagamento' => $dati['data_pagamento'],
                    'note' => $dati['note'],
                    'updated_at' => now()
                ]);

            // Aggiorna anche la scadenza collegata se esiste
            if (!empty($dati['id_scadenziario'])) {
                DB::table('scadenziario')
                    ->where('id', $dati['id_scadenziario'])
                    ->where('id_azienda', $utente->id_azienda)
                    ->update([
                        'importo_pagato' => DB::raw('importo'),
                        'stato' => 'pagato',
                        'data_pagamento' => $dati['data_pagamento'],
                        'note' => $dati['note']
                    ]);
            }

            return Redirect::to('utente/visualizza_provvigioni');
        }

        // Gestione creazione provvigione manuale (solo per amministratori)
        if (isset($dati['aggiungi_provvigione_manuale']) && $utente->admin_azienda == 1 && $utente->id_tipologia != 1) {
            unset($dati['aggiungi_provvigione_manuale']);

            // Verifica che l'agente esista e appartenga all'azienda
            $agente = DB::table('utenti')
                ->where('id', $dati['id_agente_provv'])
                ->where('id_tipologia', 1)
                ->where('id_azienda', $utente->id_azienda)
                ->first();

            if ($agente) {
                // Verifico se esiste una configurazione provvigione attiva per questo agente
                $config_provvigione = DB::table('provvigioni_agenti')
                    ->where('id_agente', $agente->id)
                    ->where('id_azienda', $utente->id_azienda)
                    ->orderBy('data_creazione', 'desc')
                    ->first();

                $tipo_provvigione = 'fisso';
                $valore = 0;

                if ($config_provvigione) {
                    $tipo_provvigione = $config_provvigione->tipo_provvigione;
                    $valore = $config_provvigione->valore;
                }

                // Il riferimento è quello inserito o un valore di default
                $riferimento = !empty($dati['riferimento_manuale']) ?
                    $dati['riferimento_manuale'] :
                    'Provvigione manuale - ' . date('d/m/Y');

                // Inserisci la scadenza per la provvigione
                $id_scadenziario = DB::table('scadenziario')->insertGetId([
                    'id_azienda' => $utente->id_azienda,
                    'id_cliente' => null, // Potrebbe non esserci un cliente associato
                    'id_agente' => $dati['id_agente_provv'],
                    'data_scadenza' => $dati['data_scadenza_provv'],
                    'importo' => $dati['importo_provvigione'],
                    'importo_pagato' => 0,
                    'tipo_movimento' => 'uscita',
                    'modalita_pagamento' => 'bonifico',
                    'stato' => 'da_pagare',
                    'note' => $dati['note_provv'] ?? 'Provvigione inserita manualmente'
                ]);

                // Inserisci la provvigione nel sistema
                DB::table('provvigioni_fatture')->insert([
                    'id_azienda' => $utente->id_azienda,
                    'id_agente' => $dati['id_agente_provv'],
                    'id_dotes' => null, // Non c'è un documento associato
                    'id_scadenziario' => $id_scadenziario,
                    'importo_fattura' => 0, // Non è associata a una fattura
                    'importo_provvigione' => $dati['importo_provvigione'],
                    'tipo_provvigione' => $tipo_provvigione,
                    'valore' => $valore,
                    'pagata' => 0,
                    'data_creazione' => now(),
                    'riferimento_fattura' => $riferimento,
                    'note' => $dati['note_provv'] ?? 'Provvigione inserita manualmente'
                ]);

                return Redirect::to('utente/visualizza_provvigioni')->with('success', 'Provvigione creata con successo');
            }

            return Redirect::to('utente/visualizza_provvigioni')->with('error', 'Agente non trovato');
        }

        // Gestione pagamento multiplo (solo per amministratori)
        if (isset($dati['paga_multiple']) && $utente->admin_azienda == 1 && $utente->id_tipologia != 1) {
            unset($dati['paga_multiple']);

            if (isset($dati['provvigioni_selezionate']) && !empty($dati['provvigioni_selezionate'])) {
                $ids = explode(',', $dati['provvigioni_selezionate']);

                // Recupera tutte le provvigioni selezionate
                $provvigioni_da_pagare = DB::table('provvigioni_fatture')
                    ->whereIn('id', $ids)
                    ->where('id_azienda', $utente->id_azienda)
                    ->get();

                // Aggiorna le provvigioni
                DB::table('provvigioni_fatture')
                    ->whereIn('id', $ids)
                    ->where('id_azienda', $utente->id_azienda)
                    ->update([
                        'pagata' => 1,
                        'data_pagamento' => $dati['data_pagamento_multipla'],
                        'note' => $dati['note_multipla'],
                        'updated_at' => now()
                    ]);

                // Aggiorna anche le scadenze collegate
                foreach ($provvigioni_da_pagare as $provvigione) {
                    if (!empty($provvigione->id_scadenziario)) {
                        DB::table('scadenziario')
                            ->where('id', $provvigione->id_scadenziario)
                            ->where('id_azienda', $utente->id_azienda)
                            ->update([
                                'importo_pagato' => DB::raw('importo'),
                                'stato' => 'pagato',
                                'data_pagamento' => $dati['data_pagamento_multipla'],
                                'note' => $dati['note_multipla']
                            ]);
                    }
                }
            }

            return Redirect::to('utente/visualizza_provvigioni');
        }

        // Gestione aggiunta configurazione provvigione (solo per amministratori)
        if (isset($dati['aggiungi_configurazione']) && $utente->admin_azienda == 1 && $utente->id_tipologia != 1) {
            unset($dati['aggiungi_configurazione']);

            $dati['id_azienda'] = $utente->id_azienda;
            $dati['data_creazione'] = now();

            DB::table('provvigioni_agenti')->insert($dati);
            return Redirect::to('utente/visualizza_provvigioni');
        }

        // Gestione eliminazione configurazione (solo per amministratori)
        if (isset($dati['elimina_configurazione']) && $utente->admin_azienda == 1 && $utente->id_tipologia != 1) {
            unset($dati['elimina_configurazione']);

            DB::table('provvigioni_agenti')
                ->where('id', $dati['id'])
                ->where('id_azienda', $utente->id_azienda)
                ->delete();

            return Redirect::to('utente/visualizza_provvigioni');
        }

        return View::make('utente.visualizza_provvigioni', compact(
            'utente',
            'provvigioni',
            'tipi_provvigioni',
            'totali',
            'filtro_agente',
            'filtro_stato',
            'filtro_data_inizio',
            'filtro_data_fine',
            'agenti'
        ));
    }

    public function duplica_documento($id)
    {
        $this->is_loggato();
        $utente = session('utente');
        $anno = session('anno');

        // Recupera il documento originale
        $dotesOriginale = DB::table('dotes')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$dotesOriginale) {
            return response()->json(['success' => false, 'message' => 'Documento non trovato o non autorizzato.']);
        }

        // Recupera le righe del documento originale
        $dorigOriginale = DB::table('dorig')
            ->where('id_dotes', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->get();

        // Trova il prossimo numero documento
        $numero_doc = DB::select('SELECT IFNULL(max(numero_doc + 1),1) as numero_doc from dotes where cd_do = "'.htmlentities($dotesOriginale->cd_do,3,'UTF-8','').'" and YEAR(data_doc) = '.$anno.' and id_azienda = '.$utente->id_azienda)[0]->numero_doc;

        // Crea il nuovo documento
        $nuovoDotes = [
            'cd_do' => $dotesOriginale->cd_do,
            'numero_doc' => $numero_doc,
            'data_doc' => date('Y-m-d'),
            'id_cliente' => $dotesOriginale->id_cliente,
            'id_azienda' => $utente->id_azienda,
            'totale' => $dotesOriginale->totale,
            'imposta' => $dotesOriginale->imposta,
            'imponibile' => $dotesOriginale->imponibile,
            'da_registrare' => 0,
            'ragione_sociale_fatturazione' => $dotesOriginale->ragione_sociale_fatturazione,
            'indirizzo_fatturazione' => $dotesOriginale->indirizzo_fatturazione,
            'comune_fatturazione' => $dotesOriginale->comune_fatturazione,
            'provincia_fatturazione' => $dotesOriginale->provincia_fatturazione,
            'partita_iva_fatturazione' => $dotesOriginale->partita_iva_fatturazione,
            'indirizzo' => $dotesOriginale->indirizzo,
            'comune' => $dotesOriginale->comune,
            'cap' => $dotesOriginale->cap,
            'sdi' => $dotesOriginale->sdi,
            'pec' => $dotesOriginale->pec,
            'oggetto_visibile' => $dotesOriginale->oggetto_visibile,
            'oggetto_interno' => $dotesOriginale->oggetto_interno,
            'data_consegna' => $dotesOriginale->data_consegna,
            'id_commessa' => $dotesOriginale->id_commessa,
            // Manutenzione: preserva vagone + campi dominio, RESETTA il workflow accettazione
            'id_vagone'              => $dotesOriginale->id_vagone ?? null,
            'automezzo'              => $dotesOriginale->automezzo ?? null,
            'localita'               => $dotesOriginale->localita ?? null,
            'reason_intake'          => $dotesOriginale->reason_intake ?? null,
            'note_operatore'         => $dotesOriginale->note_operatore ?? null,
            'stato_accettazione'     => null,
            'motivo_rifiuto'         => null,
            'tentativi'              => 0,
            'accettato_da_id_utente' => null,
            'inviato_revisione_il'   => null,
            'accettato_il'           => null,
            'rifiutato_il'           => null,
        ];

        // Inserisci il nuovo documento
        $nuovoId = DB::table('dotes')->insertGetId($nuovoDotes);

        // Duplica le righe del documento
        foreach ($dorigOriginale as $riga) {
            $nuovaRiga = [
                'id_dotes' => $nuovoId,
                'id_articolo' => $riga->id_articolo,
                'descrizione' => $riga->descrizione,
                'qta' => $riga->qta,
                'prezzo_unitario' => $riga->prezzo_unitario,
                'prezzo_totale' => $riga->prezzo_totale,
                'imponibile' => $riga->imponibile,
                'imposta' => $riga->imposta,
                'totale' => $riga->totale,
                'id_azienda' => $utente->id_azienda,
                'qta_evasa' => 0,
                'qta_evadibile_prod' => $riga->qta,
                'lotto' => $riga->lotto,
                'scadenza_lotto' => $riga->scadenza_lotto,
                'um' => $riga->um,
                'natura' => $riga->natura,
                'rif_normativo' => $riga->rif_normativo,
                'rif_normativo_pdf' => $riga->rif_normativo_pdf,
                'iva' => $riga->iva,
                // Preserva ordine righe (drag-drop)
                'n_riga' => $riga->n_riga,
                // Manutenzione: preserva legami + campi dominio della riga di lavorazione
                'id_vagone'                   => $riga->id_vagone ?? null,
                'id_lavorazione_origine'      => $riga->id_lavorazione_origine ?? null,
                'id_lavorazione_riga_origine' => $riga->id_lavorazione_riga_origine ?? null,
                'servizio'                    => $riga->servizio ?? null,
                'setup_tank'                  => isset($riga->setup_tank) ? (int) $riga->setup_tank : 0,
                'attivita'                    => $riga->attivita ?? null,
                'minuti'                      => $riga->minuti ?? null,
                'materiale'                   => $riga->materiale ?? null,
                'descrizione_materiale'       => $riga->descrizione_materiale ?? null,
            ];
            DB::table('dorig')->insert($nuovaRiga);
        }

        // Se il documento ha scadenziario, duplica anche quello
        $scadenziario = DB::table('scadenziario')
            ->where('id_dotes', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->get();

        foreach ($scadenziario as $scadenza) {
            $nuovaScadenza = [
                'id_dotes' => $nuovoId,
                'id_azienda' => $utente->id_azienda,
                'importo' => $scadenza->importo,
                'data_scadenza' => $scadenza->data_scadenza,
                'termini' => $scadenza->termini,
                'stato' => 'da_pagare',
                'tipo_movimento' => $scadenza->tipo_movimento
            ];
            DB::table('scadenziario')->insert($nuovaScadenza);
        }

        return response()->json(['success' => true, 'new_id' => $nuovoId]);
    }


    public function commesse(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if(isset($dati['aggiungi'])){
            unset($dati['aggiungi']);

            // Generazione codice commessa automatico
            $ultimo_codice = DB::table('commesse')
                ->where('id_azienda', $utente->id_azienda)
                ->orderBy('id', 'desc')
                ->value('codice_commessa');

            $nuovo_numero = 1;
            if($ultimo_codice) {
                $numero = intval(substr($ultimo_codice, 4));
                $nuovo_numero = $numero + 1;
            }

            $dati['codice_commessa'] = 'COM-' . str_pad($nuovo_numero, 5, '0', STR_PAD_LEFT);
            $dati['id_utente'] = $utente->id;
            $dati['id_azienda'] = $utente->id_azienda;
            $dati['stato'] = 'aperta';

            DB::table('commesse')->insert($dati);
            return Redirect::to('utente/commesse');
        }

        if(isset($dati['modifica'])){
            unset($dati['modifica']);
            DB::table('commesse')
                ->where('id', $dati['id'])
                ->where('id_azienda', $utente->id_azienda)
                ->update($dati);
            return Redirect::to('utente/commesse');
        }

        if(isset($dati['elimina'])){
            unset($dati['elimina']);
            DB::table('commesse')
                ->where('id', $dati['id'])
                ->where('id_azienda', $utente->id_azienda)
                ->delete();
            return Redirect::to('utente/commesse');
        }

        $commesse = DB::table('commesse')
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('data_apertura', 'desc')
            ->get();

        return View::make('utente.commesse', compact('utente', 'commesse'));
    }

    public function commesse_attivita($id_commessa, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Recupero commessa
        $commessa = DB::table('commesse')
            ->where('id', $id_commessa)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if(!$commessa) {
            return Redirect::to('utente/commesse');
        }

        if(isset($dati['aggiungi'])){
            unset($dati['aggiungi']);
            $dati['id_commessa'] = $id_commessa;
            $dati['id_utente'] = $utente->id;
            $dati['id_azienda'] = $utente->id_azienda;
            DB::table('commesse_attivita')->insert($dati);
            return Redirect::to('utente/commesse/'.$id_commessa.'/attivita');
        }

        if(isset($dati['modifica'])){
            unset($dati['modifica']);
            DB::table('commesse_attivita')
                ->where('id', $dati['id'])
                ->where('id_azienda', $utente->id_azienda)
                ->update($dati);
            return Redirect::to('utente/commesse/'.$id_commessa.'/attivita');
        }

        if(isset($dati['elimina'])){
            unset($dati['elimina']);
            DB::table('commesse_attivita')
                ->where('id', $dati['id'])
                ->where('id_azienda', $utente->id_azienda)
                ->delete();
            return Redirect::to('utente/commesse/'.$id_commessa.'/attivita');
        }

        // Recupero attività
        $attivita = DB::select('
            SELECT a.*, 
                   CONCAT(u.nome, " ", u.cognome) as responsabile
            FROM commesse_attivita a
            LEFT JOIN utenti u ON u.id = a.id_responsabile 
            WHERE a.id_commessa = ? 
            AND a.id_azienda = ?
            ORDER BY a.data_inizio ASC',
            [$id_commessa, $utente->id_azienda]
        );

        // Recupero dipendenti per dropdown responsabili
        $dipendenti = DB::table('utenti')
            ->where('id_azienda', $utente->id_azienda)
            ->where('id_tipologia', 3)
            ->get();

        // Preparo dati per Gantt
        $gantt_data = [];
        foreach($attivita as $a) {
            $gantt_data[] = [
                'id' => $a->id,
                'text' => $a->titolo,
                'start_date' => $a->data_inizio,
                'end_date' => $a->data_fine,
                'progress' => $a->completamento / 100,
                'dependencies' => $a->id_attivita_precedente ? [(string)$a->id_attivita_precedente] : []
            ];
        }

        return View::make('utente.commesse_attivita', compact('utente', 'commessa', 'attivita', 'dipendenti', 'gantt_data'));
    }

    // ... (rest of the code remains unchanged) ...

    public function getResourceGanttData(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        try {
            // Recupera tutti i dipendenti
            $dipendenti = DB::table('utenti')
                ->where('id_azienda', $utente->id_azienda)
                ->where('id_tipologia', 3)
                ->select('id', 'nome', 'cognome')
                ->get();

            $data = [];

            foreach ($dipendenti as $dipendente) {
                // Recupera tutte le attività del dipendente
                $attivita = DB::select('
                    SELECT 
                        a.*,
                        c.descrizione as commessa,
                        c.codice_commessa,
                        DATEDIFF(a.data_fine, a.data_inizio) * 8 as ore_previste
                    FROM commesse_attivita a
                    JOIN commesse c ON c.id = a.id_commessa
                    WHERE a.id_responsabile = ?
                    AND a.id_azienda = ?
                    ORDER BY a.data_inizio ASC
                ', [$dipendente->id, $utente->id_azienda]);

                // Calcola le statistiche del dipendente
                $numeroAttivita = count($attivita);
                $oreTotali = array_sum(array_map(function($att) {
                    return $att->ore_previste;
                }, $attivita));

                // Aggiungi il dipendente come "progetto"
                $data[] = [
                    "id" => "dip_" . $dipendente->id,
                    "text" => $dipendente->nome . " " . $dipendente->cognome,
                    "type" => "project",
                    "open" => true,
                    "numero_attivita" => $numeroAttivita,
                    "ore_totali" => $oreTotali
                ];

                // Aggiungi tutte le attività del dipendente
                foreach ($attivita as $att) {
                    $data[] = [
                        "id" => $att->id,
                        "text" => $att->titolo,
                        "start_date" => $att->data_inizio,
                        "end_date" => $att->data_fine,
                        "parent" => "dip_" . $dipendente->id,
                        "progress" => $att->completamento / 100,
                        "type" => "task",
                        "commessa" => $att->codice_commessa . " - " . $att->descrizione,
                        "ore_previste" => $att->ore_previste
                    ];
                }
            }

            return response()->json([
                "data" => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Si è verificato un errore nel recupero dei dati: ' . $e->getMessage()
            ], 500);
        }
    }

    // ... rest of the existing code ...

    public function dettaglio_documento($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Recupera il documento principale
        $dotes = DB::table('dotes')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$dotes) {
            return redirect()->back()->with('error', 'Documento non trovato');
        }

        // Recupera le righe del documento
        $righe = DB::table('dorig')
            ->where('id_dotes', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('n_riga', 'asc')
            ->get();

        // Recupera le scadenze
        $scadenze = DB::table('scadenziario')
            ->where('id_dotes', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('data_scadenza', 'asc')
            ->get();

        // Recupera le commesse
        $commesse = DB::table('commesse')
            ->where('id_azienda', $utente->id_azienda)
            ->where('stato', '!=', 'annullata')
            ->orderBy('codice_commessa', 'asc')
            ->get();

        return view('utente.dettaglio_documento', compact('dotes', 'righe', 'utente','scadenze','commesse'));
    }

    public function salva_scadenze(Request $request, $id_dotes)
    {
        $this->is_loggato();
        $utente = session('utente');

        try {
            DB::beginTransaction();

            $scadenze = $request->input('scadenze');

            // Rimuovi le scadenze non più presenti
            $scadenze_ids = collect($scadenze)->pluck('id')->filter()->toArray();
            DB::table('scadenziario')
                ->where('id_dotes', $id_dotes)
                ->where('id_azienda', $utente->id_azienda)
                ->whereNotIn('id', $scadenze_ids)
                ->delete();

            // Aggiorna o inserisci le scadenze
            foreach ($scadenze as $scadenza) {
                $dati = [
                    'id_dotes' => $id_dotes,
                    'id_azienda' => $utente->id_azienda,
                    'importo' => $scadenza['importo'],
                    'data_scadenza' => $scadenza['data_scadenza'],
                    'modalita_pagamento' => $scadenza['modalita_pagamento'],
                    'iban' => $scadenza['iban'],
                    'stato' => $scadenza['stato'],
                    'note' => $scadenza['note'],
                    'updated_at' => now()
                ];

                if (!empty($scadenza['id'])) {
                    // Aggiorna scadenza esistente
                    DB::table('scadenziario')
                        ->where('id', $scadenza['id'])
                        ->where('id_azienda', $utente->id_azienda)
                        ->update($dati);
                } else {
                    // Inserisci nuova scadenza
                    $dati['created_at'] = now();
                    DB::table('scadenziario')->insert($dati);
                }
            }

            DB::commit();
            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function giacenze_magazzino($id_mg) {
        $utente = session('utente');

        // Recupera il magazzino
        $magazzino = DB::table('mg')
            ->where('id', $id_mg)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$magazzino) {
            return redirect()->back()->with('error', 'Magazzino non trovato');
        }

        // Recupera le giacenze per questo magazzino
        $giacenze = DB::select('
        SELECT 
            a.id as id_articolo,
            a.codice_articolo,
            a.titolo,
            a.um,
            m.lotto,
            m.scadenza_lotto,
            SUM(m.qta) as qta
        FROM mgmov m
        JOIN articoli a ON a.id = m.id_articolo
        WHERE m.id_mg = ?
        AND m.id_azienda = ?
        GROUP BY a.id, a.codice_articolo, a.titolo, a.um, m.lotto, m.scadenza_lotto
        HAVING SUM(m.qta)
        ORDER BY a.titolo ASC',
            [$id_mg, $utente->id_azienda]
        );

        // Recupera altri magazzini per il trasferimento
        $magazzini = DB::table('mg')
            ->where('id_azienda', $utente->id_azienda)
            ->get();

        return view('utente.magazzino.giacenze', compact('magazzino', 'giacenze', 'magazzini'));
    }

    public function trasferisci_magazzino(Request $request) {
        $utente = session('utente');

        // Recupero dati dal form
        $id_articolo = $request->input('id_articolo');
        $magazzinoOrigine = $request->input('magazzinoOrigine');
        $magazzinoDestinazione = $request->input('magazzino_destinazione');
        $quantita = $request->input('quantita');
        $lotto = $request->input('lotto');
        $scadenza = $request->input('scadenza');
        $causale = $request->input('causale');

        // Crea il movimento di scarico dal magazzino di origine
        DB::table('mgmov')->insert([
            'id_azienda' => $utente->id_azienda,
            'id_utente' => $utente->id,
            'id_articolo' => $id_articolo,
            'id_mg' => $magazzinoOrigine,
            'lotto' => $lotto,
            'scadenza_lotto' => $scadenza,
            'qta' => -$quantita,
            'sca' => 1, // Indicatore di scarico
            'car' => 0,
            'ret' => 0,
            'datamov' => now(),
            'causale' => $causale . ' (Scarico)'
        ]);

        // Crea il movimento di carico nel magazzino di destinazione
        DB::table('mgmov')->insert([
            'id_azienda' => $utente->id_azienda,
            'id_utente' => $utente->id,
            'id_articolo' => $id_articolo,
            'id_mg' => $magazzinoDestinazione,
            'lotto' => $lotto,
            'scadenza_lotto' => $scadenza,
            'qta' => $quantita,
            'car' => 1, // Indicatore di carico
            'sca' => 0,
            'ret' => 0,
            'datamov' => now(),
            'causale' => $causale . ' (Carico)'
        ]);

        // Aggiorna la giacenza nell'articolo
        DB::update('
        UPDATE articoli 
        SET giacenza = (SELECT SUM(qta) FROM mgmov WHERE id_articolo = ?)
        WHERE id = ?',
            [$id_articolo, $id_articolo]
        );

        $magazzino_origine = DB::table('mg')->where('id', $magazzinoOrigine)->value('descrizione');
        $magazzino_destinazione = DB::table('mg')->where('id', $magazzinoDestinazione)->value('descrizione');

        return redirect()->back()->with('success', "Trasferimento completato: $quantita unità trasferite da $magazzino_origine a $magazzino_destinazione");
    }

    public function movimenti_magazzino($id_articolo, $id_mg)
    {
        $utente = session('utente');

        // Recupera articolo e magazzino
        $articolo = DB::table('articoli')
            ->where('id', $id_articolo)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        $magazzino = DB::table('mg')
            ->where('id', $id_mg)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        // Query modificata per includere ODL
        $movimenti = DB::table('mgmov')
            ->leftJoin('dorig', 'mgmov.id_dorig', '=', 'dorig.id')
            ->leftJoin('dotes', 'dorig.id_dotes', '=', 'dotes.id')
            ->leftJoin('odl', 'mgmov.id_odl', '=', 'odl.id')
            ->leftJoin('commesse', 'mgmov.id_commessa', '=', 'commesse.id')
            ->select(
                'mgmov.*',
                'dotes.cd_do',
                'dotes.numero_doc',
                'dotes.data_doc',
                'dotes.id as id_dotes',
                'odl.numero as numero_odl',
                'odl.id as id_odl',
                'commesse.codice_commessa',
                'commesse.descrizione as descrizione_commessa',
                'commesse.stato as stato_commessa'
            )
            ->where('mgmov.id_articolo', $id_articolo)
            ->where('mgmov.id_mg', $id_mg)
            ->where('mgmov.id_azienda', $utente->id_azienda)
            ->orderBy('mgmov.datamov', 'desc')
            ->get();

        return view('utente.magazzino.movimenti', compact('articolo', 'magazzino', 'movimenti'));


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



    public function inventario()
    {
        $this->is_loggato();
        $utente = session('utente');

        // Recupera gli articoli per l'azienda dell'utente
        $articoli = DB::select('
        SELECT a.*, 
               COALESCE((SELECT SUM(m.qta) FROM mgmov m WHERE m.id_articolo = a.id AND m.id_azienda = ?), 0) as giacenza 
        FROM articoli a 
        WHERE a.id_azienda = ? 
        ORDER BY a.titolo ASC',
            [$utente->id_azienda, $utente->id_azienda]
        );

        // Recupera i magazzini dell'azienda
        $magazzini = DB::table('mg')
            ->where('id_azienda', $utente->id_azienda)
            ->get();

        return view('utente.inventario', compact('articoli', 'utente', 'magazzini'));
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

        return view('utente.trasferimento_mg', compact('articoli', 'utente', 'magazzini'));
    }

    public function carico(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Ottiene le materie prime (solo articoli con tipologia=1)
        // con giacenza reale calcolata dalla tabella giacenze
        $materiali = DB::table('articoli')
            ->where('articoli.id_azienda', $utente->id_azienda)
            ->where('tipologia', 1)
            ->leftJoin(DB::raw('(SELECT id_articolo, SUM(qta) as giacenza_reale FROM giacenze WHERE id_azienda = '.(int)$utente->id_azienda.' GROUP BY id_articolo) as g'), 'g.id_articolo', '=', 'articoli.id')
            ->select('articoli.*', DB::raw('COALESCE(g.giacenza_reale, 0) as giacenza'))
            ->orderBy('titolo', 'asc')
            ->get();

        // Ottiene i magazzini
        $magazzini = DB::table('mg')
            ->where('id_azienda', $utente->id_azienda)
            ->get();

        // Ottiene i fornitori (solo clienti con id_tipologia=1)
        $fornitori = DB::table('fornitori')
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('ragione_sociale', 'asc')
            ->get();

        // Ottiene le commesse
        $commesse = DB::table('commesse')
            ->where('id_azienda', $utente->id_azienda)
            ->where('stato', 'aperta')
            ->orWhere('stato', 'in_corso')
            ->orderBy('data_apertura', 'desc')
            ->get();

        $azienda = DB::table('aziende')->where('id', $utente->id_azienda)->first();
        return view('utente.carico', compact('utente', 'materiali', 'magazzini', 'fornitori', 'commesse', 'azienda'));
    }

    public function scarico()
    {
        $this->is_loggato();
        $utente = session('utente');

        // Recupera gli articoli con tipologia 0 (prodotti finiti)
        $articoli = DB::table('articoli')
            ->whereIn('tipologia', [1,2])
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('titolo', 'asc')
            ->get();

        // Recupera le materie prime (articoli con tipologia 1)
        $materiali = DB::table('articoli')
            ->whereIn('tipologia', [1,2])
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('titolo', 'asc')
            ->get();

        // Recupera i magazzini
        $magazzini = DB::table('mg')
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('descrizione', 'asc')
            ->get();
        $fornitori = DB::table('fornitori')
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('ragione_sociale', 'asc')
            ->get();

        // Recupera le commesse attive
        $commesse = DB::select('
        SELECT * FROM commesse 
        WHERE id_azienda = ? 
        AND (stato = "aperta" OR stato = "in_corso")
        ORDER BY codice_commessa ASC
    ', [$utente->id_azienda]);

        $azienda = DB::table('aziende')->where('id', $utente->id_azienda)->first();
        return view('utente.scarico', compact('utente', 'articoli', 'materiali', 'magazzini', 'commesse', 'fornitori', 'azienda'));
    }

    public function getDisponibilita($id_articolo)
    {
        $this->is_loggato();
        $utente = session('utente');

        try {
            // Recupera l'articolo per verificare che esista
            $articolo = DB::table('articoli')
                ->where('id', $id_articolo)
                ->where('id_azienda', $utente->id_azienda)
                ->first();

            if (!$articolo) {
                return response()->json(['success' => false, 'message' => 'Articolo non trovato']);
            }

            // Recupera la disponibilità (giacenze per lotto e magazzino)
            $disponibilita = DB::select('
            SELECT 
                m.id_articolo,
                m.id_mg as id_magazzino,
                mg.descrizione as magazzino_descrizione,
                m.lotto,
                m.scadenza_lotto,
                a.um,
                SUM(m.qta) as giacenza
            FROM mgmov m
            JOIN articoli a ON a.id = m.id_articolo
            JOIN mg ON mg.id = m.id_mg
            WHERE m.id_articolo = ?
            AND m.id_azienda = ?
            GROUP BY m.id_articolo, m.id_mg, mg.descrizione, m.lotto, m.scadenza_lotto, a.um
            HAVING SUM(m.qta) > 0
            ORDER BY m.scadenza_lotto ASC, mg.descrizione ASC
        ', [$id_articolo, $utente->id_azienda]);

            return response()->json([
                'success' => true,
                'disponibilita' => $disponibilita
            ]);

        } catch (\Exception $e) {
            \Log::error('Errore nel recupero della disponibilità: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Si è verificato un errore: ' . $e->getMessage()
            ]);
        }
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

        // Cerca l'articolo dal barcode o codice articolo
        $article = DB::table('articoli')
            ->where('id_azienda', $utente->id_azienda)
            ->where(function($query) use ($barcode) {
                $query->where('barcode', $barcode)
                    ->orWhere('codice_articolo', $barcode);
            })
            ->first();

        if ($article) {
            // Troviamo i magazzini in cui è presente l'articolo
            $magazzinoInfo = DB::table('mgmov')
                ->join('mg', 'mg.id', '=', 'mgmov.id_mg')
                ->select('mg.id', 'mg.descrizione')
                ->where('mgmov.id_articolo', $article->id)
                ->where('mgmov.id_azienda', $utente->id_azienda)
                ->groupBy('mg.id', 'mg.descrizione')
                ->havingRaw('SUM(mgmov.qta) > 0')
                ->first();

            // Ottenere i lotti e giacenze per magazzino
            $lottiMagazzini = DB::table('mgmov')
                ->join('mg', 'mg.id', '=', 'mgmov.id_mg')
                ->select(
                    'mgmov.lotto as lotto',
                    'mgmov.scadenza_lotto as scadenza',
                    'mg.descrizione as magazzino_descrizione',
                    'mg.id as id_magazzino',
                    DB::raw('SUM(mgmov.qta) as totale_giacenza')
                )
                ->where('mgmov.id_articolo', $article->id)
                ->where('mgmov.id_azienda', $utente->id_azienda)
                ->groupBy('mgmov.lotto', 'mg.descrizione', 'mg.id', 'mgmov.scadenza_lotto')
                ->havingRaw('SUM(mgmov.qta) > 0') // Solo lotti con giacenza positiva
                ->get();

            return response()->json([
                'success' => true,
                'article' => $article,
                'magazzinoInfo' => $magazzinoInfo,
                'lottiMagazzini' => $lottiMagazzini
            ]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function controlloArticoloScarico(Request $request)
    {
        $utente = session('utente');
        $barcode = $request->query('barcode');

        // Prima controlliamo se è un barcode di giacenza (in mgmov)
        $giacenza = DB::table('mgmov')
            ->join('articoli', 'articoli.id', '=', 'mgmov.id_articolo')
            ->join('mg', 'mg.id', '=', 'mgmov.id_mg')
            ->select(
                'mgmov.*',
                'articoli.id as id_articolo',
                'articoli.codice_articolo',
                'articoli.titolo',
                'articoli.um',
                'articoli.giacenza as giacenza_totale',
                'mg.descrizione as magazzino_descrizione',
                'mg.id as id_magazzino'
            )
            ->where('mgmov.barcode', $barcode)
            ->where('mgmov.id_azienda', $utente->id_azienda)
            ->where('mgmov.car', 1) // Solo movimenti di carico (che generano barcode)
            ->first();

        if ($giacenza) {
            // È un barcode di giacenza/lotto
            $article = DB::table('articoli')
                ->where('id', $giacenza->id_articolo)
                ->where('id_azienda', $utente->id_azienda)
                ->first();

            if (!$article) {
                return response()->json(['success' => false, 'message' => 'Articolo non trovato']);
            }

            // Calcola la giacenza attuale per questo specifico lotto/magazzino
            $giacenzaAttuale = DB::table('mgmov')
                ->where('id_articolo', $giacenza->id_articolo)
                ->where('id_mg', $giacenza->id_mg)
                ->where('lotto', $giacenza->lotto)
                ->where('id_azienda', $utente->id_azienda)
                ->sum('qta');

            return response()->json([
                'success' => true,
                'article' => $article,
                'isBarcodeGiacenza' => true,
                'giacenza' => $giacenza,
                'giacenzaAttuale' => $giacenzaAttuale
            ]);
        }

        // Se non è un barcode di giacenza, verifichiamo se è un barcode di articolo
        $article = DB::table('articoli')
            ->where('id_azienda', $utente->id_azienda)
            ->where(function($query) use ($barcode) {
                $query->where('barcode', $barcode)
                    ->orWhere('codice_articolo', $barcode);
            })
            ->first();

        if ($article) {
            return response()->json([
                'success' => true,
                'article' => $article,
                'isBarcodeGiacenza' => false
            ]);
        } else {
            return response()->json(['success' => false, 'message' => 'Articolo non trovato']);
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
        $this->is_loggato();
        $utente = session('utente');

        $data = $request->json()->all();
        $newQuantity = $data['giacenza'];
        $causale = $data['causale'];
        $lotto = $data['lotto'];
        $magazzinoId = $data['magazzinoId'];
        $scadenza = $data['scadenza'];

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
            'ret' => 1, // Indica che è una rettifica
        ]);

        // Aggiorna la giacenza totale dell'articolo in base alla somma della quantità in mgmov
        $updated = DB::update('UPDATE articoli 
                       SET giacenza = (SELECT SUM(qta) FROM mgmov WHERE id_articolo = ?) 
                       WHERE id = ? AND id_azienda = ?',
            [$id, $id, $utente->id_azienda]);

        if ($updated) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Errore nell\'aggiornamento della giacenza']);
        }
    }
    public function caricoMagazzino(Request $request, $id)
    {
        $this->is_loggato();
        $utente = session('utente');

        try {
            // Valida i dati di input
            $request->validate([
                'giacenza' => 'required|numeric|min:0.01',
                'mg' => 'required|exists:mg,id',
                'lotto' => 'required|string|max:50',
                'id_fornitore' => 'required|exists:fornitori,id',
                'prezzo_unitario' => 'required|numeric|min:0',
                'aliquota_iva' => 'required|numeric|min:0|max:100',
                'allegato_ddt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', // Max 5MB
            ]);

            // Recupera i dati dal form
            $addQuantity = $request->input('giacenza');
            $lotto = $request->input('lotto');
            $causale = $request->input('causale') ?: 'Carico manuale';
            $mg = $request->input('mg');
            $scadenza = $request->input('addScadenza');
            $id_commessa = $request->input('id_commessa');
            $id_fornitore = $request->input('id_fornitore');
            $aliquota_iva = $request->input('aliquota_iva');
            $numero_ddt = $request->input('numero_ddt');
            $data_ddt = $request->input('data_ddt');
            $prezzo_unitario = $request->input('prezzo_unitario');
            $imponibile = $addQuantity * $prezzo_unitario;
            $valuta = $request->input('valuta') ?: 'EUR';

            // Debug: log dei parametri ricevuti
            \Log::info('Parametri carico magazzino', [
                'numero_ddt' => $numero_ddt,
                'data_ddt' => $data_ddt,
                'id_fornitore' => $id_fornitore,
                'prezzo_unitario' => $prezzo_unitario,
                'imponibile' => $imponibile
            ]);

            // Recupera l'articolo
            $articolo = DB::table('articoli')
                ->where('id', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->first();

            if (!$articolo) {
                return response()->json(['success' => false, 'message' => 'Articolo non trovato']);
            }

            // Verifica che il magazzino esista e appartenga all'azienda dell'utente
            $magazzino = DB::table('mg')
                ->where('id', $mg)
                ->where('id_azienda', $utente->id_azienda)
                ->first();

            if (!$magazzino) {
                return response()->json(['success' => false, 'message' => 'Magazzino non trovato']);
            }

            // Verifica che il fornitore esista
            $fornitore = DB::table('fornitori')
                ->where('id', $id_fornitore)
                ->where('id_azienda', $utente->id_azienda)
                ->first();

            if (!$fornitore) {
                return response()->json(['success' => false, 'message' => 'Fornitore non trovato']);
            }

            // Verifica che la commessa esista (se specificata)
            if ($id_commessa) {
                $commessa = DB::table('commesse')
                    ->where('id', $id_commessa)
                    ->where('id_azienda', $utente->id_azienda)
                    ->first();

                if (!$commessa) {
                    return response()->json(['success' => false, 'message' => 'Commessa non trovata']);
                }
            }

            DB::beginTransaction();

            // Carica l'allegato se presente
            $percorsoAllegato = null;
            if ($request->hasFile('allegato_ddt')) {
                $file = $request->file('allegato_ddt');
                $nomeFile = Str::random(20) . '.' . $file->getClientOriginalExtension();
                $percorsoAllegato = 'allegati_ddt/' . date('Y') . '/' . date('m');

                // Crea directory se non esiste
                if (!file_exists(public_path($percorsoAllegato))) {
                    mkdir(public_path($percorsoAllegato), 0777, true);
                }

                $file->move(public_path($percorsoAllegato), $nomeFile);
                $percorsoAllegato = $percorsoAllegato . '/' . $nomeFile;
            }

            $barcode = $this->generaEAN128($id, $lotto, $utente->id_azienda);

            // Crea il movimento di carico in mgmov
            $idMovimento = DB::table('mgmov')->insertGetId([
                'id_azienda' => $utente->id_azienda,
                'id_utente' => $utente->id,
                'id_articolo' => $id,
                'id_mg' => $mg,
                'id_cliente' => $id_fornitore, // ID del fornitore
                'id_commessa' => $id_commessa ?: null,
                'lotto' => $lotto,
                'scadenza_lotto' => $scadenza,
                'qta' => $addQuantity,
                'car' => 1, // Indicatore di carico
                'sca' => 0,
                'ret' => 0,
                'datamov' => now(),
                'causale' => $causale,
                'prezzo_unitario' => $prezzo_unitario,
                'imponibile' => $imponibile,
                'aliquota_iva' => $aliquota_iva,
                'numero_ddt' => $numero_ddt,
                'data_ddt' => $data_ddt,
                'valuta' => $valuta,
                'percorso_allegato' => $percorsoAllegato,
                'barcode' => $barcode,
            ]);

            \Log::info('Movimento magazzino creato con ID: ' . $idMovimento);
            $id_commessa = $request->input('id_commessa');
            // Crea sempre il documento BDC per ogni carico (non solo quando c'è DDT)
            $documento_ids = $this->generaDocumentoBDC(
                $id,
                $id_fornitore,
                $addQuantity,
                $numero_ddt ?: ('CAR-' . $idMovimento), // Se non c'è numero DDT, usa un numero generato
                $data_ddt ?: date('Y-m-d'), // Se non c'è data DDT, usa data odierna
                $prezzo_unitario,
                $aliquota_iva,
                $imponibile,
                $lotto,
                $percorsoAllegato,
                $utente->id_azienda,
                $utente->id

            );

            // Aggiorna il movimento con il collegamento ai documenti
            if ($documento_ids && is_array($documento_ids)) {
                \Log::info('Aggiornamento movimento con documenti', $documento_ids);

                $updated = DB::table('mgmov')
                    ->where('id', $idMovimento)
                    ->update([
                        'id_dotes' => $documento_ids['id_dotes'],
                        'id_dorig' => $documento_ids['id_dorig']
                    ]);

                \Log::info('Righe aggiornate: ' . $updated);
            } else {
                \Log::warning('Nessun documento creato per il movimento ID: ' . $idMovimento);
            }

            // Aggiorna la giacenza totale dell'articolo
            $giacenzaAggiornata = DB::select('
            SELECT SUM(qta) as totale 
            FROM mgmov 
            WHERE id_articolo = ? 
            AND id_azienda = ?
        ', [$id, $utente->id_azienda])[0]->totale ?? 0;

            DB::table('articoli')
                ->where('id', $id)
                ->update(['giacenza' => $giacenzaAggiornata]);

            // Se l'articolo non ha un prezzo impostato, aggiorniamolo con quello di acquisto
            if ($articolo->prezzo == 0 || $articolo->prezzo === null) {
                DB::table('articoli')
                    ->where('id', $id)
                    ->update(['prezzo' => $prezzo_unitario]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Carico effettuato con successo',
                'id_movimento' => $idMovimento,
                'giacenza_aggiornata' => $giacenzaAggiornata,
                'allegato' => $percorsoAllegato ? asset($percorsoAllegato) : null,
                'barcode' => $barcode,
                'articolo' => $articolo,
                'documento_ids' => $documento_ids
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            \Log::error('Errore durante il carico a magazzino: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Si è verificato un errore: ' . $e->getMessage()
            ]);
        }
    }

    public function downloadEtichetta(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Validazione dei dati in ingresso
        $request->validate([
            'articolo_id' => 'required|integer',
            'barcode' => 'required|string',
            'lotto' => 'required|string',
            'num_copie' => 'required|integer|min:1|max:100'
        ]);

        // Recupera i dati dell'articolo
        $articolo = DB::table('articoli')
            ->where('id', $request->articolo_id)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$articolo) {
            abort(404, 'Articolo non trovato');
        }

        // Configurazione per mPDF
        $mpdf = new \Mpdf\Mpdf([
            'format' => [100, 60], // Larghezza x Altezza in mm
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_top' => 5,
            'margin_bottom' => 5,
            'margin_header' => 0,
            'margin_footer' => 0
        ]);

        // Definizione del contenuto HTML
        $html = '';

        // Genera il numero di copie richiesto
        for ($i = 0; $i < $request->num_copie; $i++) {
            $html .= '
        <div style="text-align: center; width: 90mm; margin-bottom: 5mm; page-break-after: ' . ($i < $request->num_copie - 1 ? 'always' : 'auto') . ';">
            <div style="font-weight: bold; font-size: 12pt; margin-bottom: 2mm;">' . $articolo->titolo . '</div>
            <div style="font-size: 8pt; margin-bottom: 2mm;">
                <span>Codice: ' . $articolo->codice_articolo . '</span>
                <span style="margin-left: 10mm;">Lotto: ' . $request->lotto . '</span>
            </div>
            <div style="margin: 3mm 0;">
                <img src="https://barcodeapi.org/api/code128/' . urlencode($request->barcode) . '" style="max-width: 85mm; height: auto;">
            </div>
            <div style="font-size: 8pt;">' . $request->barcode . '</div>
        </div>';
        }

        // Aggiunge il contenuto al PDF
        $mpdf->WriteHTML($html);

        // Imposta il nome del file
        $filename = 'etichette_' . date('Y-m-d_H-i-s') . '.pdf';

        // Genera e restituisci il PDF per il download
        // Usa la costante Destination::DOWNLOAD invece di STRING
        return $mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
    }


    /**
     * Genera un documento BDC (Bolla di Carico)
     *
     * @param int $id_articolo ID dell'articolo
     * @param int $id_fornitore ID del fornitore
     * @param float $quantita Quantità
     * @param string $numero_ddt Numero DDT
     * @param string $data_ddt Data DDT
     * @param float $prezzo_unitario Prezzo unitario
     * @param float $aliquota_iva Aliquota IVA
     * @param float $imponibile Imponibile
     * @param string $lotto Lotto
     * @param int $id_azienda ID dell'azienda
     * @param int $id_utente ID dell'utente
     * @return int|null ID del documento creato
     * @return string Codice EAN128 generato
     */

    private function generaEAN128($id_articolo, $lotto, $id_azienda)
    {
        // Recupera l'articolo per ottenere il codice
        $articolo = DB::table('articoli')
            ->where('id', $id_articolo)
            ->where('id_azienda', $id_azienda)
            ->first();

        // Ottieni il codice articolo o usa l'ID se non disponibile
        $codiceArticolo = $articolo && $articolo->codice_articolo
            ? $articolo->codice_articolo
            : str_pad($id_articolo, 6, '0', STR_PAD_LEFT);

        // Rimuovi spazi e caratteri speciali dal codice articolo
        $codiceArticolo = preg_replace('/[^A-Za-z0-9]/', '', $codiceArticolo);

        // Limita la lunghezza a 8 caratteri
        $codiceArticolo = substr($codiceArticolo, 0, 8);
        $codiceArticolo = str_pad($codiceArticolo, 8, '0', STR_PAD_LEFT);

        // Prepara il lotto (massimo 10 caratteri)
        $lottoFormattato = preg_replace('/[^A-Za-z0-9]/', '', $lotto);
        $lottoFormattato = substr($lottoFormattato, 0, 10);
        $lottoFormattato = str_pad($lottoFormattato, 10, '0', STR_PAD_LEFT);

        // Aggiungi timestamp per rendere il codice unico
        $timestamp = date('YmdHis');

        // Componi il barcode: AI(01) + codice articolo + AI(10) + lotto + timestamp
        $barcode = '01' . $codiceArticolo . '10' . $lottoFormattato . $timestamp;

        return $barcode;
    }
    protected function generaDocumentoBDC($id_articolo, $id_fornitore, $quantita, $numero_ddt, $data_ddt, $prezzo_unitario, $aliquota_iva, $imponibile, $lotto, $percorso_allegato, $id_azienda, $id_utente)
    {
        try {
            // Verifica che esista il documento BDC nella tabella 'do'
            $documento = DB::table('do')
                ->where('cd_do', 'BDC')
                ->where('id_azienda', $id_azienda)
                ->first();

            // Se non esiste il tipo documento BDC, lo creiamo
            if (!$documento) {
                DB::table('do')->insert([
                    'cd_do' => 'BDC',
                    'id_azienda' => $id_azienda,
                    'id_utente' => $id_utente,
                    'descrizione' => 'Bolla di Carico',
                    'attivo' => 1,
                    'carico' => 1
                ]);
            }

            // Recupera dati fornitore
            $fornitore = DB::table('fornitori')
                ->where('id', $id_fornitore)
                ->where('id_azienda', $id_azienda)
                ->first();

            // Recupera dati articolo
            $articolo = DB::table('articoli')
                ->where('id', $id_articolo)
                ->where('id_azienda', $id_azienda)
                ->first();

            if (!$fornitore || !$articolo) {
                \Log::error('Fornitore o articolo non trovato per la creazione del documento BDC', [
                    'id_fornitore' => $id_fornitore,
                    'id_articolo' => $id_articolo,
                    'id_azienda' => $id_azienda
                ]);
                return null;
            }

            // Calcola imposta e totale
            $imposta = round(($imponibile * $aliquota_iva) / 100, 2);
            $totale = $imponibile + $imposta;

            // Calcola numero documento
            $ultimo_numero = DB::table('dotes')
                ->where('cd_do', 'BDC')
                ->where('id_azienda', $id_azienda)
                ->whereYear('data_doc', date('Y'))
                ->max('numero_doc');

            $numero_doc = $ultimo_numero ? $ultimo_numero + 1 : 1;

            // Debug: log dei valori prima dell'inserimento
            \Log::info('Creazione documento BDC', [
                'numero_doc' => $numero_doc,
                'id_fornitore' => $id_fornitore,
                'imponibile' => $imponibile,
                'imposta' => $imposta,
                'totale' => $totale
            ]);

            // Crea testata documento - USANDO SOLO I CAMPI ESISTENTI
            $id_dotes = DB::table('dotes')->insertGetId([
                'id_utente' => $id_utente,
                'id_cliente' => $id_fornitore,
                'id_azienda' => $id_azienda,
                'cd_do' => 'BDC',
                'tipo_documento' => 'BDC',
                'cd_cf' => $fornitore->cd_cf ?? null,
                'numero_doc' => $numero_doc,
                'data_doc' => $data_ddt,
                'partita_iva' => $fornitore->piva,
                'indirizzo' => $fornitore->indirizzo,
                'comune' => $fornitore->comune,
                'ragione_sociale' => $fornitore->ragione_sociale, // Campo esistente nella struttura
                'costo_totale' => $imponibile,
                'costo_totale_iva' => $totale,
                'iva' => $aliquota_iva,
                'allegato' => $percorso_allegato
            ]);

            if (!$id_dotes) {
                \Log::error('Impossibile creare testata documento BDC');
                return null;
            }

            \Log::info('Testata documento BDC creata con ID: ' . $id_dotes);

            // Crea riga documento - USANDO SOLO I CAMPI ESISTENTI
            $id_dorig = DB::table('dorig')->insertGetId([
                'id_utente' => $id_utente,
                'id_azienda' => $id_azienda,
                'id_cliente' => $id_fornitore,
                'id_dotes' => $id_dotes,
                'numero_doc' => $numero_doc,
                'data_doc' => $data_ddt,
                'cd_do' => 'BDC',
                'tipo_documento' => 'BDC',
                'id_articolo' => $id_articolo,
                'cd_cf' => $fornitore->cd_cf ?? null,
                'cd_ar' => $articolo->codice_articolo,
                'qta' => $quantita,
                'prezzo_unitario' => $prezzo_unitario,
                'prezzo_totale' => $imponibile,
                'prezzo_totale_iva' => $totale,
                'iva' => $aliquota_iva,
                'nome_prodotto' => $articolo->titolo,
                'dettagli_prodotto' => $articolo->descrizione,
                'lotto' => $lotto,
                'um' => $articolo->um,
                'pu' => $prezzo_unitario,
                'pt' => $imponibile,
                'imponibile' => $imponibile,
                'imposta' => $imposta,
                'totale' => $totale
            ]);

            if (!$id_dorig) {
                \Log::error('Impossibile creare riga documento BDC per testata ID: ' . $id_dotes);
                return null;
            }

            \Log::info('Riga documento BDC creata con ID: ' . $id_dorig);

            return [
                'id_dotes' => $id_dotes,
                'id_dorig' => $id_dorig
            ];

        } catch (\Exception $e) {
            \Log::error('Errore nella creazione del documento BDC: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'id_articolo' => $id_articolo,
                'id_fornitore' => $id_fornitore
            ]);
            return null;
        }
    }


    public function scaricoMagazzino(Request $request, $id)
    {
        $this->is_loggato();
        $utente = session('utente');


        try {
            // Recupera i dati dalla richiesta
            $quantity = $request->input('giacenza');
            $causale = $request->input('causale');
            $lotto = $request->input('lotto');
            $magazzinoId = $request->input('magazzinoId');
            $scadenza = $request->input('scadenza');
            $id_commessa = $request->input('id_commessa');

            // Recupera l'articolo per verificare che esista e appartenga all'azienda dell'utente
            $articolo = DB::table('articoli')
                ->where('id', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->first();

            if (!$articolo) {
                return response()->json(['success' => false, 'message' => 'Articolo non trovato']);
            }

            // Verifica che il magazzino esista e appartenga all'azienda dell'utente
            $magazzino = DB::table('mg')
                ->where('id', $magazzinoId)
                ->where('id_azienda', $utente->id_azienda)
                ->first();

            if (!$magazzino) {
                return response()->json(['success' => false, 'message' => 'Magazzino non trovato']);
            }

            // Verifica che ci sia disponibilità sufficiente nel magazzino specifico per il lotto richiesto
            $giacenzaDisponibile = DB::select('
            SELECT SUM(qta) as disponibile 
            FROM mgmov 
            WHERE id_articolo = ? 
            AND id_mg = ? 
            AND lotto = ?
            AND id_azienda = ?
        ', [$id, $magazzinoId, $lotto, $utente->id_azienda])[0]->disponibile;

            if ($giacenzaDisponibile < $quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quantità richiesta non disponibile. Disponibilità attuale: ' . $giacenzaDisponibile
                ]);
            }

            // Verifica che la commessa esista (se specificata) e appartenga all'azienda dell'utente
            if ($id_commessa) {
                $commessa = DB::table('commesse')
                    ->where('id', $id_commessa)
                    ->where('id_azienda', $utente->id_azienda)
                    ->first();

                if (!$commessa) {
                    return response()->json(['success' => false, 'message' => 'Commessa non trovata']);
                }
            }


            // Crea il movimento di scarico in mgmov
            $idMovimento = DB::table('mgmov')->insertGetId([
                'id_azienda' => $utente->id_azienda,
                'id_utente' => $utente->id,
                'id_articolo' => $id,
                'id_mg' => $magazzinoId,
                'id_commessa' => $id_commessa ?: null,
                'lotto' => $lotto,
                'scadenza_lotto' => $scadenza,
                'qta' => -$quantity, // Negativo perché è uno scarico
                'car' => 0,
                'sca' => 1, // Indicatore di scarico
                'ret' => 0,
                'datamov' => now(),
                'causale' => $causale
            ]);

            // Aggiorna la giacenza totale dell'articolo
            $giacenzaAggiornata = DB::select('
            SELECT SUM(qta) as totale 
            FROM mgmov 
            WHERE id_articolo = ? 
            AND id_azienda = ?
        ', [$id, $utente->id_azienda])[0]->totale;

            DB::table('articoli')
                ->where('id', $id)
                ->update(['giacenza' => $giacenzaAggiornata]);

            return response()->json([
                'success' => true,
                'message' => 'Scarico effettuato con successo',
                'id_movimento' => $idMovimento,
                'giacenza_aggiornata' => $giacenzaAggiornata
            ]);

        } catch (\Exception $e) {
            \Log::error('Errore durante lo scarico da magazzino: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Si è verificato un errore: ' . $e->getMessage()
            ]);
        }
    }


    public function distinta_base(){
        $this->is_loggato();

        $utente = session('utente');

        // Recupera solo gli articoli con tipologia 0
        $articoli = DB::table('articoli')->where('tipologia', 0)->where('id_azienda', $utente->id_azienda)->get();
        foreach ($articoli as $a) {
            $a->distinta_base = DB::select('SELECT db.*, m.titolo as materiale, m.um FROM distinta_base db JOIN articoli m ON m.id = db.id_materiale WHERE db.id_articolo = ' . $a->id . ' ORDER BY db.posizione ASC');
        }
        return view('utente.distinta_base', compact('articoli', 'utente'));
    }

    public function dettaglioGiacenza(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        $id_articolo = $request->input('id_articolo');
        $id_mg = $request->input('id_mg');
        $lotto = $request->input('lotto');

        if (!$id_articolo || !$id_mg) {
            return response()->json([
                'success' => false,
                'message' => 'Parametri mancanti'
            ]);
        }

        try {
            // Recupera le informazioni sul movimento più recente per questa giacenza
            $movimento = DB::table('mgmov')
                ->where('id_articolo', $id_articolo)
                ->where('id_mg', $id_mg)
                ->where('id_azienda', $utente->id_azienda)
                ->where('lotto', $lotto)
                ->orderBy('datamov', 'desc')
                ->first();

            if (!$movimento) {
                return response()->json([
                    'success' => false,
                    'message' => 'Movimento non trovato'
                ]);
            }

            // Recupera le informazioni sull'articolo
            $articolo = DB::table('articoli')
                ->where('id', $id_articolo)
                ->where('id_azienda', $utente->id_azienda)
                ->first();

            // Recupera le informazioni sul magazzino
            $magazzino = DB::table('mg')
                ->where('id', $id_mg)
                ->where('id_azienda', $utente->id_azienda)
                ->first();

            // Recupera le informazioni sul fornitore
            $fornitore = null;
            if ($movimento->id_cliente) {
                $fornitore = DB::table('fornitori')
                    ->where('id', $movimento->id_cliente)
                    ->where('id_azienda', $utente->id_azienda)
                    ->first();
            }

            // Recupera le informazioni sulla commessa
            $commessa = null;
            if ($movimento->id_commessa) {
                $commessa = DB::table('commesse')
                    ->where('id', $movimento->id_commessa)
                    ->where('id_azienda', $utente->id_azienda)
                    ->first();
            }

            // Recupera le informazioni sul documento collegato
            $documento = null;
            if ($movimento->id_dotes) {
                $documento = DB::table('dotes')
                    ->where('id', $movimento->id_dotes)
                    ->where('id_azienda', $utente->id_azienda)
                    ->first();
            }

            // Calcola la giacenza totale per questo articolo, magazzino e lotto
            $giacenza_totale = DB::table('mgmov')
                ->where('id_articolo', $id_articolo)
                ->where('id_mg', $id_mg)
                ->where('id_azienda', $utente->id_azienda)
                ->where('lotto', $lotto)
                ->sum('qta');

            // Prepara i dati da restituire
            $data = [
                'movimento' => $movimento,
                'articolo' => $articolo,
                'magazzino' => $magazzino,
                'fornitore' => $fornitore,
                'commessa' => $commessa,
                'documento' => $documento,
                'giacenza_totale' => $giacenza_totale
            ];

            // Verifica se l'allegato esiste
            if ($movimento->percorso_allegato) {
                $percorsoCompleto = public_path($movimento->percorso_allegato);
                if (file_exists($percorsoCompleto)) {
                    $data['movimento']->percorso_allegato = asset($movimento->percorso_allegato);
                } else {
                    $data['movimento']->percorso_allegato = null;
                }
            }

            return response()->json([
                'success' => true,
                'data' => $data
            ]);

        } catch (\Exception $e) {
            \Log::error('Errore nel recupero dettaglio giacenza: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Si è verificato un errore: ' . $e->getMessage()
            ]);
        }
    }

    public function distinta_base_dettaglio($id,Request $request)
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

        $materiali = DB::table('articoli')->where('id_azienda', $utente->id_azienda)->whereIn('tipologia', [1, 2, 3])->orderBy('tipologia')->orderBy('titolo')->get();

        $distinta_base = DB::table('distinta_base')
            ->join('articoli as m', 'm.id', '=', 'distinta_base.id_materiale')
            ->where('distinta_base.id_articolo', $id)
            ->select('distinta_base.*', 'm.titolo as materiale', 'm.um')
            ->get()
            ->groupBy('id_fase_articolo');

        return view('utente.distinta_base_dettaglio', compact('articolo', 'fasi_associate', 'materiali', 'distinta_base', 'utente'));
    }

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
            'id_azienda' => $utente->id_azienda, // Aggiungi azienda
            'id_agente' => $dotesOriginale->id_agente,
            'id_cliente' => $dotesOriginale->id_cliente
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
                    'data_doc' => now(),
                    'iva' => $dorigOriginale->iva,
                    'lotto' => $dorigOriginale->lotto,
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
                        'qta_evasa' => $dorigOriginale->qta_evasa + $qtaEvasa,
                    ]);
            }
        }

        // Reindirizza con successo
        return redirect()->back()->with('success', 'Evasione completata e documento creato con successo!');
    }













    public function gestione_magazzini(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();
        $tipologie_valide = array_keys(config('magazzini_tipologie.tipologie'));

        if(isset($dati['ripristina_standard'])) {
            $inseriti = self::inserisci_magazzini_standard($utente->id_azienda);
            if (count($inseriti) > 0) {
                return redirect()->back()->with('success', 'Magazzini standard inseriti: ' . implode(', ', $inseriti));
            }
            return redirect()->back()->with('success', 'Tutti i magazzini standard erano gia\' presenti.');
        }

        if(isset($dati['aggiungi']) || isset($dati['modifica'])) {
            $is_modifica = isset($dati['modifica']);
            $tipologia = in_array($dati['tipologia'] ?? '', $tipologie_valide, true) ? $dati['tipologia'] : 'altro';
            $is_default = !empty($dati['is_default']) ? 1 : 0;
            $produzione = ($tipologia === 'prodotti_finiti' && $is_default) ? 1 : 0;

            $payload = [
                'codice_magazzino' => $dati['codice_magazzino'],
                'descrizione'      => $dati['descrizione'],
                'tipologia'        => $tipologia,
                'is_default'       => $is_default,
                'produzione'       => $produzione,
            ];

            // Garantisce un solo default per tipologia: se questo viene marcato default,
            // togliamo il default dagli altri della stessa tipologia per la stessa azienda.
            if ($is_default) {
                $q = DB::table('mg')
                    ->where('id_azienda', $utente->id_azienda)
                    ->where('tipologia', $tipologia);
                if ($is_modifica) {
                    $q->where('id', '!=', $dati['id']);
                }
                $q->update([
                    'is_default' => 0,
                    'produzione' => DB::raw('CASE WHEN tipologia = "prodotti_finiti" THEN 0 ELSE produzione END'),
                ]);
            }

            if ($is_modifica) {
                DB::table('mg')->where('id', $dati['id'])->where('id_azienda', $utente->id_azienda)->update($payload);
                return redirect()->back()->with('success', 'Magazzino modificato con successo!');
            }
            $payload['id_azienda'] = $utente->id_azienda;
            DB::table('mg')->insert($payload);
            return redirect()->back()->with('success', 'Magazzino creato con successo!');
        }

        if(isset($dati['elimina'])) {
            $id = $dati['id_mg'];
            DB::table('mg')
                ->where('id', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->delete();
            return redirect()->back()->with('success', 'Magazzino eliminato con successo!');
        }

        $magazzini = DB::table('mg')
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('tipologia')
            ->orderByDesc('is_default')
            ->orderBy('codice_magazzino')
            ->get();

        $tipologie_meta = config('magazzini_tipologie.tipologie');

        return view('utente.gestione_magazzini', compact('utente', 'magazzini', 'tipologie_meta'));
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
                'esigibilita_iva' => 'D',
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

        // Copia l'ID dell'agente dal documento originale
        $datiTestata['id_agente'] = $dotesOriginale->id_agente;

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
                    $prezzoTotale = $prezzoNetto + $importoIva;

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














    public function riepilogo($anno) {
        $this->is_loggato();
        $utente = session()->get('utente');

        // Query per il fatturato mensile
        $fatturato_mensile = DB::select('
        SELECT 
            MONTH(data_doc) AS mese, 
            SUM(totale) AS totale_tot
        FROM 
            dotes
        WHERE 
            tipologia_documento LIKE "TD%" 
            AND contabilizzata = 1
            AND YEAR(data_doc) = ? 
            AND id_azienda = ?
        GROUP BY MONTH(data_doc)
        ORDER BY MONTH(data_doc)
    ', [$anno, $utente->id_azienda]);

        // Calcolo del fatturato totale
        $fatturato_totale = DB::table('dotes')
            ->where('tipologia_documento', 'LIKE', 'TD%')
            ->whereYear('data_doc', $anno)
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



    private function inviaFileXmlSftp($xmlFile,$azienda) {

        $sftpHost = '135.125.180.188';
        $sftpPort = 22; // Porta SFTP standard
        $sftpUser = 'sftp_user';
        $ppk_path = 'CERTS/ingenia_private.ppk';
        $remotePath = 'DatiVersoSdI/';

        date_default_timezone_set('UTC');

        $year = date("Y");

        // Calcola il giorno giuliano (1-366)
        $julianDay = date("z") + 1; // date("z") ritorna 0-365, aggiungiamo 1

        // Formatta il giorno giuliano con padding di zeri
        $julianDay = str_pad($julianDay, 3, "0", STR_PAD_LEFT);

        // Ora corrente nel formato HHMM
        $time = date("Hi");

        // Prende anche i secondi per creare un nome file ancora più univoco
        $seconds = date("s");

        // Crea il nome del file ZIP con i secondi su 3 cifre (001-060)
        $seconds3 = str_pad($seconds, 3, "0", STR_PAD_LEFT);
        $zipFileName = sprintf(
            'FI.%s.%s%s.%s.%s.zip',
            $azienda->partita_iva, // partita IVA azienda
            $year,                 // anno completo (es. 2025)
            $julianDay,            // giorno giuliano (es. 008)
            $time,                 // ora e minuti (es. 1430)
            $seconds3              // secondi su tre cifre (es. 009, 032, 060)
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
            $idNodo = $xml->createElement('IdentificativoNodo', $azienda->partita_iva);
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

        } else {

            throw new \Exception("Errore durante l'upload del file: " . $fileName);
        }

        return true;
    }



    public function check_partita_iva(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        $partita_iva = $request->input('piva');
        $id_cliente = $request->input('id_cliente', null); // Per escludere in caso di modifica

        $query = DB::table('clienti')
            ->where('piva', $partita_iva)
            ->where('id_azienda', $utente->id_azienda);

        // Se stiamo modificando un cliente, escludiamo l'ID corrente
        if ($id_cliente) {
            $query->where('id', '!=', $id_cliente);
        }

        $cliente_esistente = $query->first();

        if ($cliente_esistente) {
            return response()->json([
                'exists' => true,
                'cliente' => $cliente_esistente->ragione_sociale ?? $cliente_esistente->nome . ' ' . $cliente_esistente->cognome
            ]);
        }

        return response()->json(['exists' => false]);
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
        $dati_riepilogo = DB::select('SELECT sum(imponibile) imponibile, sum(imposta) imposta, iva, codice_iva, rif_normativo, rif_normativo_pdf FROM dorig WHERE id_dotes = ? GROUP BY codice_iva', [$id]);
        $scadenziario = DB::select('SELECT * from scadenziario where id_dotes ='.$id);


        // Recupero dei dati dell'azienda
        $azienda = DB::table('aziende')->where('id', $utente->id_azienda)->first();
        if (sizeof($righe) > 0) {
            $testata = $testata[0];
            $xml = View::make('fatturazione.xml', compact('testata', 'righe', 'dati_riepilogo', 'azienda','scadenziario'));

            $response = Response::create($xml, 200);
            $response->header('Content-Type', 'text/xml');
            $response->header('Cache-Control', 'public');
            $response->header('Content-Description', 'File Transfer');
            $response->header('Content-Disposition', 'attachment; filename=IT08949141215_' . str_pad(base_convert($testata->id, 10, 36), 5, '0', STR_PAD_LEFT) . '.xml');
            $response->header('Content-Transfer-Encoding', 'binary');
            return $response;
        }
    }

    public function genera_sdd($id){

        $this->is_loggato();
        $utente = session('utente');

        // Recupero della testata e delle righe
        $testata = DB::select('SELECT * FROM dotes WHERE id = ? AND id_azienda = ?', [$id, $utente->id_azienda]);
        $righe = DB::select('SELECT * FROM dorig WHERE id_dotes = ? AND id_azienda = ?', [$id, $utente->id_azienda]);
        $dati_riepilogo = DB::select('SELECT sum(imponibile) imponibile, sum(imposta) imposta, iva, codice_iva, rif_normativo, rif_normativo_pdf FROM dorig WHERE id_dotes = ? GROUP BY codice_iva', [$id]);
        $scadenziario = DB::select('SELECT * from scadenziario where id_dotes ='.$id);
        $azienda = DB::table('aziende')->where('id', $utente->id_azienda)->first();


        if (sizeof($righe) > 0) {

            $testata = $testata[0];

            // Crea il contenuto XML con una view
            $xml = View::make('ssd.xml', compact('testata', 'righe', 'dati_riepilogo', 'azienda','scadenziario'));


            $response = Response::create($xml, 200);
            $response->header('Content-Type', 'text/xml');
            $response->header('Cache-Control', 'public');
            $response->header('Content-Description', 'File Transfer');
            $response->header('Content-Disposition', 'attachment; filename=IT02883500643_' . str_pad(base_convert($testata->id, 10, 36), 5, '0', STR_PAD_LEFT) . '.xml');
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
        $dati_riepilogo = DB::select('SELECT sum(imponibile) imponibile, sum(imposta) imposta, iva, codice_iva, rif_normativo,rif_normativo_pdf FROM dorig WHERE id_dotes = ? GROUP BY codice_iva', [$id]);
        $scadenziario = DB::select('SELECT * from scadenziario where id_dotes ='.$id);


        // Recupero dei dati dell'azienda
        $azienda = DB::table('aziende')->where('id', $utente->id_azienda)->first();

        if (sizeof($righe) > 0) {
            $testata = $testata[0];
            $xml = View::make('fatturazione.xml_stampa', compact('testata', 'righe', 'dati_riepilogo', 'azienda','scadenziario'));

            $response = Response::create($xml, 200);
            $response->header('Content-Type', 'text/xml');
            return $response;
        }
    }

    public function visualizza_xml_da_file($id) {
        $utente = session('utente');

        // Query RAW come da standard
        $documento = DB::select("
        SELECT nome_file_fattura 
        FROM dotes 
        WHERE id = ? AND id_azienda = ? 
        LIMIT 1",
            [$id, $utente->id_azienda]
        );

        if (empty($documento) || empty($documento[0]->nome_file_fattura)) {
            return response()->json(['error' => 'File XML non trovato']);
        }

        $file_path = 'fatture_ricevute/'.$utente->piva.'/backup/fatture/' . $documento[0]->nome_file_fattura;
        // Leggi il contenuto del file XML
        $xml_content = file_get_contents($file_path);
        $xml_content = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $xml_content);
        $xml_content = preg_replace('/<\?xml[^>]+\?>\s*/', '', $xml_content);
        $xml_content = trim($xml_content);

        $header = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
            '<?xml-stylesheet type="text/xsl" href="../../FoglioStileAssoSoftware.xsl"?>';

        $xml_content = $header . "\n" . $xml_content;

        $response = Response::create($xml_content, 200);
        $response->header('Content-Type', 'text/xml');
        return $response;
    }

    public function impostazioni(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if (isset($dati['salva_impostazioni'])) {
            $tariffa = $dati['manut_tariffa_oraria_default'] ?? null;
            if ($tariffa === null || $tariffa === '') {
                $tariffa = 33.75;
            } else {
                $tariffa = (float) str_replace(',', '.', $tariffa);
            }

            DB::table('aziende')
                ->where('id', $utente->id_azienda)
                ->update([
                    'usa_lotti'                              => isset($dati['usa_lotti']) ? 1 : 0,
                    'usa_barcode'                            => isset($dati['usa_barcode']) ? 1 : 0,
                    'manut_anagrafica_vagoni_attiva'         => isset($dati['manut_anagrafica_vagoni_attiva']) ? 1 : 0,
                    'manut_certificato_ecm_separato'         => isset($dati['manut_certificato_ecm_separato']) ? 1 : 0,
                    'manut_workflow_accettazione_multistep'  => isset($dati['manut_workflow_accettazione_multistep']) ? 1 : 0,
                    'manut_magazzino_ricetta_default'        => isset($dati['manut_magazzino_ricetta_default']) ? 1 : 0,
                    'manut_consuntivo_materiali_manutentore' => isset($dati['manut_consuntivo_materiali_manutentore']) ? 1 : 0,
                    'manut_tariffa_oraria_default'           => $tariffa,
                ]);
            return redirect()->back()->with('success', 'Impostazioni salvate con successo');
        }

        $azienda = DB::table('aziende')->where('id', $utente->id_azienda)->first();
        $page = 'impostazioni';

        return View::make('utente.impostazioni', compact('utente', 'azienda', 'page'));
    }

    public function profilo(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if(isset($dati['modifica'])){
            unset($dati['modifica']);

            // Validazione base
            $request->validate([
                'ragione_sociale' => 'required',
                'partita_iva' => 'required',
                'indirizzo' => 'required',
                'comune' => 'required',
                'provincia' => 'required',
                'cap' => 'required|numeric',
                'codice_sdi' => 'required',
                'pec' => 'required|email',
            ]);

            // Gestione upload immagine
            if($_FILES['immagine']['name'] != ''){
                $pathinfo = pathinfo($_FILES['immagine']['name']);
                $nome = Str::random(20);
                $target = 'immagini/aziende/' .$nome.'.'.$pathinfo['extension'];
                move_uploaded_file($_FILES['immagine']['tmp_name'], $target);
                $dati['immagine'] = $target;
            }

            // Formatta il capitale sociale (rimuovi punti e virgole se necessario)
            if(isset($dati['capitale_sociale'])) {
                $dati['capitale_sociale'] = str_replace(['.', ','], ['', '.'], $dati['capitale_sociale']);
            }


            DB::table('aziende')
                ->where('id', $utente->id_azienda)
                ->update($dati);

            return Redirect::to('utente/profilo')->with('success', 'Profilo aggiornato con successo');
        }

        // Recupera i dati necessari per i select
        $regimi_fiscali = DB::table('ft_regimi_fiscali')->get();
        $modalita_pagamenti = DB::table('ft_modalita_pagamento')->get();
        $nature = DB::table('ft_nature')->get();

        // Recupera i dati dell'azienda
        $azienda = DB::table('aziende')
            ->where('id', $utente->id_azienda)
            ->first();

        return View::make('utente.profilo', compact('utente', 'azienda', 'regimi_fiscali', 'modalita_pagamenti', 'nature'));
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



    public function cambia_anno($anno){

        session(['anno' => $anno]);

        return Redirect::to('utente/index');
    }



    public function logout(){

        session()->flush();
        return Redirect::to('admin/login');
    }

    public function is_loggato(){

        if (!session()->has('utente')) return Redirect::to('admin/login')->send();

    }

    public function commesse_dashboard($id_commessa, Request $request)
    {
        $utente = $request->session()->get('utente');
        $dati = $request->all();

        // Recupera i dati della commessa
        $commessa = DB::table('commesse')
            ->where('id', $id_commessa)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$commessa) {
            return redirect('utente/commesse')->with('error', 'Commessa non trovata');
        }

        // Gestione aggiunta movimento manuale
        if (isset($dati['aggiungi_movimento_cassa'])) {
            DB::table('commesse_movimenti_cassa')->insert([
                'id_azienda' => $utente->id_azienda,
                'id_commessa' => $id_commessa,
                'tipo' => $dati['tipo_movimento'],
                'importo' => $dati['importo'],
                'data_movimento' => $dati['data_movimento'],
                'descrizione' => $dati['descrizione_movimento'],
                'modalita_pagamento' => $dati['modalita_pagamento'] ?? null,
                'note' => $dati['note_movimento'] ?? null,
                'created_at' => now(),
            ]);
            return redirect()->back()->with('success', 'Movimento registrato con successo');
        }

        // Eliminazione movimento manuale
        if (isset($dati['elimina_movimento_cassa'])) {
            DB::table('commesse_movimenti_cassa')
                ->where('id', $dati['id_movimento'])
                ->where('id_azienda', $utente->id_azienda)
                ->delete();
            return redirect()->back()->with('success', 'Movimento eliminato');
        }

        // Recupera i documenti del ciclo attivo associati alla commessa
        // Recupera i tipi documento attivi dell'azienda
        $tipi_attivi = DB::table('do')->where('id_azienda', $utente->id_azienda)->where('attivo', 1)->pluck('cd_do')->toArray();
        if(empty($tipi_attivi)) $tipi_attivi = ['FTV', 'DDT', 'OC', 'PRE', 'ORD'];

        $documenti_ciclo_attivo = DB::table('dotes')
            ->where('id_commessa', $id_commessa)
            ->where('dotes.id_azienda', $utente->id_azienda)
            ->whereIn('cd_do', $tipi_attivi)
            ->leftJoin('clienti', 'dotes.id_cliente', '=', 'clienti.id')
            ->select('dotes.*', 'clienti.ragione_sociale')
            ->orderBy('data_doc', 'desc')
            ->get();

        // Recupera i documenti del ciclo passivo associati alla commessa
        $tipi_passivi = DB::table('do')->where('id_azienda', $utente->id_azienda)->where('passivo', 1)->pluck('cd_do')->toArray();
        if(empty($tipi_passivi)) $tipi_passivi = ['FTP', 'BUF', 'OFO', 'BCD'];

        $documenti_ciclo_passivo = DB::table('dotes')
            ->where('id_commessa', $id_commessa)
            ->where('dotes.id_azienda', $utente->id_azienda)
            ->whereIn('cd_do', $tipi_passivi)
            ->leftJoin('clienti', 'dotes.id_cliente', '=', 'clienti.id')
            ->select('dotes.*', 'clienti.ragione_sociale')
            ->orderBy('data_doc', 'desc')
            ->get();

        // Recupera gli incassi associati ai documenti della commessa
        $incassi = DB::table('scadenziario')
            ->join('dotes', 'scadenziario.id_dotes', '=', 'dotes.id')
            ->where('dotes.id_commessa', $id_commessa)
            ->where('dotes.id_azienda', $utente->id_azienda)
            ->where('scadenziario.tipo_movimento', 'entrata')
            ->select('scadenziario.*', 'dotes.numero_doc', 'dotes.cd_do')
            ->orderBy('scadenziario.data_pagamento', 'desc')
            ->get();

        // Recupera i pagamenti associati ai documenti della commessa
        $pagamenti = DB::table('scadenziario')
            ->join('dotes', 'scadenziario.id_dotes', '=', 'dotes.id')
            ->where('dotes.id_commessa', $id_commessa)
            ->where('dotes.id_azienda', $utente->id_azienda)
            ->where('scadenziario.tipo_movimento', 'uscita')
            ->select('scadenziario.*', 'dotes.numero_doc', 'dotes.cd_do')
            ->orderBy('scadenziario.data_pagamento', 'desc')
            ->get();

        // Recupera i movimenti di magazzino associati ai documenti della commessa
        $movimenti_magazzino = DB::table('mgmov')
            ->leftJoin('dorig', 'mgmov.id_dorig', '=', 'dorig.id')
            ->leftJoin('dotes', 'dorig.id_dotes', '=', 'dotes.id')
            ->join('articoli', 'mgmov.id_articolo', '=', 'articoli.id')
            ->join('mg', 'mgmov.id_mg', '=', 'mg.id')
            ->where(function($query) use ($id_commessa) {
                // Cerca per id_commessa direttamente in mgmov O nei documenti collegati
                $query->where('mgmov.id_commessa', $id_commessa)
                    ->orWhere('dotes.id_commessa', $id_commessa);
            })
            ->where('mgmov.id_azienda', $utente->id_azienda) // Filtro principale per azienda
            ->select(
                'mgmov.*',
                'articoli.titolo as nome_articolo',
                'articoli.codice_articolo',
                'mg.descrizione as magazzino_nome',
                'mg.codice_magazzino',
                'dotes.numero_doc',
                'dotes.cd_do',
                'dotes.data_doc',
                // Aggiungi informazioni per distinguere il tipo di movimento
                DB::raw('CASE 
            WHEN mgmov.car = 1 THEN "Carico"
            WHEN mgmov.sca = 1 THEN "Scarico" 
            WHEN mgmov.ret = 1 THEN "Reso"
            ELSE "Movimento"
        END as tipo_movimento'),
                // Indica se ha documenti collegati
                DB::raw('CASE 
            WHEN dotes.id IS NOT NULL THEN "Con Documento"
            ELSE "Diretto"
        END as tipo_registrazione')
            )
            ->orderBy('mgmov.datamov', 'desc')
            ->get();

        // Recupera le attività associate alla commessa
        $attivita = DB::table('commesse_attivita')
            ->where('id_commessa', $id_commessa)
            ->orderBy('data_inizio', 'asc')
            ->get();

        // Calcola i totali
        $totale_fatturato = $documenti_ciclo_attivo->where('cd_do', 'FTV')->sum('totale');
        $totale_costi_documenti = $documenti_ciclo_passivo->where('cd_do', 'FTP')->sum('totale');

        // Calcola il costo totale delle attività
        $totale_costi_attivita = 0;
        foreach ($attivita as $att) {
            $totale_costi_attivita += isset($att->costo) ? $att->costo : 0;
        }

        $totale_costi = $totale_costi_documenti + $totale_costi_attivita;
        $margine = $totale_fatturato - $totale_costi;

        // Recupero ODL collegati alla commessa (tramite odl.id_commessa oppure dotes.id_commessa)
        $odl_commessa = DB::select('
            SELECT o.*, a.titolo as articolo_nome, a.codice_articolo,
                   d.cd_do, d.numero_doc
            FROM odl o
            LEFT JOIN articoli a ON a.id = o.id_articolo
            LEFT JOIN dotes d ON d.id = o.id_dotes
            WHERE o.id_azienda = ?
            AND (o.id_commessa = ? OR d.id_commessa = ?)
            ORDER BY o.data DESC',
            [$utente->id_azienda, $id_commessa, $id_commessa]
        );

        // Calcolo costi materiali da movimenti di magazzino (scarichi da ODL della commessa)
        $costi_materiali = DB::select('
            SELECT COALESCE(SUM(ABS(m.qta) * COALESCE(a.prezzo, 0)), 0) as totale
            FROM mgmov m
            LEFT JOIN articoli a ON a.id = m.id_articolo
            WHERE m.id_azienda = ? AND m.sca = 1
            AND (
                m.id_commessa = ?
                OR m.id_odl IN (SELECT o.id FROM odl o WHERE o.id_commessa = ? AND o.id_azienda = ?)
                OR m.id_odl IN (SELECT o.id FROM odl o INNER JOIN dotes d ON d.id = o.id_dotes WHERE d.id_commessa = ? AND o.id_azienda = ?)
            )',
            [$utente->id_azienda, $id_commessa, $id_commessa, $utente->id_azienda, $id_commessa, $utente->id_azienda]
        );

        // Calcolo costi manodopera dagli ODL (ore lavorate * costo orario dipendente)
        $costi_manodopera = DB::select('
            SELECT COALESCE(SUM(
                TIMESTAMPDIFF(MINUTE, r.inizio, r.fine) / 60.0 * COALESCE(u.costo_orario, 0)
            ), 0) as totale,
            COALESCE(SUM(TIMESTAMPDIFF(MINUTE, r.inizio, r.fine) / 60.0), 0) as ore_totali
            FROM odl_righe r
            INNER JOIN odl o ON o.id = r.id_odl
            LEFT JOIN dotes d ON d.id = o.id_dotes
            LEFT JOIN utenti u ON u.id = r.id_operatore_assegnato
            WHERE o.id_azienda = ?
            AND (o.id_commessa = ? OR d.id_commessa = ?)
            AND r.inizio IS NOT NULL AND r.fine IS NOT NULL
            AND r.completato = 1',
            [$utente->id_azienda, $id_commessa, $id_commessa]
        );

        // Dettaglio ore per dipendente negli ODL della commessa
        $dettaglio_manodopera = DB::select('
            SELECT u.id, CONCAT(u.nome, " ", u.cognome) as dipendente,
                   u.costo_orario,
                   COALESCE(SUM(TIMESTAMPDIFF(MINUTE, r.inizio, r.fine) / 60.0), 0) as ore,
                   COALESCE(SUM(TIMESTAMPDIFF(MINUTE, r.inizio, r.fine) / 60.0 * COALESCE(u.costo_orario, 0)), 0) as costo_totale
            FROM odl_righe r
            INNER JOIN odl o ON o.id = r.id_odl
            LEFT JOIN dotes d ON d.id = o.id_dotes
            INNER JOIN utenti u ON u.id = r.id_operatore_assegnato
            WHERE o.id_azienda = ?
            AND (o.id_commessa = ? OR d.id_commessa = ?)
            AND r.inizio IS NOT NULL AND r.fine IS NOT NULL
            AND r.completato = 1
            GROUP BY u.id, u.nome, u.cognome, u.costo_orario
            ORDER BY u.cognome',
            [$utente->id_azienda, $id_commessa, $id_commessa]
        );

        // Totale costi produzione (materiali + manodopera)
        $totale_costi_produzione = $costi_materiali[0]->totale + $costi_manodopera[0]->totale;

        // Movimenti cassa manuali
        $movimenti_cassa = DB::table('commesse_movimenti_cassa')
            ->where('id_commessa', $id_commessa)
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('data_movimento', 'desc')
            ->get();

        $incassi_manuali = $movimenti_cassa->where('tipo', 'entrata');
        $pagamenti_manuali = $movimenti_cassa->where('tipo', 'uscita');
        $totale_incassi_manuali = $incassi_manuali->sum('importo');
        $totale_pagamenti_manuali = $pagamenti_manuali->sum('importo');

        // Somma totali (documenti + manuali)
        $totale_incassato = $incassi->sum('importo_pagato') + $totale_incassi_manuali;
        $totale_pagato = $pagamenti->sum('importo_pagato') + $totale_pagamenti_manuali;

        return view('utente.commesse_dashboard', [
            'utente' => $utente,
            'commessa' => $commessa,
            'documenti_ciclo_attivo' => $documenti_ciclo_attivo,
            'documenti_ciclo_passivo' => $documenti_ciclo_passivo,
            'incassi' => $incassi,
            'pagamenti' => $pagamenti,
            'movimenti_magazzino' => $movimenti_magazzino,
            'attivita' => $attivita,
            'totale_fatturato' => $totale_fatturato,
            'totale_costi' => $totale_costi,
            'totale_costi_documenti' => $totale_costi_documenti,
            'totale_costi_attivita' => $totale_costi_attivita,
            'totale_incassato' => $totale_incassato,
            'totale_pagato' => $totale_pagato,
            'margine' => $margine,
            'odl_commessa' => $odl_commessa,
            'costi_materiali' => $costi_materiali,
            'costi_manodopera' => $costi_manodopera,
            'dettaglio_manodopera' => $dettaglio_manodopera,
            'totale_costi_produzione' => $totale_costi_produzione,
            'incassi_manuali' => $incassi_manuali,
            'pagamenti_manuali' => $pagamenti_manuali,
            'totale_incassi_manuali' => $totale_incassi_manuali,
            'totale_pagamenti_manuali' => $totale_pagamenti_manuali,
        ]);
    }

    // API per ottenere i dettagli di un'attività
    public function get_attivita($id)
    {
        $this->is_loggato();
        $utente = session('utente');

        $attivita = DB::table('commesse_attivita')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$attivita) {
            return response()->json(['success' => false, 'message' => 'Attività non trovata'], 404);
        }

        return response()->json($attivita);
    }

    // API per creare una nuova attività
    public function crea_attivita(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Validazione
        if (empty($dati['titolo']) || empty($dati['data_inizio']) || empty($dati['data_fine']) || empty($dati['id_commessa'])) {
            return response()->json(['success' => false, 'message' => 'Dati mancanti'], 400);
        }

        // Verifica che la commessa esista
        $commessa = DB::table('commesse')
            ->where('id', $dati['id_commessa'])
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$commessa) {
            return response()->json(['success' => false, 'message' => 'Commessa non trovata'], 404);
        }

        // Prepara i dati
        $datiAttivita = [
            'id_commessa' => $dati['id_commessa'],
            'id_azienda' => $utente->id_azienda,
            'id_utente' => $utente->id,
            'titolo' => $dati['titolo'],
            'descrizione' => $dati['descrizione'] ?? null,
            'data_inizio' => $dati['data_inizio'],
            'data_fine' => $dati['data_fine'],
            'completamento' => $dati['completamento'] ?? 0,
            'costo' => $dati['costo'] ?? 0,
            'stato' => $dati['stato'] ?? 'da_iniziare',
            'priorita' => $dati['priorita'] ?? 'media',
            'id_attivita_precedente' => !empty($dati['id_attivita_precedente']) ? $dati['id_attivita_precedente'] : null,
            'id_responsabile' => $dati['id_responsabile'] ?? null,
            'note' => $dati['note'] ?? null,
            'created_at' => now(),
            'updated_at' => now()
        ];

        // Inserisci l'attività
        $id = DB::table('commesse_attivita')->insertGetId($datiAttivita);

        return response()->json(['success' => true, 'message' => 'Attività creata con successo', 'id' => $id]);
    }

    // API per aggiornare un'attività esistente
    public function aggiorna_attivita(Request $request, $id)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Verifica che l'attività esista
        $attivita = DB::table('commesse_attivita')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$attivita) {
            return response()->json(['success' => false, 'message' => 'Attività non trovata'], 404);
        }

        // Prepara i dati da aggiornare
        $datiAggiornamento = [
            'titolo' => $dati['titolo'] ?? $attivita->titolo,
            'descrizione' => $dati['descrizione'] ?? $attivita->descrizione,
            'data_inizio' => $dati['data_inizio'] ?? $attivita->data_inizio,
            'data_fine' => $dati['data_fine'] ?? $attivita->data_fine,
            'completamento' => $dati['completamento'] ?? $attivita->completamento,
            'costo' => $dati['costo'] ?? $attivita->costo ?? 0,
            'stato' => $dati['stato'] ?? $attivita->stato,
            'priorita' => $dati['priorita'] ?? $attivita->priorita,
            'id_attivita_precedente' => !empty($dati['id_attivita_precedente']) ? $dati['id_attivita_precedente'] : null,
            'id_responsabile' => $dati['id_responsabile'] ?? $attivita->id_responsabile,
            'note' => $dati['note'] ?? $attivita->note,
            'updated_at' => now()
        ];

        // Aggiorna l'attività
        DB::table('commesse_attivita')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->update($datiAggiornamento);

        return response()->json(['success' => true, 'message' => 'Attività aggiornata con successo']);
    }

    // API per eliminare un'attività
    public function elimina_attivita($id)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Verifica che l'attività esista
        $attivita = DB::table('commesse_attivita')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$attivita) {
            return response()->json(['success' => false, 'message' => 'Attività non trovata'], 404);
        }

        // Elimina l'attività
        DB::table('commesse_attivita')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->delete();

        return response()->json(['success' => true, 'message' => 'Attività eliminata con successo']);
    }

    public function esportaScadenzePdf() {
        $utente = session('utente');

        // Recupera tutte le scadenze aperte (entrate e uscite) ordinate per importo decrescente
        $scadenze_entrata = DB::select("
            SELECT s.*, c.ragione_sociale as cliente,d.data_doc
            FROM scadenziario s 
            LEFT JOIN clienti c ON s.id_cliente = c.id 
            LEFT JOIN dotes d ON d.id = s.id_dotes
            WHERE s.id_azienda = ? AND s.importo_pagato < s.importo 
            ORDER BY (s.importo - s.importo_pagato) DESC
        ", [$utente->id_azienda]);

        $scadenze_uscita = DB::select("
            SELECT s.*, c.ragione_sociale as cliente 
            FROM scadenziario s 
            LEFT JOIN clienti c ON s.id_cliente = c.id 
            WHERE s.id_azienda = ? AND s.importo_pagato < s.importo 
            ORDER BY (s.importo - s.importo_pagato) DESC
        ", [$utente->id_azienda]);

        // Calcola i totali
        $totale_entrate = collect($scadenze_entrata)->sum(function($s) {
            return $s->importo - $s->importo_pagato;
        });

        $totale_uscite = collect($scadenze_uscita)->sum(function($s) {
            return $s->importo - $s->importo_pagato;
        });

        // Crea il PDF
        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 15,
            'margin_bottom' => 15
        ]);

        $html = view('utente.pdf.scadenze', compact('scadenze_entrata', 'scadenze_uscita', 'totale_entrate', 'totale_uscite'))->render();

        $mpdf->WriteHTML($html);

        return $mpdf->Output('scadenze.pdf', Destination::DOWNLOAD);
    }

    public function vagoni(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if (isset($dati['aggiungi'])) {
            unset($dati['aggiungi']);
            $dati['id_azienda'] = $utente->id_azienda;
            $dati['id_utente']  = $utente->id;
            $dati['attivo']     = isset($dati['attivo']) ? 1 : 1;
            $dati = $this->normalizza_vagone($dati);

            DB::table('vagoni')->insert($dati);
            return Redirect::to('utente/vagoni')->with('success', 'Vagone aggiunto con successo');
        }

        if (isset($dati['elimina'])) {
            DB::table('vagoni')
                ->where('id', $dati['id'])
                ->where('id_azienda', $utente->id_azienda)
                ->delete();
            return Redirect::to('utente/vagoni')->with('success', 'Vagone eliminato con successo');
        }

        $vagoni = DB::table('vagoni')
            ->leftJoin('clienti', 'clienti.id', '=', 'vagoni.id_cliente')
            ->where('vagoni.id_azienda', $utente->id_azienda)
            ->select('vagoni.*', 'clienti.ragione_sociale as cliente_ragione_sociale')
            ->orderBy('vagoni.codice')
            ->get();

        $clienti = DB::table('clienti')
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('ragione_sociale')
            ->get();

        $page = 'vagoni';
        return View::make('utente.vagoni', compact('utente', 'vagoni', 'clienti', 'page'));
    }

    public function dettaglio_vagone($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if (isset($dati['modifica'])) {
            unset($dati['modifica']);
            $dati['attivo'] = isset($dati['attivo']) ? 1 : 0;
            $dati = $this->normalizza_vagone($dati);

            DB::table('vagoni')
                ->where('id', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->update($dati);
            return Redirect::to('utente/dettaglio_vagone/'.$id)->with('success', 'Vagone modificato con successo');
        }

        $vagone = DB::table('vagoni')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$vagone) {
            return Redirect::to('utente/vagoni')->with('error', 'Vagone non trovato');
        }

        $clienti = DB::table('clienti')
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('ragione_sociale')
            ->get();

        $page = 'vagoni';
        return View::make('utente.dettaglio_vagone', compact('utente', 'vagone', 'clienti', 'page'));
    }

    public function documento_workflow($id_dotes, $azione, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        $motivo = $dati['motivo_rifiuto'] ?? null;

        [$ok, $msg] = \App\Services\WorkflowAccettazione::transiziona(
            (int) $id_dotes,
            (int) $utente->id_azienda,
            $azione,
            (int) $utente->id,
            $motivo
        );

        if ($ok) {
            return redirect()->back()->with('success', $msg);
        }
        return redirect()->back()->with('error', $msg);
    }

    public function ordina_righe_documento($id_dotes, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $ids = $request->input('ids', []);

        if (!is_array($ids)) {
            return response()->json(['ok' => false, 'error' => 'ids deve essere array']);
        }

        foreach ($ids as $idx => $id_riga) {
            DB::table('dorig')
                ->where('id', (int) $id_riga)
                ->where('id_dotes', (int) $id_dotes)
                ->where('id_azienda', $utente->id_azienda)
                ->update(['n_riga' => $idx + 1]);
        }

        return response()->json(['ok' => true, 'count' => count($ids)]);
    }

    public function applica_lavorazioni_a_documento($id_dotes, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $idsMacro = $request->input('id_lavorazioni', []);
        $idsRighe = $request->input('id_lavorazioni_righe', []);

        $nTot = 0;
        $msgs = [];

        if (is_array($idsMacro) && count($idsMacro) > 0) {
            [$ok, $msg, $n] = \App\Services\ApplicaLavorazione::applicaA(
                (int) $id_dotes, (int) $utente->id_azienda, $idsMacro, (int) $utente->id
            );
            $nTot += (int) $n;
            $msgs[] = $msg;
        }

        if (is_array($idsRighe) && count($idsRighe) > 0) {
            [$ok2, $msg2, $n2] = \App\Services\ApplicaLavorazione::applicaRigheA(
                (int) $id_dotes, (int) $utente->id_azienda, $idsRighe, (int) $utente->id
            );
            $nTot += (int) $n2;
            $msgs[] = $msg2;
        }

        if ($nTot === 0) {
            return redirect()->back()->with('error', 'Nessuna riga selezionata');
        }
        return redirect()->back()->with('success', implode(' · ', $msgs));
    }

    public function ajax_lavorazione_righe($id_lavorazione, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $righe = DB::table('lavorazioni_righe')
            ->where('id_lavorazione', $id_lavorazione)
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('ordinamento')
            ->orderBy('id')
            ->get();
        return response()->json(['righe' => $righe]);
    }

    public function dorig_aggiorna($id_riga, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $r = DB::table('dorig')->where('id', $id_riga)->where('id_azienda', $utente->id_azienda)->first();
        if (!$r) return redirect()->back()->with('error', 'Riga non trovata');

        $qta        = (float) $request->input('qta', $r->qta);
        $pu         = (float) $request->input('prezzo_unitario', $r->prezzo_unitario);
        $iva        = (int)   $request->input('iva', $r->iva);
        $descr      = $request->input('descrizione', $r->descrizione);
        $materiale  = (float) $request->input('materiale', $r->materiale ?? 0);

        $pt         = round($qta * $pu, 2);
        $imponibile = $pt + $materiale;
        $imposta    = round($imponibile - ($imponibile / (1 + ($iva / 100))), 2);
        $imponibile = round($imponibile - $imposta, 2);
        $totale     = round($imponibile + $imposta, 2);

        DB::table('dorig')->where('id', $id_riga)->where('id_azienda', $utente->id_azienda)->update([
            'descrizione'       => $descr,
            'qta'               => $qta,
            'pu'                => $pu,
            'pt'                => $pt,
            'prezzo_unitario'   => $pu,
            'prezzo_totale'     => $pt,
            'prezzo_totale_iva' => $totale,
            'iva'               => $iva,
            'imponibile'        => $imponibile,
            'imposta'           => $imposta,
            'totale'            => $totale,
            'materiale'         => $materiale,
        ]);

        \App\Services\ApplicaLavorazione::ricalcolaAggregatiDotes((int) $r->id_dotes, (int) $utente->id_azienda);

        return redirect()->back()->with('success', 'Riga aggiornata');
    }

    public function dorig_elimina($id_riga, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $r = DB::table('dorig')->where('id', $id_riga)->where('id_azienda', $utente->id_azienda)->first();
        if (!$r) return redirect()->back()->with('error', 'Riga non trovata');
        DB::table('dorig')->where('id', $id_riga)->where('id_azienda', $utente->id_azienda)->delete();
        \App\Services\ApplicaLavorazione::ricalcolaAggregatiDotes((int) $r->id_dotes, (int) $utente->id_azienda);
        return redirect()->back()->with('success', 'Riga eliminata');
    }

    public function dorig_duplica($id_riga, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $r = DB::table('dorig')->where('id', $id_riga)->where('id_azienda', $utente->id_azienda)->first();
        if (!$r) return redirect()->back()->with('error', 'Riga non trovata');

        // Calcola nuovo n_riga = max esistente +1
        $maxN = (int) DB::table('dorig')->where('id_dotes', $r->id_dotes)->where('id_azienda', $utente->id_azienda)->max('n_riga');
        $nuova = (array) $r;
        unset($nuova['id']);
        $nuova['n_riga'] = $maxN + 1;
        DB::table('dorig')->insert($nuova);

        // Ricalcola totali testata
        \App\Services\ApplicaLavorazione::ricalcolaAggregatiDotes((int) $r->id_dotes, (int) $utente->id_azienda);

        return redirect()->back()->with('success', 'Riga duplicata');
    }

    public function lavorazioni(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if (isset($dati['aggiungi'])) {
            unset($dati['aggiungi'], $dati['_token']);
            $dati['id_azienda'] = $utente->id_azienda;
            $dati['id_utente']  = $utente->id;
            $dati['totale']     = 0;
            $dati['attivo']     = 1;

            $id_lav = DB::table('lavorazioni')->insertGetId($dati);
            return Redirect::to('utente/dettaglio_lavorazione/'.$id_lav)->with('success', 'Lavorazione creata. Aggiungi le righe.');
        }

        if (isset($dati['elimina'])) {
            $id = (int) $dati['id'];
            DB::table('lavorazioni_righe')
                ->where('id_lavorazione', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->delete();
            DB::table('lavorazioni')
                ->where('id', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->delete();
            return Redirect::to('utente/lavorazioni')->with('success', 'Lavorazione eliminata');
        }

        $q = trim((string) $request->input('q', ''));

        $query = DB::table('lavorazioni')->where('id_azienda', $utente->id_azienda);

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('codice', 'like', '%'.$q.'%')
                  ->orWhere('descrizione', 'like', '%'.$q.'%');
            });
        }

        $lavorazioni = $query
            ->orderBy('descrizione')
            ->paginate(50)
            ->appends($request->query());

        $page = 'lavorazioni';
        return View::make('utente.lavorazioni', compact('utente', 'lavorazioni', 'page', 'q'));
    }

    public function dettaglio_lavorazione($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if (isset($dati['modifica_testata'])) {
            DB::table('lavorazioni')
                ->where('id', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->update([
                    'codice'      => $dati['codice'] ?? null,
                    'descrizione' => $dati['descrizione'] ?? '',
                    'attivo'      => isset($dati['attivo']) ? 1 : 0,
                ]);
            return Redirect::to('utente/dettaglio_lavorazione/'.$id)->with('success', 'Intestazione aggiornata');
        }

        if (isset($dati['aggiungi_riga'])) {
            $maxOrd = (int) DB::table('lavorazioni_righe')
                ->where('id_lavorazione', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->max('ordinamento');

            $riga = $this->normalizza_lavorazione_riga($dati);
            $riga['id_lavorazione'] = $id;
            $riga['id_azienda']     = $utente->id_azienda;
            $riga['id_utente']      = $utente->id;
            $riga['ordinamento']    = $maxOrd + 1;

            DB::table('lavorazioni_righe')->insert($riga);
            $this->ricalcola_totale_lavorazione((int) $id, (int) $utente->id_azienda);
            return Redirect::to('utente/dettaglio_lavorazione/'.$id)->with('success', 'Riga aggiunta');
        }

        if (isset($dati['modifica_riga'])) {
            $id_riga = (int) $dati['id_riga'];
            $riga = $this->normalizza_lavorazione_riga($dati);
            DB::table('lavorazioni_righe')
                ->where('id', $id_riga)
                ->where('id_lavorazione', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->update($riga);
            $this->ricalcola_totale_lavorazione((int) $id, (int) $utente->id_azienda);
            return Redirect::to('utente/dettaglio_lavorazione/'.$id)->with('success', 'Riga modificata');
        }

        if (isset($dati['elimina_riga'])) {
            DB::table('lavorazioni_righe')
                ->where('id', (int) $dati['id_riga'])
                ->where('id_lavorazione', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->delete();
            $this->ricalcola_totale_lavorazione((int) $id, (int) $utente->id_azienda);
            return Redirect::to('utente/dettaglio_lavorazione/'.$id)->with('success', 'Riga eliminata');
        }

        $lavorazione = DB::table('lavorazioni')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$lavorazione) {
            return Redirect::to('utente/lavorazioni')->with('error', 'Lavorazione non trovata');
        }

        $righe = DB::table('lavorazioni_righe')
            ->where('id_lavorazione', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('ordinamento')
            ->get();

        $page = 'lavorazioni';
        return View::make('utente.dettaglio_lavorazione', compact('utente', 'lavorazione', 'righe', 'page'));
    }

    public function importa_lavorazioni_csv(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Operazione bulk: estendi tempo e memoria per file grandi
        set_time_limit(0);
        ini_set('memory_limit', '512M');

        if (!$request->hasFile('csv')) {
            return Redirect::to('utente/lavorazioni')->with('error', 'Nessun file caricato');
        }

        $file = $request->file('csv');
        if (!$file->isValid()) {
            return Redirect::to('utente/lavorazioni')->with('error', 'File non valido');
        }

        $path = $file->getRealPath();
        if (($handle = fopen($path, 'r')) === false) {
            return Redirect::to('utente/lavorazioni')->with('error', 'Impossibile leggere il file');
        }

        // Auto-detect separator (; o ,)
        $firstLine = fgets($handle);
        if ($firstLine === false) {
            fclose($handle);
            return Redirect::to('utente/lavorazioni')->with('error', 'File vuoto');
        }
        if (substr($firstLine, 0, 3) === "\xEF\xBB\xBF") {
            $firstLine = substr($firstLine, 3);
        }
        $separator = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
        rewind($handle);

        $header = fgetcsv($handle, 0, $separator);
        if (!$header) {
            fclose($handle);
            return Redirect::to('utente/lavorazioni')->with('error', 'Header CSV mancante');
        }
        $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
        $header = array_map(function ($h) { return strtolower(trim($h)); }, $header);

        $valOrNull = function ($v) { return ($v === null || $v === '') ? null : $v; };

        $idAzienda = (int) $utente->id_azienda;
        $idUtente  = (int) $utente->id;
        $now = date('Y-m-d H:i:s');

        // 1 query sola: pre-fetch tutti i codici lavorazione gia' esistenti per questo tenant
        $codiciEsistenti = DB::table('lavorazioni')
            ->where('id_azienda', $idAzienda)
            ->whereNotNull('codice')
            ->pluck('id', 'codice')
            ->toArray();

        $lavorazioniNuove   = [];   // codice => descrizione
        $righeAccumulate    = [];   // raccolta in memoria, da inserire in batch
        $codiciSkippati     = [];

        while (($row = fgetcsv($handle, 0, $separator)) !== false) {
            if (count(array_filter($row, function ($v) { return $v !== '' && $v !== null; })) === 0) {
                continue;
            }
            $row = array_pad(array_slice($row, 0, count($header)), count($header), '');
            $r = array_combine($header, $row);

            $codice      = trim($r['codice_lavorazione'] ?? '');
            $descrizione = trim($r['descrizione_lavorazione'] ?? '');
            if ($codice === '' || $descrizione === '') continue;

            if (isset($codiciEsistenti[$codice])) {
                $codiciSkippati[$codice] = true;
                continue;
            }

            if (!isset($lavorazioniNuove[$codice])) {
                $lavorazioniNuove[$codice] = $descrizione;
            }

            $qta         = (float) str_replace(',', '.', $r['qta'] ?? 0);
            $minuti      = (float) str_replace(',', '.', $r['minuti'] ?? 0);
            $pu          = (float) str_replace(',', '.', $r['pu'] ?? 0);
            $aliquota    = (int)   ($r['aliquota'] ?? 22);
            $materiale   = (float) str_replace(',', '.', $r['materiale'] ?? 0);
            $attivitaVal = (float) str_replace(',', '.', $r['attivita'] ?? 1);
            $attivita    = $attivitaVal > 0 ? $attivitaVal : 1;

            if ($minuti > 0) {
                $pt = round($pu * $minuti / 60, 2);
            } else {
                $pt = round($pu * $attivita * $qta, 2);
            }
            $imposta = round($pt * $aliquota / 100, 2);

            $setupRaw = strtolower(trim($r['setup_tank'] ?? ''));
            $setup    = in_array($setupRaw, ['1', 'true', 'yes', 'si', 'sì'], true) ? 1 : 0;

            $righeAccumulate[] = [
                '_codice_lav'           => $codice,
                'id_azienda'            => $idAzienda,
                'ordinamento'           => (int) ($r['ordinamento'] ?? 0),
                'servizio'              => $valOrNull($r['servizio'] ?? null),
                'codice'                => $valOrNull($r['codice_riga'] ?? null),
                'setup_tank'            => $setup,
                'descrizione'           => $valOrNull($r['descrizione_riga'] ?? null),
                'attivita'              => $attivita,
                'qta'                   => $qta,
                'minuti'                => $minuti,
                'pu'                    => $pu,
                'aliquota'              => $aliquota,
                'imposta'               => $imposta,
                'imponibile'            => $pt,
                'pt'                    => $pt,
                'materiale'             => $materiale,
                'descrizione_materiale' => $valOrNull($r['descrizione_materiale'] ?? null),
                'id_utente'             => $idUtente,
                'created_at'            => $now,
                'updated_at'            => $now,
            ];
        }

        fclose($handle);

        $lavSkip     = count($codiciSkippati);
        $lavCreate   = 0;
        $righeCreate = 0;

        if (!empty($lavorazioniNuove)) {
            DB::transaction(function () use (&$lavCreate, &$righeCreate, $lavorazioniNuove, $righeAccumulate, $idAzienda, $idUtente, $now) {
                // 1. Batch insert testate
                $testateBatch = [];
                foreach ($lavorazioniNuove as $codice => $descrizione) {
                    $testateBatch[] = [
                        'id_azienda'  => $idAzienda,
                        'codice'      => $codice,
                        'descrizione' => $descrizione,
                        'totale'      => 0,
                        'attivo'      => 1,
                        'id_utente'   => $idUtente,
                        'created_at'  => $now,
                        'updated_at'  => $now,
                    ];
                }
                foreach (array_chunk($testateBatch, 500) as $chunk) {
                    DB::table('lavorazioni')->insert($chunk);
                }
                $lavCreate = count($testateBatch);

                // 2. Mapping codice -> id appena inseriti
                $codiciCreati = DB::table('lavorazioni')
                    ->where('id_azienda', $idAzienda)
                    ->whereIn('codice', array_keys($lavorazioniNuove))
                    ->pluck('id', 'codice')
                    ->toArray();

                // 3. Risolvi id_lavorazione e batch insert righe (chunked)
                $righeBatch = [];
                foreach ($righeAccumulate as $r) {
                    $codice = $r['_codice_lav'];
                    unset($r['_codice_lav']);
                    if (!isset($codiciCreati[$codice])) continue;
                    $r['id_lavorazione'] = $codiciCreati[$codice];
                    $righeBatch[] = $r;
                }
                foreach (array_chunk($righeBatch, 500) as $chunk) {
                    DB::table('lavorazioni_righe')->insert($chunk);
                    $righeCreate += count($chunk);
                }

                // 4. Ricalcola totali in singolo UPDATE con subquery
                if (!empty($codiciCreati)) {
                    $idsList = implode(',', array_map('intval', array_values($codiciCreati)));
                    DB::statement(
                        "UPDATE lavorazioni l
                         SET l.totale = COALESCE((
                             SELECT SUM(lr.imponibile) FROM lavorazioni_righe lr
                             WHERE lr.id_lavorazione = l.id AND lr.id_azienda = ?
                         ), 0)
                         WHERE l.id IN ({$idsList}) AND l.id_azienda = ?",
                        [$idAzienda, $idAzienda]
                    );
                }
            });
        }

        $msg = sprintf(
            'Import completato: %d lavorazioni nuove con %d righe. %d lavorazioni già presenti nel catalogo sono state ignorate.',
            $lavCreate, $righeCreate, $lavSkip
        );
        return Redirect::to('utente/lavorazioni')->with('success', $msg);
    }

    // ============================================================
    // DASHBOARD MANUTENTORE (responsive smartphone/tablet)
    // ============================================================

    private function _check_manutentore()
    {
        $this->is_loggato();
        $utente = session('utente');
        if (empty($utente->manutentore)) {
            // Non e' un manutentore: redirect alla home utente standard
            abort(403, 'Accesso riservato ai manutentori');
        }
        return $utente;
    }

    public function manutentore_dashboard(Request $request)
    {
        $utente = $this->_check_manutentore();

        // Interventi assegnati a me, attivi (non completati)
        $interventi = DB::table('interventi as i')
            ->leftJoin('clienti as c', 'c.id', '=', 'i.id_cliente')
            ->leftJoin('vagoni as v', 'v.id', '=', 'i.id_vagone')
            ->where('i.id_azienda', $utente->id_azienda)
            ->where('i.id_operatore_assegnato', $utente->id)
            ->whereIn('i.stato', ['in_corso'])
            ->select(
                'i.*',
                'c.ragione_sociale as cliente_ragione_sociale',
                'v.codice as vagone_codice',
                'v.tipo as vagone_tipo'
            )
            ->orderByRaw("CASE i.priorita WHEN 'alta' THEN 1 WHEN 'media' THEN 2 WHEN 'bassa' THEN 3 ELSE 4 END")
            ->orderByDesc('i.created_at')
            ->get();

        return View::make('manutentore.dashboard', compact('utente', 'interventi'));
    }

    public function manutentore_intervento($id, Request $request)
    {
        $utente = $this->_check_manutentore();

        $intervento = DB::table('interventi as i')
            ->leftJoin('clienti as c', 'c.id', '=', 'i.id_cliente')
            ->leftJoin('vagoni as v', 'v.id', '=', 'i.id_vagone')
            ->where('i.id', $id)
            ->where('i.id_azienda', $utente->id_azienda)
            ->where('i.id_operatore_assegnato', $utente->id)
            ->select(
                'i.*',
                'c.ragione_sociale as cliente_ragione_sociale',
                'c.indirizzo as cliente_indirizzo',
                'c.comune as cliente_comune',
                'v.codice as vagone_codice',
                'v.tipo as vagone_tipo'
            )
            ->first();

        if (!$intervento) {
            return Redirect::to('manutentore/dashboard')->with('error', 'Intervento non trovato o non assegnato a te');
        }

        $allegati = DB::table('interventi_allegati')
            ->where('id_intervento', $id)
            ->orderByDesc('created_at')
            ->get();

        $materiali = DB::table('interventi_materiali')
            ->where('id_intervento', $id)
            ->orderBy('id')
            ->get();

        $proposte = DB::table('interventi_lavorazioni_proposte')
            ->where('id_intervento', $id)
            ->orderBy('ordinamento')
            ->orderBy('id')
            ->get();

        return View::make('manutentore.intervento', compact('utente', 'intervento', 'allegati', 'materiali', 'proposte'));
    }

    public function manutentore_invia_report($id, Request $request)
    {
        $utente = $this->_check_manutentore();

        $intervento = DB::table('interventi')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->where('id_operatore_assegnato', $utente->id)
            ->first();

        if (!$intervento) {
            return Redirect::to('manutentore/dashboard')->with('error', 'Intervento non trovato');
        }
        if ((int) $intervento->step_corrente !== 3) {
            return Redirect::to('manutentore/intervento/'.$id)->with('error', 'L\'intervento non è in fase di report (step 3)');
        }

        $report = trim((string) $request->input('report_danni', ''));
        if ($report === '') {
            return Redirect::to('manutentore/intervento/'.$id)->with('error', 'Inserisci il report danni');
        }

        DB::table('interventi')->where('id', $id)->update(['report_danni' => $report]);

        // Upload foto/allegati (multi-file)
        if ($request->hasFile('allegati')) {
            $dir = public_path('uploads/interventi/'.$id);
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            foreach ($request->file('allegati') as $f) {
                if (!$f || !$f->isValid()) continue;
                // Cattura metadata PRIMA del move (dopo il move il file temp non esiste piu')
                $originalName = $f->getClientOriginalName();
                $mime = $f->getClientMimeType();
                $size = $f->getSize();
                $ext = $f->getClientOriginalExtension();
                $base = pathinfo($originalName, PATHINFO_FILENAME);
                $safe = Str::slug($base) ?: 'allegato';
                $fname = $safe.'_'.Str::random(8).'.'.$ext;
                $f->move($dir, $fname);
                DB::table('interventi_allegati')->insert([
                    'id_intervento' => $id,
                    'id_azienda'    => $utente->id_azienda,
                    'id_utente'     => $utente->id,
                    'filename'      => 'uploads/interventi/'.$id.'/'.$fname,
                    'original_name' => $originalName,
                    'mime'          => $mime,
                    'size'          => $size,
                    'created_at'    => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Materiali consumati (righe ripetute dal form)
        // Se id_articolo presente -> crea movimento di scarico mgmov + aggiorna giacenza articolo
        $materiali = $request->input('materiali', []);
        if (is_array($materiali)) {
            // Magazzino di default per scarichi: prima is_default=1, altrimenti il primo
            $mgDefault = DB::table('mg')->where('id_azienda', $utente->id_azienda)->where('is_default', 1)->first();
            if (!$mgDefault) {
                $mgDefault = DB::table('mg')->where('id_azienda', $utente->id_azienda)->first();
            }
            $id_mg_default = $mgDefault ? $mgDefault->id : null;

            foreach ($materiali as $m) {
                if (!is_array($m)) continue;
                $descr = trim($m['descrizione'] ?? '');
                if ($descr === '') continue;

                $id_articolo = !empty($m['id_articolo']) ? (int) $m['id_articolo'] : null;
                $qta = (float) str_replace(',', '.', $m['qta'] ?? 0);
                $um  = trim($m['um'] ?? 'PZ') ?: 'PZ';
                $id_mg = $id_articolo ? $id_mg_default : null;
                $id_mgmov = null;

                // Se l'articolo e' agganciato al magazzino: crea movimento di scarico
                if ($id_articolo && $id_mg && $qta > 0) {
                    $articolo = DB::table('articoli')->where('id', $id_articolo)->where('id_azienda', $utente->id_azienda)->first();
                    if ($articolo) {
                        $id_mgmov = DB::table('mgmov')->insertGetId([
                            'id_azienda'      => $utente->id_azienda,
                            'id_mg'           => $id_mg,
                            'id_articolo'     => $id_articolo,
                            'qta'             => $qta,
                            'sca'             => 1,
                            'car'             => 0,
                            'ret'             => 0,
                            'ini'             => 0,
                            'datamov'         => date('Y-m-d H:i:s'),
                            'causale'         => 'Scarico per Intervento #'.$id.' (manutentore)',
                            'id_utente'       => $utente->id,
                            'prezzo_unitario' => $articolo->prezzo ?? 0,
                            'imponibile'      => ($articolo->prezzo ?? 0) * $qta,
                        ]);
                        // Aggiorna giacenza articolo (decremento)
                        DB::table('articoli')->where('id', $id_articolo)->decrement('giacenza', $qta);
                    }
                }

                DB::table('interventi_materiali')->insert([
                    'id_intervento' => $id,
                    'id_azienda'    => $utente->id_azienda,
                    'id_utente'     => $utente->id,
                    'id_articolo'   => $id_articolo,
                    'id_mg'         => $id_mg,
                    'id_mgmov'      => $id_mgmov,
                    'codice'        => trim($m['codice'] ?? '') ?: null,
                    'descrizione'   => $descr,
                    'qta'           => $qta,
                    'um'            => $um,
                    'note'          => trim($m['note'] ?? '') ?: null,
                    'created_at'    => date('Y-m-d H:i:s'),
                ]);
            }
        }

        // Righe lavorazione proposte (servizio/codice/descrizione/qta/min/pu/...)
        $proposte = $request->input('lavorazioni_proposte', []);
        if (is_array($proposte)) {
            $ord = 0;
            foreach ($proposte as $r) {
                if (!is_array($r)) continue;
                $descr = trim($r['descrizione'] ?? '');
                $codice = trim($r['codice'] ?? '');
                if ($descr === '' && $codice === '') continue;
                $ord++;

                $qta       = (float) str_replace(',', '.', $r['qta'] ?? 0);
                $minuti    = (float) str_replace(',', '.', $r['minuti'] ?? 0);
                $pu        = (float) str_replace(',', '.', $r['pu'] ?? 0);
                $attivita  = (float) str_replace(',', '.', $r['attivita'] ?? 1);
                if ($attivita <= 0) $attivita = 1;
                $aliquota  = (int) ($r['aliquota'] ?? 22);
                $materiale = (float) str_replace(',', '.', $r['materiale'] ?? 0);

                if ($minuti > 0) {
                    $pt = round($pu * $minuti / 60, 2);
                } else {
                    $pt = round($pu * $attivita * $qta, 2);
                }
                $imposta = round($pt * $aliquota / 100, 2);

                DB::table('interventi_lavorazioni_proposte')->insert([
                    'id_intervento'              => $id,
                    'id_azienda'                 => $utente->id_azienda,
                    'id_utente'                  => $utente->id,
                    'ordinamento'                => $ord,
                    'servizio'                   => trim($r['servizio'] ?? '') ?: null,
                    'codice'                     => $codice ?: null,
                    'descrizione'                => $descr ?: null,
                    'setup_tank'                 => isset($r['setup_tank']) && (int) $r['setup_tank'] === 1 ? 1 : 0,
                    'attivita'                   => $attivita,
                    'qta'                        => $qta,
                    'minuti'                     => $minuti,
                    'pu'                         => $pu,
                    'aliquota'                   => $aliquota,
                    'imposta'                    => $imposta,
                    'imponibile'                 => $pt,
                    'pt'                         => $pt,
                    'materiale'                  => $materiale,
                    'descrizione_materiale'      => trim($r['descrizione_materiale'] ?? '') ?: null,
                    'id_lavorazione_origine'     => !empty($r['id_lavorazione_origine']) ? (int) $r['id_lavorazione_origine'] : null,
                    'id_lavorazione_riga_origine'=> !empty($r['id_lavorazione_riga_origine']) ? (int) $r['id_lavorazione_riga_origine'] : null,
                    'created_at'                 => date('Y-m-d H:i:s'),
                ]);
            }
        }

        $this->_intervento_avanza_step($id, (int) $utente->id_azienda, (int) $utente->id, 3, 'completato', 'Report danni inviato dal manutentore');

        return Redirect::to('manutentore/dashboard')->with('success', 'Report inviato. L\'intervento passa all\'ufficio per l\'emissione del preventivo.');
    }

    public function manutentore_storico(Request $request)
    {
        $utente = $this->_check_manutentore();

        $interventi = DB::table('interventi as i')
            ->leftJoin('clienti as c', 'c.id', '=', 'i.id_cliente')
            ->leftJoin('vagoni as v', 'v.id', '=', 'i.id_vagone')
            ->where('i.id_azienda', $utente->id_azienda)
            ->where('i.id_operatore_assegnato', $utente->id)
            ->where(function ($w) {
                $w->where('i.stato', 'completato')
                  ->orWhere('i.step_corrente', '>', 3); // gia' completato lo step 3
            })
            ->select(
                'i.*',
                'c.ragione_sociale as cliente_ragione_sociale',
                'v.codice as vagone_codice'
            )
            ->orderByDesc('i.step_3_completato_il')
            ->orderByDesc('i.created_at')
            ->paginate(30);

        return View::make('manutentore.storico', compact('utente', 'interventi'));
    }

    public function manutentore_search_articoli(Request $request)
    {
        $utente = $this->_check_manutentore();
        $q = trim((string) $request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json(['articoli' => []]);
        }
        $articoli = DB::table('articoli')
            ->where('id_azienda', $utente->id_azienda)
            ->where(function ($w) use ($q) {
                $w->where('codice_articolo', 'like', '%'.$q.'%')
                  ->orWhere('titolo', 'like', '%'.$q.'%')
                  ->orWhere('descrizione', 'like', '%'.$q.'%')
                  ->orWhere('barcode', 'like', '%'.$q.'%');
            })
            ->select('id', 'codice_articolo', 'titolo', 'descrizione', 'um', 'giacenza', 'barcode', 'prezzo')
            ->orderBy('titolo')
            ->limit(50)
            ->get();
        return response()->json(['articoli' => $articoli]);
    }

    public function manutentore_search_righe_catalogo(Request $request)
    {
        $utente = $this->_check_manutentore();
        $q = trim((string) $request->input('q', ''));
        if (mb_strlen($q) < 2) {
            return response()->json(['righe' => []]);
        }
        $righe = DB::table('lavorazioni_righe as lr')
            ->join('lavorazioni as l', 'l.id', '=', 'lr.id_lavorazione')
            ->where('lr.id_azienda', $utente->id_azienda)
            ->where('l.attivo', 1)
            ->where(function ($w) use ($q) {
                $w->where('lr.servizio', 'like', '%'.$q.'%')
                  ->orWhere('lr.codice', 'like', '%'.$q.'%')
                  ->orWhere('lr.descrizione', 'like', '%'.$q.'%')
                  ->orWhere('l.codice', 'like', '%'.$q.'%')
                  ->orWhere('l.descrizione', 'like', '%'.$q.'%');
            })
            ->select(
                'lr.id', 'lr.id_lavorazione', 'lr.servizio', 'lr.codice', 'lr.descrizione',
                'lr.attivita', 'lr.qta', 'lr.minuti', 'lr.pu', 'lr.aliquota',
                'lr.materiale', 'lr.descrizione_materiale', 'lr.setup_tank', 'lr.pt',
                'l.codice as lav_codice', 'l.descrizione as lav_descrizione'
            )
            ->orderBy('l.descrizione')
            ->orderBy('lr.ordinamento')
            ->limit(60)
            ->get();
        return response()->json(['righe' => $righe]);
    }

    public function manutentore_elimina_allegato($id_allegato, Request $request)
    {
        $utente = $this->_check_manutentore();

        $all = DB::table('interventi_allegati')
            ->where('id', $id_allegato)
            ->where('id_azienda', $utente->id_azienda)
            ->where('id_utente', $utente->id)
            ->first();

        if (!$all) {
            return redirect()->back()->with('error', 'Allegato non trovato');
        }

        $path = public_path($all->filename);
        if (file_exists($path)) {
            @unlink($path);
        }
        DB::table('interventi_allegati')->where('id', $id_allegato)->delete();

        return redirect()->back()->with('success', 'Allegato eliminato');
    }

    // ============================================================
    // INTERVENTI MANUTENZIONE - workflow 6-step
    // ============================================================

    public function interventi(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        $q = trim((string) $request->input('q', ''));
        $filtro_stato = $request->input('stato', '');

        $query = DB::table('interventi as i')
            ->leftJoin('clienti as c', 'c.id', '=', 'i.id_cliente')
            ->leftJoin('vagoni as v', 'v.id', '=', 'i.id_vagone')
            ->leftJoin('utenti as op', 'op.id', '=', 'i.id_operatore_assegnato')
            ->where('i.id_azienda', $utente->id_azienda)
            ->select(
                'i.*',
                'c.ragione_sociale as cliente_ragione_sociale',
                'v.codice as vagone_codice',
                'op.nome as operatore_nome',
                'op.cognome as operatore_cognome'
            );

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('c.ragione_sociale', 'like', '%'.$q.'%')
                  ->orWhere('v.codice', 'like', '%'.$q.'%')
                  ->orWhere('i.automezzo', 'like', '%'.$q.'%')
                  ->orWhere('i.reason_intake', 'like', '%'.$q.'%');
            });
        }
        if ($filtro_stato !== '') {
            $query->where('i.stato', $filtro_stato);
        }

        $interventi = $query
            ->orderByDesc('i.created_at')
            ->paginate(30)
            ->appends($request->query());

        $page = 'interventi';
        return View::make('utente.interventi.index', compact('utente', 'interventi', 'q', 'filtro_stato', 'page'));
    }

    public function interventi_nuovo(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if (isset($dati['salva'])) {
            $clean = function ($v) { return ($v === null || $v === '') ? null : $v; };
            $id_vagone = !empty($dati['id_vagone']) ? (int) $dati['id_vagone'] : null;

            $now = date('Y-m-d H:i:s');
            $id_int = DB::table('interventi')->insertGetId([
                'id_azienda'             => $utente->id_azienda,
                'id_utente'              => $utente->id,
                'id_cliente'             => !empty($dati['id_cliente']) ? (int) $dati['id_cliente'] : null,
                'id_vagone'              => $id_vagone,
                'automezzo'              => $clean($dati['automezzo'] ?? null),
                'data_apertura'          => $clean($dati['data_apertura'] ?? null) ?: date('Y-m-d'),
                'reason_intake'          => $clean($dati['reason_intake'] ?? null),
                'localita'               => $clean($dati['localita'] ?? null),
                'priorita'               => $dati['priorita'] ?? 'media',
                'note'                   => $clean($dati['note'] ?? null),
                'codice_cuu'             => $clean($dati['codice_cuu'] ?? null),
                'numero_ordine_cliente'  => $clean($dati['numero_ordine_cliente'] ?? null),
                'impianto'               => $clean($dati['impianto'] ?? null),
                'pdm_riferimento'        => $clean($dati['pdm_riferimento'] ?? null) ?: 'VPI',
                'odl_numero'             => $clean($dati['odl_numero'] ?? null),
                'step_corrente'          => 1,
                'stato'                  => 'in_corso',
                'created_at'             => $now,
                'updated_at'             => $now,
            ]);

            DB::table('interventi_log')->insert([
                'id_intervento' => $id_int,
                'id_azienda'    => $utente->id_azienda,
                'id_utente'     => $utente->id,
                'step'          => 1,
                'azione'        => 'aperto',
                'note'          => 'Intervento aperto',
                'created_at'    => $now,
            ]);

            return Redirect::to('utente/interventi/'.$id_int)->with('success', 'Intervento aperto. Completa lo Step 1 quando hai inserito tutti i dati.');
        }

        $clienti = DB::table('clienti')->where('id_azienda', $utente->id_azienda)->orderBy('ragione_sociale')->get();
        $vagoni  = DB::table('vagoni')->where('id_azienda', $utente->id_azienda)->where('attivo', 1)->orderBy('codice')->get();
        $page = 'interventi';
        return View::make('utente.interventi.nuovo', compact('utente', 'clienti', 'vagoni', 'page'));
    }

    public function interventi_dettaglio($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        $intervento = DB::table('interventi as i')
            ->leftJoin('clienti as c', 'c.id', '=', 'i.id_cliente')
            ->leftJoin('vagoni as v', 'v.id', '=', 'i.id_vagone')
            ->leftJoin('utenti as op', 'op.id', '=', 'i.id_operatore_assegnato')
            ->where('i.id', $id)
            ->where('i.id_azienda', $utente->id_azienda)
            ->select(
                'i.*',
                'c.ragione_sociale as cliente_ragione_sociale',
                'c.indirizzo as cliente_indirizzo',
                'c.comune as cliente_comune',
                'c.email as cliente_email',
                'c.pec as cliente_pec',
                'v.codice as vagone_codice',
                'v.tipo as vagone_tipo',
                'op.nome as operatore_nome',
                'op.cognome as operatore_cognome'
            )
            ->first();

        if (!$intervento) {
            return Redirect::to('utente/interventi')->with('error', 'Intervento non trovato');
        }

        // Firma email del utente loggato (auto-popola la modale invio preventivo)
        $firma_utente = DB::table('utenti')->where('id', $utente->id)->value('firma_email') ?: '';

        // Stato firma cliente sul preventivo collegato (se presente)
        $firma_preventivo = null;
        $segnalazioni_cliente = collect();
        if ($intervento->id_dotes_preventivo) {
            $firma_preventivo = DB::table('dotes')
                ->where('id', $intervento->id_dotes_preventivo)
                ->select('firma_token', 'firmato_il', 'firmato_da_nome', 'firma_telefono', 'firma_ip', 'firma_otp_inviato_il')
                ->first();
            $segnalazioni_cliente = DB::table('dotes_segnalazioni')
                ->where('id_dotes', $intervento->id_dotes_preventivo)
                ->orderByDesc('created_at')
                ->get();
        }

        $log = DB::table('interventi_log')
            ->where('id_intervento', $id)
            ->orderByDesc('created_at')
            ->get();

        // Dipendenti dell'azienda flaggati come manutentori (id_tipologia=3 + manutentore=1)
        $operatori = DB::table('utenti')
            ->where('id_azienda', $utente->id_azienda)
            ->where('id_tipologia', 3)
            ->where('manutentore', 1)
            ->orderBy('cognome')
            ->orderBy('nome')
            ->get();

        // Dati prodotti dal manutentore: allegati, materiali (con info magazzino), righe lavorazione proposte
        $allegati = DB::table('interventi_allegati')
            ->where('id_intervento', $id)
            ->orderByDesc('created_at')
            ->get();

        $materiali = DB::table('interventi_materiali as im')
            ->leftJoin('articoli as a', 'a.id', '=', 'im.id_articolo')
            ->leftJoin('mg', 'mg.id', '=', 'im.id_mg')
            ->where('im.id_intervento', $id)
            ->select(
                'im.*',
                'a.titolo as articolo_titolo',
                'a.giacenza as articolo_giacenza_attuale',
                'mg.descrizione as magazzino_descrizione'
            )
            ->orderBy('im.id')
            ->get();

        $proposte = DB::table('interventi_lavorazioni_proposte')
            ->where('id_intervento', $id)
            ->orderBy('ordinamento')
            ->orderBy('id')
            ->get();

        $page = 'interventi';
        return View::make('utente.interventi.dettaglio', compact('utente', 'intervento', 'log', 'operatori', 'allegati', 'materiali', 'proposte', 'firma_utente', 'firma_preventivo', 'segnalazioni_cliente', 'page'));
    }

    public function interventi_completa_step($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        $intervento = DB::table('interventi')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$intervento) {
            return redirect()->back()->with('error', 'Intervento non trovato');
        }

        $stepCorrente = (int) $intervento->step_corrente;
        if ($stepCorrente < 1 || $stepCorrente > 6) {
            return redirect()->back()->with('error', 'Step corrente non valido');
        }

        $now = date('Y-m-d H:i:s');
        $colStepCompletato = 'step_'.$stepCorrente.'_completato_il';
        $nuovoStep = $stepCorrente < 6 ? $stepCorrente + 1 : 6;
        $nuovoStato = $stepCorrente === 6 ? 'completato' : 'in_corso';

        DB::table('interventi')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->update([
                $colStepCompletato => $now,
                'step_corrente'    => $nuovoStep,
                'stato'            => $nuovoStato,
                'updated_at'       => $now,
            ]);

        DB::table('interventi_log')->insert([
            'id_intervento' => $id,
            'id_azienda'    => $utente->id_azienda,
            'id_utente'     => $utente->id,
            'step'          => $stepCorrente,
            'azione'        => 'completato',
            'note'          => $dati['note'] ?? null,
            'created_at'    => $now,
        ]);

        return Redirect::to('utente/interventi/'.$id)->with('success', 'Step '.$stepCorrente.' completato. Passa allo step '.$nuovoStep.'.');
    }

    /**
     * Genera PDF Invoice Receipt nel layout VTG (preventivo o fattura).
     * Stesso layout per entrambi: cambia solo il titolo.
     */
    private function _genera_pdf_invoice_receipt($intervento, $dotes, $dorig, $az, string $tipo = 'preventivo'): string
    {
        $titolo = $tipo === 'fattura' ? 'FATTURA' : 'INVOICE RECEIPT';

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_left' => 12, 'margin_right' => 12,
            'margin_top' => 10,  'margin_bottom' => 12,
            'default_font' => 'helvetica',
            'default_font_size' => 9,
        ]);

        $esc = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
        $eur = function ($v) { return '&euro; '.number_format((float) $v, 2, ',', '.'); };
        $num = function ($v, int $d = 2) { return number_format((float) $v, $d, ',', '.'); };

        // === HEADER: dati destinatario (cliente VTG) ===
        $html  = '<style>
            body { font-family: helvetica, sans-serif; color: #000; font-size: 9pt; }
            .title { font-size: 12pt; font-weight: bold; margin: 0 0 6pt; }
            .small { font-size: 8pt; }
            table { border-collapse: collapse; width: 100%; }
            table.intestazione td { vertical-align: top; padding-bottom: 4pt; }
            table.parties td { vertical-align: top; padding: 2pt 6pt 2pt 0; }
            .lbl { font-weight: bold; }
            .meta-tbl td { border: 1px solid #000; padding: 4pt 6pt; }
            .meta-tbl .meta-lbl { background: #f0f0f0; font-weight: bold; width: 30%; }
            .righe { margin-top: 6pt; }
            .righe th { background: #e9e9e9; border: 1px solid #000; padding: 4pt 3pt; font-size: 8pt; font-weight: bold; text-align: center; vertical-align: middle; }
            .righe td { border: 1px solid #000; padding: 4pt 3pt; font-size: 8pt; vertical-align: top; }
            .righe td.r { text-align: right; }
            .righe td.c { text-align: center; }
            .sub { background: #d9d9d9; font-weight: bold; }
            .tot { background: #ffffff; font-weight: bold; font-size: 11pt; }
            .footer-firma td { border-top: 1px solid #000; padding: 4pt 6pt; font-weight: bold; }
        </style>';

        $html .= '<div class="title">'.$titolo.'</div>';
        $html .= '<div>'.$esc($dotes->ragione_sociale).'</div>';
        $html .= '<div>'.$esc($dotes->indirizzo).'</div>';
        $html .= '<div>'.$esc($dotes->cap).' - '.strtoupper($esc($dotes->comune)).'</div>';
        $html .= '<div>'.$esc($dotes->nazione ?: 'Italy').'</div>';
        $html .= '<br>';

        // === Contracting Party + Billing Address ===
        $html .= '<table class="parties"><tr>
            <td width="50%">
                <div class="lbl">Contracting Party</div>
                <table><tr><td width="80">Department</td><td>'.$esc($intervento->op_nome ?? '').'</td></tr>
                <tr><td>Name</td><td>'.$esc($dotes->nominativo ?? '').'</td></tr>
                <tr><td>Tel</td><td></td></tr>
                <tr><td>Fax</td><td></td></tr>
                <tr><td>Email</td><td>'.$esc($dotes->pec ?: '').'</td></tr>
                <tr><td>Date</td><td>'.date('d/m/Y', strtotime($dotes->data_doc)).'</td></tr>
                </table>
            </td>
            <td width="50%">
                <div class="lbl">Billing Address</div>
                <div>'.$esc($az->ragione_sociale ?? '').'</div>
                <div>'.$esc($az->indirizzo ?? '').'</div>
                <div>'.$esc($az->cap ?? '').' &ndash; '.$esc($az->comune ?? '').'</div>
                <div>'.$esc($az->nazione ?? 'Italy').'</div>
            </td>
        </tr></table>';

        // === Meta intervento ===
        $html .= '<table class="meta-tbl" style="margin-top: 8pt;">';
        $html .= '<tr><td class="meta-lbl">Wagon Number</td><td>'.$esc($intervento->vagone_codice ?? $intervento->automezzo ?? $dotes->automezzo ?? '').'</td><td class="meta-lbl">Detail Segment</td><td></td></tr>';
        $html .= '<tr><td class="meta-lbl">Order number (if present)</td><td colspan="3">'.$esc($intervento->numero_ordine_cliente ?? '').'</td></tr>';
        $html .= '<tr><td class="meta-lbl">Place of Service</td><td colspan="3">'.$esc(strtoupper($intervento->impianto ?? $intervento->localita ?? '')).'</td></tr>';
        $html .= '<tr><td class="meta-lbl">Service performance date</td><td colspan="3">'.($intervento->data_apertura ? date('d/m/Y', strtotime($intervento->data_apertura)) : '').'</td></tr>';
        $html .= '<tr><td class="meta-lbl">Reason for intake</td><td colspan="3">'.$esc($intervento->codice_cuu ?? '').'</td></tr>';
        $html .= '</table>';

        // === Tabella righe raggruppate per Service type ===
        $html .= '<table class="righe">
            <thead><tr>
                <th width="4%">Pos.<br>Nr</th>
                <th width="6%">Service<br>type</th>
                <th width="10%">Number of<br>specification of<br>services or VTG<br>material number</th>
                <th width="9%"></th>
                <th width="33%">Description element 2 or material</th>
                <th width="6%">Activity</th>
                <th width="6%">Amount</th>
                <th width="13%">Unit price of<br>number of<br>specification of<br>services or<br>material in EUR</th>
                <th width="13%">Total (=<br>quantity x unit<br>price)</th>
            </tr></thead>
            <tbody>';

        // Raggruppa righe per servizio
        $byService = [];
        foreach ($dorig as $r) {
            $key = $r->servizio ?: '—';
            if (!isset($byService[$key])) $byService[$key] = [];
            $byService[$key][] = $r;
        }

        $posGlobal = 0;
        foreach ($byService as $service => $righeGruppo) {
            $subTot = 0;
            foreach ($righeGruppo as $r) {
                $posGlobal++;
                $setupTag = !empty($r->setup_tank) ? 'Setup Task' : '';
                $subTot += (float) $r->prezzo_totale;
                $html .= '<tr>
                    <td class="c">'.$posGlobal.'</td>
                    <td class="c">'.$esc($r->servizio).'</td>
                    <td class="c">'.$esc($r->cd_ar).'</td>
                    <td class="c">'.$setupTag.'</td>
                    <td>'.$esc($r->descrizione).'</td>
                    <td class="r">'.$num($r->attivita ?? 1, 2).'</td>
                    <td class="r">'.$num($r->qta, 2).'</td>
                    <td class="r">'.$num($r->prezzo_unitario, 2).'</td>
                    <td class="r">'.$eur($r->prezzo_totale).'</td>
                </tr>';
            }
            $html .= '<tr class="sub"><td colspan="8" class="c">Totale Servizio '.$esc($service).'</td><td class="r">'.$eur($subTot).'</td></tr>';
        }

        // Totale finale
        $html .= '<tr class="tot"><td colspan="8" class="r">Totale</td><td class="r">'.$eur($dotes->totale).'</td></tr>';
        $html .= '</tbody></table>';

        // === Footer firma ===
        $html .= '<br><table class="footer-firma" style="margin-top: 30pt;"><tr>
            <td width="50%">'.$esc($az->ragione_sociale ?? '').'<br><span style="font-weight:normal;">Workshop</span></td>
            <td width="50%" style="text-align:right;">'.date('d/m/Y').'<br><span style="font-weight:normal;">Date &nbsp;&nbsp;&nbsp;&nbsp; Signature</span></td>
        </tr></table>';

        $mpdf->WriteHTML($html);

        $tmpFile = sys_get_temp_dir().'/'.$tipo.'_'.$dotes->numero_doc.'_'.uniqid().'.pdf';
        $mpdf->Output($tmpFile, 'F');
        return $tmpFile;
    }

    private function _intervento_avanza_step(int $id, int $id_azienda, int $id_utente, int $stepCorrente, string $azione = 'completato', ?string $note = null, ?int $forzaStep = null): void
    {
        $now = date('Y-m-d H:i:s');
        $colStepCompletato = 'step_'.$stepCorrente.'_completato_il';
        $nuovoStep = $forzaStep ?? ($stepCorrente < 6 ? $stepCorrente + 1 : 6);
        $nuovoStato = ($stepCorrente === 6 && $azione === 'completato' && $forzaStep === null) ? 'completato' : 'in_corso';

        $upd = [
            'step_corrente' => $nuovoStep,
            'stato'         => $nuovoStato,
            'updated_at'    => $now,
        ];
        if ($azione === 'completato' || $azione === 'accettato') {
            $upd[$colStepCompletato] = $now;
        }

        DB::table('interventi')->where('id', $id)->where('id_azienda', $id_azienda)->update($upd);
        DB::table('interventi_log')->insert([
            'id_intervento' => $id,
            'id_azienda'    => $id_azienda,
            'id_utente'     => $id_utente,
            'step'          => $stepCorrente,
            'azione'        => $azione,
            'note'          => $note,
            'created_at'    => $now,
        ]);
    }

    public function interventi_step_2_assegna($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $i = DB::table('interventi')->where('id', $id)->where('id_azienda', $utente->id_azienda)->first();
        if (!$i) return redirect()->back()->with('error', 'Intervento non trovato');
        if ((int) $i->step_corrente !== 2) return redirect()->back()->with('error', 'Non sei sullo step 2');

        $id_operatore = (int) $request->input('id_operatore_assegnato', 0);
        if ($id_operatore <= 0) return redirect()->back()->with('error', 'Seleziona un manutentore');

        DB::table('interventi')->where('id', $id)->update(['id_operatore_assegnato' => $id_operatore]);
        $this->_intervento_avanza_step($id, (int) $utente->id_azienda, (int) $utente->id, 2, 'completato', 'Operatore assegnato (id #'.$id_operatore.')');
        return Redirect::to('utente/interventi/'.$id)->with('success', 'Manutentore assegnato. Passa allo step 3.');
    }

    public function interventi_step_3_report($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $i = DB::table('interventi')->where('id', $id)->where('id_azienda', $utente->id_azienda)->first();
        if (!$i) return redirect()->back()->with('error', 'Intervento non trovato');
        if ((int) $i->step_corrente !== 3) return redirect()->back()->with('error', 'Non sei sullo step 3');

        $report = trim((string) $request->input('report_danni', ''));
        if ($report === '') return redirect()->back()->with('error', 'Inserisci il report danni prima di completare');

        DB::table('interventi')->where('id', $id)->update(['report_danni' => $report]);
        $this->_intervento_avanza_step($id, (int) $utente->id_azienda, (int) $utente->id, 3, 'completato', 'Report danni inviato');
        return Redirect::to('utente/interventi/'.$id)->with('success', 'Report danni salvato. Passa allo step 4.');
    }

    public function interventi_step_4_emetti_preventivo($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $i = DB::table('interventi')->where('id', $id)->where('id_azienda', $utente->id_azienda)->first();
        if (!$i) return redirect()->back()->with('error', 'Intervento non trovato');
        if ((int) $i->step_corrente !== 4) return redirect()->back()->with('error', 'Non sei sullo step 4');

        // Snapshot cliente
        $cliente = DB::table('clienti')->where('id', $i->id_cliente)->first();

        // Numero progressivo PRE per anno + azienda
        $maxNum = (int) DB::table('dotes')
            ->where('id_azienda', $utente->id_azienda)
            ->where('cd_do', 'PRE')
            ->whereYear('data_doc', date('Y'))
            ->max(DB::raw('CAST(numero_doc AS UNSIGNED)'));
        $numero_doc = $maxNum + 1;

        $id_dotes = DB::table('dotes')->insertGetId([
            'cd_do'          => 'PRE',
            'numero_doc'     => $numero_doc,
            'data_doc'       => date('Y-m-d'),
            'id_cliente'     => $i->id_cliente,
            'id_azienda'     => $utente->id_azienda,
            'id_utente'      => $utente->id,
            'id_vagone'      => $i->id_vagone,
            'automezzo'      => $i->automezzo,
            'localita'       => $i->localita,
            'reason_intake'  => $i->reason_intake,
            'note_operatore' => $i->report_danni ?: $i->note,
            'ragione_sociale'=> $cliente->ragione_sociale ?? null,
            'partita_iva'    => $cliente->partita_iva ?? null,
            'cf'             => $cliente->codice_fiscale ?? null,
            'indirizzo'      => $cliente->indirizzo ?? null,
            'cap'            => $cliente->cap ?? null,
            'comune'         => $cliente->comune ?? null,
            'provincia'      => $cliente->provincia ?? null,
            'pec'            => $cliente->pec ?? null,
            'sdi'            => $cliente->codice_sdi ?? null,
            'imponibile'     => 0,
            'imposta'        => 0,
            'totale'         => 0,
            'da_registrare'  => 0,
        ]);

        DB::table('interventi')->where('id', $id)->update(['id_dotes_preventivo' => $id_dotes]);

        // Copia le righe proposte dal manutentore come righe dorig del preventivo
        $proposte = DB::table('interventi_lavorazioni_proposte')
            ->where('id_intervento', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('ordinamento')
            ->orderBy('id')
            ->get();

        $nRiga = 0;
        $sumImp = 0; $sumTax = 0; $sumTot = 0;
        foreach ($proposte as $p) {
            $nRiga++;
            $isOrario = ((float) $p->minuti) > 0;
            $attivita = $p->attivita > 0 ? (float) $p->attivita : 1;
            $qtaEff = $isOrario
                ? round(((float) $p->minuti) / 60, 3)
                : round(((float) $p->qta) * $attivita, 3);
            $imp = (float) $p->imponibile;
            $tax = (float) $p->imposta;
            $tot = $imp + $tax;
            $sumImp += $imp; $sumTax += $tax; $sumTot += $tot;

            DB::table('dorig')->insert([
                'id_azienda'                  => $utente->id_azienda,
                'id_utente'                   => $utente->id,
                'id_cliente'                  => $i->id_cliente,
                'id_dotes'                    => $id_dotes,
                'id_testata'                  => $id_dotes,
                'cd_do'                       => 'PRE',
                'numero_doc'                  => $numero_doc,
                'data_doc'                    => date('Y-m-d'),
                'cd_ar'                       => $p->codice,
                'n_riga'                      => $nRiga,
                'descrizione'                 => $p->descrizione,
                'qta'                         => $qtaEff,
                'um'                          => $isOrario ? 'H' : 'PZ',
                'pu'                          => (float) $p->pu,
                'pt'                          => (float) $p->pt,
                'prezzo_unitario'             => (float) $p->pu,
                'prezzo_totale'               => (float) $p->pt,
                'prezzo_totale_iva'           => $tot,
                'iva'                         => (int) $p->aliquota,
                'imponibile'                  => $imp,
                'imposta'                     => $tax,
                'totale'                      => $tot,
                'servizio'                    => $p->servizio,
                'setup_tank'                  => (int) $p->setup_tank,
                'attivita'                    => $attivita,
                'minuti'                      => (float) $p->minuti,
                'materiale'                   => (float) $p->materiale,
                'descrizione_materiale'       => $p->descrizione_materiale,
                'id_vagone'                   => $i->id_vagone,
                'id_lavorazione_origine'      => $p->id_lavorazione_origine,
                'id_lavorazione_riga_origine' => $p->id_lavorazione_riga_origine,
            ]);
        }

        // Aggiorna aggregati testata
        if ($nRiga > 0) {
            DB::table('dotes')->where('id', $id_dotes)->update([
                'imponibile' => $sumImp,
                'imposta'    => $sumTax,
                'totale'     => $sumTot,
            ]);
        }

        $note = 'Preventivo PRE #'.$numero_doc.' creato (dotes id '.$id_dotes.')';
        if ($nRiga > 0) $note .= ' con '.$nRiga.' righe da proposte manutentore';
        $this->_intervento_avanza_step($id, (int) $utente->id_azienda, (int) $utente->id, 4, 'completato', $note);
        return Redirect::to('utente/dettaglio_documento/'.$id_dotes)->with('success', 'Preventivo creato'.($nRiga > 0 ? ' con '.$nRiga.' righe pre-popolate dal report manutentore' : ', aggiungi le righe lavorazione').'.');
    }

    public function interventi_invia_release_email($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        $i = DB::table('interventi as i')
            ->leftJoin('clienti as c', 'c.id', '=', 'i.id_cliente')
            ->where('i.id', $id)
            ->where('i.id_azienda', $utente->id_azienda)
            ->select('i.*', 'c.ragione_sociale as cliente_ragione_sociale', 'c.email as cliente_email')
            ->first();
        if (!$i) return redirect()->back()->with('error', 'Intervento non trovato');

        $destinatari = trim((string) $request->input('destinatari', ''));
        $cc          = trim((string) $request->input('cc', ''));
        $oggetto     = trim((string) $request->input('oggetto', '')) ?: ('Release to Service - Vagone '.($i->automezzo ?: $id));
        $messaggio   = trim((string) $request->input('messaggio', ''));
        $firma       = trim((string) $request->input('firma', ''));
        $salvaFirma  = $request->input('salva_firma', 0);

        if ($destinatari === '') return redirect()->back()->with('error', 'Inserisci almeno un destinatario');

        if ($salvaFirma && $firma !== '') {
            DB::table('utenti')->where('id', $utente->id)->update(['firma_email' => $firma]);
        }

        $az = DB::table('aziende')->where('id', $utente->id_azienda)->first();

        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtps.aruba.it';
            $mail->SMTPAuth = true;
            $mail->Username = 'noreply@gestya.it';
            $mail->Password = 'jwZFTChzg8gp41?c';
            $mail->SMTPSecure = 'ssl';
            $mail->CharSet = 'utf-8';
            $mail->Port = 465;
            $mail->setFrom('noreply@gestya.it', $az->ragione_sociale ?? 'Gestya');
            if (!empty($utente->email)) $mail->addReplyTo($utente->email);

            foreach (preg_split('/[,;]+/', $destinatari) as $em) {
                $em = trim($em);
                if ($em && filter_var($em, FILTER_VALIDATE_EMAIL)) $mail->addAddress($em);
            }
            if ($cc !== '') {
                foreach (preg_split('/[,;]+/', $cc) as $em) {
                    $em = trim($em);
                    if ($em && filter_var($em, FILTER_VALIDATE_EMAIL)) $mail->addCC($em);
                }
            }

            $bodyHtml = '<div style="font-family: Arial, sans-serif; font-size: 14px; color: #222; line-height: 1.5;">' .
                nl2br(htmlspecialchars($messaggio, ENT_QUOTES, 'UTF-8')) .
                '<p style="font-size:12px;color:#666;margin-top:18px;">In allegato il documento <strong>Release to Service</strong> conforme VPI-EMG 01 Annex 15-1.</p>' .
                (!empty($firma) ? '<hr style="margin: 24px 0; border:0; border-top: 1px solid #e5e7eb;"><div style="color:#555;">'.nl2br(htmlspecialchars($firma, ENT_QUOTES, 'UTF-8')).'</div>' : '') .
                '</div>';

            $mail->isHTML(true);
            $mail->Subject = $oggetto;
            $mail->Body = $bodyHtml;
            $mail->AltBody = strip_tags($messaggio."\n\n".$firma);

            // Allega PDF Release to Service
            try {
                $tmpPath = $this->_genera_release_pdf_to_file((int) $id, (int) $utente->id_azienda);
                if ($tmpPath && file_exists($tmpPath)) {
                    $mail->addAttachment($tmpPath, 'release_to_service_'.($i->automezzo ?: $id).'.pdf');
                }
            } catch (\Exception $e) {
                \Log::warning('PDF Release allegato fallito: '.$e->getMessage());
            }

            $sent = $mail->send();
            if (!$sent) return redirect()->back()->with('error', 'SMTP non ha consegnato la mail. ErrorInfo: '.$mail->ErrorInfo);

            DB::table('interventi')->where('id', $id)->update(['release_inviato_il' => date('Y-m-d H:i:s')]);
            DB::table('interventi_log')->insert([
                'id_intervento' => $id,
                'id_azienda'    => $utente->id_azienda,
                'id_utente'     => $utente->id,
                'step'          => $i->step_corrente,
                'azione'        => 'release_inviato',
                'note'          => 'Release to Service inviato a: '.$destinatari.($cc ? ' (CC: '.$cc.')' : ''),
                'created_at'    => date('Y-m-d H:i:s'),
            ]);

            return Redirect::to('utente/interventi/'.$id)->with('success', 'Release to Service inviato via email a: '.$destinatari);
        } catch (\Exception $e) {
            \Log::error('Errore invio Release intervento #'.$id, ['msg' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Errore invio email: '.$e->getMessage());
        }
    }

    public function interventi_invia_preventivo_email($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        $i = DB::table('interventi as i')
            ->leftJoin('clienti as c', 'c.id', '=', 'i.id_cliente')
            ->where('i.id', $id)
            ->where('i.id_azienda', $utente->id_azienda)
            ->select('i.*', 'c.ragione_sociale as cliente_ragione_sociale', 'c.email as cliente_email')
            ->first();

        if (!$i) return redirect()->back()->with('error', 'Intervento non trovato');
        if (!$i->id_dotes_preventivo) return redirect()->back()->with('error', 'Nessun preventivo collegato all\'intervento');

        $destinatari = trim((string) $request->input('destinatari', ''));
        $cc          = trim((string) $request->input('cc', ''));
        $oggetto     = trim((string) $request->input('oggetto', '')) ?: 'Preventivo n. '.$i->id_dotes_preventivo;
        $messaggio   = trim((string) $request->input('messaggio', ''));
        $firma       = trim((string) $request->input('firma', ''));
        $salvaFirma  = $request->input('salva_firma', 0);

        if ($destinatari === '') {
            return redirect()->back()->with('error', 'Inserisci almeno un destinatario');
        }

        // Salva firma sul profilo utente se richiesto
        if ($salvaFirma && $firma !== '') {
            DB::table('utenti')->where('id', $utente->id)->update(['firma_email' => $firma]);
        }

        $azienda = DB::table('aziende')->where('id', $utente->id_azienda)->first();

        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtps.aruba.it';
            $mail->SMTPAuth = true;
            $mail->Username = 'noreply@gestya.it';
            $mail->Password = 'jwZFTChzg8gp41?c';
            $mail->SMTPSecure = 'ssl';
            $mail->CharSet = 'utf-8';
            $mail->Port = 465;
            $fromName = $azienda->ragione_sociale ?? 'Gestya';
            $mail->setFrom('noreply@gestya.it', $fromName);
            if (!empty($utente->email)) {
                $mail->addReplyTo($utente->email);
            }

            $destArr = preg_split('/[,;]+/', $destinatari);
            foreach ($destArr as $em) {
                $em = trim($em);
                if ($em && filter_var($em, FILTER_VALIDATE_EMAIL)) $mail->addAddress($em);
            }
            if ($cc !== '') {
                foreach (preg_split('/[,;]+/', $cc) as $em) {
                    $em = trim($em);
                    if ($em && filter_var($em, FILTER_VALIDATE_EMAIL)) $mail->addCC($em);
                }
            }

            // Genera (o riusa) il firma_token per il link pubblico al preventivo
            $firmaToken = DB::table('dotes')->where('id', $i->id_dotes_preventivo)->value('firma_token');
            if (empty($firmaToken)) {
                $firmaToken = Str::random(48);
                DB::table('dotes')->where('id', $i->id_dotes_preventivo)->update(['firma_token' => $firmaToken]);
            }
            // Flag firma richiesta dal toggle del form (default true)
            $firmaRichiesta = $request->input('firma_richiesta', '1') === '1';
            $linkFirma = url('/firma/'.$firmaToken).($firmaRichiesta ? '' : '?view=1');

            $ctaLabel = $firmaRichiesta ? 'Apri e Firma il Preventivo' : 'Apri il Preventivo';
            $ctaSub   = $firmaRichiesta
                ? 'Cliccando il pulsante potrai visualizzare il preventivo e firmarlo digitalmente via SMS OTP.'
                : 'Cliccando il pulsante potrai visualizzare il preventivo in dettaglio.';

            $bodyHtml = '<div style="font-family: Arial, sans-serif; font-size: 14px; color: #222; line-height: 1.5;">' .
                nl2br(htmlspecialchars($messaggio, ENT_QUOTES, 'UTF-8')) .
                '<p style="margin-top: 18px;"><a href="'.htmlspecialchars($linkFirma, ENT_QUOTES, 'UTF-8').'" style="display:inline-block;padding:12px 22px;background:#0f766e;color:#fff;text-decoration:none;border-radius:6px;font-weight:600;">'.$ctaLabel.'</a></p>' .
                '<p style="font-size:12px;color:#666;">'.$ctaSub.'</p>' .
                (!empty($firma) ? '<hr style="margin: 24px 0; border:0; border-top: 1px solid #e5e7eb;"><div style="color:#555;">'.nl2br(htmlspecialchars($firma, ENT_QUOTES, 'UTF-8')).'</div>' : '') .
                '</div>';

            $mail->isHTML(true);
            $mail->Subject = $oggetto;
            $mail->Body = $bodyHtml;
            $mail->AltBody = strip_tags($messaggio."\n\n".$linkFirma."\n\n".$firma);

            // Allega PDF Invoice Receipt VTG (formato cliente)
            try {
                $dotes = DB::table('dotes')->where('id', $i->id_dotes_preventivo)->first();
                $dorig = DB::table('dorig')->where('id_dotes', $i->id_dotes_preventivo)->orderBy('n_riga')->get();
                $az = DB::table('aziende')->where('id', $utente->id_azienda)->first();
                // Carica intervento completo per il PDF
                $iFull = DB::table('interventi as i')
                    ->leftJoin('vagoni as v', 'v.id', '=', 'i.id_vagone')
                    ->leftJoin('utenti as op', 'op.id', '=', 'i.id_operatore_assegnato')
                    ->where('i.id', $id)
                    ->select('i.*', 'v.codice as vagone_codice', 'op.nome as op_nome')
                    ->first();
                if ($dotes && $iFull) {
                    $tmpFile = $this->_genera_pdf_invoice_receipt($iFull, $dotes, $dorig, $az, 'preventivo');
                    $mail->addAttachment($tmpFile, 'preventivo_'.$dotes->numero_doc.'.pdf');
                }
            } catch (\Exception $e) {
                // Se la generazione fallisce l'email parte comunque col solo link
                \Log::warning('PDF preventivo allegato fallito: '.$e->getMessage());
            }
            // Vecchio template generico rimosso, sostituito da _genera_pdf_invoice_receipt sopra
            if (false) {
                $az = null; $dotes = null; $dorig = []; $tmpFile = '';

                if ($dotes) {
                    $mpdf = new \Mpdf\Mpdf([
                        'mode' => 'utf-8',
                        'format' => 'A4',
                        'margin_left' => 12,
                        'margin_right' => 12,
                        'margin_top' => 14,
                        'margin_bottom' => 14,
                        'default_font' => 'helvetica',
                        'default_font_size' => 9,
                    ]);

                    $esc = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
                    $eur = function ($v) { return '&euro; '.number_format((float) $v, 2, ',', '.'); };

                    $html = '<style>
                        body { font-family: helvetica, sans-serif; color: #222; }
                        h1 { color: #0f766e; font-size: 22px; margin: 0; }
                        .header-tbl { width: 100%; margin-bottom: 14px; }
                        .header-tbl td { vertical-align: top; padding: 0; }
                        .box { border: 1px solid #e5e7eb; border-radius: 4px; padding: 10px; }
                        .label { color: #6b7280; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
                        .strong { font-weight: bold; }
                        table.righe { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 9px; }
                        table.righe th { background: #0f766e; color: #fff; padding: 6px 4px; text-align: left; font-size: 8px; }
                        table.righe td { padding: 5px 4px; border-bottom: 1px solid #e5e7eb; }
                        table.righe tr:nth-child(even) td { background: #f9fafb; }
                        .totali { width: 280px; margin-left: auto; margin-top: 12px; }
                        .totali td { padding: 4px 8px; }
                        .totali .grand { border-top: 2px solid #0f766e; font-size: 14px; padding-top: 8px; color: #0f766e; font-weight: bold; }
                        .footer { margin-top: 24px; font-size: 8px; color: #888; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 10px; }
                        .firma-box { border: 1px dashed #d1d5db; padding: 24px; margin-top: 30px; min-height: 80px; }
                    </style>';

                    // Header con mittente + destinatario + dati doc
                    $html .= '<h1>Preventivo</h1>';
                    $html .= '<p style="margin:4px 0 14px;color:#6b7280;">N. <strong>'.$esc($dotes->numero_doc).'/'.date('Y', strtotime($dotes->data_doc)).'</strong> del '.date('d/m/Y', strtotime($dotes->data_doc)).'</p>';

                    $html .= '<table class="header-tbl"><tr>
                        <td width="48%"><div class="box">
                            <div class="label">Mittente</div>
                            <div class="strong">'.$esc($az->ragione_sociale ?? '').'</div>
                            <div>'.$esc($az->indirizzo ?? '').'<br>'.$esc($az->cap ?? '').' '.$esc($az->comune ?? '').' ('.$esc($az->provincia ?? '').')</div>
                            '.(!empty($az->partita_iva) ? '<div style="color:#666;font-size:8px;">P.IVA '.$esc($az->partita_iva).'</div>' : '').'
                            '.(!empty($az->email_ricezione_fatture) ? '<div style="color:#666;font-size:8px;">'.$esc($az->email_ricezione_fatture).'</div>' : '').'
                        </div></td>
                        <td width="4%"></td>
                        <td width="48%"><div class="box">
                            <div class="label">Destinatario</div>
                            <div class="strong">'.$esc($dotes->ragione_sociale).'</div>
                            <div>'.$esc($dotes->indirizzo).'<br>'.$esc($dotes->cap).' '.$esc($dotes->comune).' ('.$esc($dotes->provincia).')</div>
                            '.(!empty($dotes->partita_iva) ? '<div style="color:#666;font-size:8px;">P.IVA '.$esc($dotes->partita_iva).'</div>' : '').'
                            '.(!empty($dotes->pec) ? '<div style="color:#666;font-size:8px;">'.$esc($dotes->pec).'</div>' : '').'
                        </div></td>
                    </tr></table>';

                    // Dati intervento (vagone, localita, motivo)
                    if (!empty($dotes->automezzo) || !empty($dotes->localita) || !empty($dotes->reason_intake)) {
                        $html .= '<div class="box" style="margin-bottom:14px;">';
                        if (!empty($dotes->automezzo)) $html .= '<div><span class="label">Vagone:</span> <strong>'.$esc($dotes->automezzo).'</strong></div>';
                        if (!empty($dotes->localita))  $html .= '<div><span class="label">Localita:</span> '.$esc($dotes->localita).'</div>';
                        if (!empty($dotes->reason_intake)) $html .= '<div><span class="label">Motivo rientro:</span> '.$esc($dotes->reason_intake).'</div>';
                        $html .= '</div>';
                    }

                    // Tabella righe
                    $html .= '<table class="righe">
                        <thead><tr>
                            <th width="4%">#</th>
                            <th width="7%">Serv.</th>
                            <th width="10%">Codice</th>
                            <th width="40%">Descrizione</th>
                            <th width="8%" style="text-align:right">Qta</th>
                            <th width="10%" style="text-align:right">P.U.</th>
                            <th width="6%" style="text-align:right">IVA%</th>
                            <th width="15%" style="text-align:right">Totale</th>
                        </tr></thead><tbody>';
                    $n = 0;
                    foreach ($dorig as $r) {
                        $n++;
                        $desc = $esc($r->descrizione ?: $r->nome_prodotto);
                        if (!empty($r->descrizione_materiale)) {
                            $desc .= '<br><span style="color:#666;font-size:8px;">Materiale: '.$esc($r->descrizione_materiale);
                            if (!empty($r->materiale) && $r->materiale > 0) $desc .= ' ('.$eur($r->materiale).')';
                            $desc .= '</span>';
                        }
                        if (!empty($r->minuti) && $r->minuti > 0) {
                            $desc .= '<br><span style="color:#666;font-size:8px;">Tempo: '.rtrim(rtrim(number_format($r->minuti, 2, ',', ''), '0'), ',').' min</span>';
                        }
                        $html .= '<tr>
                            <td>'.$n.'</td>
                            <td>'.$esc($r->servizio).'</td>
                            <td>'.$esc($r->cd_ar).'</td>
                            <td>'.$desc.'</td>
                            <td style="text-align:right">'.rtrim(rtrim(number_format($r->qta, 3, ',', '.'), '0'), ',').' '.$esc($r->um).'</td>
                            <td style="text-align:right">'.$eur($r->prezzo_unitario).'</td>
                            <td style="text-align:right">'.$r->iva.'</td>
                            <td style="text-align:right;font-weight:bold">'.$eur($r->prezzo_totale).'</td>
                        </tr>';
                    }
                    $html .= '</tbody></table>';

                    // Totali
                    $html .= '<table class="totali">
                        <tr><td>Imponibile</td><td style="text-align:right">'.$eur($dotes->imponibile).'</td></tr>
                        <tr><td>Imposta</td><td style="text-align:right">'.$eur($dotes->imposta).'</td></tr>
                        <tr class="grand"><td>TOTALE</td><td style="text-align:right">'.$eur($dotes->totale).'</td></tr>
                    </table>';

                    // Firma
                    if (!empty($dotes->firmato_il)) {
                        $html .= '<div class="firma-box" style="background:#ecfdf5;border-color:#6ee7b7;">
                            <div class="strong" style="color:#065f46;">FIRMATO DIGITALMENTE</div>
                            <div>'.$esc($dotes->firmato_da_nome).' il '.date('d/m/Y H:i', strtotime($dotes->firmato_il)).'</div>
                            <div style="color:#666;font-size:8px;">Via SMS OTP — Numero '.$esc($dotes->firma_telefono).' — IP '.$esc($dotes->firma_ip).'</div>
                        </div>';
                    } else {
                        $html .= '<div class="firma-box">
                            <div class="label">Firma per accettazione del cliente</div>
                            <div style="height:40px;"></div>
                            <div style="border-top:1px solid #999;width:60%;padding-top:4px;color:#666;font-size:8px;">Data e firma</div>
                        </div>';
                    }

                    $html .= '<div class="footer">Documento generato il '.date('d/m/Y H:i').' tramite Gestya</div>';

                    $mpdf->WriteHTML($html);
                    $mpdf->Output($tmpFile, 'F');
                    $mail->addAttachment($tmpFile, 'preventivo_'.$dotes->numero_doc.'.pdf');
                }
            } // chiude if(false) dead-code legacy

            // Debug verbose: cattura output SMTP completo
            $smtpDebugOutput = '';
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = function($str, $level) use (&$smtpDebugOutput) {
                $smtpDebugOutput .= '[L'.$level.'] '.$str."\n";
            };

            $sent = $mail->send();

            \Log::info('Invio preventivo intervento #'.$id, [
                'destinatari' => $destinatari,
                'cc'          => $cc,
                'sent'        => $sent,
                'error_info'  => $mail->ErrorInfo,
                'smtp_debug'  => $smtpDebugOutput,
            ]);

            if (!$sent) {
                return Redirect::to('utente/interventi/'.$id)->with('error', 'SMTP non ha consegnato la mail. ErrorInfo: '.$mail->ErrorInfo);
            }

            DB::table('interventi_log')->insert([
                'id_intervento' => $id,
                'id_azienda'    => $utente->id_azienda,
                'id_utente'     => $utente->id,
                'step'          => 5,
                'azione'        => 'preventivo_inviato',
                'note'          => 'Email inviata a: '.$destinatari.($cc ? ' (CC: '.$cc.')' : '').($firmaRichiesta ? ' [firma OTP richiesta]' : ' [sola visualizzazione]'),
                'created_at'    => date('Y-m-d H:i:s'),
            ]);

            DB::table('dotes_invii_email')->insert([
                'id_dotes'             => $i->id_dotes_preventivo,
                'id_azienda'           => $utente->id_azienda,
                'tipo'                 => 'preventivo',
                'destinatari'          => substr($destinatari, 0, 1000),
                'cc'                   => $cc ? substr($cc, 0, 1000) : null,
                'firma_richiesta'      => $firmaRichiesta ? 1 : 0,
                'inviato_da_id_utente' => $utente->id,
            ]);

            return Redirect::to('utente/interventi/'.$id)->with('success', 'Preventivo inviato via email a: '.$destinatari.($firmaRichiesta ? '' : ' (senza richiesta di firma)'));
        } catch (\Exception $e) {
            \Log::error('Errore invio preventivo intervento #'.$id, [
                'msg'   => $e->getMessage(),
                'trace' => substr($e->getTraceAsString(), 0, 2000),
            ]);
            return redirect()->back()->with('error', 'Errore invio email: '.$e->getMessage());
        }
    }

    public function interventi_step_4_emetti_certificato($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $i = DB::table('interventi as i')
            ->leftJoin('clienti as c', 'c.id', '=', 'i.id_cliente')
            ->where('i.id', $id)
            ->where('i.id_azienda', $utente->id_azienda)
            ->select('i.*', 'c.ragione_sociale as cliente_ragione_sociale')
            ->first();
        if (!$i) return redirect()->back()->with('error', 'Intervento non trovato');
        if ($i->id_dotes_certificato) return redirect()->back()->with('error', 'Certificato gia\' creato');

        $cliente = DB::table('clienti')->where('id', $i->id_cliente)->first();

        // Numero progressivo CERT per anno + azienda
        $maxNum = (int) DB::table('dotes')
            ->where('id_azienda', $utente->id_azienda)
            ->where('cd_do', 'CERT')
            ->whereYear('data_doc', date('Y'))
            ->max(DB::raw('CAST(numero_doc AS UNSIGNED)'));
        $numero_doc = $maxNum + 1;

        $id_cert = DB::table('dotes')->insertGetId([
            'cd_do'          => 'CERT',
            'numero_doc'     => $numero_doc,
            'data_doc'       => date('Y-m-d'),
            'id_cliente'     => $i->id_cliente,
            'id_azienda'     => $utente->id_azienda,
            'id_utente'      => $utente->id,
            'id_vagone'      => $i->id_vagone,
            'automezzo'      => $i->automezzo,
            'localita'       => $i->localita,
            'reason_intake'  => $i->reason_intake,
            'note_operatore' => $i->report_danni ?: $i->note,
            'ragione_sociale'=> $cliente->ragione_sociale ?? null,
            'partita_iva'    => $cliente->partita_iva ?? null,
            'cf'             => $cliente->codice_fiscale ?? null,
            'indirizzo'      => $cliente->indirizzo ?? null,
            'cap'            => $cliente->cap ?? null,
            'comune'         => $cliente->comune ?? null,
            'provincia'      => $cliente->provincia ?? null,
            'imponibile'     => 0,
            'imposta'        => 0,
            'totale'         => 0,
            'da_registrare'  => 0,
        ]);

        // Copia righe dal preventivo se esiste (le lavorazioni eseguite finiscono anche sul certificato)
        if ($i->id_dotes_preventivo) {
            $righePrev = DB::table('dorig')->where('id_dotes', $i->id_dotes_preventivo)->orderBy('n_riga')->get();
            $nRiga = 0;
            foreach ($righePrev as $r) {
                $nRiga++;
                DB::table('dorig')->insert([
                    'id_azienda'            => $utente->id_azienda,
                    'id_utente'             => $utente->id,
                    'id_cliente'            => $i->id_cliente,
                    'id_dotes'              => $id_cert,
                    'id_testata'            => $id_cert,
                    'cd_do'                 => 'CERT',
                    'numero_doc'            => $numero_doc,
                    'data_doc'              => date('Y-m-d'),
                    'cd_ar'                 => $r->cd_ar,
                    'n_riga'                => $nRiga,
                    'descrizione'           => $r->descrizione,
                    'qta'                   => $r->qta,
                    'um'                    => $r->um,
                    'pu'                    => $r->pu,
                    'pt'                    => $r->pt,
                    'prezzo_unitario'       => $r->prezzo_unitario,
                    'prezzo_totale'         => $r->prezzo_totale,
                    'iva'                   => $r->iva,
                    'imponibile'            => $r->imponibile,
                    'imposta'               => $r->imposta,
                    'totale'                => $r->totale,
                    'servizio'              => $r->servizio,
                    'setup_tank'            => $r->setup_tank,
                    'attivita'              => $r->attivita,
                    'minuti'                => $r->minuti,
                    'materiale'             => $r->materiale,
                    'descrizione_materiale' => $r->descrizione_materiale,
                    'id_vagone'             => $r->id_vagone,
                ]);
            }
        }

        DB::table('interventi')->where('id', $id)->update(['id_dotes_certificato' => $id_cert]);
        DB::table('interventi_log')->insert([
            'id_intervento' => $id,
            'id_azienda'    => $utente->id_azienda,
            'id_utente'     => $utente->id,
            'step'          => 4,
            'azione'        => 'certificato_creato',
            'note'          => 'Certificato di Manutenzione CERT #'.$numero_doc.' creato (dotes id '.$id_cert.')',
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        return Redirect::to('utente/interventi/'.$id)->with('success', 'Certificato di Manutenzione creato. Puoi scaricarlo in PDF dallo step 4.');
    }

    /**
     * Genera PDF Release to Service e lo salva su file temporaneo. Ritorna il path.
     */
    private function _genera_release_pdf_to_file(int $id, int $id_azienda): ?string
    {
        $i = DB::table('interventi as i')
            ->leftJoin('clienti as c', 'c.id', '=', 'i.id_cliente')
            ->leftJoin('vagoni as v', 'v.id', '=', 'i.id_vagone')
            ->leftJoin('utenti as op', 'op.id', '=', 'i.id_operatore_assegnato')
            ->leftJoin('utenti as rm', 'rm.id', '=', 'i.id_responsabile_manutenzione')
            ->where('i.id', $id)
            ->where('i.id_azienda', $id_azienda)
            ->select(
                'i.*',
                'c.ragione_sociale as cliente_rs',
                'v.codice as vagone_codice', 'v.tipo as vagone_tipo', 'v.numero_uic as vagone_uic',
                'v.peso_a_vuoto_kg', 'v.portata_massima_kg', 'v.lunghezza_metri',
                'v.data_immatricolazione', 'v.data_ultima_revisione_generale',
                'op.nome as op_nome', 'op.cognome as op_cognome',
                'rm.nome as rm_nome', 'rm.cognome as rm_cognome'
            )
            ->first();
        if (!$i) return null;

        $az  = DB::table('aziende')->where('id', $id_azienda)->first();
        $esc = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
        $dt  = function ($v) { return $v ? date('d-m-Y', strtotime($v)) : ''; };
        $rmLabel = $i->rm_nome ? trim($i->rm_nome.' '.$i->rm_cognome) : '';
        $tare = $i->peso_a_vuoto_kg ? number_format($i->peso_a_vuoto_kg, 0, '', '').' kg' : '';

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8', 'format' => 'A4',
            'margin_left' => 10, 'margin_right' => 10,
            'margin_top' => 10, 'margin_bottom' => 12,
            'default_font' => 'helvetica', 'default_font_size' => 8,
        ]);

        $html = '<style>
            body { font-family: helvetica, sans-serif; color: #000; font-size: 8pt; }
            h1 { font-size: 14pt; font-weight: bold; margin: 0 0 6pt; }
            table { border-collapse: collapse; width: 100%; }
            .box td { border: 1px solid #000; padding: 3pt 5pt; vertical-align: top; font-size: 8pt; }
            .lbl { background: #e0e0e0; font-weight: bold; font-size: 7pt; }
            .azhdr { text-align: right; font-size: 8pt; }
            .azhdr strong { font-size: 10pt; }
            .small { font-size: 7pt; }
        </style>';
        $html .= '<table><tr>
            <td width="60%"><h1>Release to service</h1></td>
            <td width="40%" class="azhdr">
                <strong>'.$esc($az->ragione_sociale ?? '').'</strong><br>
                <span class="small">'.$esc($az->indirizzo ?? '').'</span><br>
                <span class="small">'.$esc($az->cap ?? '').' '.$esc($az->comune ?? '').' ('.$esc($az->provincia ?? '').')</span><br>
                <span class="small">P.IVA: '.$esc($az->partita_iva ?? '').'</span>
            </td>
        </tr></table>';
        $html .= '<table class="box" style="margin-top:6pt;"><tr>
            <td width="28%" class="lbl">Wagon number:</td>
            <td width="28%" class="lbl">Order number:</td>
            <td width="22%" class="lbl">Workshop short code:</td>
            <td width="22%" rowspan="2"></td>
        </tr><tr>
            <td>'.$esc($i->vagone_codice ?: $i->automezzo).'</td>
            <td>'.$esc($i->odl_numero ?: '').'/'.($i->data_apertura ? date('Y', strtotime($i->data_apertura)) : date('Y')).'</td>
            <td>'.$esc($az->ragione_sociale ?? '').'</td>
        </tr></table>';
        $html .= '<table class="box"><tr>
            <td width="28%" class="lbl">Keeper/ECM:</td>
            <td width="28%" class="lbl">Customer order number:</td>
            <td width="44%" class="lbl">Created on:</td>
        </tr><tr>
            <td>'.$esc($i->cliente_rs ?: '').'</td>
            <td>'.$esc($i->numero_ordine_cliente ?: '').'</td>
            <td>'.$dt($i->data_apertura).'</td>
        </tr></table>';
        $html .= '<table class="box" style="margin-top:4pt;"><tr>
            <td width="28%" class="lbl">Arrival date:</td>
            <td width="28%" class="lbl">Departure date:</td>
            <td width="44%" class="lbl">Maintenance level performed:</td>
        </tr><tr>
            <td>'.$dt($i->data_apertura).'</td>
            <td>'.date('d-m-Y').'</td>
            <td>'.$esc($i->codice_cuu ?: '').'</td>
        </tr></table>';
        $html .= '<table class="box" style="margin-top:6pt;"><tr>
            <td width="20%" class="lbl">Wagon number old:</td>
            <td width="20%" class="lbl">Tare weight:</td>
            <td width="12%" class="lbl">Type:</td>
            <td width="12%" class="lbl">Cycle:</td>
            <td width="12%" class="lbl">Date:</td>
            <td width="12%" class="lbl">Extension:</td>
        </tr><tr>
            <td>'.$esc($i->matricola_carro_old ?: '-').'</td>
            <td>'.$esc($tare).'</td>
            <td>'.$esc($i->vagone_tipo ?: '').'</td>
            <td class="small">6Irev/ari-dts</td>
            <td>'.$dt($i->data_ultima_revisione_generale ?? null).'</td>
            <td></td>
        </tr></table>';
        $html .= '<table class="box" style="margin-top:6pt;"><tr>
            <td width="50%"><span class="lbl">Bogie type:</span> '.$esc($i->vagone_tipo ?: '').'</td>
            <td width="50%"><span class="lbl">Brake type:</span> KE-GP-A</td>
        </tr></table>';
        $otherInfo = '';
        if (!empty($i->report_danni)) $otherInfo .= $esc($i->report_danni)."\n\n";
        if (!empty($i->note))         $otherInfo .= $esc($i->note);
        $html .= '<table class="box" style="margin-top:6pt;"><tr>
            <td class="lbl">Other information (in accordance with the specification of the ECM):</td>
        </tr><tr>
            <td style="height: 80pt; white-space: pre-wrap;">'.$otherInfo.'</td>
        </tr></table>';
        $html .= '<div class="small" style="margin-top:8pt; text-align:justify;">';
        $html .= 'The above entries correspond to the condition of the wagon and the inscriptions found on the wagon. All work was performed properly. The wagon is safe for service. We hereby certify that this wagon is leaving our workshop according to the applicable laws and ordinances, the regulations of the keeper and the RID (if applicable).';
        $html .= '</div>';
        $html .= '<table class="box" style="margin-top:10pt;"><tr>
            <td width="30%" class="lbl">Email:</td>
            <td width="20%" class="lbl">Telephone no.:</td>
            <td width="15%" class="lbl">Fax no.:</td>
            <td width="35%" class="lbl">Name (of the responsible employee):</td>
        </tr><tr>
            <td>'.$esc($az->email_ricezione_fatture ?? '').'</td>
            <td>Tel.: 081 5127059</td>
            <td>-</td>
            <td>ECM '.$esc($az->ragione_sociale ?? '').': '.$esc($rmLabel ?: '').'</td>
        </tr></table>';
        $html .= '<div class="small" style="margin-top:14pt; color:#666;">VPI-EMG 01 Annex 15-1 | Version: 01/12/2024<br>Translation: 10/12/2024</div>';

        $mpdf->WriteHTML($html);
        $tmpFile = sys_get_temp_dir().'/release_'.$id.'_'.uniqid().'.pdf';
        $mpdf->Output($tmpFile, 'F');
        return $tmpFile;
    }

    /**
     * Release to service - endpoint download.
     */
    public function interventi_release_to_service_pdf($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $path = $this->_genera_release_pdf_to_file((int) $id, (int) $utente->id_azienda);
        if (!$path) abort(404, 'Intervento non trovato');
        return response()->download($path, 'release_to_service_'.$id.'.pdf')->deleteFileAfterSend(true);
    }

    /**
     * Versione vecchia in-line (riferimento, non chiamata).
     */
    public function interventi_release_to_service_pdf_OLD($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $i = DB::table('interventi as i')
            ->leftJoin('clienti as c', 'c.id', '=', 'i.id_cliente')
            ->leftJoin('vagoni as v', 'v.id', '=', 'i.id_vagone')
            ->leftJoin('utenti as op', 'op.id', '=', 'i.id_operatore_assegnato')
            ->leftJoin('utenti as rm', 'rm.id', '=', 'i.id_responsabile_manutenzione')
            ->where('i.id', $id)
            ->where('i.id_azienda', $utente->id_azienda)
            ->select(
                'i.*',
                'c.ragione_sociale as cliente_rs',
                'v.codice as vagone_codice', 'v.tipo as vagone_tipo', 'v.numero_uic as vagone_uic',
                'v.peso_a_vuoto_kg', 'v.portata_massima_kg', 'v.lunghezza_metri',
                'v.data_immatricolazione', 'v.data_ultima_revisione_generale',
                'op.nome as op_nome', 'op.cognome as op_cognome',
                'rm.nome as rm_nome', 'rm.cognome as rm_cognome'
            )
            ->first();
        if (!$i) abort(404, 'Intervento non trovato');

        $az = DB::table('aziende')->where('id', $utente->id_azienda)->first();
        $esc = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
        $dt = function ($v) { return $v ? date('d-m-Y', strtotime($v)) : ''; };
        $rmLabel = $i->rm_nome ? trim($i->rm_nome.' '.$i->rm_cognome) : '';

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8', 'format' => 'A4',
            'margin_left' => 10, 'margin_right' => 10,
            'margin_top' => 10, 'margin_bottom' => 12,
            'default_font' => 'helvetica', 'default_font_size' => 8,
        ]);

        $html = '<style>
            body { font-family: helvetica, sans-serif; color: #000; font-size: 8pt; }
            h1 { font-size: 14pt; font-weight: bold; margin: 0 0 6pt; }
            table { border-collapse: collapse; width: 100%; }
            .box td { border: 1px solid #000; padding: 3pt 5pt; vertical-align: top; font-size: 8pt; }
            .lbl { background: #e0e0e0; font-weight: bold; font-size: 7pt; }
            .sub { background: #f5f5f5; font-weight: bold; }
            .azhdr { text-align: right; font-size: 8pt; }
            .azhdr strong { font-size: 10pt; }
            .small { font-size: 7pt; }
            .check-cell { font-family: DejaVuSans, sans-serif; }
        </style>';

        // Header titolo + azienda
        $html .= '<table><tr>
            <td width="60%"><h1>Release to service</h1></td>
            <td width="40%" class="azhdr">
                <strong>'.$esc($az->ragione_sociale ?? '').'</strong><br>
                <span class="small">'.$esc($az->indirizzo ?? '').'</span><br>
                <span class="small">'.$esc($az->cap ?? '').' '.$esc($az->comune ?? '').' ('.$esc($az->provincia ?? '').')</span><br>
                <span class="small">P.IVA: '.$esc($az->partita_iva ?? '').'</span>
            </td>
        </tr></table>';

        // Riga 1: Wagon number / Order number / Workshop / Logo azienda
        $html .= '<table class="box" style="margin-top:6pt;"><tr>
            <td width="28%" class="lbl">Wagon number:</td>
            <td width="28%" class="lbl">Order number:</td>
            <td width="22%" class="lbl">Workshop short code:</td>
            <td width="22%" rowspan="2"></td>
        </tr><tr>
            <td>'.$esc($i->vagone_codice ?: $i->automezzo).'</td>
            <td>'.$esc($i->odl_numero ?: '').'/'.($i->data_apertura ? date('Y', strtotime($i->data_apertura)) : date('Y')).'</td>
            <td>'.$esc($az->ragione_sociale ?? '').'</td>
        </tr></table>';

        // Riga 2: Keeper/ECM / Customer order / Created on
        $html .= '<table class="box"><tr>
            <td width="28%" class="lbl">Keeper/ECM:</td>
            <td width="28%" class="lbl">Customer order number:</td>
            <td width="44%" class="lbl">Created on:</td>
        </tr><tr>
            <td>'.$esc($i->cliente_rs ?: '').'</td>
            <td>'.$esc($i->numero_ordine_cliente ?: '').'</td>
            <td>'.$dt($i->data_apertura).'</td>
        </tr></table>';

        // Date arrivo/partenza/livello
        $html .= '<table class="box" style="margin-top:4pt;"><tr>
            <td width="28%" class="lbl">Arrival date:</td>
            <td width="28%" class="lbl">Departure date:</td>
            <td width="44%" class="lbl">Maintenance level performed:</td>
        </tr><tr>
            <td>'.$dt($i->data_apertura).'</td>
            <td>'.date('d-m-Y').'</td>
            <td>'.$esc($i->codice_cuu ?: '').'</td>
        </tr></table>';

        // Tare weight / Type / Cycle / Date / Extension
        $tare = $i->peso_a_vuoto_kg ? number_format($i->peso_a_vuoto_kg, 0, '', '').' kg' : '';
        $html .= '<table class="box" style="margin-top:6pt;"><tr>
            <td width="20%" class="lbl">Wagon number old:</td>
            <td width="20%" class="lbl">Tare weight:</td>
            <td width="12%" class="lbl">Type:</td>
            <td width="12%" class="lbl">Cycle:</td>
            <td width="12%" class="lbl">Date:</td>
            <td width="12%" class="lbl">Extension:</td>
        </tr><tr>
            <td>'.$esc($i->matricola_carro_old ?: '-').'</td>
            <td>'.$esc($tare).'</td>
            <td>'.$esc($i->vagone_tipo ?: '').'</td>
            <td class="small">6Irev/ari-dts</td>
            <td>'.$dt($i->vagone_data_ultima_revisione_generale ?? null).'</td>
            <td></td>
        </tr></table>';

        // Bogie type / inscriptions
        $html .= '<table class="box" style="margin-top:6pt;"><tr>
            <td width="50%"><span class="lbl">Bogie type:</span> '.$esc($i->vagone_tipo ?: '').'</td>
            <td width="50%"><span class="lbl">Brake type:</span> KE-GP-A</td>
        </tr></table>';

        // Other information (note + report danni)
        $otherInfo = '';
        if (!empty($i->report_danni)) $otherInfo .= $esc($i->report_danni)."\n\n";
        if (!empty($i->note))         $otherInfo .= $esc($i->note);
        $html .= '<table class="box" style="margin-top:6pt;"><tr>
            <td class="lbl">Other information (in accordance with the specification of the ECM):</td>
        </tr><tr>
            <td style="height: 80pt; white-space: pre-wrap;">'.$otherInfo.'</td>
        </tr></table>';

        // Dichiarazione standard
        $html .= '<div class="small" style="margin-top:8pt; text-align:justify;">';
        $html .= 'The above entries correspond to the condition of the wagon and the inscriptions found on the wagon. All work was performed properly. The wagon is safe for service. We hereby certify that this wagon is leaving our workshop according to the applicable laws and ordinances, the regulations of the keeper and the RID (if applicable).';
        $html .= '</div>';

        // Firma responsabile ECM
        $html .= '<table class="box" style="margin-top:10pt;"><tr>
            <td width="30%" class="lbl">Email:</td>
            <td width="20%" class="lbl">Telephone no.:</td>
            <td width="15%" class="lbl">Fax no.:</td>
            <td width="35%" class="lbl">Name (of the responsible employee):</td>
        </tr><tr>
            <td>'.$esc($az->email_ricezione_fatture ?? '').'</td>
            <td>Tel.: 081 5127059</td>
            <td>-</td>
            <td>ECM '.$esc($az->ragione_sociale ?? '').': '.$esc($rmLabel ?: '').'</td>
        </tr></table>';

        // Footer documento
        $html .= '<div class="small" style="margin-top:14pt; color:#666;">';
        $html .= 'VPI-EMG 01 Annex 15-1 | Version: 01/12/2024<br>Translation: 10/12/2024';
        $html .= '</div>';

        $mpdf->WriteHTML($html);
        return $mpdf->Output('release_to_service_'.($i->vagone_codice ?: $id).'.pdf', 'D');
    }

    /**
     * Mobile repair report - layout VPI-EMG 10 Annex 2
     * Report compilato dal manutentore sul posto: lavori eseguiti, componenti sostituiti,
     * Release confirmation con doppio check 'Repair' + 'Release to service'.
     */
    public function interventi_mobile_repair_report_pdf($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $i = DB::table('interventi as i')
            ->leftJoin('clienti as c', 'c.id', '=', 'i.id_cliente')
            ->leftJoin('vagoni as v', 'v.id', '=', 'i.id_vagone')
            ->leftJoin('utenti as rm', 'rm.id', '=', 'i.id_responsabile_manutenzione')
            ->where('i.id', $id)
            ->where('i.id_azienda', $utente->id_azienda)
            ->select(
                'i.*',
                'c.ragione_sociale as cliente_rs',
                'v.codice as vagone_codice', 'v.tipo as vagone_tipo', 'v.peso_a_vuoto_kg',
                'rm.nome as rm_nome', 'rm.cognome as rm_cognome'
            )
            ->first();
        if (!$i) abort(404, 'Intervento non trovato');

        $az = DB::table('aziende')->where('id', $utente->id_azienda)->first();
        $materiali = DB::table('interventi_materiali')->where('id_intervento', $id)->get();

        $esc = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
        $dt  = function ($v) { return $v ? date('d-m-Y', strtotime($v)) : ''; };
        $rmLabel = $i->rm_nome ? trim($i->rm_nome.' '.$i->rm_cognome) : '';
        $tare = $i->peso_a_vuoto_kg ? number_format($i->peso_a_vuoto_kg, 0, '', '').' kg' : '';

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8', 'format' => 'A4',
            'margin_left' => 10, 'margin_right' => 10,
            'margin_top' => 10, 'margin_bottom' => 12,
            'default_font' => 'helvetica', 'default_font_size' => 8,
        ]);

        $html = '<style>
            body { font-family: helvetica, sans-serif; color: #000; font-size: 8pt; }
            h1 { font-size: 14pt; font-weight: bold; margin: 0 0 6pt; }
            table { border-collapse: collapse; width: 100%; }
            .box td { border: 1px solid #000; padding: 3pt 5pt; vertical-align: top; font-size: 8pt; }
            .lbl { background: #e0e0e0; font-weight: bold; font-size: 7pt; }
            .azhdr { text-align: right; font-size: 8pt; }
            .azhdr strong { font-size: 10pt; }
            .small { font-size: 7pt; }
            .area { height: 90pt; white-space: pre-wrap; vertical-align: top; }
        </style>';

        $html .= '<table><tr>
            <td width="60%"><h1>Mobile repair report</h1></td>
            <td width="40%" class="azhdr">
                <strong>'.$esc($az->ragione_sociale ?? '').'</strong><br>
                <span class="small">'.$esc($az->indirizzo ?? '').'</span><br>
                <span class="small">'.$esc($az->cap ?? '').' '.$esc($az->comune ?? '').' ('.$esc($az->provincia ?? '').')</span><br>
                <span class="small">P.IVA: '.$esc($az->partita_iva ?? '').'</span>
            </td>
        </tr></table>';

        $html .= '<table class="box" style="margin-top:6pt;"><tr>
            <td width="28%" class="lbl">Wagon number:</td>
            <td width="28%" class="lbl">Order number:</td>
            <td width="22%" class="lbl">Workshop short code:</td>
            <td width="22%" rowspan="2"></td>
        </tr><tr>
            <td>'.$esc($i->vagone_codice ?: $i->automezzo).'</td>
            <td>'.$esc($i->odl_numero ?: '').'/'.($i->data_apertura ? date('Y', strtotime($i->data_apertura)) : date('Y')).'</td>
            <td>'.$esc($az->ragione_sociale ?? '').'</td>
        </tr></table>';

        $html .= '<table class="box"><tr>
            <td width="28%" class="lbl">Keeper/ECM:</td>
            <td width="28%" class="lbl">Customer order number:</td>
            <td width="44%" class="lbl">Produced on:</td>
        </tr><tr>
            <td>'.$esc($i->cliente_rs ?: '').'</td>
            <td>'.$esc($i->numero_ordine_cliente ?: '').'</td>
            <td>'.date('d-m-Y').'</td>
        </tr></table>';

        $html .= '<table class="box" style="margin-top:4pt;"><tr>
            <td width="50%" class="lbl">Mobile repair performed on:</td>
            <td width="50%" class="lbl">Maintenance level performed:</td>
        </tr><tr>
            <td>'.$esc(strtoupper($i->impianto ?: $i->localita ?: '')).'</td>
            <td>'.$esc($i->codice_cuu ?: '').'</td>
        </tr></table>';

        $html .= '<table class="box" style="margin-top:6pt;"><tr>
            <td width="20%" class="lbl">Old wagon number:</td>
            <td width="20%" class="lbl">Tare weight:</td>
            <td width="12%" class="lbl">Type:</td>
            <td width="12%" class="lbl">Cycle:</td>
            <td width="12%" class="lbl">Date:</td>
            <td width="12%" class="lbl">Extension:</td>
        </tr><tr>
            <td>'.$esc($i->matricola_carro_old ?: '-').'</td>
            <td>'.$esc($tare).'</td>
            <td>'.$esc($i->vagone_tipo ?: '').'</td>
            <td class="small">6Irev/ari-dts</td>
            <td></td>
            <td></td>
        </tr></table>';

        // Repair work performed (report del manutentore)
        $html .= '<table class="box" style="margin-top:6pt;"><tr>
            <td class="lbl">Repair work performed:</td>
        </tr><tr>
            <td class="area">'.$esc($i->report_danni ?: '').'</td>
        </tr></table>';

        $html .= '<table class="box" style="margin-top:4pt;"><tr>
            <td class="lbl">Postponed work (including reason):</td>
        </tr><tr>
            <td class="area">-</td>
        </tr></table>';

        // Replaced components (dai materiali consumati)
        $replaced = '';
        foreach ($materiali as $m) {
            $qta = rtrim(rtrim(number_format($m->qta, 3, ',', '.'), '0'), ',');
            $replaced .= 'N° '.$qta.' '.$m->descrizione."\n";
        }
        $html .= '<table class="box" style="margin-top:4pt;"><tr>
            <td class="lbl">Replaced components:</td>
        </tr><tr>
            <td class="area">'.$esc(trim($replaced) ?: '-').'</td>
        </tr></table>';

        $html .= '<table class="box" style="margin-top:4pt;"><tr>
            <td class="lbl">Other information (as specified by keeper/ECM):</td>
        </tr><tr>
            <td class="area">'.$esc($i->note ?: '-').'</td>
        </tr></table>';

        // Release confirmation
        $html .= '<table class="box" style="margin-top:6pt;"><tr><td colspan="2" class="lbl">Release confirmation</td></tr>';
        $html .= '<tr><td width="22%">[X] Repair</td><td>The described work steps were performed correctly and completely.</td></tr>';
        $html .= '<tr><td>[X] Release to service</td><td class="small">The additional check as per VPI-EMG 01, Annex 19, found no fault. We hereby certify that this wagon corresponds to the applicable laws and ordinances, the regulations of the keeper/ECM and the RID (if applicable).</td></tr>';
        $html .= '</table>';

        // Firma responsabile
        $html .= '<table class="box" style="margin-top:6pt;"><tr>
            <td width="35%" class="lbl">Name (of the responsible employee):</td>
            <td width="20%" class="lbl">Telephone no.:</td>
            <td width="20%" class="lbl">Date of issue:</td>
            <td width="25%" class="lbl">Email:</td>
        </tr><tr>
            <td>ECM '.$esc($az->ragione_sociale ?? '').': '.$esc($rmLabel ?: '').'</td>
            <td>Tel.: 081 5127059</td>
            <td>'.date('d-m-Y').'</td>
            <td>'.$esc($az->email_ricezione_fatture ?? '').'</td>
        </tr></table>';

        $html .= '<div class="small" style="margin-top:14pt; color:#666;">VPI-EMG 10 Annex 2 | Edition: 01/12/2024<br>Translation: 10/12/2024</div>';

        $mpdf->WriteHTML($html);
        return $mpdf->Output('mobile_repair_report_'.($i->vagone_codice ?: $id).'.pdf', 'D');
    }

    /**
     * Modulo OdL ORR - apertura ordine di lavoro interno
     * Documento interno con dati cliente, matricola, codice avaria CUU,
     * personale incaricato (RM/CS/OP), tabella controlli e firme.
     */
    public function interventi_modulo_odl_pdf($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $i = DB::table('interventi as i')
            ->leftJoin('clienti as c', 'c.id', '=', 'i.id_cliente')
            ->leftJoin('vagoni as v', 'v.id', '=', 'i.id_vagone')
            ->leftJoin('utenti as op', 'op.id', '=', 'i.id_operatore_assegnato')
            ->leftJoin('utenti as cs', 'cs.id', '=', 'i.id_capo_squadra')
            ->leftJoin('utenti as rm', 'rm.id', '=', 'i.id_responsabile_manutenzione')
            ->where('i.id', $id)
            ->where('i.id_azienda', $utente->id_azienda)
            ->select(
                'i.*',
                'c.ragione_sociale as cliente_rs',
                'v.codice as vagone_codice',
                'op.nome as op_nome', 'op.cognome as op_cognome',
                'cs.nome as cs_nome', 'cs.cognome as cs_cognome',
                'rm.nome as rm_nome', 'rm.cognome as rm_cognome'
            )
            ->first();
        if (!$i) abort(404, 'Intervento non trovato');

        $az = DB::table('aziende')->where('id', $utente->id_azienda)->first();
        // Righe da intervento: prima cerca lavorazioni proposte (manutentore), poi righe del preventivo
        $righe = DB::table('interventi_lavorazioni_proposte')->where('id_intervento', $id)->orderBy('ordinamento')->get();
        if ($righe->count() === 0 && $i->id_dotes_preventivo) {
            $righe = DB::table('dorig')->where('id_dotes', $i->id_dotes_preventivo)->orderBy('n_riga')->get();
        }

        $esc = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
        $opLabel = $i->op_nome ? trim($i->op_nome.' '.$i->op_cognome) : '';
        $csLabel = $i->cs_nome ? trim($i->cs_nome.' '.$i->cs_cognome) : '';
        $rmLabel = $i->rm_nome ? trim($i->rm_nome.' '.$i->rm_cognome) : '';

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8', 'format' => 'A4',
            'margin_left' => 8, 'margin_right' => 8,
            'margin_top' => 8, 'margin_bottom' => 10,
            'default_font' => 'helvetica', 'default_font_size' => 8,
        ]);

        $html = '<style>
            body { font-family: helvetica, sans-serif; color: #000; font-size: 8pt; }
            h1 { font-size: 14pt; font-weight: bold; margin: 0; }
            table { border-collapse: collapse; width: 100%; }
            .b td { border: 1px solid #000; padding: 3pt 5pt; vertical-align: top; }
            .lbl { background: #d6d6d6; font-weight: bold; font-size: 8pt; }
            .lbl-italic { font-style: italic; text-align: center; padding: 4pt; }
            .small { font-size: 7pt; }
            .checkbox { font-family: DejaVuSans, sans-serif; }
            .sez-title { background: #b8c1d6; text-align: center; font-weight: bold; padding: 4pt; }
        </style>';

        $html .= '<table><tr>
            <td width="30%"><strong>ORR SRL</strong><br><span class="small">OFFICINA RIPARAZIONE ROTABILI</span></td>
            <td width="40%" class="lbl-italic">Descrizione Attivita da eseguire</td>
            <td width="30%" style="text-align:right;"><strong>Mod. OdL</strong></td>
        </tr></table>';

        // CLIENTE / IMPIANTO
        $html .= '<table class="b" style="margin-top:4pt;"><tr>
            <td width="20%" class="lbl">CLIENTE:</td>
            <td width="30%">'.$esc($i->cliente_rs ?: '').'</td>
            <td width="20%" class="lbl">IMPIANTO:</td>
            <td width="30%">'.$esc(strtoupper($i->impianto ?: $i->localita ?: '')).'</td>
        </tr></table>';

        // OdL n / DATA
        $html .= '<table class="b"><tr>
            <td width="20%" class="lbl">OdL n°:</td>
            <td width="30%">'.$esc($i->odl_numero ?: $id).' / '.($i->data_apertura ? date('Y', strtotime($i->data_apertura)) : date('Y')).'</td>
            <td width="20%" class="lbl">DATA INTERVENTO:</td>
            <td width="30%">'.($i->data_apertura ? date('d/m/Y', strtotime($i->data_apertura)) : '').'</td>
        </tr></table>';

        // PdM Riferimento + ricambi
        $html .= '<table class="b"><tr>
            <td width="40%" class="lbl">PdM di Riferimento:</td>
            <td width="60%">'.$esc($i->pdm_riferimento ?: 'VPI').' &nbsp; &nbsp; <span class="small">Ricambi forniti dal detentore [&nbsp;&nbsp;] - Ricambi reperiti da ORR [&nbsp;&nbsp;]</span></td>
        </tr></table>';

        // Matricola Carro / Cod Avaria / OdM cliente
        $html .= '<table class="b" style="margin-top:6pt;"><tr>
            <td width="33%" class="lbl" style="text-align:center;">Matricola Carro</td>
            <td width="33%" class="lbl" style="text-align:center;">Cod Avaria CUU</td>
            <td width="34%" class="lbl" style="text-align:center;">OdM cliente di Riferimento</td>
        </tr><tr>
            <td>Carro N°: '.$esc($i->vagone_codice ?: $i->automezzo).'</td>
            <td>'.$esc($i->codice_cuu ?: '').'</td>
            <td>'.$esc($i->numero_ordine_cliente ?: '').'</td>
        </tr></table>';

        // Personale Incaricato
        $html .= '<table class="b" style="margin-top:6pt;"><tr>
            <td width="33%" class="lbl">Personale Incaricato:</td>
            <td width="33%" class="lbl" style="text-align:center;">Capo Squadra (CS)</td>
            <td width="34%" class="lbl" style="text-align:center;">Operatore (OP)</td>
        </tr><tr>
            <td>Il RM: '.$esc($rmLabel).'</td>
            <td>'.$esc($csLabel).'</td>
            <td>'.$esc($opLabel).'</td>
        </tr><tr>
            <td colspan="2" class="lbl">Per accettazione il CS:</td>
            <td></td>
        </tr></table>';

        // RIF. ISTRUZIONI LAVORO
        $html .= '<table class="b" style="margin-top:6pt;"><tr>
            <td class="lbl" width="30%">RIF. ISTRUZIONI LAVORO:</td>
            <td>'.$esc($i->reason_intake ?: '').'</td>
        </tr></table>';

        // Sezione controlli
        $html .= '<div class="sez-title" style="margin-top:6pt; border: 1px solid #000;">OPERAZIONI DI CORRETTIVA DA EFFETTUARE rif. PDM</div>';
        $html .= '<table class="b">
            <tr>
                <td rowspan="2" width="18%" class="lbl" style="text-align:center;">MATRICOLA CARRO</td>
                <td rowspan="2" width="32%" class="lbl" style="text-align:center;">DESCRIZIONE AVARIA<br>SEGNALATA DAL COMMITTENTE</td>
                <td rowspan="2" width="8%" class="lbl" style="text-align:center;">Note</td>
                <td colspan="3" class="lbl" style="text-align:center;">Controlli Eseguiti</td>
            </tr>
            <tr>
                <td width="10%" class="lbl" style="text-align:center;">I°<br>Controllo</td>
                <td width="10%" class="lbl" style="text-align:center;">II°<br>Controllo</td>
                <td width="22%" class="lbl" style="text-align:center;">EVENTUALE AZIONE<br>CORRETTIVA INTRAPRESA</td>
            </tr>';
        if ($righe->count() > 0) {
            $first = true;
            foreach ($righe as $idx => $r) {
                $html .= '<tr>
                    <td>'.($first ? $esc($i->vagone_codice ?: $i->automezzo) : '').'</td>
                    <td>'.$esc($r->descrizione).'</td>
                    <td style="text-align:center;">[&nbsp;&nbsp;]</td>
                    <td style="text-align:center;">NC</td>
                    <td style="text-align:center;"></td>
                    <td></td>
                </tr>';
                $first = false;
            }
        } else {
            for ($k = 0; $k < 5; $k++) {
                $html .= '<tr><td>&nbsp;</td><td>&nbsp;</td><td style="text-align:center;">[&nbsp;&nbsp;]</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>';
            }
        }
        $html .= '</table>';

        // Note / Restrizioni d'uso
        $html .= '<table class="b" style="margin-top:6pt;"><tr><td colspan="2" class="lbl-italic" style="background:#d6d6d6;">Note / Restrizioni D\'Uso</td></tr>';
        $html .= '<tr><td colspan="2" style="height:30pt;"></td></tr></table>';

        // Dichiarazione
        $html .= '<div class="small" style="margin-top:6pt; text-align:justify; padding:4pt; border:1px solid #000;">';
        $html .= 'Si certifica che le lavorazioni di manutenzione sul carro [X] ovvero sui carri indicati [&nbsp;&nbsp;] sono state eseguite da personale qualificato in conformita a quanto previsto dai PDM, dal CUU e dalle Specifiche Tecniche e disposizioni ricevute dall\'ECM. I materiali sono approvvigionati e conformi a quanto previsto dal CUU e dalle disposizioni ricevute dall\'ECM. Le attivita sono state effettuate nel rispetto del piano della qualita dell\'impresa e delle vigenti leggi in materia di sicurezza e igiene del lavoro e protezione ambientale. <strong>L\'esito del collaudo finale eseguito e:</strong>';
        $html .= '</div>';

        $html .= '<table class="b small"><tr>
            <td width="33%">-a) [&nbsp;&nbsp;] Conforme — Reimmesso in Servizio a norma del Reg. 2019/779</td>
            <td width="33%">-b) [&nbsp;&nbsp;] Non Conforme</td>
            <td width="34%">-c) [&nbsp;&nbsp;] Restrizioni d\'uso applicate</td>
        </tr></table>';

        // Firme
        $html .= '<table class="b" style="margin-top:6pt;"><tr>
            <td width="33%" class="lbl">Firma RM</td>
            <td width="33%" class="lbl">Firma CS</td>
            <td width="34%" class="lbl">Firma OP</td>
        </tr><tr>
            <td style="height:40pt;">'.$esc($rmLabel).'</td>
            <td>'.$esc($csLabel).'</td>
            <td>'.$esc($opLabel).'</td>
        </tr></table>';

        $html .= '<div class="small" style="margin-top:4pt; color:#666;">C=CONFORME &nbsp; NC=NON CONFORME &nbsp; NA=NON APPLICABILE &nbsp; CS=CAPO SQUADRA &nbsp; OP=OPERATORE &nbsp; RM=Responsabile Manutenzione<br>Mod. OdL rev.4 del 10/10/22 pag. 1 di 1</div>';

        $mpdf->WriteHTML($html);
        return $mpdf->Output('odl_'.($i->odl_numero ?: $id).'.pdf', 'D');
    }

    public function interventi_certificato_pdf($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $i = DB::table('interventi as i')
            ->leftJoin('clienti as c', 'c.id', '=', 'i.id_cliente')
            ->leftJoin('vagoni as v', 'v.id', '=', 'i.id_vagone')
            ->leftJoin('utenti as op', 'op.id', '=', 'i.id_operatore_assegnato')
            ->where('i.id', $id)
            ->where('i.id_azienda', $utente->id_azienda)
            ->select('i.*', 'c.ragione_sociale as cliente_rs', 'c.indirizzo as cliente_ind', 'c.comune as cliente_com', 'v.codice as vagone_codice', 'v.tipo as vagone_tipo', 'v.numero_uic as vagone_uic', 'op.nome as op_nome', 'op.cognome as op_cognome')
            ->first();
        if (!$i || !$i->id_dotes_certificato) abort(404, 'Certificato non trovato');

        $dotes = DB::table('dotes')->where('id', $i->id_dotes_certificato)->first();
        $dorig = DB::table('dorig')->where('id_dotes', $i->id_dotes_certificato)->orderBy('n_riga')->get();
        $az    = DB::table('aziende')->where('id', $utente->id_azienda)->first();

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8', 'format' => 'A4',
            'margin_left' => 14, 'margin_right' => 14,
            'margin_top' => 16,  'margin_bottom' => 16,
            'default_font' => 'helvetica', 'default_font_size' => 10,
        ]);
        $esc = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
        $dataIntervento = $i->data_apertura ? date('d/m/Y', strtotime($i->data_apertura)) : '—';
        $dataCertificato = date('d/m/Y', strtotime($dotes->data_doc));
        $manutentoreLabel = $i->op_nome ? trim($i->op_nome.' '.$i->op_cognome) : '—';

        $html = '<style>
            body { font-family: helvetica, sans-serif; color: #222; }
            .title { text-align:center; color:#065f46; font-size: 22px; font-weight: bold; letter-spacing: 1px; margin: 0 0 4px; }
            .subtitle { text-align:center; color:#6b7280; font-size: 11px; margin: 0 0 16px; }
            .doc-num { text-align:center; font-size: 12px; margin-bottom: 14px; }
            .box { border: 1px solid #d1d5db; border-radius: 4px; padding: 10px; margin-bottom: 12px; }
            .label { color: #6b7280; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
            .h2 { color:#065f46; font-size: 13px; font-weight: bold; margin: 14px 0 6px; border-bottom: 1px solid #d1d5db; padding-bottom: 4px; }
            table.righe { width: 100%; border-collapse: collapse; margin-top: 6px; font-size: 9px; }
            table.righe th { background: #065f46; color: #fff; padding: 6px 4px; text-align: left; font-size: 8px; }
            table.righe td { padding: 5px 4px; border-bottom: 1px solid #e5e7eb; }
            .declar { font-size: 10px; line-height: 1.5; text-align: justify; margin-top: 12px; }
            .firma { margin-top: 30px; }
            .firma td { vertical-align: bottom; }
            .firma-line { border-top: 1px solid #999; padding-top: 4px; font-size: 9px; color:#6b7280; }
            .footer { margin-top: 24px; font-size: 8px; color: #888; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 10px; }
        </style>';
        $html .= '<div class="title">CERTIFICATO DI MANUTENZIONE</div>';
        $html .= '<div class="subtitle">Dichiarazione di esecuzione lavori di manutenzione su rotabile ferroviario</div>';
        $html .= '<div class="doc-num">Documento n. <strong>'.$esc($dotes->numero_doc).'/'.date('Y', strtotime($dotes->data_doc)).'</strong> del '.$dataCertificato.'</div>';

        $html .= '<table class="header-tbl" width="100%"><tr>
            <td width="48%"><div class="box">
                <div class="label">Officina manutentrice</div>
                <div><strong>'.$esc($az->ragione_sociale ?? '').'</strong></div>
                <div>'.$esc($az->indirizzo ?? '').'<br>'.$esc($az->cap ?? '').' '.$esc($az->comune ?? '').' ('.$esc($az->provincia ?? '').')</div>
                '.(!empty($az->partita_iva) ? '<div style="font-size:8px;color:#666;">P.IVA '.$esc($az->partita_iva).'</div>' : '').'
            </div></td>
            <td width="4%"></td>
            <td width="48%"><div class="box">
                <div class="label">Cliente</div>
                <div><strong>'.$esc($dotes->ragione_sociale).'</strong></div>
                <div>'.$esc($dotes->indirizzo).'<br>'.$esc($dotes->cap).' '.$esc($dotes->comune).' ('.$esc($dotes->provincia).')</div>
                '.(!empty($dotes->partita_iva) ? '<div style="font-size:8px;color:#666;">P.IVA '.$esc($dotes->partita_iva).'</div>' : '').'
            </div></td>
        </tr></table>';

        $html .= '<div class="h2">Dati dell\'intervento</div>';
        $html .= '<div class="box">
            <table width="100%"><tr>
                <td width="50%"><div class="label">Vagone</div><strong>'.$esc($i->vagone_codice ?: $i->automezzo ?: '—').'</strong>'.($i->vagone_tipo ? ' <span style="color:#666;">('.$esc($i->vagone_tipo).')</span>' : '').'</td>
                <td width="50%"><div class="label">Numero UIC</div>'.$esc($i->vagone_uic ?: '—').'</td>
            </tr><tr>
                <td><div class="label">Data apertura intervento</div>'.$dataIntervento.'</td>
                <td><div class="label">Località</div>'.$esc($i->localita ?: '—').'</td>
            </tr><tr>
                <td><div class="label">Manutentore responsabile</div>'.$esc($manutentoreLabel).'</td>
                <td><div class="label">Motivo del rientro</div>'.$esc($i->reason_intake ?: '—').'</td>
            </tr></table>
        </div>';

        if ($i->report_danni) {
            $html .= '<div class="h2">Report danni rilevati</div>';
            $html .= '<div class="box" style="white-space:pre-wrap;">'.$esc($i->report_danni).'</div>';
        }

        $html .= '<div class="h2">Lavorazioni eseguite</div>';
        $html .= '<table class="righe"><thead><tr>
            <th width="6%">#</th><th width="10%">Serv.</th><th width="14%">Codice</th>
            <th>Descrizione</th><th width="10%" style="text-align:right">Qta</th><th width="10%" style="text-align:right">Minuti</th>
        </tr></thead><tbody>';
        if (count($dorig) > 0) {
            $n = 0;
            foreach ($dorig as $r) {
                $n++;
                $minTxt = (float) $r->minuti > 0 ? rtrim(rtrim(number_format($r->minuti, 2, ',', ''), '0'), ',') : '—';
                $html .= '<tr>
                    <td>'.$n.'</td>
                    <td>'.$esc($r->servizio).'</td>
                    <td>'.$esc($r->cd_ar).'</td>
                    <td>'.$esc($r->descrizione).'</td>
                    <td style="text-align:right">'.rtrim(rtrim(number_format($r->qta, 3, ',', '.'), '0'), ',').' '.$esc($r->um).'</td>
                    <td style="text-align:right">'.$minTxt.'</td>
                </tr>';
            }
        } else {
            $html .= '<tr><td colspan="6" style="text-align:center;color:#999;">Nessuna riga di lavorazione registrata</td></tr>';
        }
        $html .= '</tbody></table>';

        $html .= '<div class="declar">';
        $html .= '<p><strong>Dichiarazione.</strong> Si dichiara che le lavorazioni di manutenzione sopra elencate sono state eseguite a regola d\'arte presso le officine di '.$esc($az->ragione_sociale ?? '').' nel rispetto delle normative tecniche vigenti applicabili al rotabile ferroviario sopra identificato, e che il mezzo è stato verificato e trovato idoneo all\'esercizio per quanto di competenza dell\'intervento.</p>';
        $html .= '</div>';

        $html .= '<table class="firma" width="100%"><tr>
            <td width="48%"><div class="firma-line">Manutentore responsabile<br><strong style="color:#222;">'.$esc($manutentoreLabel).'</strong></div></td>
            <td width="4%"></td>
            <td width="48%"><div class="firma-line">Per accettazione cliente<br>(timbro e firma)</div></td>
        </tr></table>';

        $html .= '<div class="footer">Documento generato il '.date('d/m/Y H:i').' tramite Gestya · Certificato n. '.$esc($dotes->numero_doc).'/'.date('Y', strtotime($dotes->data_doc)).'</div>';

        $mpdf->WriteHTML($html);
        return $mpdf->Output('certificato_manutenzione_'.$dotes->numero_doc.'.pdf', 'D');
    }

    public function interventi_step_5_decisione($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $i = DB::table('interventi')->where('id', $id)->where('id_azienda', $utente->id_azienda)->first();
        if (!$i) return redirect()->back()->with('error', 'Intervento non trovato');
        if ((int) $i->step_corrente !== 5) return redirect()->back()->with('error', 'Non sei sullo step 5');

        $azione = $request->input('azione', '');
        $now = date('Y-m-d H:i:s');

        if ($azione === 'accetta') {
            DB::table('interventi')->where('id', $id)->update([
                'accettato_il'           => $now,
                'accettato_da_id_utente' => $utente->id,
                'rifiutato_il'           => null,
                'motivo_rifiuto'         => null,
            ]);
            $this->_intervento_avanza_step($id, (int) $utente->id_azienda, (int) $utente->id, 5, 'accettato', 'Preventivo accettato');
            return Redirect::to('utente/interventi/'.$id)->with('success', 'Preventivo accettato. Passa allo step 6 — invio fattura.');
        }

        if ($azione === 'rifiuta') {
            $motivo = trim((string) $request->input('motivo_rifiuto', ''));
            if ($motivo === '') return redirect()->back()->with('error', 'Inserisci il motivo del rifiuto');

            DB::table('interventi')->where('id', $id)->update([
                'rifiutato_il'   => $now,
                'motivo_rifiuto' => $motivo,
            ]);
            // Torna allo step 4 per rilavorazione
            $this->_intervento_avanza_step($id, (int) $utente->id_azienda, (int) $utente->id, 5, 'rifiutato', 'Rifiutato: '.$motivo, 4);
            return Redirect::to('utente/interventi/'.$id)->with('error', 'Preventivo rifiutato. Torna allo step 4 per rilavorazione.');
        }

        return redirect()->back()->with('error', 'Azione non valida');
    }

    public function interventi_step_6_crea_fattura($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $i = DB::table('interventi')->where('id', $id)->where('id_azienda', $utente->id_azienda)->first();
        if (!$i) return redirect()->back()->with('error', 'Intervento non trovato');
        if ((int) $i->step_corrente !== 6) return redirect()->back()->with('error', 'Non sei sullo step 6');
        if (!$i->id_dotes_preventivo) return redirect()->back()->with('error', 'Nessun preventivo collegato da cui creare la fattura');
        if ($i->id_dotes_fattura) return redirect()->back()->with('error', 'Fattura gia\' creata');

        $prev = DB::table('dotes')->where('id', $i->id_dotes_preventivo)->first();
        if (!$prev) return redirect()->back()->with('error', 'Preventivo non trovato');

        // Numero progressivo FTV per anno + azienda
        $maxNum = (int) DB::table('dotes')
            ->where('id_azienda', $utente->id_azienda)
            ->where('cd_do', 'FTV')
            ->whereYear('data_doc', date('Y'))
            ->max(DB::raw('CAST(numero_doc AS UNSIGNED)'));
        $numero_doc = $maxNum + 1;

        // Crea FTV con snapshot dal preventivo
        $id_ftv = DB::table('dotes')->insertGetId([
            'cd_do'           => 'FTV',
            'tipologia_documento' => 'TD01',
            'numero_doc'      => $numero_doc,
            'data_doc'        => date('Y-m-d'),
            'id_cliente'      => $prev->id_cliente,
            'id_azienda'      => $utente->id_azienda,
            'id_utente'       => $utente->id,
            'id_vagone'       => $prev->id_vagone ?? null,
            'automezzo'       => $prev->automezzo ?? null,
            'localita'        => $prev->localita ?? null,
            'reason_intake'   => $prev->reason_intake ?? null,
            'note_operatore'  => $prev->note_operatore ?? null,
            'ragione_sociale' => $prev->ragione_sociale,
            'partita_iva'     => $prev->partita_iva,
            'cf'              => $prev->cf,
            'indirizzo'       => $prev->indirizzo,
            'cap'             => $prev->cap,
            'comune'          => $prev->comune,
            'provincia'       => $prev->provincia,
            'pec'             => $prev->pec,
            'sdi'             => $prev->sdi,
            'imponibile'      => $prev->imponibile,
            'imposta'         => $prev->imposta,
            'totale'          => $prev->totale,
            'da_registrare'   => 0,
        ]);

        // Copia tutte le righe da dorig PRE a dorig FTV
        $righe = DB::table('dorig')->where('id_dotes', $prev->id)->orderBy('n_riga')->get();
        $nRiga = 0;
        foreach ($righe as $r) {
            $nRiga++;
            DB::table('dorig')->insert([
                'id_azienda'                  => $utente->id_azienda,
                'id_utente'                   => $utente->id,
                'id_cliente'                  => $prev->id_cliente,
                'id_dotes'                    => $id_ftv,
                'id_testata'                  => $id_ftv,
                'cd_do'                       => 'FTV',
                'numero_doc'                  => $numero_doc,
                'data_doc'                    => date('Y-m-d'),
                'cd_ar'                       => $r->cd_ar,
                'n_riga'                      => $nRiga,
                'descrizione'                 => $r->descrizione,
                'qta'                         => $r->qta,
                'um'                          => $r->um,
                'pu'                          => $r->pu,
                'pt'                          => $r->pt,
                'prezzo_unitario'             => $r->prezzo_unitario,
                'prezzo_totale'               => $r->prezzo_totale,
                'iva'                         => $r->iva,
                'imponibile'                  => $r->imponibile,
                'imposta'                     => $r->imposta,
                'totale'                      => $r->totale,
                'servizio'                    => $r->servizio,
                'setup_tank'                  => $r->setup_tank,
                'attivita'                    => $r->attivita,
                'minuti'                      => $r->minuti,
                'materiale'                   => $r->materiale,
                'descrizione_materiale'       => $r->descrizione_materiale,
                'id_vagone'                   => $r->id_vagone,
                'id_lavorazione_origine'      => $r->id_lavorazione_origine,
                'id_lavorazione_riga_origine' => $r->id_lavorazione_riga_origine,
            ]);
        }

        DB::table('interventi')->where('id', $id)->update(['id_dotes_fattura' => $id_ftv]);
        DB::table('interventi_log')->insert([
            'id_intervento' => $id,
            'id_azienda'    => $utente->id_azienda,
            'id_utente'     => $utente->id,
            'step'          => 6,
            'azione'        => 'fattura_creata',
            'note'          => 'Fattura FTV #'.$numero_doc.' creata dal preventivo (dotes id '.$id_ftv.', '.$nRiga.' righe copiate)',
            'created_at'    => date('Y-m-d H:i:s'),
        ]);

        return Redirect::to('utente/modifica_documento/'.$id_ftv)->with('success', 'Fattura creata. Modifica le righe se serve, poi torna all\'intervento per generare PDF/XML.');
    }

    public function interventi_preventivo_pdf($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $i = DB::table('interventi as i')
            ->leftJoin('vagoni as v', 'v.id', '=', 'i.id_vagone')
            ->leftJoin('utenti as op', 'op.id', '=', 'i.id_operatore_assegnato')
            ->where('i.id', $id)
            ->where('i.id_azienda', $utente->id_azienda)
            ->select('i.*', 'v.codice as vagone_codice', 'op.nome as op_nome')
            ->first();
        if (!$i || !$i->id_dotes_preventivo) abort(404, 'Preventivo non trovato');

        $dotes = DB::table('dotes')->where('id', $i->id_dotes_preventivo)->first();
        $dorig = DB::table('dorig')->where('id_dotes', $i->id_dotes_preventivo)->orderBy('n_riga')->get();
        $az    = DB::table('aziende')->where('id', $utente->id_azienda)->first();

        $path = $this->_genera_pdf_invoice_receipt($i, $dotes, $dorig, $az, 'preventivo');
        return response()->download($path, 'preventivo_'.$dotes->numero_doc.'.pdf')->deleteFileAfterSend(true);
    }

    public function interventi_fattura_pdf($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $i = DB::table('interventi as i')
            ->leftJoin('vagoni as v', 'v.id', '=', 'i.id_vagone')
            ->leftJoin('utenti as op', 'op.id', '=', 'i.id_operatore_assegnato')
            ->where('i.id', $id)
            ->where('i.id_azienda', $utente->id_azienda)
            ->select('i.*', 'v.codice as vagone_codice', 'op.nome as op_nome')
            ->first();
        if (!$i || !$i->id_dotes_fattura) abort(404, 'Fattura non trovata');

        $dotes = DB::table('dotes')->where('id', $i->id_dotes_fattura)->first();
        $dorig = DB::table('dorig')->where('id_dotes', $i->id_dotes_fattura)->orderBy('n_riga')->get();
        $az    = DB::table('aziende')->where('id', $utente->id_azienda)->first();

        $path = $this->_genera_pdf_invoice_receipt($i, $dotes, $dorig, $az, 'fattura');
        return response()->download($path, 'fattura_'.$dotes->numero_doc.'.pdf')->deleteFileAfterSend(true);
    }

    public function interventi_fattura_pdf_OLD_GENERIC($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $i = DB::table('interventi')->where('id', $id)->where('id_azienda', $utente->id_azienda)->first();
        if (!$i || !$i->id_dotes_fattura) abort(404, 'Fattura non trovata');

        $dotes = DB::table('dotes')->where('id', $i->id_dotes_fattura)->first();
        $dorig = DB::table('dorig')->where('id_dotes', $i->id_dotes_fattura)->orderBy('n_riga')->get();
        $az    = DB::table('aziende')->where('id', $utente->id_azienda)->first();

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8', 'format' => 'A4',
            'margin_left' => 12, 'margin_right' => 12,
            'margin_top' => 14,  'margin_bottom' => 14,
            'default_font' => 'helvetica',
            'default_font_size' => 9,
        ]);
        $esc = function ($v) { return htmlspecialchars((string) $v, ENT_QUOTES, 'UTF-8'); };
        $eur = function ($v) { return '&euro; '.number_format((float) $v, 2, ',', '.'); };

        $html = '<style>
            body { font-family: helvetica, sans-serif; color: #222; }
            h1 { color: #1e40af; font-size: 22px; margin: 0; }
            .header-tbl { width: 100%; margin-bottom: 14px; }
            .header-tbl td { vertical-align: top; padding: 0; }
            .box { border: 1px solid #e5e7eb; border-radius: 4px; padding: 10px; }
            .label { color: #6b7280; font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px; }
            table.righe { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 9px; }
            table.righe th { background: #1e40af; color: #fff; padding: 6px 4px; text-align: left; font-size: 8px; }
            table.righe td { padding: 5px 4px; border-bottom: 1px solid #e5e7eb; }
            table.righe tr:nth-child(even) td { background: #f9fafb; }
            .totali { width: 280px; margin-left: auto; margin-top: 12px; }
            .totali td { padding: 4px 8px; }
            .totali .grand { border-top: 2px solid #1e40af; font-size: 14px; padding-top: 8px; color: #1e40af; font-weight: bold; }
            .footer { margin-top: 24px; font-size: 8px; color: #888; text-align: center; border-top: 1px solid #e5e7eb; padding-top: 10px; }
        </style>';
        $html .= '<h1>FATTURA</h1>';
        $html .= '<p style="margin:4px 0 14px;color:#6b7280;">N. <strong>'.$esc($dotes->numero_doc).'/'.date('Y', strtotime($dotes->data_doc)).'</strong> del '.date('d/m/Y', strtotime($dotes->data_doc)).'</p>';
        $html .= '<table class="header-tbl"><tr>
            <td width="48%"><div class="box">
                <div class="label">Cedente</div>
                <div><strong>'.$esc($az->ragione_sociale ?? '').'</strong></div>
                <div>'.$esc($az->indirizzo ?? '').'<br>'.$esc($az->cap ?? '').' '.$esc($az->comune ?? '').' ('.$esc($az->provincia ?? '').')</div>
                '.(!empty($az->partita_iva) ? '<div style="color:#666;font-size:8px;">P.IVA '.$esc($az->partita_iva).'</div>' : '').'
            </div></td>
            <td width="4%"></td>
            <td width="48%"><div class="box">
                <div class="label">Cessionario</div>
                <div><strong>'.$esc($dotes->ragione_sociale).'</strong></div>
                <div>'.$esc($dotes->indirizzo).'<br>'.$esc($dotes->cap).' '.$esc($dotes->comune).' ('.$esc($dotes->provincia).')</div>
                '.(!empty($dotes->partita_iva) ? '<div style="color:#666;font-size:8px;">P.IVA '.$esc($dotes->partita_iva).'</div>' : '').'
            </div></td>
        </tr></table>';
        $html .= '<table class="righe"><thead><tr>
            <th width="4%">#</th><th width="10%">Codice</th><th width="46%">Descrizione</th>
            <th width="10%" style="text-align:right">Qta</th>
            <th width="10%" style="text-align:right">P.U.</th>
            <th width="6%" style="text-align:right">IVA%</th>
            <th width="14%" style="text-align:right">Totale</th>
        </tr></thead><tbody>';
        $n = 0;
        foreach ($dorig as $r) {
            $n++;
            $html .= '<tr>
                <td>'.$n.'</td>
                <td>'.$esc($r->cd_ar).'</td>
                <td>'.$esc($r->descrizione ?: $r->nome_prodotto).'</td>
                <td style="text-align:right">'.rtrim(rtrim(number_format($r->qta, 3, ',', '.'), '0'), ',').' '.$esc($r->um).'</td>
                <td style="text-align:right">'.$eur($r->prezzo_unitario).'</td>
                <td style="text-align:right">'.$r->iva.'</td>
                <td style="text-align:right;font-weight:bold">'.$eur($r->prezzo_totale).'</td>
            </tr>';
        }
        $html .= '</tbody></table>';
        $html .= '<table class="totali">
            <tr><td>Imponibile</td><td style="text-align:right">'.$eur($dotes->imponibile).'</td></tr>
            <tr><td>Imposta</td><td style="text-align:right">'.$eur($dotes->imposta).'</td></tr>
            <tr class="grand"><td>TOTALE FATTURA</td><td style="text-align:right">'.$eur($dotes->totale).'</td></tr>
        </table>';
        $html .= '<div class="footer">Documento generato il '.date('d/m/Y H:i').' tramite Gestya</div>';

        $mpdf->WriteHTML($html);
        return $mpdf->Output('fattura_'.$dotes->numero_doc.'.pdf', 'D'); // download
    }

    public function interventi_fattura_xml($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $i = DB::table('interventi')->where('id', $id)->where('id_azienda', $utente->id_azienda)->first();
        if (!$i || !$i->id_dotes_fattura) abort(404, 'Fattura non trovata');

        $dotes = DB::table('dotes')->where('id', $i->id_dotes_fattura)->first();
        $dorig = DB::table('dorig')->where('id_dotes', $i->id_dotes_fattura)->orderBy('n_riga')->get();
        $az    = DB::table('aziende')->where('id', $utente->id_azienda)->first();

        // XML SDI FPR12 minimal (lo stesso template del vecchio sw ORR adattato)
        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<p:FatturaElettronica versione="FPR12" xmlns:ds="http://www.w3.org/2000/09/xmldsig#" xmlns:p="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2 http://www.fatturapa.gov.it/export/fatturazione/sdi/fatturapa/v1.2/Schema_del_file_xml_FatturaPA_versione_1.2.xsd">'."\n";
        $xml .= '<FatturaElettronicaHeader>';
        $xml .= '<DatiTrasmissione><IdTrasmittente><IdPaese>IT</IdPaese><IdCodice>'.($az->partita_iva ?? '').'</IdCodice></IdTrasmittente><ProgressivoInvio>'.$dotes->id.'</ProgressivoInvio><FormatoTrasmissione>FPR12</FormatoTrasmissione><CodiceDestinatario>'.($dotes->sdi ?: '0000000').'</CodiceDestinatario>';
        if (!empty($dotes->pec)) $xml .= '<PECDestinatario>'.htmlspecialchars($dotes->pec).'</PECDestinatario>';
        $xml .= '</DatiTrasmissione>';
        $xml .= '<CedentePrestatore><DatiAnagrafici><IdFiscaleIVA><IdPaese>IT</IdPaese><IdCodice>'.($az->partita_iva ?? '').'</IdCodice></IdFiscaleIVA><Anagrafica><Denominazione>'.htmlspecialchars($az->ragione_sociale ?? '').'</Denominazione></Anagrafica><RegimeFiscale>'.($az->regime_fiscale ?? 'RF01').'</RegimeFiscale></DatiAnagrafici><Sede><Indirizzo>'.htmlspecialchars($az->indirizzo ?? '').'</Indirizzo><CAP>'.($az->cap ?? '').'</CAP><Comune>'.htmlspecialchars($az->comune ?? '').'</Comune><Provincia>'.($az->provincia ?? '').'</Provincia><Nazione>IT</Nazione></Sede></CedentePrestatore>';
        $xml .= '<CessionarioCommittente><DatiAnagrafici>';
        if (!empty($dotes->partita_iva)) $xml .= '<IdFiscaleIVA><IdPaese>IT</IdPaese><IdCodice>'.htmlspecialchars($dotes->partita_iva).'</IdCodice></IdFiscaleIVA>';
        if (!empty($dotes->cf)) $xml .= '<CodiceFiscale>'.htmlspecialchars($dotes->cf).'</CodiceFiscale>';
        $xml .= '<Anagrafica><Denominazione>'.htmlspecialchars($dotes->ragione_sociale).'</Denominazione></Anagrafica></DatiAnagrafici><Sede><Indirizzo>'.htmlspecialchars($dotes->indirizzo).'</Indirizzo><CAP>'.htmlspecialchars($dotes->cap).'</CAP><Comune>'.htmlspecialchars($dotes->comune).'</Comune><Provincia>'.htmlspecialchars($dotes->provincia).'</Provincia><Nazione>IT</Nazione></Sede></CessionarioCommittente>';
        $xml .= '</FatturaElettronicaHeader>';
        $xml .= '<FatturaElettronicaBody>';
        $xml .= '<DatiGenerali><DatiGeneraliDocumento><TipoDocumento>'.($dotes->tipologia_documento ?: 'TD01').'</TipoDocumento><Divisa>EUR</Divisa><Data>'.date('Y-m-d', strtotime($dotes->data_doc)).'</Data><Numero>'.htmlspecialchars($dotes->numero_doc).'/'.date('Y', strtotime($dotes->data_doc)).'</Numero><ImportoTotaleDocumento>'.number_format($dotes->totale, 2, '.', '').'</ImportoTotaleDocumento></DatiGeneraliDocumento></DatiGenerali>';
        $xml .= '<DatiBeniServizi>';
        $n = 0;
        foreach ($dorig as $r) {
            $n++;
            $pu = $r->iva > 0 ? round($r->prezzo_unitario / (1 + $r->iva / 100), 4) : (float) $r->prezzo_unitario;
            $pt = $r->iva > 0 ? round($r->prezzo_totale  / (1 + $r->iva / 100), 4) : (float) $r->prezzo_totale;
            $xml .= '<DettaglioLinee>';
            $xml .= '<NumeroLinea>'.$n.'</NumeroLinea>';
            $xml .= '<Descrizione>'.htmlspecialchars(substr($r->descrizione ?: $r->nome_prodotto, 0, 100)).'</Descrizione>';
            $xml .= '<Quantita>'.number_format($r->qta, 3, '.', '').'</Quantita>';
            $xml .= '<UnitaMisura>'.htmlspecialchars($r->um ?: 'NR').'</UnitaMisura>';
            $xml .= '<PrezzoUnitario>'.number_format($pu, 4, '.', '').'</PrezzoUnitario>';
            $xml .= '<PrezzoTotale>'.number_format($pt, 4, '.', '').'</PrezzoTotale>';
            $xml .= '<AliquotaIVA>'.number_format($r->iva, 2, '.', '').'</AliquotaIVA>';
            $xml .= '</DettaglioLinee>';
        }
        $xml .= '<DatiRiepilogo><AliquotaIVA>22.00</AliquotaIVA><ImponibileImporto>'.number_format($dotes->imponibile, 2, '.', '').'</ImponibileImporto><Imposta>'.number_format($dotes->imposta, 2, '.', '').'</Imposta><EsigibilitaIVA>I</EsigibilitaIVA></DatiRiepilogo>';
        $xml .= '</DatiBeniServizi>';
        $xml .= '</FatturaElettronicaBody>';
        $xml .= '</p:FatturaElettronica>';

        $filename = 'IT'.($az->partita_iva ?? '00000000000').'_'.str_pad(dechex($dotes->id), 5, '0', STR_PAD_LEFT).'.xml';
        return response($xml, 200, [
            'Content-Type'        => 'application/xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    public function interventi_step_6_completa($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $i = DB::table('interventi')->where('id', $id)->where('id_azienda', $utente->id_azienda)->first();
        if (!$i) return redirect()->back()->with('error', 'Intervento non trovato');
        if ((int) $i->step_corrente !== 6) return redirect()->back()->with('error', 'Non sei sullo step 6');

        DB::table('interventi')->where('id', $id)->update(['fattura_inviata_il' => date('Y-m-d H:i:s')]);
        $this->_intervento_avanza_step($id, (int) $utente->id_azienda, (int) $utente->id, 6, 'completato', 'Fattura inviata, intervento chiuso');
        return Redirect::to('utente/interventi/'.$id)->with('success', 'Fattura segnata come inviata. Intervento completato!');
    }

    public function interventi_step_6_fattura($id, Request $request)
    {
        // Backward compatibility: vecchio endpoint -> ora "completa"
        return $this->interventi_step_6_completa($id, $request);
    }

    public function ajax_catalogo_lavorazioni_righe(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        $lavorazioni = DB::table('lavorazioni')
            ->where('id_azienda', $utente->id_azienda)
            ->where('attivo', 1)
            ->orderBy('descrizione')
            ->get();

        if (count($lavorazioni) === 0) {
            return response()->json(['lavorazioni' => []]);
        }

        $idsLav = $lavorazioni->pluck('id')->toArray();
        $righe = DB::table('lavorazioni_righe')
            ->whereIn('id_lavorazione', $idsLav)
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('id_lavorazione')
            ->orderBy('ordinamento')
            ->get()
            ->groupBy('id_lavorazione');

        $out = [];
        foreach ($lavorazioni as $lav) {
            $out[] = [
                'id'          => $lav->id,
                'codice'      => $lav->codice,
                'descrizione' => $lav->descrizione,
                'totale'      => (float) $lav->totale,
                'righe'       => $righe->get($lav->id, collect())->values(),
            ];
        }
        return response()->json(['lavorazioni' => $out]);
    }

    public function ordina_righe_lavorazione($id_lavorazione, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $ids = $request->input('ids', []);

        if (!is_array($ids)) {
            return response()->json(['ok' => false, 'error' => 'ids deve essere array']);
        }

        foreach ($ids as $idx => $id_riga) {
            DB::table('lavorazioni_righe')
                ->where('id', (int) $id_riga)
                ->where('id_lavorazione', (int) $id_lavorazione)
                ->where('id_azienda', $utente->id_azienda)
                ->update(['ordinamento' => $idx + 1]);
        }

        return response()->json(['ok' => true, 'count' => count($ids)]);
    }

    private function normalizza_lavorazione_riga(array $dati): array
    {
        $out = [
            'servizio'              => isset($dati['servizio']) && $dati['servizio'] !== '' ? $dati['servizio'] : null,
            'codice'                => isset($dati['codice']) && $dati['codice'] !== '' ? $dati['codice'] : null,
            'setup_tank'            => isset($dati['setup_tank']) ? 1 : 0,
            'descrizione'           => $dati['descrizione'] ?? null,
            'attivita'              => $this->parse_decimal($dati['attivita'] ?? 1),
            'qta'                   => $this->parse_decimal($dati['qta'] ?? 0),
            'minuti'                => $this->parse_decimal($dati['minuti'] ?? 0),
            'pu'                    => $this->parse_decimal($dati['pu'] ?? 0),
            'aliquota'              => (int) ($dati['aliquota'] ?? 22),
            'materiale'             => $this->parse_decimal($dati['materiale'] ?? 0),
            'descrizione_materiale' => isset($dati['descrizione_materiale']) && $dati['descrizione_materiale'] !== '' ? $dati['descrizione_materiale'] : null,
        ];

        // attivita = moltiplicatore (default 1 se mancante o 0)
        $attivita = $out['attivita'] > 0 ? $out['attivita'] : 1;

        // Logica calcolo allineata al vecchio sw ORR:
        //   se minuti > 0  ->  pt = pu * minuti / 60
        //   altrimenti      ->  pt = pu * attivita * qta
        if ($out['minuti'] > 0) {
            $out['pt'] = round($out['pu'] * $out['minuti'] / 60, 2);
        } else {
            $out['pt'] = round($out['pu'] * $attivita * $out['qta'], 2);
        }
        $out['imponibile'] = $out['pt'];
        $out['imposta']    = round($out['pt'] * $out['aliquota'] / 100, 2);

        return $out;
    }

    private function parse_decimal($v): float
    {
        if ($v === null || $v === '') return 0.0;
        if (is_numeric($v)) return (float) $v;
        return (float) str_replace(',', '.', $v);
    }

    private function ricalcola_totale_lavorazione(int $id_lavorazione, int $id_azienda): void
    {
        $totale = (float) DB::table('lavorazioni_righe')
            ->where('id_lavorazione', $id_lavorazione)
            ->where('id_azienda', $id_azienda)
            ->sum('imponibile');

        DB::table('lavorazioni')
            ->where('id', $id_lavorazione)
            ->where('id_azienda', $id_azienda)
            ->update(['totale' => $totale]);
    }

    private function normalizza_vagone(array $dati): array
    {
        unset($dati['_token']);

        $campi_numerici = [
            'id_cliente', 'id_sede',
            'intervallo_revisione_mesi',
            'peso_a_vuoto_kg', 'portata_massima_kg', 'lunghezza_metri',
        ];
        foreach ($campi_numerici as $c) {
            if (array_key_exists($c, $dati) && ($dati[$c] === '' || $dati[$c] === null)) {
                $dati[$c] = null;
            }
        }

        $campi_data = ['data_immatricolazione', 'data_ultima_revisione_generale'];
        foreach ($campi_data as $c) {
            if (array_key_exists($c, $dati) && ($dati[$c] === '' || $dati[$c] === null)) {
                $dati[$c] = null;
            }
        }

        $decimali = ['peso_a_vuoto_kg', 'portata_massima_kg', 'lunghezza_metri'];
        foreach ($decimali as $c) {
            if (isset($dati[$c]) && is_string($dati[$c]) && $dati[$c] !== '') {
                $dati[$c] = (float) str_replace(',', '.', $dati[$c]);
            }
        }

        return $dati;
    }

    public function esportaScadenzeRecuperoCrediti(Request $request) {
        $this->is_loggato();
        $utente = session('utente');

        // Parametri di filtro
        $solo_scadute = $request->has('solo_scadute');
        $solo_sollecitate = $request->has('solo_sollecitate');
        $giorni_scadenza = intval($request->input('giorni_scadenza', 0));
        $importo_minimo = floatval($request->input('importo_minimo', 0));

        // Costruisci la query base
        $query = "
            SELECT s.*, 
                c.ragione_sociale, c.indirizzo, c.cap, c.comune, c.provincia, 
                c.cf, c.piva, c.telefono, c.mail_recapito, c.pec,
                d.numero_doc, d.data_doc
            FROM scadenziario s 
            LEFT JOIN clienti c ON s.id_cliente = c.id 
            LEFT JOIN dotes d ON d.id = s.id_dotes 
            WHERE s.id_azienda = ? 
            AND s.tipo_movimento = 'entrata' 
            AND s.importo_pagato < s.importo
        ";

        $params = [$utente->id_azienda];

        // Aggiungi filtri aggiuntivi in base ai parametri
        if ($solo_scadute) {
            $query .= " AND s.data_scadenza < ?";
            $params[] = date('Y-m-d');
        }

        if ($solo_sollecitate) {
            $query .= " AND s.numero_solleciti > 0";
        }

        if ($giorni_scadenza > 0) {
            $data_limite = date('Y-m-d', strtotime("-{$giorni_scadenza} days"));
            $query .= " AND s.data_scadenza <= ?";
            $params[] = $data_limite;
        }

        if ($importo_minimo > 0) {
            $query .= " AND (s.importo - s.importo_pagato) >= ?";
            $params[] = $importo_minimo;
        }

        $query .= " ORDER BY s.data_scadenza ASC";

        // Esegui la query con i parametri
        $scadenze = DB::select($query, $params);

        // Se non ci sono scadenze da esportare, reindirizza con messaggio
        if (empty($scadenze)) {
            session()->flash('message', 'Nessuna scadenza trovata con i criteri selezionati.');
            return Redirect::to('utente/scadenziario');
        }

        // Recupera informazioni azienda
        $azienda = DB::table('aziende')->find($utente->id_azienda);

        // Crea un nuovo foglio Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Imposta intestazioni delle colonne
        $sheet->setCellValue('A1', 'Nr/Codice Cliente');
        $sheet->setCellValue('B1', 'Ragione Sociale');
        $sheet->setCellValue('C1', 'Recapito/Indirizzo');
        $sheet->setCellValue('D1', 'Indirizzo/Sede Legale');
        $sheet->setCellValue('E1', 'Cap');
        $sheet->setCellValue('F1', 'Città');
        $sheet->setCellValue('G1', 'Provincia');
        $sheet->setCellValue('H1', 'Cod. Fiscale');
        $sheet->setCellValue('I1', 'P. Iva');
        $sheet->setCellValue('J1', 'Legale Rappresentante');
        $sheet->setCellValue('K1', 'Cod. Fiscale del Legale Rappresentante');
        $sheet->setCellValue('L1', 'Tel. Recapito');
        $sheet->setCellValue('M1', 'Cellulare 1');
        $sheet->setCellValue('N1', 'Cellulare 2');
        $sheet->setCellValue('O1', 'Fax');
        $sheet->setCellValue('P1', 'E-mail');
        $sheet->setCellValue('Q1', 'PEC');
        $sheet->setCellValue('R1', 'Fattura n.');
        $sheet->setCellValue('S1', 'Data');
        $sheet->setCellValue('T1', 'Scadenza');
        $sheet->setCellValue('U1', 'Importo');
        $sheet->setCellValue('V1', 'Acconto');
        $sheet->setCellValue('W1', 'Debito residuo');

        // Stile per l'intestazione
        $styleArray = [
            'font' => [
                'bold' => true,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'CCCCCC',
                ],
            ],
        ];

        $sheet->getStyle('A1:W1')->applyFromArray($styleArray);

        // Popola i dati
        $row = 2;
        foreach ($scadenze as $scadenza) {
            $sheet->setCellValue('A' . $row, $scadenza->id_cliente);
            $sheet->setCellValue('B' . $row, $scadenza->ragione_sociale ?? '');
            $sheet->setCellValue('C' . $row, $scadenza->indirizzo ?? '');
            $sheet->setCellValue('D' . $row, $scadenza->indirizzo ?? ''); // Stesso indirizzo legale
            $sheet->setCellValue('E' . $row, $scadenza->cap ?? '');
            $sheet->setCellValue('F' . $row, $scadenza->comune ?? '');
            $sheet->setCellValue('G' . $row, $scadenza->provincia ?? '');
            $sheet->setCellValue('H' . $row, $scadenza->cf ?? '');
            $sheet->setCellValue('I' . $row, $scadenza->piva ?? '');
            // Legale rappresentante non disponibile, campo vuoto
            $sheet->setCellValue('J' . $row, '');
            // Codice fiscale del legale rappresentante non disponibile
            $sheet->setCellValue('K' . $row, '');
            $sheet->setCellValue('L' . $row, $scadenza->telefono ?? '');
            $sheet->setCellValue('M' . $row, '');
            $sheet->setCellValue('N' . $row, '');
            $sheet->setCellValue('O' . $row, '');
            $sheet->setCellValue('P' . $row, $scadenza->mail_recapito ?? '');
            $sheet->setCellValue('Q' . $row, $scadenza->pec ?? '');
            $sheet->setCellValue('R' . $row, $scadenza->numero_doc ?? '');

            // Formatta le date
            $dataDoc = $scadenza->data_doc ? \Carbon\Carbon::parse($scadenza->data_doc)->format('d/m/Y') : '';
            $dataScadenza = \Carbon\Carbon::parse($scadenza->data_scadenza)->format('d/m/Y');

            $sheet->setCellValue('S' . $row, $dataDoc);
            $sheet->setCellValue('T' . $row, $dataScadenza);

            // Formatta gli importi
            $sheet->setCellValue('U' . $row, $scadenza->importo);
            $sheet->setCellValue('V' . $row, $scadenza->importo_pagato);
            $sheet->setCellValue('W' . $row, $scadenza->importo - $scadenza->importo_pagato);

            // Formatta celle con numeri in formato valuta
            $sheet->getStyle('U'.$row.':W'.$row)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);

            $row++;
        }

        // Auto dimensiona le colonne
        foreach(range('A','W') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Crea il writer per l'output
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        // Prepara l'intestazione per il download
        $fileName = 'scadenze_recupero_crediti_'.date('Y-m-d').'.xlsx';

        // Redirect output to client browser
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $fileName . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    }
}
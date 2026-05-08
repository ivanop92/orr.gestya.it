<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Http\Request;

class GanttController extends Controller
{
    public function load($id_commessa)
    {
        $this->is_loggato();
        $utente = session('utente');

        $tasks = DB::select('
            SELECT 
                a.id,
                a.titolo as text,
                a.descrizione,
                a.data_inizio as start_date,
                a.data_fine as end_date,
                a.completamento/100 as progress,
                a.id_attivita_precedente as parent,
                CONCAT(u.nome, " ", u.cognome) as responsabile,
                a.id_responsabile
            FROM commesse_attivita a
            LEFT JOIN utenti u ON u.id = a.id_responsabile 
            WHERE a.id_commessa = ? 
            AND a.id_azienda = ?',
            [$id_commessa, $utente->id_azienda]
        );

        $dipendenti = DB::table('utenti')
            ->where('id_azienda', $utente->id_azienda)
            ->where('id_tipologia', 3)
            ->select('id', DB::raw('CONCAT(nome, " ", cognome) as text'))
            ->get();

        return Response::json([
            'data' => $tasks,
            'collections' => [
                'dipendenti' => $dipendenti
            ]
        ]);
    }

    private function parseDate($date)
    {
        if (empty($date)) return null;

        try {
            // Se la data è nel formato JavaScript Date
            if (strpos($date, 'GMT') !== false) {
                return Carbon::parse($date)->format('Y-m-d');
            }

            // Se la data è già nel formato Y-m-d
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return $date;
            }

            // Altrimenti prova a parsare la data
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            \Log::error('Errore nel parsing della data: ' . $e->getMessage());
            return null;
        }
    }

    public function task(Request $request, $id_commessa)
    {
        $this->is_loggato();
        $utente = session('utente');

        $action = $request->input('action');

        try {
            switch ($action) {
                case "add":
                    $task = [
                        'id_commessa' => $id_commessa,
                        'id_azienda' => $utente->id_azienda,
                        'id_utente' => $utente->id,
                        'titolo' => $request->input('text'),
                        'data_inizio' => $this->parseDate($request->input('start_date')),
                        'data_fine' => $this->parseDate($request->input('end_date')),
                        'completamento' => ($request->input('progress', 0) * 100),
                        'id_attivita_precedente' => $request->input('parent'),
                        'id_responsabile' => $request->input('id_responsabile'),
                        'stato' => 'da_iniziare'
                    ];

                    // Log per debug
                    \Log::info('Dati task add:', $task);

                    $id = DB::table('commesse_attivita')->insertGetId($task);
                    return Response::json(['action' => 'inserted', 'tid' => $id]);

                case "update":
                    $task = [
                        'titolo' => $request->input('text'),
                        'data_inizio' => $this->parseDate($request->input('start_date')),
                        'data_fine' => $this->parseDate($request->input('end_date')),
                        'completamento' => ($request->input('progress', 0) * 100),
                        'id_attivita_precedente' => $request->input('parent'),
                        'id_responsabile' => $request->input('id_responsabile')
                    ];

                    // Log per debug
                    \Log::info('Dati task update:', [
                        'task_id' => $request->input('task_id'),
                        'data' => $task
                    ]);

                    DB::table('commesse_attivita')
                        ->where('id', $request->input('task_id'))
                        ->where('id_azienda', $utente->id_azienda)
                        ->update($task);
                    return Response::json(['action' => 'updated']);

                case "delete":
                    DB::table('commesse_attivita')
                        ->where('id', $request->input('task_id'))
                        ->where('id_azienda', $utente->id_azienda)
                        ->delete();
                    return Response::json(['action' => 'deleted']);

                default:
                    return Response::json(['error' => 'Invalid action'], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Errore nel task handler: ' . $e->getMessage());
            return Response::json(['error' => $e->getMessage()], 500);
        }
    }


    public function exportPDF(Request $request, $id_commessa)
    {
        $this->is_loggato();
        $utente = session('utente');

        try {
            // Recupera commessa
            $commessa = DB::table('commesse')
                ->where('id', $id_commessa)
                ->where('id_azienda', $utente->id_azienda)
                ->first();

            if(!$commessa) {
                return response()->json(['error' => 'Commessa non trovata'], 404);
            }

            // Recupera attività
            $attivita = DB::select('
                SELECT 
                    a.*,
                    CONCAT(u.nome, " ", u.cognome) as responsabile
                FROM commesse_attivita a
                LEFT JOIN utenti u ON u.id = a.id_responsabile 
                WHERE a.id_commessa = ? 
                AND a.id_azienda = ?
                ORDER BY a.data_inizio ASC',
                [$id_commessa, $utente->id_azienda]
            );

            // Prepara i dati per il template
            $data = [
                'commessa' => $commessa,
                'attivita' => $attivita,
                'data_stampa' => Carbon::now()->format('d/m/Y H:i'),
                'utente' => $utente
            ];

            // Genera il PDF usando mPDF
            $mpdf = new Mpdf([
                'format' => 'A4-L',
                'margin_left' => 10,
                'margin_right' => 10,
                'margin_top' => 15,
                'margin_bottom' => 15,
                'margin_header' => 10,
                'margin_footer' => 10
            ]);

            // Imposta header
            $mpdf->SetHTMLHeader('
                <div style="text-align: center; border-bottom: 1px solid #ccc; padding-bottom: 5px;">
                    <h2>Gantt Commessa: ' . $commessa->codice_commessa . '</h2>
                    <p>Data stampa: ' . $data['data_stampa'] . '</p>
                </div>
            ');

            // Imposta footer
            $mpdf->SetHTMLFooter('
                <div style="text-align: center; border-top: 1px solid #ccc; padding-top: 5px;">
                    <p>Pagina {PAGENO} di {nb}</p>
                </div>
            ');

            // Genera contenuto HTML
            $html = view('utente.commesse.gantt_pdf', $data)->render();

            // Scrivi il contenuto nel PDF
            $mpdf->WriteHTML($html);

            // Output del PDF
            return response($mpdf->Output('', 'S'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'attachment; filename="gantt_' . $commessa->codice_commessa . '_' . Carbon::now()->format('Ymd') . '.pdf"');

        } catch (\Exception $e) {
            \Log::error('Errore nella generazione del PDF: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    private function is_loggato(){
        if (!session()->has('utente'))
            return Redirect::to('admin/login')->send();
    }
}
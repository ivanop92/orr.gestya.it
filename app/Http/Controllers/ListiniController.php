<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ListiniController extends Controller
{
    /**
     * Visualizza l'elenco dei listini
     */
    public function index(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Gestione aggiunta listino
        if (isset($dati['aggiungi'])) {
            unset($dati['aggiungi']);

            // Aggiungi id_azienda
            $dati['id_azienda'] = $utente->id_azienda;

            // Imposta attivo a 1 se selezionato
            $dati['attivo'] = isset($dati['attivo']) ? 1 : 0;

            // Inserisci il nuovo listino
            DB::table('listini')->insert($dati);

            return Redirect::to('utente/listini')->with('success', 'Listino aggiunto con successo!');
        }

        // Gestione modifica listino
        if (isset($dati['modifica'])) {
            unset($dati['modifica']);

            // Imposta attivo a 1 se selezionato, altrimenti 0
            $dati['attivo'] = isset($dati['attivo']) ? 1 : 0;

            // Aggiorna il listino
            DB::table('listini')
                ->where('id', $dati['id'])
                ->where('id_azienda', $utente->id_azienda)
                ->update($dati);

            return Redirect::to('utente/listini')->with('success', 'Listino modificato con successo!');
        }

        // Gestione eliminazione listino
        if (isset($dati['elimina'])) {
            // Controlla se ci sono articoli o clienti associati
            $articoli_count = DB::table('listini_articoli')
                ->where('id_listino', $dati['id'])
                ->count();

            $clienti_count = DB::table('listini_clienti')
                ->where('id_listino', $dati['id'])
                ->count();

            if ($articoli_count > 0 || $clienti_count > 0) {
                return Redirect::to('utente/listini')
                    ->with('error', 'Impossibile eliminare il listino: ci sono articoli o clienti associati');
            }

            // Elimina il listino
            DB::table('listini')
                ->where('id', $dati['id'])
                ->where('id_azienda', $utente->id_azienda)
                ->delete();

            return Redirect::to('utente/listini')->with('success', 'Listino eliminato con successo!');
        }

        // Recupera tutti i listini per l'azienda corrente
        $listini = DB::select(
            'SELECT l.*, 
                   (SELECT COUNT(*) FROM listini_articoli WHERE id_listino = l.id) as num_articoli,
                   (SELECT COUNT(*) FROM listini_clienti WHERE id_listino = l.id) as num_clienti
             FROM listini l
             WHERE l.id_azienda = ?
             ORDER BY l.priorita DESC, l.codice ASC',
            [$utente->id_azienda]
        );

        // Recupera tutti i listini per l'azienda corrente
        $listini = DB::select(
            'SELECT l.*, 
               (SELECT COUNT(*) FROM listini_articoli WHERE id_listino = l.id) as num_articoli,
               (SELECT COUNT(*) FROM listini_clienti WHERE id_listino = l.id) as num_clienti
         FROM listini l
         WHERE l.id_azienda = ?
         ORDER BY l.priorita DESC, l.codice ASC',
            [$utente->id_azienda]
        );

        // Recupera tutti i clienti con i relativi listini associati
        // Recupera SOLO i clienti che hanno almeno un listino associato
        $clienti_con_listini = DB::select(
            'SELECT DISTINCT c.id, c.ragione_sociale, c.nome, c.cognome, c.piva, c.comune
     FROM clienti c
     JOIN listini_clienti lc ON c.id = lc.id_cliente
     JOIN listini l ON l.id = lc.id_listino
     WHERE c.id_azienda = ?
     AND l.id_azienda = ?
     ORDER BY c.ragione_sociale, c.nome, c.cognome',
            [$utente->id_azienda, $utente->id_azienda]
        );

        // Per ogni cliente, recupera i listini associati
        foreach ($clienti_con_listini as $cliente) {
            $cliente->listini = DB::select(
                'SELECT lc.*, l.codice as codice_listino, l.descrizione as descrizione_listino, l.attivo as listino_attivo
             FROM listini_clienti lc
             JOIN listini l ON l.id = lc.id_listino
             WHERE lc.id_cliente = ?
             AND l.id_azienda = ?
             ORDER BY l.priorita DESC, l.codice ASC',
                [$cliente->id, $utente->id_azienda]
            );
        }


        return View::make('utente.listini.index', compact('utente', 'listini', 'clienti_con_listini'));
    }

    /**
     * Visualizza il form per aggiungere un nuovo listino
     */
    public function create()
    {
        $this->is_loggato();
        $utente = session('utente');

        return View::make('utente.listini.create', compact('utente'));
    }

    /**
     * Visualizza i dettagli di un listino specifico
     */
    public function dettaglio($id)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Recupera il listino
        $listino = DB::table('listini')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$listino) {
            return Redirect::to('utente/listini')->with('error', 'Listino non trovato');
        }

        // Recupera gli articoli associati al listino
        $articoli_listino = DB::select(
            'SELECT la.*, a.codice_articolo, a.titolo as nome_articolo, a.prezzo as prezzo_base, a.um 
             FROM listini_articoli la 
             JOIN articoli a ON la.id_articolo = a.id 
             WHERE la.id_listino = ? 
             ORDER BY a.titolo ASC',
            [$id]
        );

        // Recupera i clienti associati al listino
        $clienti_listino = DB::select(
            'SELECT lc.*, c.ragione_sociale, c.nome, c.cognome, c.piva, c.comune 
             FROM listini_clienti lc 
             JOIN clienti c ON lc.id_cliente = c.id 
             WHERE lc.id_listino = ? 
             ORDER BY c.ragione_sociale ASC, c.nome ASC',
            [$id]
        );

        // Recupera tutti gli articoli disponibili per l'azienda
        $articoli = DB::table('articoli')
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('titolo', 'asc')
            ->get();

        // Recupera tutti i clienti disponibili per l'azienda
        $clienti = DB::table('clienti')
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('ragione_sociale', 'asc')
            ->get();

        return View::make('utente.listini.dettaglio', compact(
            'utente', 'listino', 'articoli_listino', 'clienti_listino', 'articoli', 'clienti'
        ));
    }

    /**
     * Aggiunge un articolo al listino
     */
    public function aggiungi_articolo(Request $request, $id_listino)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Verifica che il listino esista e appartenga all'azienda
        $listino = DB::table('listini')
            ->where('id', $id_listino)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$listino) {
            return Redirect::to('utente/listini')->with('error', 'Listino non trovato');
        }

        if (isset($dati['aggiungi'])) {
            unset($dati['aggiungi']);

            // Verifica che l'articolo esista già in questo listino con la stessa quantità minima
            $articolo_esistente = DB::table('listini_articoli')
                ->where('id_listino', $id_listino)
                ->where('id_articolo', $dati['id_articolo'])
                ->where('quantita_minima', $dati['quantita_minima'])
                ->first();

            if ($articolo_esistente) {
                // Aggiorna anziché inserire
                DB::table('listini_articoli')
                    ->where('id', $articolo_esistente->id)
                    ->update([
                        'prezzo' => $dati['prezzo'],
                        'sconto_percentuale' => $dati['sconto_percentuale'],
                        'data_inizio' => $dati['data_inizio'],
                        'data_fine' => $dati['data_fine']
                    ]);
            } else {
                // Inserisci nuovo articolo nel listino
                $dati['id_listino'] = $id_listino;
                DB::table('listini_articoli')->insert($dati);
            }

            return Redirect::to('utente/listini/dettaglio/'.$id_listino)
                ->with('success', 'Articolo aggiunto al listino con successo!');
        }

        // Recupera tutti gli articoli disponibili per l'azienda
        $articoli = DB::table('articoli')
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('titolo', 'asc')
            ->get();

        // CAMBIAMENTO: Usa la vista aggiungi_articolo
        return View::make('utente.listini.aggiungi_articolo', compact('utente', 'listino', 'articoli'));
    }



    /**
     * Rimuove un articolo dal listino
     */
    public function elimina_articolo(Request $request, $id_listino, $id_articolo_listino)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Verifica che il listino esista e appartenga all'azienda
        $listino = DB::table('listini')
            ->where('id', $id_listino)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$listino) {
            return Redirect::to('utente/listini')->with('error', 'Listino non trovato');
        }

        // Elimina l'articolo dal listino
        DB::table('listini_articoli')
            ->where('id', $id_articolo_listino)
            ->delete();

        return Redirect::to('utente/listini/dettaglio/'.$id_listino)
            ->with('success', 'Articolo rimosso dal listino con successo!');
    }

    /**
     * Aggiunge un cliente al listino
     */
    public function aggiungi_cliente(Request $request, $id_listino)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Verifica che il listino esista e appartenga all'azienda
        $listino = DB::table('listini')
            ->where('id', $id_listino)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$listino) {
            return Redirect::to('utente/listini')->with('error', 'Listino non trovato');
        }

        if (isset($dati['aggiungi'])) {
            unset($dati['aggiungi']);

            // Verifica che il cliente esista già in questo listino
            $cliente_esistente = DB::table('listini_clienti')
                ->where('id_listino', $id_listino)
                ->where('id_cliente', $dati['id_cliente'])
                ->first();

            if ($cliente_esistente) {
                // Aggiorna anziché inserire
                DB::table('listini_clienti')
                    ->where('id', $cliente_esistente->id)
                    ->update([
                        'data_inizio' => $dati['data_inizio'],
                        'data_fine' => $dati['data_fine'],
                        'note' => $dati['note']
                    ]);
            } else {
                // Inserisci nuovo cliente nel listino
                $dati['id_listino'] = $id_listino;
                DB::table('listini_clienti')->insert($dati);
            }

            return Redirect::to('utente/listini/dettaglio/'.$id_listino)
                ->with('success', 'Cliente associato al listino con successo!');
        }

        // Recupera tutti i clienti disponibili per l'azienda
        $clienti = DB::table('clienti')
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('ragione_sociale', 'asc')
            ->get();

        // CAMBIAMENTO: Usa la vista aggiungi_cliente
        return View::make('utente.listini.aggiungi_cliente', compact('utente', 'listino', 'clienti'));
    }

    /**
     * Rimuove un cliente dal listino
     */
    public function elimina_cliente(Request $request, $id_listino, $id_cliente_listino)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Verifica che il listino esista e appartenga all'azienda
        $listino = DB::table('listini')
            ->where('id', $id_listino)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$listino) {
            return Redirect::to('utente/listini')->with('error', 'Listino non trovato');
        }

        // Elimina il cliente dal listino
        DB::table('listini_clienti')
            ->where('id', $id_cliente_listino)
            ->delete();

        return Redirect::to('utente/listini/dettaglio/'.$id_listino)
            ->with('success', 'Cliente rimosso dal listino con successo!');
    }

    /**
     * Importa prezzi da un file CSV
     */
    public function importa_csv(Request $request, $id_listino)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Verifica che il listino esista e appartenga all'azienda
        $listino = DB::table('listini')
            ->where('id', $id_listino)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$listino) {
            return Redirect::to('utente/listini')->with('error', 'Listino non trovato');
        }

        if ($request->hasFile('file_csv')) {
            $file = $request->file('file_csv');

            // Apri il file CSV
            if (($handle = fopen($file->getRealPath(), "r")) !== FALSE) {
                // Leggi la prima riga (intestazioni)
                $headers = fgetcsv($handle, 1000, ",");

                // Converti le intestazioni in minuscolo e normalizza
                $headers = array_map(function($header) {
                    return strtolower(trim($header));
                }, $headers);

                // Trova gli indici delle colonne necessarie
                $codice_index = array_search('codice_articolo', $headers);
                $prezzo_index = array_search('prezzo', $headers);
                $sconto_index = array_search('sconto_percentuale', $headers);
                $qta_min_index = array_search('quantita_minima', $headers);

                if ($codice_index === false || $prezzo_index === false) {
                    return Redirect::to('utente/listini/dettaglio/'.$id_listino)
                        ->with('error', 'Il file CSV deve contenere almeno le colonne "codice_articolo" e "prezzo"');
                }

                $count_added = 0;
                $count_updated = 0;
                $count_skipped = 0;

                // Leggi le righe di dati
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Salta righe vuote o incomplete
                    if (!isset($data[$codice_index]) || !isset($data[$prezzo_index]) || empty($data[$codice_index])) {
                        $count_skipped++;
                        continue;
                    }

                    $codice_articolo = trim($data[$codice_index]);
                    $prezzo = floatval(str_replace(',', '.', $data[$prezzo_index]));

                    // Valori opzionali
                    $sconto = $sconto_index !== false && isset($data[$sconto_index]) ?
                        floatval(str_replace(',', '.', $data[$sconto_index])) : 0;
                    $qta_min = $qta_min_index !== false && isset($data[$qta_min_index]) ?
                        intval($data[$qta_min_index]) : 1;

                    // Trova l'articolo dal codice
                    $articolo = DB::table('articoli')
                        ->where('codice_articolo', $codice_articolo)
                        ->where('id_azienda', $utente->id_azienda)
                        ->first();

                    if (!$articolo) {
                        $count_skipped++;
                        continue;
                    }

                    // Verifica se l'articolo esiste già in questo listino
                    $articolo_listino = DB::table('listini_articoli')
                        ->where('id_listino', $id_listino)
                        ->where('id_articolo', $articolo->id)
                        ->where('quantita_minima', $qta_min)
                        ->first();

                    $data_record = [
                        'id_listino' => $id_listino,
                        'id_articolo' => $articolo->id,
                        'prezzo' => $prezzo,
                        'sconto_percentuale' => $sconto,
                        'quantita_minima' => $qta_min
                    ];

                    if ($articolo_listino) {
                        // Aggiorna il record esistente
                        DB::table('listini_articoli')
                            ->where('id', $articolo_listino->id)
                            ->update($data_record);
                        $count_updated++;
                    } else {
                        // Inserisci nuovo record
                        DB::table('listini_articoli')->insert($data_record);
                        $count_added++;
                    }
                }

                fclose($handle);

                return Redirect::to('utente/listini/dettaglio/'.$id_listino)
                    ->with('success', "Importazione completata: $count_added articoli aggiunti, $count_updated aggiornati, $count_skipped saltati");
            } else {
                return Redirect::to('utente/listini/dettaglio/'.$id_listino)
                    ->with('error', "Impossibile aprire il file");
            }
        }

        return View::make('utente.listini.importa_csv', compact('utente', 'listino'));
    }

    /**
     * Scarica un esempio di file CSV per l'importazione prezzi
     */
    public function scarica_esempio_csv()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=esempio_importazione_prezzi.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['codice_articolo', 'prezzo', 'sconto_percentuale', 'quantita_minima']);
            fputcsv($file, ['ART001', '100.00', '0', '1']);
            fputcsv($file, ['ART002', '200.00', '5', '1']);
            fputcsv($file, ['ART002', '180.00', '0', '10']);
            fputcsv($file, ['ART003', '50.00', '10', '1']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Funzione per verificare se l'utente è loggato
     */
    private function is_loggato()
    {
        if (!session()->has('utente')) {
            return Redirect::to('admin/login')->send();
        }
    }

    /**
     * Funzione per ottenere il prezzo di un articolo per un cliente specifico
     * Da richiamare nelle altre parti dell'applicazione
     */
    public static function get_prezzo_cliente($id_articolo, $id_cliente, $quantita = 1)
    {
        // Recupera l'azienda dal cliente
        $cliente = DB::table('clienti')->find($id_cliente);
        if (!$cliente) {
            return null;
        }

        $id_azienda = $cliente->id_azienda;

        // Recupera il prezzo base dell'articolo
        $articolo = DB::table('articoli')->where('id', $id_articolo)->first();
        if (!$articolo) {
            return null;
        }

        $prezzo_base = $articolo->prezzo;

        // Cerca listini attivi associati al cliente
        $oggi = date('Y-m-d');

        // 1. Listini specifici per il cliente
        $listini_cliente = DB::select(
            'SELECT l.id
            FROM listini l
            JOIN listini_clienti lc ON l.id = lc.id_listino
            WHERE l.id_azienda = ?
            AND lc.id_cliente = ?
            AND l.attivo = 1
            AND (l.data_inizio IS NULL OR l.data_inizio <= ?)
            AND (l.data_fine IS NULL OR l.data_fine >= ?)
            ORDER BY l.priorita DESC',
            [$id_azienda, $id_cliente, $oggi, $oggi]
        );

        // Cerca il prezzo nei listini trovati
        foreach ($listini_cliente as $listino) {
            $prezzo_listino = DB::table('listini_articoli')
                ->where('id_listino', $listino->id)
                ->where('id_articolo', $id_articolo)
                ->where('quantita_minima', '<=', $quantita)
                ->orderBy('quantita_minima', 'desc')
                ->first();

            if ($prezzo_listino) {
                // Calcola il prezzo finale considerando lo sconto percentuale
                if ($prezzo_listino->sconto_percentuale > 0) {
                    return $prezzo_listino->prezzo * (1 - ($prezzo_listino->sconto_percentuale / 100));
                }
                return $prezzo_listino->prezzo;
            }
        }

        // Se non troviamo un prezzo specifico, ritorna il prezzo base
        return $prezzo_base;
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Mpdf\Mpdf;

class MappingBarcodeController extends Controller
{
    /**
     * Verifica che l'utente sia loggato
     */
    public function is_loggato()
    {
        if (!session()->has('utente')) return Redirect::to('admin/login')->send();
    }

    /**
     * Visualizza la pagina di ricezione con barcode
     */
    public function index(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Gestione del salvataggio mapping - questo avviene quando il form di mappatura è inviato
        if (isset($dati['salva_mapping'])) {
            // Chiamiamo il metodo salvaMappingBarcode e otteniamo il risultato
            $risultato = $this->salvaMappingBarcode($request);

            // Se vogliamo utilizzare un redirect interno, dobbiamo ritornare il risultato
            return $risultato;
        }

        // Recupera i fornitori dell'azienda (clienti con tipologia 1)
        $fornitori = DB::select('SELECT * from fornitori where id_azienda = ? order by ragione_sociale asc', [$utente->id_azienda]);

        // Recupera i magazzini dell'azienda
        $magazzini = DB::table('mg')
            ->where('id_azienda', $utente->id_azienda)
            ->get();

        // Recupera le commesse attive
        $commesse = DB::table('commesse')
            ->where('id_azienda', $utente->id_azienda)
            ->whereIn('stato', ['aperta', 'in_corso'])
            ->get();

        // Carica gli ultimi 10 carichi a magazzino
        $ultimi_carichi = DB::select('
        SELECT m.*, a.titolo, a.codice_articolo, mg.descrizione as magazzino_descrizione
        FROM mgmov m
        LEFT JOIN articoli a ON a.id = m.id_articolo
        LEFT JOIN mg ON mg.id = m.id_mg
        WHERE m.id_azienda = ? 
        AND m.qta > 0
        ORDER BY m.datamov DESC 
        LIMIT 10
    ', [$utente->id_azienda]);

        // Carica tutti gli articoli per la mappatura
        $articoli = DB::table('articoli')
            ->where('id_azienda', $utente->id_azienda)
            ->select('id', 'codice_articolo', 'titolo')
            ->orderBy('titolo')
            ->get();

        return View::make('utente.ricezione_barcode', compact('utente', 'fornitori', 'magazzini', 'commesse', 'ultimi_carichi', 'articoli'));
    }

    /**
     * Gestisce le richieste AJAX per decodificare il barcode
     */
    public function decode_barcode(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        \Log::info('Richiesta decode_barcode:', $request->all());

        if (!$request->has('barcode')) {
            return response()->json(['success' => false, 'message' => 'Barcode non specificato']);
        }

        $barcode = $request->input('barcode');
        $id_fornitore = $request->input('id_fornitore');

        try {
            // Decodifica il barcode GS1-128
            $dati_barcode = $this->parse_gs1_barcode($barcode);
            $gtin = $dati_barcode['01'] ?? null;

            \Log::info('Barcode decodificato:', ['barcode' => $barcode, 'dati' => $dati_barcode, 'gtin' => $gtin]);

            // Cerca l'articolo nel database usando il codice articolo
            $articolo = null;
            if ($gtin) {
                $articolo = DB::table('articoli')
                    ->where('codice_articolo', $gtin)
                    ->where('id_azienda', $utente->id_azienda)
                    ->first();

                \Log::info('Ricerca per GTIN:', ['gtin' => $gtin, 'trovato' => !is_null($articolo)]);
            }

            // Se non è stato trovato, prova a cercarlo per barcode
            if (!$articolo) {
                $articolo = DB::table('articoli')
                    ->where('barcode', $barcode)
                    ->where('id_azienda', $utente->id_azienda)
                    ->first();

                \Log::info('Ricerca per barcode:', ['barcode' => $barcode, 'trovato' => !is_null($articolo)]);
            }

            // Se ancora non trovato, verifica se esiste un mapping diretto
            if (!$articolo && $id_fornitore) {
                $mapping = DB::table('mapping_barcode_fornitori')
                    ->where('id_fornitore', $id_fornitore)
                    ->where('barcode_fornitore', $barcode)
                    ->where('id_azienda', $utente->id_azienda)
                    ->first();

                \Log::info('Ricerca mapping diretto:', ['id_fornitore' => $id_fornitore, 'trovato' => !is_null($mapping)]);

                if ($mapping) {
                    $articolo = DB::table('articoli')
                        ->where('id', $mapping->id_articolo)
                        ->where('id_azienda', $utente->id_azienda)
                        ->first();
                }
            }

            // Se ancora non trovato, verifica se esiste un mapping con lo stesso GTIN
            if (!$articolo && $gtin && $id_fornitore) {
                // Cerca altri barcode dello stesso fornitore con lo stesso GTIN
                $mappings_esistenti = DB::select('
                    SELECT m.id_articolo 
                    FROM mapping_barcode_fornitori m
                    JOIN articoli a ON a.id = m.id_articolo
                    WHERE m.id_fornitore = ?
                    AND m.id_azienda = ?
                    AND a.codice_articolo = ?
                    LIMIT 1
                ', [$id_fornitore, $utente->id_azienda, $gtin]);

                \Log::info('Ricerca mapping per GTIN:', ['gtin' => $gtin, 'trovati' => count($mappings_esistenti)]);

                if (count($mappings_esistenti) > 0) {
                    $id_articolo = $mappings_esistenti[0]->id_articolo;
                    $articolo = DB::table('articoli')
                        ->where('id', $id_articolo)
                        ->where('id_azienda', $utente->id_azienda)
                        ->first();

                    // Crea automaticamente il mapping per questo barcode specifico
                    if ($articolo) {
                        \Log::info('Creazione automatica mapping per barcode con stesso GTIN');

                        DB::table('mapping_barcode_fornitori')->insert([
                            'id_azienda' => $utente->id_azienda,
                            'id_fornitore' => $id_fornitore,
                            'barcode_fornitore' => $barcode,
                            'id_articolo' => $articolo->id,
                            'note' => 'Mappatura automatica basata su GTIN ' . $gtin,
                            'created_at' => now(),
                            'updated_at' => now()
                        ]);
                    }
                }
            }

            if (!$articolo) {
                // Articolo non trovato, proponi creazione o associazione
                \Log::info('Articolo non trovato, si propone la mappatura');

                return response()->json([
                    'success' => false,
                    'message' => 'Articolo non trovato',
                    'barcode_data' => $dati_barcode,
                    'show_mapping' => true
                ]);
            }

            // Prepara i dati da ritornare
            $result = [
                'success' => true,
                'articolo' => $articolo,
                'barcode_data' => $dati_barcode,
                'barcode_originale' => $barcode,
                'lotto' => $dati_barcode['10'] ?? null,          // 10 è l'AI per il lotto
                'data_scadenza' => isset($dati_barcode['17']) ?  // 17 è l'AI per la data di scadenza (YYMMDD)
                    $this->format_expiry_date($dati_barcode['17']) : null,
                'quantita' => $dati_barcode['30'] ?? 1,          // 30 è l'AI per la quantità
            ];

            \Log::info('Risposta decode_barcode:', $result);

            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error('Errore in decode_barcode:', ['message' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return response()->json(['success' => false, 'message' => 'Errore: ' . $e->getMessage()]);
        }
    }

    /**
     * Salva un nuovo mapping tra barcode fornitore e articolo
     * e crea immediatamente un carico a magazzino
     */
    public function salvaMappingBarcode(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        \Log::info('Richiesta salvaMappingBarcode:', $request->all());

        // Validazione
        $validated = $request->validate([
            'id_fornitore' => 'required|numeric',
            'barcode_fornitore' => 'required|string',
            'id_articolo' => 'required|numeric',
            'id_magazzino' => 'nullable|numeric',
        ]);

        try {
            DB::beginTransaction();

            \Log::info('Inizio transazione DB');

            // Verifica se esiste già un mapping per questo barcode e fornitore
            $mapping_esistente = DB::table('mapping_barcode_fornitori')
                ->where('id_fornitore', $request->id_fornitore)
                ->where('barcode_fornitore', $request->barcode_fornitore)
                ->where('id_azienda', $utente->id_azienda)
                ->first();

            if ($mapping_esistente) {
                // Aggiorna il mapping esistente
                \Log::info('Aggiornamento mapping esistente ID: ' . $mapping_esistente->id);

                DB::table('mapping_barcode_fornitori')
                    ->where('id', $mapping_esistente->id)
                    ->update([
                        'id_articolo' => $request->id_articolo,
                        'note' => $request->note ?? null,
                        'updated_at' => now()
                    ]);
            } else {
                // Crea un nuovo mapping
                \Log::info('Creazione nuovo mapping');

                DB::table('mapping_barcode_fornitori')->insert([
                    'id_azienda' => $utente->id_azienda,
                    'id_fornitore' => $request->id_fornitore,
                    'barcode_fornitore' => $request->barcode_fornitore,
                    'id_articolo' => $request->id_articolo,
                    'note' => $request->note ?? null,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Recupera l'articolo
            $articolo = DB::table('articoli')
                ->where('id', $request->id_articolo)
                ->where('id_azienda', $utente->id_azienda)
                ->first();

            // Decodifica il barcode per ottenere informazioni come lotto e data di scadenza
            $dati_barcode = $this->parse_gs1_barcode($request->barcode_fornitore);
            \Log::info('Dati barcode decodificati:', $dati_barcode ?: []);

            // Determina il magazzino di default se non specificato
            $id_magazzino = $request->id_magazzino ?? DB::table('mg')
                ->where('id_azienda', $utente->id_azienda)
                ->value('id');

            \Log::info('Magazzino selezionato: ' . $id_magazzino);

            // Prepara i dati per il movimento di magazzino
            $quantita = $dati_barcode['30'] ?? 1;
            $lotto = $dati_barcode['10'] ?? 'LOTTO-' . date('Ymd');
            $scadenza = isset($dati_barcode['17']) ?
                $this->format_expiry_date($dati_barcode['17']) : null;

            \Log::info('Preparazione movimento magazzino:', [
                'articolo' => $request->id_articolo,
                'magazzino' => $id_magazzino,
                'quantita' => $quantita,
                'lotto' => $lotto,
                'scadenza' => $scadenza
            ]);

            // Crea un movimento di magazzino (carico)
            $movimento_id = DB::table('mgmov')->insertGetId([
                'id_articolo' => $request->id_articolo,
                'id_utente' => $utente->id,
                'id_azienda' => $utente->id_azienda,
                'id_mg' => $id_magazzino,
                'datamov' => now(),
                'lotto' => $lotto,
                'scadenza_lotto' => $scadenza,
                'qta' => $quantita,
                'car' => 1,                          // Indica un carico
                'sca' => 0,                          // Non è uno scarico
                'ret' => 0,                          // Non è una rettifica
                'causale' => 'Carico da mappatura barcode fornitore',
                'barcode' => $request->barcode_fornitore, // Barcode originale del fornitore
            ]);

            \Log::info('Movimento magazzino creato con ID: ' . $movimento_id);

            // Aggiorna la giacenza totale dell'articolo
            $affected = DB::update('UPDATE articoli SET giacenza = (SELECT SUM(qta) FROM mgmov WHERE id_articolo = ?) WHERE id = ?',
                [$request->id_articolo, $request->id_articolo]);

            \Log::info('Aggiornamento giacenza articolo: ' . $affected . ' righe modificate');

            DB::commit();
            \Log::info('Transazione completata con successo');

            // Utilizza la route nominata per il redirect - assicurati che la route esista!
            return redirect()->route('utente.ricezione_barcode')
                ->with('success', 'Associazione barcode creata e articolo caricato a magazzino con successo');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Errore in salvaMappingBarcode:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->route('utente.ricezione_barcode')
                ->with('error', 'Errore: ' . $e->getMessage());
        }
    }

    /**
     * Gestisce il carico a magazzino con barcode
     */
    public function carico_barcode(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        \Log::info('Richiesta carico_barcode:', $request->all());

        // Validazione
        $validated = $request->validate([
            'id_articolo' => 'required|numeric',
            'quantita' => 'required|numeric|min:0.01',
            'id_magazzino' => 'required|numeric',
            'lotto' => 'required|string',
            'barcode_originale' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            // Recupera l'articolo
            $articolo = DB::table('articoli')
                ->where('id', $request->id_articolo)
                ->where('id_azienda', $utente->id_azienda)
                ->first();

            if (!$articolo) {
                DB::rollBack();
                return response()->json(['success' => false, 'message' => 'Articolo non trovato']);
            }

            \Log::info('Articolo trovato:', (array)$articolo);

            // Crea il movimento di magazzino
            $id_movimento = DB::table('mgmov')->insertGetId([
                'id_articolo' => $request->id_articolo,
                'id_utente' => $utente->id,
                'id_azienda' => $utente->id_azienda,
                'id_mg' => $request->id_magazzino,
                'datamov' => now(),
                'lotto' => $request->lotto,
                'scadenza_lotto' => $request->data_scadenza,
                'qta' => $request->quantita,
                'car' => 1,                          // Indica un carico
                'sca' => 0,                          // Non è uno scarico
                'ret' => 0,                          // Non è una rettifica
                'causale' => $request->causale ?? 'Carico da barcode fornitore',
                'barcode' => $request->barcode_originale, // Salva il barcode originale del fornitore
            ]);

            \Log::info('Movimento magazzino creato con ID: ' . $id_movimento);

            // Aggiorna la giacenza totale dell'articolo
            $affected = DB::update('UPDATE articoli SET giacenza = (SELECT IFNULL(SUM(qta), 0) FROM mgmov WHERE id_articolo = ?) WHERE id = ?',
                [$request->id_articolo, $request->id_articolo]);

            \Log::info('Aggiornamento giacenza articolo: ' . $affected . ' righe modificate');

            DB::commit();
            \Log::info('Transazione completata con successo');

            return response()->json([
                'success' => true,
                'message' => 'Carico a magazzino effettuato con successo',
                'id_movimento' => $id_movimento,
                'articolo' => $articolo,
                'barcode' => $request->barcode_originale // Ritorna il barcode originale per l'etichetta
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Errore durante il carico a magazzino: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['success' => false, 'message' => 'Errore: ' . $e->getMessage()]);
        }
    }

    /**
     * Genera etichetta con il barcode del fornitore
     */
    public function stampa_etichetta_barcode(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Validazione
        $request->validate([
            'articolo_id' => 'required|numeric',
            'barcode' => 'required|string',
            'lotto' => 'required|string',
            'num_copie' => 'required|numeric|min:1|max:100'
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
        return $mpdf->Output($filename, \Mpdf\Output\Destination::DOWNLOAD);
    }

    /**
     * Parse GS1-128 barcode
     */
    private function parse_gs1_barcode($barcode)
    {
        // Rimuovi eventuali caratteri di controllo o spazi
        $barcode = trim($barcode);

        // Verifica se il barcode inizia con una parentesi (tipico dei GS1)
        if (substr($barcode, 0, 1) != '(') {
            // Prova a vedere se è un barcode GS1 senza parentesi (solo numeri)
            if (preg_match('/^[\d]+$/', $barcode)) {
                // Se è un EAN-13 o simile, ritorna solo il codice
                return ['01' => $barcode];
            }

            // Prova a vedere se è un codice GS1 senza parentesi ma con Application Identifiers
            if (strlen($barcode) >= 16 && substr($barcode, 0, 2) == '01') {
                $result = [];
                $i = 0;

                while ($i < strlen($barcode)) {
                    // Leggi l'AI (sempre 2 o 4 cifre)
                    $ai = substr($barcode, $i, 2);
                    $i += 2;

                    // Determina la lunghezza del valore in base all'AI
                    $length = 0;

                    switch ($ai) {
                        case '01': // GTIN
                            $length = 14;
                            break;
                        case '10': // Lotto (variabile)
                            // Cerca il prossimo AI
                            $next_ai_pos = $this->find_next_ai($barcode, $i);
                            $length = $next_ai_pos ? $next_ai_pos - $i : strlen($barcode) - $i;
                            break;
                        case '17': // Data scadenza
                            $length = 6;
                            break;
                        case '30': // Quantità (variabile)
                            // Cerca il prossimo AI
                            $next_ai_pos = $this->find_next_ai($barcode, $i);
                            $length = $next_ai_pos ? $next_ai_pos - $i : strlen($barcode) - $i;
                            break;
                        default:
                            // Per altri AI, considera fino al prossimo AI
                            $next_ai_pos = $this->find_next_ai($barcode, $i);
                            $length = $next_ai_pos ? $next_ai_pos - $i : strlen($barcode) - $i;
                    }

                    // Estrai il valore
                    $value = substr($barcode, $i, $length);
                    $result[$ai] = $value;

                    $i += $length;
                }

                return $result;
            }

            return false;
        }

        // Estrai gli AI con il pattern standard (01)12345678901234
        $result = [];
        $pattern = '/\((\d{2,4})\)([^\(]+)/';
        preg_match_all($pattern, $barcode, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $ai = $match[1];    // Application Identifier
            $value = $match[2]; // Valore
            $result[$ai] = $value;
        }

        return $result;
    }

    /**
     * Trova la posizione del prossimo AI in un barcode GS1 senza parentesi
     */
    private function find_next_ai($barcode, $start_pos)
    {
        $ai_list = ['00', '01', '02', '10', '11', '12', '13', '15', '17', '20', '21', '30', '37', '90', '91', '92', '93', '94', '95', '96', '97', '98', '99'];

        for ($i = $start_pos + 1; $i < strlen($barcode) - 1; $i++) {
            $possible_ai = substr($barcode, $i, 2);
            if (in_array($possible_ai, $ai_list)) {
                return $i;
            }
        }

        return false;
    }

    /**
     * Converte la data di scadenza dal formato YYMMDD a Y-m-d
     */
    private function format_expiry_date($date)
    {
        if(strlen($date) != 6) return null;

        $year = substr($date, 0, 2);
        $month = substr($date, 2, 2);
        $day = substr($date, 4, 2);

        // Converti in anno a 4 cifre (assumendo che gli anni 00-49 siano 2000-2049, 50-99 siano 1950-1999)
        $year = (int)$year;
        if($year < 50) {
            $year += 2000;
        } else {
            $year += 1900;
        }

        return sprintf('%04d-%02d-%02d', $year, $month, $day);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticoloController extends Controller
{
    public function dettaglio_articolo($id, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Recupera l'articolo
        $articolo = DB::table('articoli')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$articolo) {
            return redirect('utente/articoli')->with('error', 'Articolo non trovato');
        }

        // Gestione del salvataggio dei fattori di conversione
        if (isset($dati['salva_fattori_conversione'])) {
            unset($dati['salva_fattori_conversione']);

            // Elimina i fattori di conversione esistenti
            DB::table('fattori_conversione')
                ->where('id_articolo', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->delete();

            // Inserisci i nuovi fattori di conversione
            if (isset($dati['fattore_valore']) && is_array($dati['fattore_valore'])) {
                foreach ($dati['fattore_valore'] as $key => $valore) {
                    if ($valore && isset($dati['fattore_um'][$key]) && !empty($dati['fattore_um'][$key])) {
                        DB::table('fattori_conversione')->insert([
                            'id_articolo' => $id,
                            'id_azienda' => $utente->id_azienda,
                            'valore' => $valore,
                            'unita_misura' => $dati['fattore_um'][$key],
                            'created_at' => now()
                        ]);
                    }
                }
            }

            return redirect('utente/dettaglio_articolo/'.$id)->with('success', 'Fattori di conversione aggiornati con successo');
        }

        // Gestione operazioni di carico/scarico
        if (isset($dati['esegui_movimento'])) {
            unset($dati['esegui_movimento']);

            $tipo_movimento = $dati['tipo_movimento']; // 'carico' o 'scarico'
            $id_mg = $dati['id_mg'];
            $qta = $dati['qta'];
            $lotto = $dati['lotto'] ?? null;
            $scadenza_lotto = isset($dati['scadenza_lotto']) && !empty($dati['scadenza_lotto']) ?
                date('Y-m-d', strtotime($dati['scadenza_lotto'])) : null;
            $causale = $dati['causale'] ?? ($tipo_movimento == 'carico' ? 'Carico manuale' : 'Scarico manuale');

            // Calcola la quantità reale (positiva per carico, negativa per scarico)
            $qta_effettiva = $tipo_movimento == 'carico' ? $qta : -$qta;

            // Inserisci il movimento
            DB::table('mgmov')->insert([
                'id_articolo' => $id,
                'id_utente' => $utente->id,
                'id_azienda' => $utente->id_azienda,
                'id_mg' => $id_mg,
                'lotto' => $lotto,
                'scadenza_lotto' => $scadenza_lotto,
                'qta' => $qta_effettiva,
                'datamov' => now(),
                'causale' => $causale,
                'car' => $tipo_movimento == 'carico' ? 1 : 0,
                'sca' => $tipo_movimento == 'scarico' ? 1 : 0
            ]);

            // Aggiorna la giacenza dell'articolo
            DB::update('UPDATE articoli SET giacenza = (SELECT SUM(qta) FROM mgmov WHERE id_articolo = ?) WHERE id = ?',
                [$id, $id]);

            return redirect('utente/dettaglio_articolo/'.$id)->with('success',
                ($tipo_movimento == 'carico' ? 'Carico' : 'Scarico') . ' effettuato con successo');
        }


        // Gestione del salvataggio delle modifiche
        if (isset($dati['salva_generale'])) {
            unset($dati['salva_generale']);

            $update = [];
            $update['codice_articolo'] = $dati['codice_articolo'];
            $update['titolo'] = $dati['titolo'];
            $update['descrizione'] = $dati['descrizione'] ?? '';
            $update['tipologia'] = $dati['tipologia'];
            $update['barcode'] = $dati['barcode'];
            $update['marca_modello'] = $dati['marca_modello'];
            $update['id_cliente'] = !empty($dati['id_cliente']) ? $dati['id_cliente'] : null;
            $update['data_primo_carico'] = !empty($dati['data_primo_carico']) ? $dati['data_primo_carico'] : null;

            DB::table('articoli')
                ->where('id', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->update($update);

            return redirect('utente/dettaglio_articolo/'.$id)->with('success', 'Dati generali aggiornati con successo');
        }

        if (isset($dati['salva_dimensioni'])) {
            unset($dati['salva_dimensioni']);

            $update = [];
            $update['dimensione_l'] = $dati['dimensione_l'];
            $update['dimensione_h'] = $dati['dimensione_h'];
            $update['dimensione_p'] = $dati['dimensione_p'];
            $update['lunghezza'] = $dati['lunghezza'];
            $update['volume_metri'] = $dati['volume_metri'];
            $update['volume_metri_cubi'] = $dati['volume_metri_cubi'];
            $update['altezza'] = $dati['altezza'];

            DB::table('articoli')
                ->where('id', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->update($update);

            return redirect('utente/dettaglio_articolo/'.$id)->with('success', 'Dimensioni aggiornate con successo');
        }

        if (isset($dati['salva_prezzi'])) {
            unset($dati['salva_prezzi']);

            $update = [];
            $update['prezzo'] = $dati['prezzo'];
            $update['prezzo_lordo'] = $dati['prezzo_lordo'];
            $update['prezzo_netto'] = $dati['prezzo_netto'];

            DB::table('articoli')
                ->where('id', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->update($update);

            return redirect('utente/dettaglio_articolo/'.$id)->with('success', 'Prezzi aggiornati con successo');
        }

        if (isset($dati['salva_produzione'])) {
            unset($dati['salva_produzione']);

            // Salvataggio fasi di produzione
            if (isset($dati['fasi']) && is_array($dati['fasi'])) {
                // Elimina le associazioni esistenti
                DB::table('fasi_articoli')
                    ->where('id_articolo', $id)
                    ->where('id_azienda', $utente->id_azienda)
                    ->delete();

                // Inserisci le nuove associazioni
                foreach ($dati['fasi'] as $id_fase) {
                    DB::table('fasi_articoli')->insert([
                        'id_azienda' => $utente->id_azienda,
                        'id_utente' => $utente->id,
                        'id_fase' => $id_fase,
                        'id_articolo' => $id,
                        'tempo_medio_minuti' => $dati['tempo_medio'][$id_fase] ?? 0
                    ]);
                }
            }

            return redirect('utente/dettaglio_articolo/'.$id)->with('success', 'Dati di produzione aggiornati con successo');
        }

        if (isset($dati['salva_magazzino'])) {
            unset($dati['salva_magazzino']);

            $update = [];
            $update['giacenza'] = $dati['giacenza'];
            $update['punto_riordino'] = $dati['punto_riordino'];
            $update['id_mg'] = $dati['id_mg']; // Magazzino di default
            $update['um'] = $dati['um'];

            DB::table('articoli')
                ->where('id', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->update($update);

            return redirect('utente/dettaglio_articolo/'.$id)->with('success', 'Dati di magazzino aggiornati con successo');
        }

        if (isset($dati['salva_contabilita'])) {
            unset($dati['salva_contabilita']);

            $update = [];
            $update['aliquota_iva'] = $dati['aliquota_iva'];
            $update['conto_acquisti'] = $dati['conto_acquisti'];
            $update['conto_vendite'] = $dati['conto_vendite'];

            DB::table('articoli')
                ->where('id', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->update($update);

            return redirect('utente/dettaglio_articolo/'.$id)->with('success', 'Dati contabili aggiornati con successo');
        }

        if (isset($dati['salva_attributi'])) {
            unset($dati['salva_attributi']);

            // Gestione attributi personalizzati
            $attributi = [];
            if (isset($dati['attributi']) && is_array($dati['attributi'])) {
                $attributi = $dati['attributi'];
            }

            $update = [];
            $update['attributi'] = json_encode($attributi);

            DB::table('articoli')
                ->where('id', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->update($update);

            return redirect('utente/dettaglio_articolo/'.$id)->with('success', 'Attributi aggiornati con successo');
        }

        if (isset($dati['salva_categoria'])) {
            unset($dati['salva_categoria']);

            $update = [];
            $update['id_categoria'] = $dati['id_categoria'];

            DB::table('articoli')
                ->where('id', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->update($update);

            return redirect('utente/dettaglio_articolo/'.$id)->with('success', 'Categoria aggiornata con successo');
        }

        if (isset($dati['salva_note'])) {
            unset($dati['salva_note']);

            $update = [];
            $update['note'] = $dati['note'];

            DB::table('articoli')
                ->where('id', $id)
                ->where('id_azienda', $utente->id_azienda)
                ->update($update);

            return redirect('utente/dettaglio_articolo/'.$id)->with('success', 'Note aggiornate con successo');
        }

        // Gestione salvataggio immagine (modifica quello esistente)
        if (isset($dati['salva_immagine'])) {
            unset($dati['salva_immagine']);

            if (isset($_FILES['immagine']) && $_FILES['immagine']['name'] != '') {
                // Elimina immagine precedente
                if ($articolo->immagine && file_exists($articolo->immagine)) {
                    unlink($articolo->immagine);
                }

                $pathinfo = pathinfo($_FILES['immagine']['name']);
                $nome = Str::random(20);
                $target = 'immagini/articoli/' . $nome . '.' . $pathinfo['extension'];

                // Crea directory se non esiste
                if (!file_exists('immagini/articoli')) {
                    mkdir('immagini/articoli', 0777, true);
                }

                move_uploaded_file($_FILES['immagine']['tmp_name'], $target);

                DB::table('articoli')
                    ->where('id', $id)
                    ->where('id_azienda', $utente->id_azienda)
                    ->update(['immagine' => $target]);
            }

            return redirect('utente/dettaglio_articolo/'.$id)->with('success', 'Immagine aggiornata con successo');
        }

        // Gestione salvataggio nuovo file
        if (isset($dati['salva_file'])) {
            unset($dati['salva_file']);

            if (isset($_FILES['nuovo_file']) && $_FILES['nuovo_file']['name'] != '') {
                $pathinfo = pathinfo($_FILES['nuovo_file']['name']);
                $nome_file = Str::random(20);
                $directory = 'files/articoli/' . $id . '/';

                // Crea directory se non esiste
                if (!file_exists($directory)) {
                    mkdir($directory, 0777, true);
                }

                $path_completo = $directory . $nome_file . '.' . $pathinfo['extension'];

                if (move_uploaded_file($_FILES['nuovo_file']['tmp_name'], $path_completo)) {
                    // Salva nel database
                    DB::table('articoli_files')->insert([
                        'id_articolo' => $id,
                        'id_azienda' => $utente->id_azienda,
                        'nome_file' => $_FILES['nuovo_file']['name'],
                        'path_file' => $path_completo,
                        'tipo_file' => $dati['tipo_file'],
                        'created_at' => now()
                    ]);
                }
            }

            return redirect('utente/dettaglio_articolo/'.$id)->with('success', 'File aggiunto con successo');
        }

        // Recupera informazioni aggiuntive per i select e i valori da mostrare nei campi
        $categorie = DB::table('categorie')
            ->where('id_azienda', $utente->id_azienda)
            ->get();

        $fasi = DB::table('fasi')
            ->where('id_azienda', $utente->id_azienda)
            ->get();

        $magazzini = DB::table('mg')
            ->where('id_azienda', $utente->id_azienda)
            ->get();

        // Recupera le fasi attualmente associate all'articolo
        $fasi_associate = DB::table('fasi_articoli')
            ->where('id_articolo', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->pluck('id_fase')
            ->toArray();

        // Recupera i tempi medi per ogni fase associata
        $tempi_medi = DB::table('fasi_articoli')
            ->where('id_articolo', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->pluck('tempo_medio_minuti', 'id_fase')
            ->toArray();
        // Recupera i fattori di conversione esistenti
        $fattori_conversione = DB::table('fattori_conversione')
            ->where('id_articolo', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->get();

        // Recupera i lotti disponibili per questo articolo
        $lotti = DB::table('mgmov')
            ->select('lotto', 'scadenza_lotto', DB::raw('SUM(qta) as giacenza'))
            ->where('id_articolo', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->whereNotNull('lotto')
            ->groupBy('lotto', 'scadenza_lotto')
            ->having(DB::raw('SUM(qta)'), '>', 0)
            ->get();

        // Recupera informazioni aggiuntive per i select e i valori da mostrare nei campi
        $categorie = DB::table('categorie')
            ->where('id_azienda', $utente->id_azienda)
            ->get();

        $fasi = DB::table('fasi')
            ->where('id_azienda', $utente->id_azienda)
            ->get();

        $magazzini = DB::table('mg')
            ->where('id_azienda', $utente->id_azienda)
            ->get();

        // Recupera familgie associate (se esiste)
        $famiglia = null;
        if ($articolo->id_famiglia) {
            $famiglia = DB::table('famiglie')
                ->where('id', $articolo->id_famiglia)
                ->where('id_azienda', $utente->id_azienda)
                ->first();
        }

        $famiglie = DB::table('famiglie')
            ->where('id_azienda', $utente->id_azienda)
            ->get();

        $clienti = DB::table('clienti')
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('ragione_sociale')
            ->get();

        // Recupera informazioni sulla distinta base
        $distinta_base = DB::select('
            SELECT db.*, m.titolo as materiale, m.um, m.prezzo 
            FROM distinta_base db
            JOIN articoli m ON m.id = db.id_materiale
            WHERE db.id_articolo = ? 
            ORDER BY db.id_fase_articolo ASC',
            [$id]
        );

        // Raggruppa la distinta base per fase
        $distinta_per_fase = [];
        foreach ($distinta_base as $item) {
            if (!isset($distinta_per_fase[$item->id_fase_articolo])) {
                $distinta_per_fase[$item->id_fase_articolo] = [];
            }
            $distinta_per_fase[$item->id_fase_articolo][] = $item;
        }

        return View::make('utente.dettaglio_articolo', compact(
            'utente',
            'articolo',
            'categorie',
            'fasi',
            'magazzini',
            'fasi_associate',
            'tempi_medi',
            'famiglia',
            'famiglie',
            'distinta_per_fase',
            'fattori_conversione',
            'lotti',
            'clienti'
        ));
    }


    public function elimina_file_articolo(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Recupera il file dal database
        $file = DB::table('articoli_files')
            ->where('id', $dati['id'])
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if ($file) {
            // Elimina il file fisico se esiste
            if (file_exists($file->path_file)) {
                unlink($file->path_file);
            }

            // Elimina il record dal database
            DB::table('articoli_files')
                ->where('id', $dati['id'])
                ->where('id_azienda', $utente->id_azienda)
                ->delete();

            return response()->json(['success' => true]);
        }

        return response()->json(['success' => false]);
    }

    public function carico_scarico_magazzino(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        $id_articolo = $dati['id_articolo'];
        $id_mg = $dati['id_mg'];

        // Recupera i lotti disponibili per questo articolo nel magazzino specifico
        $lotti = DB::table('mgmov')
            ->select('lotto', 'scadenza_lotto', DB::raw('SUM(qta) as giacenza'))
            ->where('id_articolo', $id_articolo)
            ->where('id_azienda', $utente->id_azienda)
            ->where('id_mg', $id_mg)
            ->whereNotNull('lotto')
            ->groupBy('lotto', 'scadenza_lotto')
            ->having(DB::raw('SUM(qta)'), '>', 0)
            ->get();

        $magazzino = DB::table('mg')
            ->where('id', $id_mg)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        $articolo = DB::table('articoli')
            ->where('id', $id_articolo)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        return response()->json([
            'success' => true,
            'lotti' => $lotti,
            'magazzino' => $magazzino,
            'articolo' => $articolo
        ]);
    }
    public function aggiungi_famiglia(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if (isset($dati['nome'])) {
            $id = DB::table('famiglie')->insertGetId([
                'nome' => $dati['nome'],
                'descrizione' => $dati['descrizione'] ?? '',
                'id_azienda' => $utente->id_azienda,
                'id_utente' => $utente->id
            ]);

            return response()->json(['success' => true, 'id' => $id, 'nome' => $dati['nome']]);
        }

        return response()->json(['success' => false, 'message' => 'Dati mancanti']);
    }

    public function aggiungi_categoria(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if (isset($dati['nome'])) {
            $id = DB::table('categorie')->insertGetId([
                'nome' => $dati['nome'],
                'descrizione' => $dati['descrizione'] ?? '',
                'id_azienda' => $utente->id_azienda,
                'id_utente' => $utente->id
            ]);

            return response()->json(['success' => true, 'id' => $id, 'nome' => $dati['nome']]);
        }

        return response()->json(['success' => false, 'message' => 'Dati mancanti']);
    }

    /**
     * Genera un nuovo barcode per l'articolo
     */
    public function generaBarcode($id)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Recupera l'articolo
        $articolo = DB::table('articoli')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$articolo) {
            return response()->json(['success' => false, 'message' => 'Articolo non trovato']);
        }

        // Genera un codice barcode
        // Codice articolo padding a sinistra con zeri fino a 6 cifre
        $codiceArticolo = str_pad($articolo->id, 6, '0', STR_PAD_LEFT);

        // ID azienda padding a sinistra con zeri fino a 4 cifre
        $idAzienda = str_pad($utente->id_azienda, 4, '0', STR_PAD_LEFT);

        // Timestamp breve (ultime 2 cifre)
        $timestamp = substr(time(), -2);

        // Assembla il barcode (12 cifre)
        $barcode = $idAzienda . $codiceArticolo . $timestamp;

        // Aggiorna il barcode nell'articolo
        DB::table('articoli')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->update(['barcode' => $barcode]);

        return response()->json([
            'success' => true,
            'barcode' => $barcode
        ]);
    }

    /**
     * Visualizza un'immagine barcode
     */
    public function visualizzaBarcode(Request $request)
    {
        $barcode = $request->query('barcode');

        if (!$barcode) {
            return response()->json(['success' => false, 'message' => 'Barcode non specificato']);
        }

        // Per test/sviluppo, reindirizza a un servizio esterno
        $format = 'CODE128';
        $barcodeUrl = "https://barcode.tec-it.com/barcode.ashx?data=" . urlencode($barcode) . "&code=" . $format;

        return redirect($barcodeUrl);
    }

    /**
     * Stampa il barcode usando MPdf
     */
    public function stampaBarcode(Request $request)
    {
        $this->is_loggato();
        $barcode = $request->query('barcode');

        if (!$barcode) {
            return redirect()->back()->with('error', 'Barcode non specificato');
        }

        // Ottieni l'URL dell'immagine del barcode
        $format = 'CODE128';
        $barcodeUrl = "https://barcode.tec-it.com/barcode.ashx?data=" . urlencode($barcode) . "&code=" . $format;

        // In produzione, genera l'immagine localmente invece di usare un servizio esterno
        // Puoi utilizzare una libreria come picqer/php-barcode-generator

        // Genera il PDF con MPdf
        $mpdf = new \Mpdf\Mpdf([
            'format' => [50, 25], // Dimensioni in mm per un'etichetta standard
            'margin_left' => 2,
            'margin_right' => 2,
            'margin_top' => 2,
            'margin_bottom' => 2
        ]);

        // HTML contenuto nel PDF
        $html = '
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 0;
            margin: 0;
        }
        .barcode-container {
            width: 100%;
        }
        .barcode-container img {
            max-width: 100%;
            height: auto;
        }
        .barcode-value {
            font-size: 10pt;
            margin-top: 2mm;
        }
    </style>
    <div class="barcode-container">
        <img src="' . $barcodeUrl . '" alt="Barcode">
        <div class="barcode-value">' . $barcode . '</div>
    </div>';

        $mpdf->WriteHTML($html);

        // Output del PDF
        return $mpdf->Output('barcode_' . $barcode . '.pdf', 'I');
    }

    /**
     * Cerca articoli per nome o codice
     */
    public function cercaArticoli(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        $query = $request->query('q');
        $exclude = $request->query('exclude'); // ID articolo da escludere

        if (!$query || strlen($query) < 3) {
            return response()->json(['success' => false, 'message' => 'Query di ricerca troppo corta']);
        }

        // Cerca articoli
        $articoli = DB::table('articoli')
            ->where('id_azienda', $utente->id_azienda)
            ->where(function($q) use ($query) {
                $q->where('codice_articolo', 'LIKE', "%{$query}%")
                    ->orWhere('titolo', 'LIKE', "%{$query}%");
            })
            ->when($exclude, function($q) use ($exclude) {
                return $q->where('id', '!=', $exclude);
            })
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'articoli' => $articoli
        ]);
    }

    /**
     * Aggiunge un articolo alternativo
     */
    public function aggiungiAlternativa(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Verifica che gli ID siano validi
        if (!isset($dati['id_articolo']) || !isset($dati['id_articolo_alternativo'])) {
            return response()->json(['success' => false, 'message' => 'Parametri mancanti']);
        }

        // Verifica che gli articoli esistano ed appartengano all'azienda
        $articoloPrincipale = DB::table('articoli')
            ->where('id', $dati['id_articolo'])
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        $articoloAlternativo = DB::table('articoli')
            ->where('id', $dati['id_articolo_alternativo'])
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$articoloPrincipale || !$articoloAlternativo) {
            return response()->json(['success' => false, 'message' => 'Articolo non trovato']);
        }

        // Verifica che l'associazione non esista già
        $esistente = DB::table('articoli_alternativi')
            ->where('id_articolo', $dati['id_articolo'])
            ->where('id_articolo_alternativo', $dati['id_articolo_alternativo'])
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if ($esistente) {
            return response()->json(['success' => false, 'message' => 'Questa alternativa è già stata associata']);
        }

        // Aggiungi l'associazione
        DB::table('articoli_alternativi')->insert([
            'id_articolo' => $dati['id_articolo'],
            'id_articolo_alternativo' => $dati['id_articolo_alternativo'],
            'id_azienda' => $utente->id_azienda,
            'created_at' => now()
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * Rimuove un articolo alternativo
     */
    public function rimuoviAlternativa($id)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Verifica che l'associazione esista ed appartenga all'azienda
        $alternativa = DB::table('articoli_alternativi')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if (!$alternativa) {
            return response()->json(['success' => false, 'message' => 'Alternativa non trovata']);
        }

        // Rimuovi l'associazione
        DB::table('articoli_alternativi')
            ->where('id', $id)
            ->delete();

        return response()->json(['success' => true]);
    }

    private function is_loggato()
    {
        if (!session()->has('utente')) {
            return Redirect::to('admin/login')->send();
        }
    }
}
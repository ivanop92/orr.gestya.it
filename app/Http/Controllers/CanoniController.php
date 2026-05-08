<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CanoniController extends Controller
{
    public function index(Request $request)
    {
        $utente = session('utente');

        $canoni = DB::select('SELECT c.*,cl.ragione_sociale as cliente from canoni c LEFT JOIN clienti cl on cl.id = c.id_cliente where c.id_azienda ='.$utente->id_azienda.' order by created_at desc');

        foreach ($canoni as $canone) {
            $canone->prossima_ricorrenza = $this->calcolaProssimaRicorrenza($canone);
        }

        $clienti = DB::select('SELECT c.*, a.descrizione as sezione FROM clienti c LEFT JOIN ateco_sezioni a ON a.id = c.id_sezione WHERE c.id_tipologia = 2 AND c.id_azienda = ? ORDER BY c.ragione_sociale ASC',
            [$utente->id_azienda]
        );


        return view('canoni.index', compact('canoni','clienti'));
    }

    private function calcolaProssimaRicorrenza($canone)
    {
        $oggi = now();
        $dataInizio = Carbon::parse($canone->data_inizio);
        
        switch($canone->tipo_ricorrenza) {
            case 'giorni':
                $prossima = $dataInizio;
                while($prossima <= $oggi) {
                    $prossima = $prossima->addDays($canone->valore_ricorrenza);
                }
                break;
            
            case 'mensile':
                // Per il tipo mensile, il valore_ricorrenza rappresenta il giorno del mese
                $prossima = Carbon::createFromDate(
                    $oggi->year,
                    $oggi->month,
                    min($canone->valore_ricorrenza, $oggi->daysInMonth)
                );
                
                // Se la data calcolata è già passata, passa al mese successivo
                if ($prossima <= $oggi) {
                    $prossima = $prossima->addMonth();
                }
                
                // Se la data calcolata è precedente alla data di inizio, usa la data di inizio
                if ($prossima < $dataInizio) {
                    $prossima = Carbon::createFromDate(
                        $dataInizio->year,
                        $dataInizio->month,
                        min($canone->valore_ricorrenza, $dataInizio->daysInMonth)
                    );
                }
                break;
            
            case 'annuale':
                // Per il tipo annuale, il valore_ricorrenza rappresenta il giorno dell'anno
                $prossima = Carbon::createFromDate(
                    $oggi->year,
                    $dataInizio->month,
                    $dataInizio->day
                );
                
                // Se la data calcolata è già passata, passa all'anno successivo
                if ($prossima <= $oggi) {
                    $prossima = $prossima->addYear();
                }
                
                // Se la data calcolata è precedente alla data di inizio, usa la data di inizio
                if ($prossima < $dataInizio) {
                    $prossima = $dataInizio;
                }
                break;
        }
        
        return $prossima->format('Y-m-d');
    }

    public function store(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        unset($dati['_token']);

                // Calcola la prossima ricorrenza
        $prossimaRicorrenza = $this->calcolaProssimaRicorrenza((object)[
            'tipo_ricorrenza' => $dati['tipo_ricorrenza'],
            'valore_ricorrenza' => $dati['valore_ricorrenza'],
            'data_inizio' => $dati['data_inizio']
        ]);

        $dati['prossima_ricorrenza'] = $prossimaRicorrenza;
        $dati['id_azienda'] = $utente->id_azienda;

        DB::table('canoni')->insert($dati);

        return redirect()->route('canoni.index')->with('success', 'Canone creato con successo');
    }

    public function update(Request $request, $id)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        // Calcola la prossima ricorrenza
        $prossimaRicorrenza = $this->calcolaProssimaRicorrenza((object)[
            'tipo_ricorrenza' => $dati['tipo_ricorrenza'],
            'valore_ricorrenza' => $dati['valore_ricorrenza'],
            'data_inizio' => $dati['data_inizio']
        ]);

        unset($dati['_method']);

        $dati['prossima_ricorrenza'] = $prossimaRicorrenza;

        DB::table('canoni')
            ->where('id', $id)
            ->update($dati);

        return redirect()->route('canoni.index')->with('success', 'Canone aggiornato con successo');
    }

    public function destroy($id)
    {
        $utente = session('utente');
        
        DB::table('canoni')
            ->where('id', $id)
            ->where('id_azienda', $utente->id_azienda)
            ->delete();

        return redirect()->route('canoni.index')->with('success', 'Canone eliminato con successo');
    }

    public function generaFatture()
    {
        $utente = session('utente');
        $anno_corrente = date('Y');
        
        // Recupera tutti i canoni attivi
        $canoni = DB::table('canoni')
            ->where('id_azienda', $utente->id_azienda)
            ->where('stato', 'attivo')
            ->where(function($query) use ($anno_corrente) {
                $query->whereNull('data_fine')
                    ->orWhere('data_fine', '>=', $anno_corrente . '-01-01');
            })
            ->get();

        foreach ($canoni as $canone) {
            // Verifica se il canone deve essere generato per l'anno corrente
            $data_generazione = $this->calcolaDataGenerazione($canone, $anno_corrente);
            
            if ($data_generazione && $data_generazione <= now()) {
                // Crea la fattura per il canone
                $this->creaFatturaCanone($canone, $data_generazione);
            }
        }

        return redirect()->route('canoni.index')->with('success', 'Fatture generate con successo');
    }

    private function calcolaDataGenerazione($canone, $anno)
    {
        switch ($canone->tipo_ricorrenza) {
            case 'giorni':
                // Calcola la prossima data basata sul numero di giorni
                $data_base = $canone->data_inizio;
                while ($data_base < $anno . '-01-01') {
                    $data_base = date('Y-m-d', strtotime($data_base . ' + ' . $canone->valore_ricorrenza . ' days'));
                }
                return $data_base;

            case 'mensile':
                // Genera il giorno X di ogni mese
                return $anno . '-' . str_pad($canone->valore_ricorrenza, 2, '0', STR_PAD_LEFT) . '-01';

            case 'annuale':
                // Genera il giorno X dell'anno
                return $anno . '-' . date('m-d', strtotime($canone->data_inizio));

            default:
                return null;
        }
    }

    private function creaFatturaCanone($canone, $data_generazione)
    {
        // Verifica se esiste già una fattura per questo canone in questa data
        $fattura_esistente = DB::table('fatture')
            ->where('id_azienda', $canone->id_azienda)
            ->where('id_canone', $canone->id)
            ->where('data_fattura', $data_generazione)
            ->exists();

        if (!$fattura_esistente) {
            // Crea la fattura
            $id_fattura = DB::table('fatture')->insertGetId([
                'id_azienda' => $canone->id_azienda,
                'id_canone' => $canone->id,
                'data_fattura' => $data_generazione,
                'importo' => $canone->importo,
                'descrizione' => $canone->descrizione,
                'stato' => 'da_pagare',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Crea la scadenza per la fattura
            DB::table('scadenziario')->insert([
                'id_dotes' => $id_fattura,
                'id_azienda' => $canone->id_azienda,
                'importo' => $canone->importo,
                'data_scadenza' => date('Y-m-d', strtotime($data_generazione . ' + 30 days')),
                'termini' => '30 giorni',
                'stato' => 'da_pagare',
                'tipo_movimento' => 'fattura',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    public function creaFattura($id)
    {
        $canone = DB::table('canoni')->where('id', $id)->first();
        
        if (!$canone) {
            return response()->json(['error' => 'Canone non trovato'], 404);
        }

        if ($canone->stato != 'attivo') {
            return response()->json(['error' => 'Il canone non è attivo'], 400);
        }

        // Verifica se esiste già un documento per questa ricorrenza
        $documentoEsistente = DB::table('dotes')
            ->where('id_canone', $id)
            ->where('data_documento', $canone->prossima_ricorrenza)
            ->first();

        if ($documentoEsistente) {
            return response()->json(['error' => 'Documento già creato per questa ricorrenza'], 400);
        }

        // Crea il documento testata (dotes)
        $idDotes = DB::table('dotes')->insertGetId([
            'id_azienda' => $canone->id_azienda,
            'id_canone' => $id,
            'data_documento' => $canone->prossima_ricorrenza,
            'importo_totale' => $canone->importo,
            'stato' => 'da_pagare',
            'tipo_documento' => 'fattura',
            'descrizione' => $canone->descrizione,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Crea la riga del documento (dorig)
        DB::table('dorig')->insert([
            'id_dotes' => $idDotes,
            'descrizione' => $canone->descrizione,
            'importo' => $canone->importo,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Aggiorna la prossima ricorrenza
        $nuovaProssimaRicorrenza = $this->calcolaProssimaRicorrenza($canone);
        DB::table('canoni')
            ->where('id', $id)
            ->update(['prossima_ricorrenza' => $nuovaProssimaRicorrenza]);

        return response()->json(['success' => true, 'id_dotes' => $idDotes]);
    }

    public function is_loggato(){

        if (!session()->has('utente')) return Redirect::to('admin/login')->send();

    }

} 
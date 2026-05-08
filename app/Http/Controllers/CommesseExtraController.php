<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommesseExtraController extends Controller
{
    // Verifica se l'utente è loggato
    private function is_loggato()
    {
        if (!session()->has('utente')) {
            return redirect('login');
        }
    }
    
    // API per ottenere i dettagli di una voce extra
    public function get_voce_extra($id)
    {
        $this->is_loggato();
        $utente = session('utente');
        
        $voce = DB::table('commesse_extra')->where('id', $id)->first();
        
        if (!$voce) {
            return response()->json(['success' => false, 'message' => 'Voce extra non trovata']);
        }
        
        // Verifica che l'utente abbia i permessi per visualizzare la voce
        $commessa = DB::table('commesse')->where('id', $voce->id_commessa)->first();
        if (!$commessa || $commessa->id_azienda != $utente->id_azienda) {
            return response()->json(['success' => false, 'message' => 'Non hai i permessi per visualizzare questa voce']);
        }
        
        return response()->json($voce);
    }
    
    // API per creare una nuova voce extra
    public function crea_voce_extra(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        
        // Validazione dei dati
        $request->validate([
            'id_commessa' => 'required|integer',
            'tipo' => 'required|in:ricavo,costo',
            'descrizione' => 'required|string|max:255',
            'importo' => 'required|numeric|min:0',
            'data' => 'required|date',
            'note' => 'nullable|string'
        ]);
        
        // Verifica che l'utente abbia i permessi per la commessa
        $commessa = DB::table('commesse')->where('id', $request->id_commessa)->first();
        if (!$commessa || $commessa->id_azienda != $utente->id_azienda) {
            return response()->json(['success' => false, 'message' => 'Non hai i permessi per questa commessa']);
        }
        
        // Crea la voce extra
        $id = DB::table('commesse_extra')->insertGetId([
            'id_commessa' => $request->id_commessa,
            'id_azienda' => $utente->id_azienda,
            'id_utente' => $utente->id,
            'tipo' => $request->tipo,
            'descrizione' => $request->descrizione,
            'importo' => $request->importo,
            'data' => $request->data,
            'note' => $request->note,
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        return response()->json(['success' => true, 'id' => $id]);
    }
    
    // API per aggiornare una voce extra
    public function aggiorna_voce_extra(Request $request, $id)
    {
        $this->is_loggato();
        $utente = session('utente');
        
        // Validazione dei dati
        $request->validate([
            'tipo' => 'required|in:ricavo,costo',
            'descrizione' => 'required|string|max:255',
            'importo' => 'required|numeric|min:0',
            'data' => 'required|date',
            'note' => 'nullable|string'
        ]);
        
        // Verifica che la voce esista e l'utente abbia i permessi
        $voce = DB::table('commesse_extra')->where('id', $id)->first();
        if (!$voce) {
            return response()->json(['success' => false, 'message' => 'Voce extra non trovata']);
        }
        
        $commessa = DB::table('commesse')->where('id', $voce->id_commessa)->first();
        if (!$commessa || $commessa->id_azienda != $utente->id_azienda) {
            return response()->json(['success' => false, 'message' => 'Non hai i permessi per modificare questa voce']);
        }
        
        // Aggiorna la voce extra
        DB::table('commesse_extra')->where('id', $id)->update([
            'tipo' => $request->tipo,
            'descrizione' => $request->descrizione,
            'importo' => $request->importo,
            'data' => $request->data,
            'note' => $request->note,
            'updated_at' => now()
        ]);
        
        return response()->json(['success' => true]);
    }
    
    // API per eliminare una voce extra
    public function elimina_voce_extra($id)
    {
        $this->is_loggato();
        $utente = session('utente');
        
        $voce = DB::table('commesse_extra')->where('id', $id)->first();
        
        if (!$voce) {
            return response()->json(['success' => false, 'message' => 'Voce extra non trovata']);
        }
        
        // Verifica che l'utente abbia i permessi per eliminare la voce
        $commessa = DB::table('commesse')->where('id', $voce->id_commessa)->first();
        if (!$commessa || $commessa->id_azienda != $utente->id_azienda) {
            return response()->json(['success' => false, 'message' => 'Non hai i permessi per eliminare questa voce']);
        }
        
        // Elimina la voce extra
        DB::table('commesse_extra')->where('id', $id)->delete();
        
        return response()->json(['success' => true]);
    }
} 
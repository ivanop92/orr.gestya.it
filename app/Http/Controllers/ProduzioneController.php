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
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TariffeImport;
use App\Http\Controllers\URL;

class ProduzioneController extends Controller
{

    public function login(Request $request, $token_azienda)
    {
        $dati = $request->all();
        $error = '';

        $azienda = DB::select('SELECT * FROM aziende WHERE token_azienda = ?', [$token_azienda]);

        if (empty($azienda)) {
            return Redirect::to('/');
        }

        $azienda = $azienda[0];

        $fasi = DB::table('fasi')
            ->where('id_azienda', $azienda->id)
            ->orderBy('descrizione')
            ->get();

        // Step 2 (kiosk): operatore selezionato, verifica PIN
        if (isset($dati['login_pin']) && !empty($dati['id_operatore']) && isset($dati['pin'])) {
            $utenti = DB::select('SELECT * FROM utenti
                        WHERE abilitato = 1
                        AND id = ?
                        AND id_azienda = ?
                        AND id_tipologia = 3
                        AND pin IS NOT NULL
                        AND pin = ?',
                [
                    (int) $dati['id_operatore'],
                    $azienda->id,
                    trim($dati['pin'])
                ]);

            if (sizeof($utenti) > 0) {
                $utente = $utenti[0];

                session(['utente' => $utente]);
                session(['utente_produzione' => $utente]);
                session(['azienda_produzione' => $azienda]);
                session(['tipo_lavoro' => 'tutti']);
                session(['anno' => date("Y")]);
                session()->save();

                return Redirect::to('produzione/dashboard');
            }

            $error = 'PIN errato';
        }

        // Fallback: vecchio login email/password (per operatori senza PIN ancora impostato)
        if (isset($dati['login'])) {
            $utenti = DB::select('SELECT * FROM utenti
                        WHERE abilitato = 1
                        AND email = ?
                        AND password = ?
                        AND id_azienda = ?
                        AND id_tipologia = 3',
                [
                    htmlentities($dati['email'], 3, 'UTF-8'),
                    htmlentities($dati['password'], 3, 'UTF-8'),
                    $azienda->id
                ]);

            if (sizeof($utenti) > 0) {
                $utente = $utenti[0];

                session(['utente' => $utente]);
                session(['utente_produzione' => $utente]);
                session(['azienda_produzione' => $azienda]);
                session(['tipo_lavoro' => 'tutti']);
                session(['anno' => date("Y")]);
                session()->save();

                return Redirect::to('produzione/dashboard');
            } else {
                $error = 'Inserisci username e password corretti';
            }
        }

        // Carica gli operatori abilitati per la griglia kiosk
        $operatori = DB::select('SELECT id, nome, cognome, immagine, pin FROM utenti
                        WHERE abilitato = 1
                        AND id_azienda = ?
                        AND id_tipologia = 3
                        ORDER BY nome ASC, cognome ASC',
            [$azienda->id]
        );

        return View::make('produzione.login', compact('error', 'azienda', 'fasi', 'operatori'));
    }


    public function cambia_tipo_lavoro(Request $request)
    {
        $this->is_loggato();
        $dati = $request->all();

        $tipo_lavoro = $dati['tipo_lavoro'] ?? 'odl';
        if (!in_array($tipo_lavoro, ['odl', 'commesse', 'tutti'])) {
            $tipo_lavoro = 'odl';
        }

        session(['tipo_lavoro' => $tipo_lavoro]);
        session()->save();

        return Redirect::to('produzione/dashboard');
    }

    public function dettaglio_attivita($id)
    {
        $this->is_loggato();
        $utente = session('utente_produzione');
        $azienda = session('azienda_produzione');

        // Recupera l'attività specifica
        $attivita = DB::select('
        SELECT 
            ca.*, 
            c.codice_commessa,
            c.descrizione as commessa_descrizione,
            u.nome as nome_responsabile,
            u.cognome as cognome_responsabile
        FROM commesse_attivita ca
        JOIN commesse c ON c.id = ca.id_commessa
        LEFT JOIN utenti u ON u.id = ca.id_responsabile
        WHERE ca.id = ? 
        AND ca.id_azienda = ?
        LIMIT 1',
            [$id, $azienda->id]
        );

        if (empty($attivita)) {
            return Redirect::to('produzione/dashboard')->with('error', 'Attività non trovata');
        }

        $attivita = $attivita[0];

        // Recupera gli allegati dell'attività
        $allegati = DB::table('commesse_attivita_allegati')
            ->where('id_attivita', $id)
            ->where('id_azienda', $azienda->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return View::make('produzione.dettaglio_attivita', compact('utente', 'azienda', 'attivita', 'allegati'));
    }



    public function carica_allegato($id_attivita, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente_produzione');
        $azienda = session('azienda_produzione');

        if (!$request->hasFile('allegato')) {
            return redirect()->back()->with('error', 'Nessun file selezionato');
        }

        $file = $request->file('allegato');
        $tipo = $request->input('tipo', 'documento');
        $descrizione = $request->input('descrizione', '');
        $nota_allegato = $request->input('nota_allegato', '');

        // Controlla se l'attività esiste
        $attivita = DB::table('commesse_attivita')
            ->where('id', $id_attivita)
            ->where('id_azienda', $azienda->id)
            ->first();

        if (!$attivita) {
            return redirect()->back()->with('error', 'Attività non trovata');
        }

        // Genera nome univoco per il file
        $nome_file = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Percorso di salvataggio
        $path_destinazione = 'uploads/commesse/attivita/' . $id_attivita . '/';

        // Crea la directory se non esiste
        if (!file_exists(public_path($path_destinazione))) {
            mkdir(public_path($path_destinazione), 0755, true);
        }

        // Sposta il file
        if ($file->move(public_path($path_destinazione), $nome_file)) {
            // Salva nel database
            DB::table('commesse_attivita_allegati')->insert([
                'id_attivita' => $id_attivita,
                'id_azienda' => $azienda->id,
                'id_utente' => $utente->id,
                'nome_originale' => $file->getClientOriginalName(),
                'nome_file' => $nome_file,
                'path_file' => $path_destinazione . $nome_file,
                'tipo_file' => $file->getMimeType(),
                'dimensione' => $file->getSize(),
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
                    ->where('id_azienda', $azienda->id)
                    ->update(['note' => $nuova_nota, 'updated_at' => now()]);
            }

            return redirect()->back()->with('success', 'Allegato caricato con successo');
        } else {
            return redirect()->back()->with('error', 'Errore durante il caricamento del file');
        }
    }

    public function elimina_allegato($id)
    {
        $this->is_loggato();
        $utente = session('utente_produzione');
        $azienda = session('azienda_produzione');

        // Trova l'allegato
        $allegato = DB::table('commesse_attivita_allegati')
            ->where('id', $id)
            ->where('id_azienda', $azienda->id)
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
            ->where('id_azienda', $azienda->id)
            ->delete();

        return redirect()->back()->with('success', 'Allegato eliminato con successo');
    }
    public function start_attivita(Request $request, $id)
    {
        $this->is_loggato();
        $utente = session('utente_produzione');
        $azienda = session('azienda_produzione');

        // Verifica che l'attività esista e appartenga all'azienda
        $attivita = DB::table('commesse_attivita')
            ->where('id', $id)
            ->where('id_azienda', $azienda->id)
            ->first();

        if (!$attivita) {
            return Redirect::to('produzione/dashboard')->with('error', 'Attività non trovata');
        }

        // Verifica se l'attività è già stata iniziata
        if ($attivita->data_inizio_effettiva) {
            return Redirect::to('produzione/dettaglio_attivita/' . $id)
                ->with('error', 'Questa attività è già stata avviata in precedenza');
        }

        // Aggiorna lo stato dell'attività con data inizio effettiva
        DB::table('commesse_attivita')
            ->where('id', $id)
            ->update([
                'data_inizio_effettiva' => now(),
                'stato' => 'in_corso',
                'updated_at' => now()
            ]);

        return Redirect::to('produzione/dettaglio_attivita/' . $id)
            ->with('success', 'Attività avviata con successo');
    }

    public function fine_attivita(Request $request, $id)
    {
        $this->is_loggato();
        $utente = session('utente_produzione');
        $azienda = session('azienda_produzione');
        $dati = $request->all();

        // Verifica che l'attività esista e appartenga all'azienda
        $attivita = DB::table('commesse_attivita')
            ->where('id', $id)
            ->where('id_azienda', $azienda->id)
            ->first();

        if (!$attivita) {
            return Redirect::to('produzione/dashboard')->with('error', 'Attività non trovata');
        }

        // Verifica se l'attività è già stata completata
        if ($attivita->data_fine_effettiva) {
            return Redirect::to('produzione/dettaglio_attivita/' . $id)
                ->with('error', 'Questa attività è già stata completata');
        }

        // Verifica se l'attività è stata avviata
        if (!$attivita->data_inizio_effettiva) {
            return Redirect::to('produzione/dettaglio_attivita/' . $id)
                ->with('error', 'Non puoi completare un\'attività non ancora avviata');
        }

        // Aggiorna lo stato dell'attività
        DB::table('commesse_attivita')
            ->where('id', $id)
            ->update([
                'data_fine_effettiva' => now(),
                'completamento' => 100, // Imposta il completamento al 100%
                'stato' => 'completata',
                'note' => $dati['note'] ?? $attivita->note, // Aggiorna le note se fornite
                'updated_at' => now()
            ]);

        return Redirect::to('produzione/dettaglio_attivita/' . $id)
            ->with('success', 'Attività completata con successo');
    }

// Modifica alla funzione esistente aggiorna_attivita
    public function aggiorna_attivita(Request $request, $id)
    {
        $this->is_loggato();
        $utente = session('utente_produzione');
        $azienda = session('azienda_produzione');
        $dati = $request->all();

        // Verifica che l'attività esista e appartenga all'azienda
        $attivita = DB::table('commesse_attivita')
            ->where('id', $id)
            ->where('id_azienda', $azienda->id)
            ->first();

        if (!$attivita) {
            return redirect()->back()->with('error', 'Attività non trovata');
        }

        // Aggiorna solo le note e il completamento ma NON lo stato
        // (lo stato viene gestito dalle funzioni start_attivita e fine_attivita)
        $completamento = $dati['completamento'] ?? $attivita->completamento;
        $note = $dati['note'] ?? $attivita->note;

        $update = [
            'completamento' => $completamento,
            'note' => $note,
            'updated_at' => now()
        ];

        DB::table('commesse_attivita')
            ->where('id', $id)
            ->update($update);

        return redirect()->back()->with('success', 'Attività aggiornata con successo');
    }

    public function cambia_fase(Request $request)
    {
        $this->is_loggato();
        $dati = $request->all();
        // Rimuovi la fase selezionata dalla sessione
        session(['fase_selezionata' => $dati['id_fase']]);
        session()->save();

        return Redirect::to('produzione/dashboard');
    }

    public function dashboard()
    {
        $this->is_loggato();
        $utente = session('utente_produzione');
        $azienda = session('azienda_produzione');
        $fase_corrente = null;
        $fasi = [];

        // Auto-detect: l'azienda usa ODL, attività commessa, o entrambi?
        $azienda_usa_odl = DB::select('SELECT COUNT(*) as c FROM odl WHERE id_azienda = ? LIMIT 1', [$azienda->id])[0]->c > 0;
        $azienda_usa_commesse = DB::select('SELECT COUNT(*) as c FROM commesse_attivita WHERE id_azienda = ? LIMIT 1', [$azienda->id])[0]->c > 0;

        if ($azienda_usa_odl && $azienda_usa_commesse) {
            $tipo_lavoro = 'tutti';
        } elseif ($azienda_usa_commesse) {
            $tipo_lavoro = 'commesse';
        } else {
            // default: ODL (anche se l'azienda non ne ha ancora)
            $tipo_lavoro = 'odl';
        }

        $odl_attivi = [];
        $attivita_commesse = [];

        if ($tipo_lavoro === 'odl' || $tipo_lavoro === 'tutti') {
            $odl_attivi = DB::select('
            SELECT DISTINCT o.*, a.titolo as articolo
            FROM odl o
            JOIN articoli a ON a.id = o.id_articolo
            JOIN odl_righe r ON r.id_odl = o.id
            WHERE o.id_azienda = ?
            AND o.stato IN (0, 1)
            AND (r.completato IS NULL OR r.completato = 0)
            AND (r.id_operatore_assegnato IS NULL OR r.id_operatore_assegnato = 0 OR r.id_operatore_assegnato = ?)
            ORDER BY o.data DESC, o.id DESC',
                [$utente->id_azienda, $utente->id]
            );
        }

        if ($tipo_lavoro === 'commesse' || $tipo_lavoro === 'tutti') {
            $attivita_commesse = DB::select('
            SELECT
                ca.*,
                c.codice_commessa,
                c.descrizione as commessa_descrizione,
                u.nome as nome_responsabile,
                u.cognome as cognome_responsabile
            FROM commesse_attivita ca
            JOIN commesse c ON c.id = ca.id_commessa
            LEFT JOIN utenti u ON u.id = ca.id_responsabile
            WHERE ca.id_azienda = ?
            AND ca.stato IN ("da_iniziare", "in_corso")
            AND (ca.data_fine IS NULL OR ca.data_fine >= CURDATE())
            ORDER BY ca.priorita DESC, ca.data_fine ASC',
                [$azienda->id]
            );
        }

        return View::make('produzione.dashboard', compact('utente', 'azienda', 'odl_attivi', 'attivita_commesse', 'fase_corrente', 'fasi', 'tipo_lavoro'));
    }




    public function dettaglio_odl($id)
    {
        $this->is_loggato();
        $utente = session('utente_produzione');
        $azienda = session('azienda_produzione');

        $fase_corrente = null;

        // Recupera l'ODL
        $odl = DB::table('odl')
            ->where('id', $id)
            ->where('id_azienda', $azienda->id)
            ->first();

        if (!$odl) {
            return Redirect::to('produzione/dashboard')
                ->with('error', 'Ordine di lavoro non trovato');
        }

        // Recupera l'articolo
        $articolo = DB::table('articoli')
            ->where('id', $odl->id_articolo)
            ->where('id_azienda', $azienda->id)
            ->first();

        if (!$articolo) {
            return Redirect::to('produzione/dashboard')
                ->with('error', 'Articolo non trovato');
        }

        // Recupera TUTTE le righe dell'ODL assegnate a questo operatore (o non assegnate)
        $odl_righe = DB::select('
        SELECT o.*, f.descrizione as nome_fase
        FROM odl_righe o
        JOIN fasi f ON f.id = o.id_fase
        WHERE o.id_odl = ?
        AND o.id_azienda = ?
        AND (o.id_operatore_assegnato IS NULL OR o.id_operatore_assegnato = 0 OR o.id_operatore_assegnato = ?)
        ORDER BY o.id ASC',
            [$id, $azienda->id, $utente->id]
        );

        if (empty($odl_righe)) {
            return Redirect::to('produzione/dashboard')
                ->with('error', 'Questo ordine di lavoro non ha fasi associate a te');
        }

        // Recupera tutte le fasi dell'articolo per verificare i prerequisiti
        $fasi_articolo = DB::select('
        SELECT fa.id_fase, f.descrizione, fa.tempo_medio_minuti
        FROM fasi_articoli fa
        JOIN fasi f ON f.id = fa.id_fase
        WHERE fa.id_articolo = ?
        AND fa.id_azienda = ?',
            [$articolo->id, $azienda->id]
        );

        // Per ogni riga, verifica se le fasi precedenti sono completate
        $fasi_precedenti_completate = true;
        $prerequisiti_per_riga = [];
        foreach ($odl_righe as &$riga) {
            $riga->fasi_precedenti_ok = true;
            $riga->prerequisiti_mancanti = [];

            // Trova l'indice di questa fase nell'ordinamento
            $fase_index = -1;
            foreach ($fasi_articolo as $index => $fase) {
                if ($fase->id_fase == $riga->id_fase) {
                    $fase_index = $index;
                    break;
                }
            }

            // Controlla tutte le fasi che precedono quella corrente
            if ($fase_index > 0) {
                for ($i = 0; $i < $fase_index; $i++) {
                    $fase_precedente = $fasi_articolo[$i];
                    $fase_precedente_completata = DB::table('odl_righe')
                        ->where('id_odl', $id)
                        ->where('id_fase', $fase_precedente->id_fase)
                        ->where('completato', 1)
                        ->exists();

                    if (!$fase_precedente_completata) {
                        $riga->fasi_precedenti_ok = false;
                        $riga->prerequisiti_mancanti[] = $fase_precedente->descrizione;
                    }
                }
            }
        }
        unset($riga);

        // Ottieni i materiali e allegati per la fase attuale
        foreach ($odl_righe as &$riga) {
            $riga->materiali = DB::select('
            SELECT a.titolo, a.id, db.qta, db.id_fase_articolo,a.um
            FROM articoli a
            LEFT JOIN distinta_base db ON db.id_materiale = a.id
            WHERE db.id_articolo = ?
            AND db.id_fase_articolo = ?
            AND a.id_azienda = ?',
                [$articolo->id, $riga->id_fase, $azienda->id]
            );

            // Recupera gli allegati per questa fase
            $riga->allegati = DB::table('odl_righe_allegati')
                ->where('id_odl_riga', $riga->id)
                ->where('id_azienda', $azienda->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Controlla eventuali prerequisiti aggiuntivi
        $prerequisiti_mancanti = [];
        if (!$fasi_precedenti_completate) {
            // Trova quali fasi precedenti non sono completate per mostrarle all'utente
            for ($i = 0; $i < $fase_corrente_index; $i++) {
                $fase_precedente = $fasi_articolo[$i];

                $fase_precedente_completata = DB::table('odl_righe')
                    ->where('id_odl', $id)
                    ->where('id_fase', $fase_precedente->id_fase)
                    ->where('completato', 1)
                    ->exists();

                if (!$fase_precedente_completata) {
                    $prerequisiti_mancanti[] = $fase_precedente->descrizione;
                }
            }
        }

        // Carica semilavorati dalla distinta base del prodotto finito (con la fase a cui sono associati)
        $semi_in_db = DB::select('
            SELECT a.id, a.titolo, a.codice_articolo, db.qta, db.id_fase_articolo
            FROM distinta_base db
            JOIN articoli a ON a.id = db.id_materiale
            WHERE db.id_articolo = ? AND a.tipologia = 3 AND a.id_azienda = ?',
            [$articolo->id, $azienda->id]
        );

        // Auto-popola odl_semilavorati se non già fatto
        foreach ($semi_in_db as $s) {
            $esiste = DB::table('odl_semilavorati')
                ->where('id_odl', $id)
                ->where('id_articolo', $s->id)
                ->where('id_fase', $s->id_fase_articolo)
                ->exists();
            if (!$esiste) {
                DB::table('odl_semilavorati')->insert([
                    'id_odl'     => $id,
                    'id_azienda' => $azienda->id,
                    'id_articolo'=> $s->id,
                    'id_fase'    => $s->id_fase_articolo,
                    'qta'        => $s->qta,
                    'stato'      => 0,
                    'created_at' => now(),
                ]);
            }
        }

        // Carica semilavorati ODL con info articolo e fase
        $odl_semilavorati = DB::select('
            SELECT os.*, a.titolo, a.codice_articolo, f.descrizione as nome_fase
            FROM odl_semilavorati os
            JOIN articoli a ON a.id = os.id_articolo
            LEFT JOIN fasi f ON f.id = os.id_fase
            WHERE os.id_odl = ? AND os.id_azienda = ?
            ORDER BY os.id_fase ASC, a.titolo ASC',
            [$id, $azienda->id]
        );

        // Per ogni semilavorato carica le sue fasi di produzione
        foreach ($odl_semilavorati as $semi) {
            $semi->fasi = DB::select('
                SELECT f.descrizione
                FROM fasi_articoli fa
                JOIN fasi f ON f.id = fa.id_fase
                WHERE fa.id_articolo = ? AND fa.id_azienda = ?
                ORDER BY fa.id ASC',
                [$semi->id_articolo, $azienda->id]
            );
        }

        return View::make('produzione.dettaglio_odl', compact(
            'utente', 'azienda', 'odl', 'odl_righe', 'articolo',
            'fase_corrente', 'fasi_precedenti_completate', 'prerequisiti_mancanti', 'odl_semilavorati'
        ));
    }

    public function salva_nota_fase(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente_produzione');
        $azienda = session('azienda_produzione');

        $id_riga = $request->input('id_riga');
        $nota = $request->input('nota', '');

        $riga = DB::table('odl_righe')
            ->where('id', $id_riga)
            ->where('id_azienda', $azienda->id)
            ->first();

        if ($riga) {
            DB::table('odl_righe')
                ->where('id', $id_riga)
                ->where('id_azienda', $azienda->id)
                ->update(['note' => $nota]);

            return redirect()->back()->with('success', 'Nota salvata con successo');
        }

        return redirect()->back()->with('error', 'Fase non trovata');
    }

    public function start_fase(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente_produzione');
        $dati = $request->all();

        if (isset($dati['id'])) {
            $righe = DB::select('
                SELECT * FROM odl_righe
                WHERE id = ? AND id_azienda = ?',
                [$dati['id'], $utente->id_azienda]
            );

            if (sizeof($righe) > 0) {
                $riga = $righe[0];

                $odl = DB::select('
                    SELECT * FROM odl 
                    WHERE id = ? AND id_azienda = ?',
                    [$riga->id_odl, $utente->id_azienda]
                );

                if (sizeof($odl) > 0) {
                    $odl = $odl[0];

                    // Aggiorna lo stato dell'ODL se era in attesa (0)
                    if ($odl->stato == 0) {
                        DB::update('
                            UPDATE odl 
                            SET stato = 1 
                            WHERE id = ? AND id_azienda = ?',
                            [$odl->id, $utente->id_azienda]
                        );
                    }

                    // Registra l'inizio della fase e assegna l'operatore
                    DB::update('
                        UPDATE odl_righe
                        SET inizio = NOW(), id_operatore_assegnato = COALESCE(id_operatore_assegnato, ?)
                        WHERE id = ? AND id_azienda = ?',
                        [$utente->id, $dati['id'], $utente->id_azienda]
                    );

                    return Redirect::to('produzione/dettaglio_odl/' . $riga->id_odl);
                }
            }
        }

        return Redirect::to('produzione/dashboard');
    }



    public function fine_fase(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente_produzione');
        $utente_produzione = session('utente_produzione');
        $dati = $request->all();

        if (isset($dati['id'])) {
            $righe = DB::select('
                SELECT * FROM odl_righe 
                WHERE id = ? AND id_azienda = ?',
                [$dati['id'], $utente->id_azienda]
            );

            if (sizeof($righe) > 0) {
                $riga = $righe[0];

                // Assegna l'operatore se non è già assegnato
                if (empty($riga->id_operatore_assegnato)) {
                    DB::table('odl_righe')
                        ->where('id', $riga->id)
                        ->update(['id_operatore_assegnato' => $utente->id]);
                }

                ApiController::chudi_fase(
                    $dati['id'],
                    $dati['quantita_fatta'],
                    $dati['note'],
                    $dati['id_fase'],
                    $dati['lotto'] ?? null,  // Passa il lotto solo se esiste, altrimenti null
                    $dati['scadenza_lotto'] ?? null,
                    $utente,
                    $riga->id_odl,
                    $dati['id_dorig'],
                    $dati['lotto_produzione'] ?? null
                );

                // Completa automaticamente i semilavorati associati a questa fase
                if (isset($dati['semilavorati_completati']) && is_array($dati['semilavorati_completati'])) {
                    DB::table('odl_semilavorati')
                        ->whereIn('id', $dati['semilavorati_completati'])
                        ->where('id_azienda', $utente->id_azienda)
                        ->where('stato', 0)
                        ->update([
                            'stato' => 1,
                            'id_operatore' => $utente->id,
                            'completato_at' => now(),
                        ]);
                }

                $righe_odl = DB::SELECT('SELECT * from odl_righe where id_odl = ? AND id_azienda = ?', [$riga->id_odl, $utente->id_azienda]);

                $num_righe = sizeof($righe_odl);
                $num_righe_completate = 0;

                foreach ($righe_odl as $riga_odl) {
                    if ($riga_odl->completato == 1) {
                        $num_righe_completate++;
                    }
                }

                if ($num_righe_completate == $num_righe) {
                    return Redirect::to('stampa/etichetta_odl/' . $riga->id_odl);
                } else {
                    return Redirect::to('produzione/dettaglio_odl/' . $riga->id_odl);
                }
            
            }
        }

        return Redirect::to('produzione/dashboard');
    }

    public function logout()
    {
        $azienda = session('azienda_produzione');
        $token = $azienda->token_azienda ?? null;

        session()->forget(['utente', 'utente_produzione', 'azienda_produzione', 'tipo_lavoro', 'anno']);

        if ($token) {
            return Redirect::to('produzione/login/' . $token);
        }
        return Redirect::to('/');
    }

    private function is_loggato()
    {
        if (!session()->has('utente_produzione')) {
            return Redirect::to('/')->send();
        }
    }
}
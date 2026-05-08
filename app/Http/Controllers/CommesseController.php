<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;

class CommesseController extends Controller
{
    /**
     * Verifica se l'utente è loggato
     */
    public function is_loggato()
    {
        if (!session()->has('utente')) {
            return Redirect::to('/')->send();
        }
    }

    /**
     * Mostra l'elenco delle commesse
     */
    public function index(Request $request)
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
            return Redirect::to('commesse');
        }

        if(isset($dati['modifica'])){
            unset($dati['modifica']);
            DB::table('commesse')
                ->where('id', $dati['id'])
                ->where('id_azienda', $utente->id_azienda)
                ->update($dati);
            return Redirect::to('commesse');
        }

        if(isset($dati['elimina'])){
            unset($dati['elimina']);
            DB::table('commesse')
                ->where('id', $dati['id'])
                ->where('id_azienda', $utente->id_azienda)
                ->delete();
            return Redirect::to('commesse');
        }

        $commesse = DB::table('commesse')
            ->where('id_azienda', $utente->id_azienda)
            ->orderBy('data_apertura', 'desc')
            ->get();

        return View::make('commesse.index', compact('utente', 'commesse'));
    }

    /**
     * Mostra la dashboard di una commessa specifica
     */
    public function dashboard($id_commessa)
    {
        $this->is_loggato();
        $utente = session('utente');



        // Recupero commessa
        $commessa = DB::table('commesse')
            ->where('id', $id_commessa)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if(!$commessa) {
            return Redirect::to('commesse');
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

        // Recupero documenti collegati alla commessa
        // Usa ENTRAMBI gli approcci: dotes.id_commessa (diretto) e commesse_documenti (tabella junction)
        $documenti = DB::select('
            SELECT d.*, c.ragione_sociale as cliente_nome
            FROM dotes d
            LEFT JOIN clienti c ON c.id = d.id_cliente
            WHERE d.id_azienda = ?
            AND (
                d.id_commessa = ?
                OR d.id IN (SELECT id_dotes FROM commesse_documenti WHERE id_commessa = ? AND id_azienda = ?)
            )
            ORDER BY d.data_doc DESC',
            [$utente->id_azienda, $id_commessa, $id_commessa, $utente->id_azienda]
        );

        // Recupero movimenti di magazzino collegati alla commessa
        // Include sia movimenti collegati direttamente, sia quelli generati da ODL della commessa
        $movimenti_magazzino = DB::select('
            SELECT m.*, a.codice_articolo, a.titolo as articolo_nome, mg.descrizione as magazzino_nome,
                   a.prezzo as prezzo_articolo, o_ref.numero as odl_numero
            FROM mgmov m
            LEFT JOIN articoli a ON a.id = m.id_articolo
            LEFT JOIN mg ON mg.id = m.id_mg
            LEFT JOIN odl o_ref ON o_ref.id = m.id_odl
            WHERE m.id_azienda = ?
            AND (
                m.id_commessa = ?
                OR m.id_odl IN (SELECT o.id FROM odl o WHERE o.id_commessa = ? AND o.id_azienda = ?)
                OR m.id_odl IN (SELECT o.id FROM odl o INNER JOIN dotes d ON d.id = o.id_dotes WHERE d.id_commessa = ? AND o.id_azienda = ?)
            )
            ORDER BY m.datamov DESC',
            [$utente->id_azienda, $id_commessa, $id_commessa, $utente->id_azienda, $id_commessa, $utente->id_azienda]
        );

        // Recupero ODL collegati alla commessa
        // Cerca ODL con id_commessa diretto, oppure ODL creati da documenti collegati alla commessa
        $odl_commessa = DB::select('
            SELECT o.*, a.titolo as articolo_nome, a.codice_articolo,
                   d.cd_do, d.numero_doc
            FROM odl o
            LEFT JOIN articoli a ON a.id = o.id_articolo
            LEFT JOIN dotes d ON d.id = o.id_dotes
            WHERE o.id_azienda = ?
            AND (
                o.id_commessa = ?
                OR d.id_commessa = ?
            )
            ORDER BY o.data DESC',
            [$utente->id_azienda, $id_commessa, $id_commessa]
        );

        // Calcolo costi materiali da movimenti di magazzino (solo scarichi)
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

        // Calcolo ricavi (documenti di vendita collegati alla commessa)
        $ricavi = DB::select('
            SELECT COALESCE(SUM(d.totale), 0) as totale
            FROM dotes d
            WHERE d.id_azienda = ?
            AND (
                d.id_commessa = ?
                OR d.id IN (SELECT id_dotes FROM commesse_documenti WHERE id_commessa = ? AND id_azienda = ?)
            )
            AND d.cd_do IN (SELECT cd_do FROM `do` WHERE fatturazione_uscita = 1 AND id_azienda = ?)',
            [$utente->id_azienda, $id_commessa, $id_commessa, $utente->id_azienda, $utente->id_azienda]
        );

        // Calcolo costi (documenti di acquisto)
        $costi = DB::select('
            SELECT COALESCE(SUM(d.totale), 0) as totale
            FROM dotes d
            WHERE d.id_azienda = ?
            AND (
                d.id_commessa = ?
                OR d.id IN (SELECT id_dotes FROM commesse_documenti WHERE id_commessa = ? AND id_azienda = ?)
            )
            AND d.cd_do IN (SELECT cd_do FROM `do` WHERE fatturazione_ingresso = 1 AND id_azienda = ?)',
            [$utente->id_azienda, $id_commessa, $id_commessa, $utente->id_azienda, $utente->id_azienda]
        );

        // Calcolo incassi (pagamenti ricevuti)
        $incassi = DB::select('
            SELECT COALESCE(SUM(s.importo_pagato), 0) as totale
            FROM scadenziario s
            INNER JOIN dotes d ON d.id = s.id_dotes
            WHERE d.id_azienda = ?
            AND (
                d.id_commessa = ?
                OR d.id IN (SELECT id_dotes FROM commesse_documenti WHERE id_commessa = ? AND id_azienda = ?)
            )
            AND s.tipo_movimento = "entrata"',
            [$utente->id_azienda, $id_commessa, $id_commessa, $utente->id_azienda]
        );

        // Calcolo pagamenti (pagamenti effettuati)
        $pagamenti = DB::select('
            SELECT COALESCE(SUM(s.importo_pagato), 0) as totale
            FROM scadenziario s
            INNER JOIN dotes d ON d.id = s.id_dotes
            WHERE d.id_azienda = ?
            AND (
                d.id_commessa = ?
                OR d.id IN (SELECT id_dotes FROM commesse_documenti WHERE id_commessa = ? AND id_azienda = ?)
            )
            AND s.tipo_movimento = "uscita"',
            [$utente->id_azienda, $id_commessa, $id_commessa, $utente->id_azienda]
        );

        // Calcolo incassi da ricevere
        $incassi_da_ricevere = DB::select('
            SELECT COALESCE(SUM(s.importo - s.importo_pagato), 0) as totale
            FROM scadenziario s
            INNER JOIN dotes d ON d.id = s.id_dotes
            WHERE d.id_azienda = ?
            AND (
                d.id_commessa = ?
                OR d.id IN (SELECT id_dotes FROM commesse_documenti WHERE id_commessa = ? AND id_azienda = ?)
            )
            AND s.tipo_movimento = "entrata" AND s.importo_pagato < s.importo',
            [$utente->id_azienda, $id_commessa, $id_commessa, $utente->id_azienda]
        );

        // Calcolo pagamenti da effettuare
        $pagamenti_da_effettuare = DB::select('
            SELECT COALESCE(SUM(s.importo - s.importo_pagato), 0) as totale
            FROM scadenziario s
            INNER JOIN dotes d ON d.id = s.id_dotes
            WHERE d.id_azienda = ?
            AND (
                d.id_commessa = ?
                OR d.id IN (SELECT id_dotes FROM commesse_documenti WHERE id_commessa = ? AND id_azienda = ?)
            )
            AND s.tipo_movimento = "uscita" AND s.importo_pagato < s.importo',
            [$utente->id_azienda, $id_commessa, $id_commessa, $utente->id_azienda]
        );

        return View::make('commesse.dashboard', compact(
            'utente',
            'commessa',
            'attivita',
            'documenti',
            'movimenti_magazzino',
            'odl_commessa',
            'costi_materiali',
            'costi_manodopera',
            'dettaglio_manodopera',
            'ricavi',
            'costi',
            'incassi',
            'pagamenti',
            'incassi_da_ricevere',
            'pagamenti_da_effettuare'
        ));
    }

    /**
     * Gestione delle attività di una commessa
     */
    public function attivita($id_commessa, Request $request)
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
            return Redirect::to('commesse');
        }

        if(isset($dati['aggiungi'])){
            unset($dati['aggiungi']);
            $dati['id_commessa'] = $id_commessa;
            $dati['id_utente'] = $utente->id;
            $dati['id_azienda'] = $utente->id_azienda;
            DB::table('commesse_attivita')->insert($dati);
            return Redirect::to('commesse/'.$id_commessa.'/attivita');
        }

        if(isset($dati['modifica'])){
            unset($dati['modifica']);
            DB::table('commesse_attivita')
                ->where('id', $dati['id'])
                ->where('id_azienda', $utente->id_azienda)
                ->update($dati);
            return Redirect::to('commesse/'.$id_commessa.'/attivita');
        }

        if(isset($dati['elimina'])){
            unset($dati['elimina']);
            DB::table('commesse_attivita')
                ->where('id', $dati['id'])
                ->where('id_azienda', $utente->id_azienda)
                ->delete();
            return Redirect::to('commesse/'.$id_commessa.'/attivita');
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
            ->where('id_tipologia', 3) // Assumo che id_tipologia 3 siano i dipendenti
            ->orderBy('cognome')
            ->get();

        return View::make('commesse.attivita', compact('utente', 'commessa', 'attivita', 'dipendenti'));
    }

    /**
     * Gestione dei documenti collegati a una commessa
     */
    public function documenti($id_commessa, Request $request)
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
            return Redirect::to('commesse');
        }

        if(isset($dati['collega_documento'])){
            unset($dati['collega_documento']);
            
            // Verifica se il documento è già collegato
            $esistente = DB::table('commesse_documenti')
                ->where('id_commessa', $id_commessa)
                ->where('id_dotes', $dati['id_dotes'])
                ->where('id_azienda', $utente->id_azienda)
                ->exists();
                
            if(!$esistente) {
                DB::table('commesse_documenti')->insert([
                    'id_commessa' => $id_commessa,
                    'id_dotes' => $dati['id_dotes'],
                    'id_azienda' => $utente->id_azienda,
                    'id_utente' => $utente->id,
                    'created_at' => now()
                ]);
            }
            
            return Redirect::to('commesse/'.$id_commessa.'/documenti');
        }

        if(isset($dati['scollega_documento'])){
            unset($dati['scollega_documento']);
            
            DB::table('commesse_documenti')
                ->where('id_commessa', $id_commessa)
                ->where('id_dotes', $dati['id_dotes'])
                ->where('id_azienda', $utente->id_azienda)
                ->delete();
                
            return Redirect::to('commesse/'.$id_commessa.'/documenti');
        }

        // Recupero documenti collegati alla commessa
        $documenti = DB::select('
            SELECT d.*, c.ragione_sociale as cliente_nome, do.descrizione as tipo_documento
            FROM dotes d
            LEFT JOIN clienti c ON c.id = d.id_cliente
            LEFT JOIN do ON do.cd_do = d.cd_do
            WHERE d.id IN (
                SELECT id_dotes FROM commesse_documenti WHERE id_commessa = ? AND id_azienda = ?
            )
            ORDER BY d.data_doc DESC',
            [$id_commessa, $utente->id_azienda]
        );

        // Recupero documenti disponibili per il collegamento (non ancora collegati)
        $documenti_disponibili = DB::select('
            SELECT d.*, c.ragione_sociale as cliente_nome, do.descrizione as tipo_documento
            FROM dotes d
            LEFT JOIN clienti c ON c.id = d.id_cliente
            LEFT JOIN do ON do.cd_do = d.cd_do
            WHERE d.id_azienda = ?
            AND d.id NOT IN (
                SELECT id_dotes FROM commesse_documenti WHERE id_commessa = ? AND id_azienda = ?
            )
            ORDER BY d.data_doc DESC
            LIMIT 100',
            [$utente->id_azienda, $id_commessa, $utente->id_azienda]
        );

        return View::make('commesse.documenti', compact('utente', 'commessa', 'documenti', 'documenti_disponibili'));
    }

    /**
     * Gestione dei movimenti di magazzino collegati a una commessa
     */
    public function magazzino($id_commessa, Request $request)
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
            return Redirect::to('commesse');
        }

        if(isset($dati['collega_movimento'])){
            unset($dati['collega_movimento']);
            
            // Aggiorna il movimento di magazzino con l'id della commessa
            DB::table('mgmov')
                ->where('id', $dati['id_movimento'])
                ->where('id_azienda', $utente->id_azienda)
                ->update(['id_commessa' => $id_commessa]);
                
            return Redirect::to('commesse/'.$id_commessa.'/magazzino');
        }

        if(isset($dati['scollega_movimento'])){
            unset($dati['scollega_movimento']);
            
            // Rimuove il collegamento con la commessa
            DB::table('mgmov')
                ->where('id', $dati['id_movimento'])
                ->where('id_azienda', $utente->id_azienda)
                ->update(['id_commessa' => null]);
                
            return Redirect::to('commesse/'.$id_commessa.'/magazzino');
        }

        // Recupero movimenti di magazzino collegati alla commessa
        $movimenti = DB::select('
            SELECT m.*, a.codice_articolo, a.titolo as articolo_nome, mg.descrizione as magazzino_nome
            FROM mgmov m
            LEFT JOIN articoli a ON a.id = m.id_articolo
            LEFT JOIN mg ON mg.id = m.id_mg
            WHERE m.id_commessa = ? AND m.id_azienda = ?
            ORDER BY m.datamov DESC',
            [$id_commessa, $utente->id_azienda]
        );

        // Recupero movimenti disponibili per il collegamento (non ancora collegati)
        $movimenti_disponibili = DB::select('
            SELECT m.*, a.codice_articolo, a.titolo as articolo_nome, mg.descrizione as magazzino_nome
            FROM mgmov m
            LEFT JOIN articoli a ON a.id = m.id_articolo
            LEFT JOIN mg ON mg.id = m.id_mg
            WHERE m.id_azienda = ? AND (m.id_commessa IS NULL OR m.id_commessa = 0)
            ORDER BY m.datamov DESC
            LIMIT 100',
            [$utente->id_azienda]
        );

        return View::make('commesse.magazzino', compact('utente', 'commessa', 'movimenti', 'movimenti_disponibili'));
    }

    /**
     * Gestione dei pagamenti e incassi di una commessa
     */
    public function pagamenti($id_commessa, Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        // Recupero commessa
        $commessa = DB::table('commesse')
            ->where('id', $id_commessa)
            ->where('id_azienda', $utente->id_azienda)
            ->first();

        if(!$commessa) {
            return Redirect::to('commesse');
        }

        // Recupero pagamenti e incassi relativi ai documenti collegati alla commessa
        $pagamenti = DB::select('
            SELECT s.*, d.cd_do, d.numero_doc, d.data_doc, d.ragione_sociale_fatturazione,
                   DATEDIFF(CURDATE(), s.data_scadenza) as giorni_scaduto
            FROM scadenziario s
            LEFT JOIN dotes d ON d.id = s.id_dotes
            WHERE s.id_dotes IN (
                SELECT id_dotes FROM commesse_documenti WHERE id_commessa = ? AND id_azienda = ?
            )
            AND s.tipo_movimento = "uscita"
            ORDER BY s.data_scadenza ASC',
            [$id_commessa, $utente->id_azienda]
        );

        $incassi = DB::select('
            SELECT s.*, d.cd_do, d.numero_doc, d.data_doc, d.ragione_sociale_fatturazione,
                   DATEDIFF(CURDATE(), s.data_scadenza) as giorni_scaduto
            FROM scadenziario s
            LEFT JOIN dotes d ON d.id = s.id_dotes
            WHERE s.id_dotes IN (
                SELECT id_dotes FROM commesse_documenti WHERE id_commessa = ? AND id_azienda = ?
            )
            AND s.tipo_movimento = "entrata"
            ORDER BY s.data_scadenza ASC',
            [$id_commessa, $utente->id_azienda]
        );

        return View::make('commesse.pagamenti', compact('utente', 'commessa', 'pagamenti', 'incassi'));
    }

    /**
     * Aggiorna lo stato di una commessa
     */
    public function aggiorna_stato(Request $request, $id_commessa)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        DB::table('commesse')
            ->where('id', $id_commessa)
            ->where('id_azienda', $utente->id_azienda)
            ->update(['stato' => $dati['stato']]);

        return Redirect::to('commesse/'.$id_commessa.'/dashboard');
    }

    /**
     * Aggiorna i valori di budget, costi e ricavi di una commessa
     */
    public function aggiorna_valori(Request $request, $id_commessa)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        DB::table('commesse')
            ->where('id', $id_commessa)
            ->where('id_azienda', $utente->id_azienda)
            ->update([
                'budget' => $dati['budget'],
                'costi' => $dati['costi'],
                'ricavi' => $dati['ricavi']
            ]);

        return Redirect::to('commesse/'.$id_commessa.'/dashboard');
    }
} 
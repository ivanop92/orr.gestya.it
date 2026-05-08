<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Log;

class WinfattController extends Controller
{
    public function is_loggato()
    {
        if (!session()->has('utente')) return Redirect::to('admin/login')->send();
    }

    public function import_clienti(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if (isset($dati['importa_winfatt'])) {
            return $this->processaTxtClienti($request, $utente);
        }

        return response()->json(['error' => 'Metodo non supportato'], 400);
    }

    public function import_fornitori(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');
        $dati = $request->all();

        if (isset($dati['importa_winfatt_fornitori'])) {
            return $this->processaTxtFornitori($request, $utente);
        }

        return response()->json(['error' => 'Metodo non supportato'], 400);
    }

    private function processaTxtClienti($request, $utente)
    {
        try {
            // Verifica che il file sia stato caricato
            if (!$request->hasFile('file_winfatt')) {
                return response()->json(['success' => false, 'message' => 'Nessun file caricato']);
            }

            $file = $request->file('file_winfatt');

            // Verifica che sia un file txt
            if ($file->getClientOriginalExtension() !== 'txt') {
                return response()->json(['success' => false, 'message' => 'Il file deve essere in formato TXT']);
            }

            // Legge il contenuto del file
            $contenuto_txt = file_get_contents($file->getRealPath());

            if (empty($contenuto_txt)) {
                return response()->json(['success' => false, 'message' => 'File vuoto']);
            }

            $righe = explode("\n", $contenuto_txt);
            $clienti_importati = 0;
            $clienti_aggiornati = 0;
            $errori = [];

            foreach ($righe as $numero_riga => $riga) {
                $riga = trim($riga);
                if (empty($riga)) continue;

                try {
                    $campi = explode("\t", $riga);

                    // Verifica che ci siano abbastanza campi (dal tuo esempio ne vedo almeno 17)
                    if (count($campi) < 17) {
                        $errori[] = "Riga " . ($numero_riga + 1) . ": Numero di campi insufficiente (" . count($campi) . " trovati)";
                        continue;
                    }

                    // Mappa i campi del TXT ai campi del database
                    $cliente_data = $this->mappaCampiWinfatt($campi, $utente);

                    // Verifica se il cliente esiste già usando il codice cliente Winfatt
                    $cliente_esistente = DB::select('SELECT id FROM clienti WHERE cd_cf = ? AND id_azienda = ?',
                        [$cliente_data['cd_cf'], $utente->id_azienda]);

                    if (empty($cliente_esistente)) {
                        // Inserisce nuovo cliente
                        DB::table('clienti')->insert($cliente_data);
                        $clienti_importati++;
                        Log::info('Nuovo cliente inserito: ' . $cliente_data['ragione_sociale']);
                    } else {
                        // Aggiorna cliente esistente (rimuovi cd_cf per non sovrascriverlo)
                        $update_data = $cliente_data;
                        unset($update_data['cd_cf']);

                        DB::table('clienti')
                            ->where('cd_cf', $cliente_data['cd_cf'])
                            ->where('id_azienda', $utente->id_azienda)
                            ->update($update_data);
                        $clienti_aggiornati++;
                        Log::info('Cliente aggiornato: ' . $cliente_data['ragione_sociale']);
                    }

                } catch (\Exception $e) {
                    $errori[] = "Riga " . ($numero_riga + 1) . ": " . $e->getMessage();
                    Log::error('Errore importazione riga ' . ($numero_riga + 1) . ': ' . $e->getMessage());
                }
            }

            $message = "Importazione completata.\nNuovi clienti: {$clienti_importati}\nClienti aggiornati: {$clienti_aggiornati}";
            if (!empty($errori)) {
                $message .= "\n\nPrimi 5 errori:\n" . implode("\n", array_slice($errori, 0, 5));
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $clienti_importati + $clienti_aggiornati,
                'new' => $clienti_importati,
                'updated' => $clienti_aggiornati
            ]);

        } catch (\Exception $e) {
            Log::error('Errore generale importazione: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'importazione: ' . $e->getMessage()
            ]);
        }
    }

    private function mappaCampiWinfatt($campi, $utente)
    {
        // Usa il codice cliente di Winfatt come cd_cf
        $cd_cf_winfatt = trim($campi[0]);

        // Pulisce e valida la partita IVA
        $piva = $this->pulisciPartitaIva(trim($campi[8]));

        // Debug per tracciare i dati
        Log::info('Importazione cliente - Codice: ' . $cd_cf_winfatt . ', P.IVA originale: ' . trim($campi[8]) . ', P.IVA pulita: ' . $piva);

        // Basandomi sul tuo file di esempio, mappa i campi così:
        // 0: Codice cliente
        // 1: Ragione sociale
        // 2: Nome (se presente)
        // 3: Cognome (se presente)
        // 4: Indirizzo
        // 5: CAP (nelle tue righe sembra essere nel campo comune)
        // 6: Comune
        // 7: Provincia
        // 8: Nazione
        // 9: Partita IVA
        // 10: Codice Fiscale
        // 11: Codice SDI
        // 12: PEC
        // 13: Telefono
        // 14: Telefono2
        // 15: Email
        // 16: Attivo

        return [
            'id_azienda' => $utente->id_azienda,
            'id_utente' => 0,
            'id_agente' => 0,
            'id_reparto' => null,
            'id_sezione' => 0,
            'cd_cf' => $cd_cf_winfatt,
            'ragione_sociale' => trim($campi[1]) ?: 'Cliente Importato',
            'nome' => isset($campi[2]) ? trim($campi[2]) : null,
            'cognome' => isset($campi[3]) ? trim($campi[3]) : null,
            'indirizzo' => trim($campi[4]) ?: null,
            'cap' => $this->estraiCAP(trim($campi[5])),
            'comune' => $this->pulisciComune(trim($campi[5])),
            'provincia' => trim($campi[6]) ?: null,
            'nazione' => trim($campi[7]) ?: 'IT',
            'piva' => $piva,
            'codice_fiscale' => trim($campi[9]) ?: null,
            'sdi' => trim($campi[10]) ?: null,
            'pec' => trim($campi[11]) ?: null,
            'telefono' => trim($campi[12]) ?: null,
            'telefono2' => trim($campi[13]) ?: null,
            'email' => trim($campi[14]) ?: '',
            'attivo' => isset($campi[16]) ? (int)(trim($campi[16]) ?: 1) : 1,
            'immagine' => '/default/assets/images/users/user-dummy-img.jpg',
            'esigibilita_iva' => 'I',
            'data_nascita' => null,
            'luogo_nascita' => null,
            'cciaa' => null,
            'rea' => null
        ];
    }

    private function pulisciPartitaIva($piva)
    {
        if (empty($piva) || $piva === '0000000' || strlen($piva) < 8) {
            return null;
        }

        // Rimuove tutti i caratteri non numerici
        $piva_pulita = preg_replace('/[^0-9]/', '', $piva);

        // Verifica che sia una P.IVA italiana valida (11 cifre)
        if (strlen($piva_pulita) == 11 && is_numeric($piva_pulita)) {
            return $piva_pulita;
        }

        return !empty($piva_pulita) ? $piva_pulita : null;
    }

    private function estraiCAP($campo_comune)
    {
        // Cerca pattern CAP (5 cifre) nel campo comune
        if (preg_match('/\b(\d{5})\b/', $campo_comune, $matches)) {
            return $matches[1];
        }
        return null;
    }

    private function pulisciComune($campo_comune)
    {
        // Rimuove il CAP dal nome del comune
        $comune = preg_replace('/\b\d{5}\b/', '', $campo_comune);
        return trim($comune) ?: null;
    }

    private function processaTxtFornitori($request, $utente)
    {
        try {
            // Verifica che il file sia stato caricato
            if (!$request->hasFile('file_winfatt_fornitori')) {
                return response()->json(['success' => false, 'message' => 'Nessun file caricato']);
            }

            $file = $request->file('file_winfatt_fornitori');

            // Verifica che sia un file txt
            if ($file->getClientOriginalExtension() !== 'txt') {
                return response()->json(['success' => false, 'message' => 'Il file deve essere in formato TXT']);
            }

            // Legge il contenuto del file
            $contenuto_txt = file_get_contents($file->getRealPath());

            if (empty($contenuto_txt)) {
                return response()->json(['success' => false, 'message' => 'File vuoto']);
            }

            $righe = explode("\n", $contenuto_txt);
            $fornitori_importati = 0;
            $fornitori_aggiornati = 0;
            $errori = [];

            foreach ($righe as $numero_riga => $riga) {
                $riga = trim($riga);
                if (empty($riga)) continue;

                try {
                    $campi = explode("\t", $riga);

                    // Verifica che ci siano abbastanza campi
                    if (count($campi) < 17) {
                        $errori[] = "Riga " . ($numero_riga + 1) . ": Numero di campi insufficiente (" . count($campi) . " trovati)";
                        continue;
                    }

                    // Mappa i campi del TXT ai campi del database
                    $fornitore_data = $this->mappaCampiFornitori($campi, $utente);

                    // Verifica se il fornitore esiste già usando il codice fornitore Winfatt
                    $fornitore_esistente = DB::select('SELECT id FROM fornitori WHERE cd_cf = ? AND id_azienda = ?',
                        [$fornitore_data['cd_cf'], $utente->id_azienda]);

                    if (empty($fornitore_esistente)) {
                        // Inserisce nuovo fornitore
                        DB::table('fornitori')->insert($fornitore_data);
                        $fornitori_importati++;
                        Log::info('Nuovo fornitore inserito: ' . $fornitore_data['ragione_sociale']);
                    } else {
                        // Aggiorna fornitore esistente (rimuovi cd_cf per non sovrascriverlo)
                        $update_data = $fornitore_data;
                        unset($update_data['cd_cf']);

                        DB::table('fornitori')
                            ->where('cd_cf', $fornitore_data['cd_cf'])
                            ->where('id_azienda', $utente->id_azienda)
                            ->update($update_data);
                        $fornitori_aggiornati++;
                        Log::info('Fornitore aggiornato: ' . $fornitore_data['ragione_sociale']);
                    }

                } catch (\Exception $e) {
                    $errori[] = "Riga " . ($numero_riga + 1) . ": " . $e->getMessage();
                    Log::error('Errore importazione fornitore riga ' . ($numero_riga + 1) . ': ' . $e->getMessage());
                }
            }

            $message = "Importazione fornitori completata.\nNuovi fornitori: {$fornitori_importati}\nFornitori aggiornati: {$fornitori_aggiornati}";
            if (!empty($errori)) {
                $message .= "\n\nPrimi 5 errori:\n" . implode("\n", array_slice($errori, 0, 5));
            }

            return response()->json([
                'success' => true,
                'message' => $message,
                'count' => $fornitori_importati + $fornitori_aggiornati,
                'new' => $fornitori_importati,
                'updated' => $fornitori_aggiornati
            ]);

        } catch (\Exception $e) {
            Log::error('Errore generale importazione fornitori: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Errore durante l\'importazione: ' . $e->getMessage()
            ]);
        }
    }

    private function mappaCampiFornitori($campi, $utente)
    {
        // Usa il codice fornitore di Winfatt come cd_cf
        $cd_cf_winfatt = trim($campi[0]);

        // Pulisce e valida la partita IVA
        $piva = $this->pulisciPartitaIva(trim($campi[8]));

        // Debug per tracciare i dati
        Log::info('Importazione fornitore - Codice: ' . $cd_cf_winfatt . ', P.IVA originale: ' . trim($campi[8]) . ', P.IVA pulita: ' . $piva);

        // Basandomi sul file fornitori, mappa i campi così:
        // Struttura uguale ai clienti ma con id_tipologia = 1 (fornitore)
        return [
            'id_azienda' => $utente->id_azienda,
            'id_utente' => 0,
            'id_agente' => 0,
            'id_reparto' => null,
            'id_sezione' => 0,
            'cd_cf' => $cd_cf_winfatt,
            'ragione_sociale' => trim($campi[1]) ?: 'Fornitore Importato',
            'nome' => isset($campi[2]) ? trim($campi[2]) : null,
            'cognome' => isset($campi[3]) ? trim($campi[3]) : null,
            'indirizzo' => trim($campi[4]) ?: null,
            'cap' => $this->estraiCAP(trim($campi[5])),
            'comune' => $this->pulisciComune(trim($campi[5])),
            'provincia' => trim($campi[6]) ?: null,
            'nazione' => trim($campi[7]) ?: 'IT',
            'piva' => $piva,
            'codice_fiscale' => trim($campi[9]) ?: null,
            'sdi' => trim($campi[10]) ?: null,
            'pec' => trim($campi[11]) ?: null,
            'telefono' => trim($campi[12]) ?: null,
            'telefono2' => trim($campi[13]) ?: null,
            'email' => trim($campi[14]) ?: '',
            'attivo' => isset($campi[16]) ? (int)(trim($campi[16]) ?: 1) : 1,
            'immagine' => '/default/assets/images/users/user-dummy-img.jpg',
            'esigibilita_iva' => 'I',
            'data_nascita' => null,
            'luogo_nascita' => null,
            'cciaa' => null,
            'rea' => null
        ];
    }
}
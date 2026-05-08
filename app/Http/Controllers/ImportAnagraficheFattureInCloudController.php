<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ImportAnagraficheFattureInCloudController extends Controller
{
    public function index(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        return View::make('utente.import_anagrafiche_fatture_in_cloud', compact('utente'));
    }

    public function import_clienti_excel(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        if (!$request->hasFile('file_excel')) {
            Session::flash('tipo_messaggio', 'error');
            Session::flash('messaggio', 'Nessun file caricato');
            return Redirect::back();
        }

        $file = $request->file('file_excel');
        $extension = $file->getClientOriginalExtension();

        // Verifica che il file sia un Excel
        if (!in_array($extension, ['xlsx', 'xls'])) {
            Session::flash('tipo_messaggio', 'error');
            Session::flash('messaggio', 'Il file deve essere in formato Excel (.xlsx o .xls)');
            return Redirect::back();
        }

        // Carica il file Excel
        $importati = 0;
        $aggiornati = 0;
        $errori = 0;
        $log = [];

        try {
            // Legge il file Excel
            $data = Excel::toArray(new \stdClass(), $file)[0];

            // Debug log
            $log[] = "File letto con successo. Trovate " . count($data) . " righe.";

            // Se il file è vuoto
            if (count($data) <= 1) {
                Session::flash('tipo_messaggio', 'error');
                Session::flash('messaggio', 'Il file Excel non contiene dati');
                return Redirect::back();
            }

            // La prima riga contiene le intestazioni
            $headers = $data[0];
            $log[] = "Intestazioni trovate: " . implode(", ", $headers);

            // Mappa per tenere traccia degli indici delle colonne
            $column_indexes = [];
            foreach ($headers as $index => $header) {
                $column_indexes[trim($header)] = $index;
            }

            $log[] = "Indici colonne: " . json_encode($column_indexes);

            // Processa ogni riga (saltando l'intestazione)
            for ($i = 1; $i < count($data); $i++) {
                $row = $data[$i];

                try {
                    // Prepara i dati del cliente
                    $cliente_data = [
                        'id_azienda' => $utente->id_azienda,
                        'id_utente' => $utente->id,
                        'id_tipologia' => 2, // Cliente
                        'immagine' => '/default/assets/images/users/user-dummy-img.jpg'
                    ];

                    // Mappatura colonne - supporta vari formati (Fatture in Cloud, file Italsud, ecc.)
                    if (isset($column_indexes['Denominazione'])) {
                        $cliente_data['ragione_sociale'] = $row[$column_indexes['Denominazione']];
                    }

                    if (isset($column_indexes['Nome'])) {
                        $cliente_data['nome'] = $row[$column_indexes['Nome']];
                    }

                    if (isset($column_indexes['Cognome'])) {
                        $cliente_data['cognome'] = $row[$column_indexes['Cognome']];
                    }

                    if (isset($column_indexes['Indirizzo'])) {
                        $cliente_data['indirizzo'] = $row[$column_indexes['Indirizzo']];
                    }

                    if (isset($column_indexes['CAP'])) {
                        $cliente_data['cap'] = $row[$column_indexes['CAP']];
                    }

                    // Comune / Città
                    if (isset($column_indexes['Comune'])) {
                        $cliente_data['comune'] = $row[$column_indexes['Comune']];
                    }
                    if (isset($column_indexes['Città'])) {
                        $cliente_data['comune'] = $row[$column_indexes['Città']];
                    }

                    // Provincia / Prov.
                    if (isset($column_indexes['Provincia'])) {
                        $cliente_data['provincia'] = $row[$column_indexes['Provincia']];
                    }
                    if (isset($column_indexes['Prov.'])) {
                        $cliente_data['provincia'] = $row[$column_indexes['Prov.']];
                    }

                    if (isset($column_indexes['Nazione'])) {
                        $cliente_data['nazione'] = $row[$column_indexes['Nazione']];
                    } else {
                        $cliente_data['nazione'] = 'IT'; // Default Italia
                    }

                    // Telefono / Tel.
                    if (isset($column_indexes['Telefono'])) {
                        $cliente_data['telefono'] = $row[$column_indexes['Telefono']];
                    }
                    if (isset($column_indexes['Tel.'])) {
                        $cliente_data['telefono'] = $row[$column_indexes['Tel.']];
                    }
                    if (isset($column_indexes['Cellulare'])) {
                        $cliente_data['telefono'] = $row[$column_indexes['Cellulare']];
                    }

                    // Email / e-mail
                    if (isset($column_indexes['Email']) && !empty($row[$column_indexes['Email']])) {
                        $cliente_data['email'] = $row[$column_indexes['Email']];
                    }
                    if (isset($column_indexes['e-mail']) && !empty($row[$column_indexes['e-mail']])) {
                        $cliente_data['email'] = $row[$column_indexes['e-mail']];
                    }
                    if (isset($column_indexes['Indirizzo e-mail']) && !empty($row[$column_indexes['Indirizzo e-mail']])) {
                        $cliente_data['email'] = $row[$column_indexes['Indirizzo e-mail']];
                    }

                    // Partita IVA / Partita Iva / P.IVA/TAX ID
                    if (isset($column_indexes['Partita IVA']) && !empty($row[$column_indexes['Partita IVA']])) {
                        $cliente_data['piva'] = $row[$column_indexes['Partita IVA']];
                    }
                    if (isset($column_indexes['Partita Iva']) && !empty($row[$column_indexes['Partita Iva']])) {
                        $cliente_data['piva'] = $row[$column_indexes['Partita Iva']];
                    }
                    if (isset($column_indexes['P.IVA/TAX ID']) && !empty($row[$column_indexes['P.IVA/TAX ID']])) {
                        $cliente_data['piva'] = $row[$column_indexes['P.IVA/TAX ID']];
                    }

                    if (isset($column_indexes['Codice Fiscale']) && !empty($row[$column_indexes['Codice Fiscale']])) {
                        $cliente_data['cf'] = $row[$column_indexes['Codice Fiscale']];
                    }

                    if (isset($column_indexes['PEC']) && !empty($row[$column_indexes['PEC']])) {
                        $cliente_data['pec'] = $row[$column_indexes['PEC']];
                    }

                    if (isset($column_indexes['Indirizzo PEC']) && !empty($row[$column_indexes['Indirizzo PEC']])) {
                        $cliente_data['pec'] = $row[$column_indexes['Indirizzo PEC']];
                    }

                    // Codice SDI / Codice Destinatario / Cod. destinatario
                    if (isset($column_indexes['Codice SDI']) && !empty($row[$column_indexes['Codice SDI']])) {
                        $cliente_data['sdi'] = $row[$column_indexes['Codice SDI']];
                    }
                    if (isset($column_indexes['Codice Destinatario']) && !empty($row[$column_indexes['Codice Destinatario']])) {
                        $cliente_data['sdi'] = $row[$column_indexes['Codice Destinatario']];
                    }
                    if (isset($column_indexes['Cod. destinatario']) && !empty($row[$column_indexes['Cod. destinatario']])) {
                        $cliente_data['sdi'] = $row[$column_indexes['Cod. destinatario']];
                    }

                    if (isset($column_indexes['Referente']) && !empty($row[$column_indexes['Referente']])) {
                        $cliente_data['referente'] = $row[$column_indexes['Referente']];
                    }

                    if (isset($column_indexes['CCIAA']) && !empty($row[$column_indexes['CCIAA']])) {
                        $cliente_data['cciaa'] = $row[$column_indexes['CCIAA']];
                    }

                    if (isset($column_indexes['REA']) && !empty($row[$column_indexes['REA']])) {
                        $cliente_data['rea'] = $row[$column_indexes['REA']];
                    }

                    // Verifica se abbiamo almeno la P.IVA o la ragione sociale o il nome per identificare il cliente
                    if (empty($cliente_data['piva']) && empty($cliente_data['ragione_sociale']) && empty($cliente_data['nome'])) {
                        $errori++;
                        $log[] = "Riga " . ($i + 1) . " - Cliente senza P.IVA, Denominazione o Nome: dati insufficienti";
                        continue;
                    }

                    // Verifica se il cliente esiste già
                    $cliente_esistente = null;

                    if (!empty($cliente_data['piva'])) {
                        $cliente_esistente = DB::table('clienti')
                            ->where('piva', $cliente_data['piva'])
                            ->where('id_azienda', $utente->id_azienda)
                            ->first();
                    } elseif (!empty($cliente_data['ragione_sociale'])) {
                        $cliente_esistente = DB::table('clienti')
                            ->where('ragione_sociale', $cliente_data['ragione_sociale'])
                            ->where('id_azienda', $utente->id_azienda)
                            ->first();
                    } elseif (!empty($cliente_data['nome']) && !empty($cliente_data['cognome'])) {
                        $cliente_esistente = DB::table('clienti')
                            ->where('nome', $cliente_data['nome'])
                            ->where('cognome', $cliente_data['cognome'])
                            ->where('id_azienda', $utente->id_azienda)
                            ->first();
                    }

                    // Genera token per il cliente
                    $cliente_data['token_utente_per_bando'] = Str::random(20);

                    // Crea codice cliente unico se è un nuovo cliente
                    if (!$cliente_esistente) {
                        // Recupera l'ultimo valore del contatore dal database
                        $lastCounter = DB::table('clienti')
                            ->where('id_azienda', $utente->id_azienda)
                            ->max('cd_cf');

                        // Estrai solo il numero dall'ultimo valore (rimuovendo 'C')
                        if ($lastCounter) {
                            $counterCliente = (int) substr($lastCounter, 1);
                        } else {
                            $counterCliente = 0; // Inizia da zero se non esiste alcun cliente
                        }

                        $counterCliente++;
                        $counterCliente = str_pad($counterCliente, 7, '0', STR_PAD_LEFT);
                        $cliente_data['cd_cf'] = 'C'.$counterCliente;
                    }

                    // Inserisci o aggiorna il cliente
                    if ($cliente_esistente) {
                        $id_cliente = $cliente_esistente->id;
                        DB::table('clienti')
                            ->where('id', $id_cliente)
                            ->update($cliente_data);
                        $aggiornati++;
                        $log[] = "Riga " . ($i + 1) . " - Cliente aggiornato: " .
                            (isset($cliente_data['ragione_sociale']) ? $cliente_data['ragione_sociale'] : $cliente_data['nome'] . ' ' . $cliente_data['cognome']);
                    } else {
                        $cliente_data['timeins'] = now();
                        DB::table('clienti')->insert($cliente_data);
                        $importati++;
                        $log[] = "Riga " . ($i + 1) . " - Nuovo cliente inserito: " .
                            (isset($cliente_data['ragione_sociale']) ? $cliente_data['ragione_sociale'] : $cliente_data['nome'] . ' ' . $cliente_data['cognome']);
                    }
                } catch (\Exception $e) {
                    $errori++;
                    $log[] = "Errore riga " . ($i + 1) . ": " . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            $log[] = "Errore generale: " . $e->getMessage();
            Session::flash('import_log', $log);
            Session::flash('tipo_messaggio', 'error');
            Session::flash('messaggio', 'Errore durante la lettura del file: ' . $e->getMessage());
            return Redirect::back();
        }

        // Salva il log in sessione
        Session::flash('import_log', $log);

        // Risultato dell'importazione
        $messaggio = "Importazione completata. Clienti importati: $importati, aggiornati: $aggiornati, errori: $errori";

        Session::flash('tipo_messaggio', 'success');
        Session::flash('messaggio', $messaggio);
        Session::flash('importati', $importati);
        Session::flash('aggiornati', $aggiornati);
        Session::flash('errori', $errori);
        Session::flash('mostra_risultati_import', true);

        return Redirect::to('utente/clienti');
    }

    public function import_fornitori_excel(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        if (!$request->hasFile('file_excel')) {
            Session::flash('tipo_messaggio', 'error');
            Session::flash('messaggio', 'Nessun file caricato');
            return Redirect::back();
        }

        $file = $request->file('file_excel');
        $extension = $file->getClientOriginalExtension();

        // Verifica che il file sia un Excel
        if (!in_array($extension, ['xlsx', 'xls'])) {
            Session::flash('tipo_messaggio', 'error');
            Session::flash('messaggio', 'Il file deve essere in formato Excel (.xlsx o .xls)');
            return Redirect::back();
        }

        // Carica il file Excel
        $importati = 0;
        $aggiornati = 0;
        $errori = 0;
        $log = [];

        try {
            // Legge il file Excel
            $data = Excel::toArray(new \stdClass(), $file)[0];

            // Debug log
            $log[] = "File letto con successo. Trovate " . count($data) . " righe.";

            // Se il file è vuoto
            if (count($data) <= 1) {
                Session::flash('tipo_messaggio', 'error');
                Session::flash('messaggio', 'Il file Excel non contiene dati');
                return Redirect::back();
            }

            // La prima riga contiene le intestazioni
            $headers = $data[0];
            $log[] = "Intestazioni trovate: " . implode(", ", $headers);

            // Mappa per tenere traccia degli indici delle colonne
            $column_indexes = [];
            foreach ($headers as $index => $header) {
                $column_indexes[trim($header)] = $index;
            }

            $log[] = "Indici colonne: " . json_encode($column_indexes);

            // Processa ogni riga (saltando l'intestazione)
            for ($i = 1; $i < count($data); $i++) {
                $row = $data[$i];

                try {
                    // Prepara i dati del fornitore
                    $fornitore_data = [
                        'id_azienda' => $utente->id_azienda,
                        'id_utente' => $utente->id,
                        'id_tipologia' => 1, // Fornitore
                        'immagine' => '/default/assets/images/users/user-dummy-img.jpg'
                    ];

                    // Mappatura colonne - supporta vari formati (Fatture in Cloud, file Italsud, ecc.)
                    if (isset($column_indexes['Denominazione'])) {
                        $fornitore_data['ragione_sociale'] = $row[$column_indexes['Denominazione']];
                    }

                    if (isset($column_indexes['Nome'])) {
                        $fornitore_data['nome'] = $row[$column_indexes['Nome']];
                    }

                    if (isset($column_indexes['Cognome'])) {
                        $fornitore_data['cognome'] = $row[$column_indexes['Cognome']];
                    }

                    if (isset($column_indexes['Indirizzo'])) {
                        $fornitore_data['indirizzo'] = $row[$column_indexes['Indirizzo']];
                    }

                    if (isset($column_indexes['CAP'])) {
                        $fornitore_data['cap'] = $row[$column_indexes['CAP']];
                    }

                    // Comune / Città
                    if (isset($column_indexes['Comune'])) {
                        $fornitore_data['comune'] = $row[$column_indexes['Comune']];
                    }
                    if (isset($column_indexes['Città'])) {
                        $fornitore_data['comune'] = $row[$column_indexes['Città']];
                    }

                    // Provincia / Prov.
                    if (isset($column_indexes['Provincia'])) {
                        $fornitore_data['provincia'] = $row[$column_indexes['Provincia']];
                    }
                    if (isset($column_indexes['Prov.'])) {
                        $fornitore_data['provincia'] = $row[$column_indexes['Prov.']];
                    }

                    if (isset($column_indexes['Nazione'])) {
                        $fornitore_data['nazione'] = $row[$column_indexes['Nazione']];
                    } else {
                        $fornitore_data['nazione'] = 'IT'; // Default Italia
                    }

                    // Telefono / Tel.
                    if (isset($column_indexes['Telefono'])) {
                        $fornitore_data['telefono'] = $row[$column_indexes['Telefono']];
                    }
                    if (isset($column_indexes['Tel.'])) {
                        $fornitore_data['telefono'] = $row[$column_indexes['Tel.']];
                    }
                    if (isset($column_indexes['Cellulare'])) {
                        $fornitore_data['telefono'] = $row[$column_indexes['Cellulare']];
                    }

                    // Email / e-mail
                    if (isset($column_indexes['Email']) && !empty($row[$column_indexes['Email']])) {
                        $fornitore_data['email'] = $row[$column_indexes['Email']];
                    }
                    if (isset($column_indexes['e-mail']) && !empty($row[$column_indexes['e-mail']])) {
                        $fornitore_data['email'] = $row[$column_indexes['e-mail']];
                    }
                    if (isset($column_indexes['Indirizzo e-mail']) && !empty($row[$column_indexes['Indirizzo e-mail']])) {
                        $fornitore_data['email'] = $row[$column_indexes['Indirizzo e-mail']];
                    }

                    // Partita IVA / Partita Iva / P.IVA/TAX ID
                    if (isset($column_indexes['Partita IVA']) && !empty($row[$column_indexes['Partita IVA']])) {
                        $fornitore_data['piva'] = $row[$column_indexes['Partita IVA']];
                    }
                    if (isset($column_indexes['Partita Iva']) && !empty($row[$column_indexes['Partita Iva']])) {
                        $fornitore_data['piva'] = $row[$column_indexes['Partita Iva']];
                    }
                    if (isset($column_indexes['P.IVA/TAX ID']) && !empty($row[$column_indexes['P.IVA/TAX ID']])) {
                        $fornitore_data['piva'] = $row[$column_indexes['P.IVA/TAX ID']];
                    }

                    if (isset($column_indexes['Codice Fiscale']) && !empty($row[$column_indexes['Codice Fiscale']])) {
                        $fornitore_data['cf'] = $row[$column_indexes['Codice Fiscale']];
                    }

                    if (isset($column_indexes['PEC']) && !empty($row[$column_indexes['PEC']])) {
                        $fornitore_data['pec'] = $row[$column_indexes['PEC']];
                    }

                    if (isset($column_indexes['Indirizzo PEC']) && !empty($row[$column_indexes['Indirizzo PEC']])) {
                        $fornitore_data['pec'] = $row[$column_indexes['Indirizzo PEC']];
                    }

                    // Codice SDI / Codice Destinatario / Cod. destinatario
                    if (isset($column_indexes['Codice SDI']) && !empty($row[$column_indexes['Codice SDI']])) {
                        $fornitore_data['sdi'] = $row[$column_indexes['Codice SDI']];
                    }
                    if (isset($column_indexes['Codice Destinatario']) && !empty($row[$column_indexes['Codice Destinatario']])) {
                        $fornitore_data['sdi'] = $row[$column_indexes['Codice Destinatario']];
                    }
                    if (isset($column_indexes['Cod. destinatario']) && !empty($row[$column_indexes['Cod. destinatario']])) {
                        $fornitore_data['sdi'] = $row[$column_indexes['Cod. destinatario']];
                    }

                    if (isset($column_indexes['Referente']) && !empty($row[$column_indexes['Referente']])) {
                        $fornitore_data['referente'] = $row[$column_indexes['Referente']];
                    }

                    if (isset($column_indexes['CCIAA']) && !empty($row[$column_indexes['CCIAA']])) {
                        $fornitore_data['cciaa'] = $row[$column_indexes['CCIAA']];
                    }

                    if (isset($column_indexes['REA']) && !empty($row[$column_indexes['REA']])) {
                        $fornitore_data['rea'] = $row[$column_indexes['REA']];
                    }

                    // Verifica se abbiamo almeno la P.IVA o la ragione sociale o il nome per identificare il fornitore
                    if (empty($fornitore_data['piva']) && empty($fornitore_data['ragione_sociale']) && empty($fornitore_data['nome'])) {
                        $errori++;
                        $log[] = "Riga " . ($i + 1) . " - Fornitore senza P.IVA, Denominazione o Nome: dati insufficienti";
                        continue;
                    }

                    // Verifica se il fornitore esiste già
                    $fornitore_esistente = null;

                    if (!empty($fornitore_data['piva'])) {
                        $fornitore_esistente = DB::table('fornitori')
                            ->where('piva', $fornitore_data['piva'])
                            ->where('id_azienda', $utente->id_azienda)
                            ->first();
                    } elseif (!empty($fornitore_data['ragione_sociale'])) {
                        $fornitore_esistente = DB::table('fornitori')
                            ->where('ragione_sociale', $fornitore_data['ragione_sociale'])
                            ->where('id_azienda', $utente->id_azienda)
                            ->first();
                    } elseif (!empty($fornitore_data['nome']) && !empty($fornitore_data['cognome'])) {
                        $fornitore_esistente = DB::table('fornitori')
                            ->where('nome', $fornitore_data['nome'])
                            ->where('cognome', $fornitore_data['cognome'])
                            ->where('id_azienda', $utente->id_azienda)
                            ->first();
                    }

                    // Genera token per il fornitore
                    $fornitore_data['token_utente_per_bando'] = Str::random(20);

                    // Crea codice fornitore unico se è un nuovo fornitore
                    if (!$fornitore_esistente) {
                        // Recupera l'ultimo valore del contatore dalla tabella fornitori
                        $lastCounter = DB::table('fornitori')
                            ->where('id_azienda', $utente->id_azienda)
                            ->max('cd_cf');

                        // Estrai solo il numero dall'ultimo valore (rimuovendo 'F')
                        if ($lastCounter && substr($lastCounter, 0, 1) === 'F') {
                            $counterFornitore = (int) substr($lastCounter, 1);
                        } else {
                            $counterFornitore = 0; // Inizia da zero se non esiste alcun fornitore
                        }

                        $counterFornitore++;
                        $counterFornitore = str_pad($counterFornitore, 7, '0', STR_PAD_LEFT);
                        $fornitore_data['cd_cf'] = 'F'.$counterFornitore;
                    }

                    // Inserisci o aggiorna il fornitore
                    if ($fornitore_esistente) {
                        $id_fornitore = $fornitore_esistente->id;
                        DB::table('fornitori')
                            ->where('id', $id_fornitore)
                            ->update($fornitore_data);
                        $aggiornati++;
                        $log[] = "Riga " . ($i + 1) . " - Fornitore aggiornato: " .
                            (isset($fornitore_data['ragione_sociale']) ? $fornitore_data['ragione_sociale'] : $fornitore_data['nome'] . ' ' . $fornitore_data['cognome']);
                    } else {
                        $fornitore_data['timeins'] = now();
                        DB::table('fornitori')->insert($fornitore_data);
                        $importati++;
                        $log[] = "Riga " . ($i + 1) . " - Nuovo fornitore inserito: " .
                            (isset($fornitore_data['ragione_sociale']) ? $fornitore_data['ragione_sociale'] : $fornitore_data['nome'] . ' ' . $fornitore_data['cognome']);
                    }
                } catch (\Exception $e) {
                    $errori++;
                    $log[] = "Errore riga " . ($i + 1) . ": " . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            $log[] = "Errore generale: " . $e->getMessage();
            Session::flash('import_log', $log);
            Session::flash('tipo_messaggio', 'error');
            Session::flash('messaggio', 'Errore durante la lettura del file: ' . $e->getMessage());
            return Redirect::back();
        }

        // Salva il log in sessione
        Session::flash('import_log', $log);

        // Risultato dell'importazione
        $messaggio = "Importazione completata. Fornitori importati: $importati, aggiornati: $aggiornati, errori: $errori";

        Session::flash('tipo_messaggio', 'success');
        Session::flash('messaggio', $messaggio);
        Session::flash('importati', $importati);
        Session::flash('aggiornati', $aggiornati);
        Session::flash('errori', $errori);
        Session::flash('mostra_risultati_import', true);

        return Redirect::to('utente/fornitori');
    }

    public function import_articoli_excel(Request $request)
    {
        $this->is_loggato();
        $utente = session('utente');

        if (!$request->hasFile('file_excel')) {
            Session::flash('tipo_messaggio', 'error');
            Session::flash('messaggio', 'Nessun file caricato');
            return Redirect::back();
        }

        $file = $request->file('file_excel');
        $extension = $file->getClientOriginalExtension();

        if (!in_array($extension, ['xlsx', 'xls'])) {
            Session::flash('tipo_messaggio', 'error');
            Session::flash('messaggio', 'Il file deve essere in formato Excel (.xlsx o .xls)');
            return Redirect::back();
        }

        $importati = 0;
        $aggiornati = 0;
        $errori = 0;
        $log = [];

        try {
            $data = Excel::toArray(new \stdClass(), $file)[0];
            $log[] = "File letto. Trovate " . count($data) . " righe.";

            if (count($data) <= 1) {
                Session::flash('tipo_messaggio', 'error');
                Session::flash('messaggio', 'Il file Excel non contiene dati');
                return Redirect::back();
            }

            $headers = $data[0];
            $column_indexes = [];
            foreach ($headers as $index => $header) {
                $column_indexes[trim((string)$header)] = $index;
            }

            // Pre-carica tutti i clienti dell'azienda per fuzzy match
            $clienti = DB::table('clienti')
                ->where('id_azienda', $utente->id_azienda)
                ->select('id', 'ragione_sociale')
                ->get();

            for ($i = 1; $i < count($data); $i++) {
                $row = $data[$i];

                try {
                    $articolo_data = [
                        'id_azienda' => $utente->id_azienda,
                        'id_utente'  => $utente->id,
                        'tipologia'  => 0, // prodotto finito
                        'immagine'   => '/placehold_immagine.png',
                    ];

                    // Codice articolo
                    if (isset($column_indexes['Cod.']) && !empty($row[$column_indexes['Cod.']])) {
                        $articolo_data['codice_articolo'] = trim($row[$column_indexes['Cod.']]);
                    }
                    if (isset($column_indexes['Codice']) && !empty($row[$column_indexes['Codice']])) {
                        $articolo_data['codice_articolo'] = trim($row[$column_indexes['Codice']]);
                    }

                    // Descrizione
                    if (isset($column_indexes['Descrizione']) && !empty($row[$column_indexes['Descrizione']])) {
                        $articolo_data['titolo'] = trim($row[$column_indexes['Descrizione']]);
                        $articolo_data['descrizione'] = trim($row[$column_indexes['Descrizione']]);
                    }
                    if (isset($column_indexes['Titolo']) && !empty($row[$column_indexes['Titolo']])) {
                        $articolo_data['titolo'] = trim($row[$column_indexes['Titolo']]);
                    }

                    // Prezzo (Listino 1)
                    if (isset($column_indexes['Listino 1']) && !empty($row[$column_indexes['Listino 1']])) {
                        $prezzo = str_replace(['.', '€', ' '], '', $row[$column_indexes['Listino 1']]);
                        $prezzo = str_replace(',', '.', $prezzo);
                        $articolo_data['prezzo'] = (float) $prezzo;
                    }
                    if (isset($column_indexes['Prezzo']) && !empty($row[$column_indexes['Prezzo']])) {
                        $prezzo = str_replace(['.', '€', ' '], '', $row[$column_indexes['Prezzo']]);
                        $prezzo = str_replace(',', '.', $prezzo);
                        $articolo_data['prezzo'] = (float) $prezzo;
                    }

                    // Giacenza
                    if (isset($column_indexes['Q.tà in giacenza']) && $row[$column_indexes['Q.tà in giacenza']] !== null) {
                        $articolo_data['giacenza'] = (float) str_replace(',', '.', $row[$column_indexes['Q.tà in giacenza']]);
                    }
                    if (isset($column_indexes['Giacenza']) && $row[$column_indexes['Giacenza']] !== null) {
                        $articolo_data['giacenza'] = (float) str_replace(',', '.', $row[$column_indexes['Giacenza']]);
                    }

                    // Data primo carico
                    if (isset($column_indexes['Data primo carico']) && !empty($row[$column_indexes['Data primo carico']])) {
                        $data_raw = $row[$column_indexes['Data primo carico']];
                        // Supporta gg/mm/aaaa e aaaa-mm-gg e numerico Excel
                        if (is_numeric($data_raw)) {
                            // Numero seriale Excel
                            $unix = ($data_raw - 25569) * 86400;
                            $articolo_data['data_primo_carico'] = date('Y-m-d', $unix);
                        } elseif (strpos($data_raw, '/') !== false) {
                            $parts = explode('/', $data_raw);
                            if (count($parts) === 3) {
                                $articolo_data['data_primo_carico'] = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
                            }
                        } else {
                            $articolo_data['data_primo_carico'] = $data_raw;
                        }
                    }

                    // Cliente (colonna Categoria) - fuzzy match
                    if (isset($column_indexes['Categoria']) && !empty($row[$column_indexes['Categoria']])) {
                        $nome_cliente = trim($row[$column_indexes['Categoria']]);
                        $id_cliente_trovato = null;

                        // 1. Match esatto
                        foreach ($clienti as $cl) {
                            if (strcasecmp($cl->ragione_sociale, $nome_cliente) === 0) {
                                $id_cliente_trovato = $cl->id;
                                break;
                            }
                        }

                        // 2. Fuzzy: normalizza rimuovendo punteggiatura e spazi
                        if (!$id_cliente_trovato) {
                            $normalizza = function($s) {
                                return strtoupper(preg_replace('/[^A-Z0-9]/i', '', $s));
                            };
                            $nome_norm = $normalizza($nome_cliente);
                            $best_score = 0;
                            foreach ($clienti as $cl) {
                                $cl_norm = $normalizza($cl->ragione_sociale);
                                similar_text($nome_norm, $cl_norm, $pct);
                                if ($pct > $best_score && $pct >= 70) {
                                    $best_score = $pct;
                                    $id_cliente_trovato = $cl->id;
                                }
                            }
                        }

                        if ($id_cliente_trovato) {
                            $articolo_data['id_cliente'] = $id_cliente_trovato;
                            $log[] = "Riga " . ($i + 1) . " - Cliente '$nome_cliente' → ID $id_cliente_trovato";
                        } else {
                            $log[] = "Riga " . ($i + 1) . " - Cliente '$nome_cliente' non trovato";
                        }
                    }

                    // Verifica dati minimi
                    if (empty($articolo_data['codice_articolo']) && empty($articolo_data['titolo'])) {
                        $errori++;
                        $log[] = "Riga " . ($i + 1) . " - Saltata: nessun codice né descrizione";
                        continue;
                    }

                    // Cerca articolo esistente per codice
                    $articolo_esistente = null;
                    if (!empty($articolo_data['codice_articolo'])) {
                        $articolo_esistente = DB::table('articoli')
                            ->where('codice_articolo', $articolo_data['codice_articolo'])
                            ->where('id_azienda', $utente->id_azienda)
                            ->first();
                    }

                    if ($articolo_esistente) {
                        DB::table('articoli')->where('id', $articolo_esistente->id)->update($articolo_data);
                        $aggiornati++;
                        $log[] = "Riga " . ($i + 1) . " - Aggiornato: " . ($articolo_data['codice_articolo'] ?? $articolo_data['titolo']);
                    } else {
                        $articolo_data['data_creazione'] = now()->toDateString();
                        DB::table('articoli')->insert($articolo_data);
                        $importati++;
                        $log[] = "Riga " . ($i + 1) . " - Inserito: " . ($articolo_data['codice_articolo'] ?? $articolo_data['titolo']);
                    }

                } catch (\Exception $e) {
                    $errori++;
                    $log[] = "Errore riga " . ($i + 1) . ": " . $e->getMessage();
                }
            }
        } catch (\Exception $e) {
            $log[] = "Errore generale: " . $e->getMessage();
            Session::flash('import_log', $log);
            Session::flash('tipo_messaggio', 'error');
            Session::flash('messaggio', 'Errore durante la lettura del file: ' . $e->getMessage());
            return Redirect::back();
        }

        Session::flash('import_log', $log);
        Session::flash('tipo_messaggio', 'success');
        Session::flash('messaggio', "Importazione completata. Prodotti importati: $importati, aggiornati: $aggiornati, errori: $errori");
        Session::flash('importati', $importati);
        Session::flash('aggiornati', $aggiornati);
        Session::flash('errori', $errori);
        Session::flash('mostra_risultati_import', true);

        return Redirect::to('utente/articoli?tipo=prodotto_finito');
    }

    /**
     * Verifica che l'utente sia loggato
     */
    public function is_loggato()
    {
        if (!session()->has('utente')) return Redirect::to('admin/login')->send();
    }
}
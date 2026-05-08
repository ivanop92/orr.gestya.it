<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use SimpleXMLElement;
use PHPMailer\PHPMailer\PHPMailer;


class UtilsController extends Controller
{
    /**
     * Crea documento in dotes e dorig da XML
     * @param SimpleXMLElement $xml XML della fattura
     * @param int $id_azienda ID dell'azienda
     * @param string $tipo_doc Tipo documento (FAP per passivo, FAT per attivo)
     * @return array Status dell'importazione e ID del documento creato
     */
    public function crea_documento_da_xml($xml_string, $id_azienda, $tipo_doc = 'FTI')
    {

            $xml_string = $this->pulisci_xml($xml_string);

            // Converte la stringa in oggetto SimpleXMLElement
            $xml = new SimpleXMLElement($xml_string);

            DB::beginTransaction();

            $dati_generali = $xml->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento;


            // Gestisce diversamente cedente/prestatore in base al tipo documento
            if ($tipo_doc == 'FTI') {
                $controparte = $xml->FatturaElettronicaHeader->CedentePrestatore;
                $tipo_controparte = 'fornitore';
            } else {
                $controparte = $xml->FatturaElettronicaHeader->CessionarioCommittente;
                $tipo_controparte = 'cliente';
            }

            // Cerca o crea l'anagrafica
            $id_cliente = $this->trova_o_crea_anagrafica($controparte, $id_azienda, $tipo_controparte);

            // Inserisce testata in dotes
            $id_dotes = DB::table('dotes')->insertGetId([
                'id_azienda' => $id_azienda,
                'cd_do' => $tipo_doc,
                'tipo_documento' => 'fattura',
                'tipologia_documento' => (string)$dati_generali->TipoDocumento,
                'id_cliente' => $id_cliente,
                'numero_doc' => (string)$dati_generali->Numero,
                'data_doc' => (string)$dati_generali->Data,
                'data' => (string)$dati_generali->Data,
                'anno' => date('Y', strtotime((string)$dati_generali->Data)),
                'ragione_sociale' => (string)$controparte->DatiAnagrafici->Anagrafica->Denominazione,
                'ragione_sociale_fatturazione' => (string)$controparte->DatiAnagrafici->Anagrafica->Denominazione,
                'partita_iva' => (string)$controparte->DatiAnagrafici->IdFiscaleIVA->IdCodice,
                'partita_iva_fatturazione' => (string)$controparte->DatiAnagrafici->IdFiscaleIVA->IdCodice,
                'indirizzo' => $this->concatena_indirizzo($controparte->Sede),
                'indirizzo_fatturazione' => $this->concatena_indirizzo($controparte->Sede),
                'cap' => (string)$controparte->Sede->CAP,
                'citta' => (string)$controparte->Sede->Comune,
                'provincia' => (string)$controparte->Sede->Provincia,
                'nazione' => (string)$controparte->Sede->Nazione,
                'imposta' => $this->calcola_imposta_totale($xml),
                'totale' => (float)$dati_generali->ImportoTotaleDocumento,
                'imponibile' => (float)$dati_generali->ImportoTotaleDocumento - $this->calcola_imposta_totale($xml),
                'fattura_in_ingresso' => ($tipo_doc == 'FTI' ? 1 : 0),
                'da_registrare' => 1,
                'stato' => 0
            ]);

            if(isset($xml->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento->DatiRitenuta)) {
                $dati_ritenuta = $xml->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento->DatiRitenuta;

                $update_ritenuta['tipo_ritenuta'] = (string)$dati_ritenuta->TipoRitenuta;
                $update_ritenuta['importo_ritenuta'] = (float)$dati_ritenuta->ImportoRitenuta;
                $update_ritenuta['aliquota_ritenuta'] = (float)$dati_ritenuta->AliquotaRitenuta;
                $update_ritenuta['causale_pagamento'] = (string)$dati_ritenuta->CausalePagamento;

                DB::table('dotes')->where('id',$id_dotes)->where('id_azienda',$id_azienda)->update($update_ritenuta);

                $data_fattura = new \DateTime($dati_generali->Data);
                $data_scadenza_ritenuta = new \DateTime($dati_generali->Data);
                $data_scadenza_ritenuta->modify('first day of next month');
                $data_scadenza_ritenuta->modify('+15 days');

                $fattura = DB::select("SELECT totale, iban, modalita_pagamento FROM dotes WHERE id = ? LIMIT 1", [$id_dotes])[0];

                $insert['id_dotes'] = $id_dotes;
                $insert['id_azienda'] = $id_azienda;
                $insert['id_cliente'] = $id_cliente;
                $insert['modalita_pagamento'] = $fattura->modalita_pagamento;
                $insert['tipo_movimento'] = 'uscita';
                $insert['data_scadenza']  = $data_scadenza_ritenuta->format('Y-m-d');
                $insert['importo'] = $update_ritenuta['importo_ritenuta'];
                $insert['note'] = 'Versamento ritenuta d\'acconto - ' . $update_ritenuta['tipo_ritenuta'].' - '. $update_ritenuta['causale_pagamento'];
                DB::table('scadenziario')->insert($insert);

            }

            if(isset($xml->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento->DatiCassaPrevidenziale)) {
                $dati_cassa = $xml->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento->DatiCassaPrevidenziale;

                $update_cassa['tipo_cassa'] = (string)$dati_cassa->TipoCassa;
                $update_cassa['aliquota_cassa'] = (float)$dati_cassa->AlCassa;
                $update_cassa['importo_cassa'] = (float)$dati_cassa->ImportoContributoCassa;
                $update_cassa['imponibile_cassa'] = (float)$dati_cassa->ImponibileCassa;
                $update_cassa['aliquota_iva_cassa'] = (float)$dati_cassa->AliquotaIV;

                DB::table('dotes')->where('id',$id_dotes)->where('id_azienda',$id_azienda)->update($update_cassa);
            }

            // Inserisce righe in dorig
            $this->inserisci_righe_documento($xml, $id_azienda, $id_dotes, $tipo_doc);

            // Gestione pagamenti se presenti
            if (isset($xml->FatturaElettronicaBody->DatiPagamento)) {
                $this->gestisci_pagamenti($xml->FatturaElettronicaBody->DatiPagamento,$id_cliente,$id_dotes,$id_azienda,$dati_generali->Data);
            } else {

                $fattura = DB::select("SELECT id_cliente,id_azienda,totale, iban, modalita_pagamento FROM dotes WHERE id = ? LIMIT 1", [$id_dotes])[0];
                $insert['id_dotes'] = $id_dotes;
                $insert['id_azienda'] = $fattura->id_azienda;
                $insert['id_cliente'] = $id_cliente;
                $insert['modalita_pagamento'] = $fattura->modalita_pagamento;
                $insert['data_scadenza']  = (string)$dati_generali->Data;
                $insert['importo'] = $fattura->totale;
                $insert['iban'] = $fattura->iban;
                $insert['tipo_movimento'] = 'uscita';
                DB::table('scadenziario')->insert($insert);

            }

            DB::commit();
            return ['success' => true, 'id_dotes' => $id_dotes];

    }

    private function pulisci_xml($xml_string)
    {
        // Rimuove BOM se presente
        $bom = pack('H*','EFBBBF');
        $xml_string = preg_replace("/^$bom/", '', $xml_string);

        // Rimuove caratteri non validi per XML
        $xml_string = preg_replace('/[^\x{0009}\x{000a}\x{000d}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $xml_string);

        // Corregge namespace se necessario
        if (!strpos($xml_string, 'xmlns')) {
            $xml_string = str_replace('<FatturaElettronica>', '<FatturaElettronica xmlns="http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fatture/v1.2">', $xml_string);
        }

        return $xml_string;
    }

    /**
     * Cerca o crea l'anagrafica cliente/fornitore
     */
    private function trova_o_crea_anagrafica($controparte, $id_azienda, $tipo)
    {
        $id = DB::table('clienti')
            ->where('id_azienda', $id_azienda)
            ->where('piva', (string)$controparte->DatiAnagrafici->IdFiscaleIVA->IdCodice)
            ->value('id');

        if (!$id) {
            $prefisso = ($tipo == 'fornitore') ? 'F' : 'C';
            $id_tipologia = ($tipo == 'fornitore') ? 1 : 2;
            $cd_cf = $prefisso . str_pad(DB::table('clienti')->where('id_azienda',$id_azienda)->max('id') + 1, 7, '0', STR_PAD_LEFT);

            $id  = DB::table('clienti')->insertGetId([
                'id_azienda' => $id_azienda,
                'cd_cf' => $cd_cf,
                'id_tipologia' => $id_tipologia,
                'ragione_sociale' => (string)$controparte->DatiAnagrafici->Anagrafica->Denominazione,
                'piva' => (string)$controparte->DatiAnagrafici->IdFiscaleIVA->IdCodice,
                'indirizzo' => $this->concatena_indirizzo($controparte->Sede),
                'cap' => (string)$controparte->Sede->CAP,
                'comune' => (string)$controparte->Sede->Comune,
                'provincia' => (string)$controparte->Sede->Provincia,
                'nazione' => (string)$controparte->Sede->Nazione
            ]);
        }

        return $id;
    }

    /**
     * Inserisce le righe del documento
     */
    private function inserisci_righe_documento($xml, $id_azienda, $id_dotes, $tipo_doc)
    {
        $numero_riga = 1;
        foreach ($xml->FatturaElettronicaBody->DatiBeniServizi->DettaglioLinee as $linea) {
            DB::table('dorig')->insert([
                'id_azienda' => $id_azienda,
                'id_dotes' => $id_dotes,
                'n_riga' => $numero_riga++,
                'nome_prodotto' => (string)$linea->Descrizione,
                'descrizione' => (string)$linea->Descrizione,
                'qta' => (float)$linea->Quantita ?: 1,
                'um' => (string)$linea->UnitaMisura ?: 'NR',
                'prezzo_unitario' => (float)$linea->PrezzoUnitario,
                'prezzo_totale' => (float)$linea->PrezzoTotale,
                'iva' => (int)$linea->AliquotaIVA,
                'codice_iva' => $this->determina_codice_iva($linea),
                'imposta' => $this->calcola_imposta_riga($linea),
                'imponibile' => (float)$linea->PrezzoTotale,
                'fattura_in_ingresso' => ($tipo_doc == 'FAP' ? 1 : 0)
            ]);
        }
    }

    /**
     * Gestisce i pagamenti del documento
     */
    private function gestisci_pagamenti($dati_pagamento,$id_cliente,$id_dotes,$id_azienda,$data_documento)
    {


        $insert['id_dotes'] = $id_dotes;
        $insert['id_azienda'] = $id_azienda;
        $insert['id_cliente'] = $id_cliente;

        if(isset($dati_pagamento->DettaglioPagamento) && sizeof($dati_pagamento->DettaglioPagamento) > 0){

            foreach ($dati_pagamento->DettaglioPagamento as $dettaglio) {
                $insert['modalita_pagamento'] = (string)$dettaglio->ModalitaPagamento;
                $insert['data_scadenza'] = isset($dettaglio->DataScadenzaPagamento)?(string)$dettaglio->DataScadenzaPagamento:(string)$data_documento;
                $insert['importo'] = (float)$dettaglio->ImportoPagamento;
                $insert['iban'] = (string)$dettaglio->IBAN;
                $insert['tipo_movimento'] = 'uscita';
                DB::table('scadenziario')->insert($insert);
            }

        } else {

            $fattura = DB::select("SELECT totale, iban, modalita_pagamento FROM dotes WHERE id = ? LIMIT 1", [$id_dotes])[0];
            $insert['modalita_pagamento'] = $fattura->modalita_pagamento;
            $insert['data_scadenza']  = date('Y-m-d', strtotime('+30 days'));
            $insert['importo'] = $fattura->totale;
            $insert['iban'] = $fattura->iban;
            $insert['tipo_movimento'] = 'uscita';
            DB::table('scadenziario')->insert($insert);


        }

    }

    /**
     * Utility per concatenare l'indirizzo
     */
    private function concatena_indirizzo($sede)
    {
        $indirizzo = (string)$sede->Indirizzo;
        if (!empty($sede->NumeroCivico)) {
            $indirizzo .= ', ' . (string)$sede->NumeroCivico;
        }
        return $indirizzo;
    }

    /**
     * Calcola l'imposta totale dal riepilogo IVA
     */
    private function calcola_imposta_totale($xml)
    {
        $imposta = 0;
        foreach ($xml->FatturaElettronicaBody->DatiBeniServizi->DatiRiepilogo as $riepilogo) {
            $imposta += (float)$riepilogo->Imposta;
        }
        return $imposta;
    }
    /**
     * Calcola l'imposta totale dal riepilogo IVA
     */
    private function calcola_imponibile_totale($xml)
    {
        $imponibile = 0;
        foreach ($xml->FatturaElettronicaBody->DatiBeniServizi->DatiRiepilogo as $riepilogo) {
            $imponibile += (float)$riepilogo->PrezzoTotale;
        }
        return $imponibile;
    }

    /**
     * Calcola l'imposta per singola riga
     */
    private function calcola_imposta_riga($linea)
    {
        $imponibile = (float)$linea->PrezzoTotale;
        $aliquota = (float)$linea->AliquotaIVA;
        return round($imponibile * ($aliquota / 100), 2);
    }

    /**
     * Determina il codice IVA
     */
    private function determina_codice_iva($linea)
    {
        if (isset($linea->Natura)) {
            switch ((string)$linea->Natura) {
                case 'N1': return 'N1';
                case 'N2': return 'N2';
                case 'N3': return 'N3';
                case 'N4': return 'N4';
                case 'N5': return 'N5';
                case 'N6': return 'N6';
                default: return '22';
            }
        }
        return '22';
    }



    public static function invia_notifica_fattura($azienda, $testata, $xml_content) {
        try {
            $xml = new \SimpleXMLElement($xml_content);

            $fornitore = $xml->FatturaElettronicaHeader->CedentePrestatore->DatiAnagrafici->Anagrafica->Denominazione;
            $numero_fattura = $xml->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento->Numero;
            $data_fattura = date('d/m/Y', strtotime($xml->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento->Data));
            $importo = $xml->FatturaElettronicaBody->DatiGenerali->DatiGeneraliDocumento->ImportoTotaleDocumento;

            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtps.aruba.it';
            $mail->SMTPAuth = true;
            $mail->Username = 'noreply@gestya.it';
            $mail->Password = 'jwZFTChzg8gp41?c';
            $mail->SMTPSecure = 'ssl';
            $mail->CharSet = 'utf-8';
            $mail->Port = 465;
            $mail->setFrom('noreply@gestya.it', 'Gestya.it - Fatturazione Elettronica');
            $mail->addAddress($azienda->email_ricezione_fatture);

            $mail->isHTML(true);
            $mail->Subject = "Nuova Fattura Ricevuta da " . $fornitore;

            // Corpo HTML della mail con stile moderno
            $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;'>
            <div style='text-align: center; margin-bottom: 30px;'>
                <img src='https://gestya.it//logo_gestya.jpg' alt='Gestya Logo' style='max-width: 200px;'>
            </div>
            
            <div style='background-color: #f8f9fa; border-radius: 10px; padding: 20px; margin-bottom: 20px;'>
                <h2 style='color: #2b3481; margin-bottom: 20px;'>Nuova Fattura Elettronica Ricevuta</h2>
                
                <p style='color: #666; line-height: 1.6;'>
                    Gentile {$azienda->ragione_sociale},<br>
                    è stata ricevuta una nuova fattura elettronica con i seguenti dettagli:
                </p>
            </div>

            <div style='background-color: #ffffff; border: 1px solid #e9ecef; border-radius: 10px; padding: 20px; margin-bottom: 20px;'>
                <table style='width: 100%; border-collapse: collapse;'>
                    <tr>
                        <td style='padding: 10px; color: #666;'>Fornitore:</td>
                        <td style='padding: 10px; color: #2b3481; font-weight: bold;'>{$fornitore}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; color: #666;'>Numero Fattura:</td>
                        <td style='padding: 10px; color: #2b3481; font-weight: bold;'>{$numero_fattura}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; color: #666;'>Data:</td>
                        <td style='padding: 10px; color: #2b3481; font-weight: bold;'>{$data_fattura}</td>
                    </tr>
                    <tr>
                        <td style='padding: 10px; color: #666;'>Importo Totale:</td>
                        <td style='padding: 10px; color: #2b3481; font-weight: bold;'>€ {$importo}</td>
                    </tr>
                </table>
            </div>

            <div style='text-align: center;'>
                <a href='https://gestya.it' style='display: inline-block; background-color: #2b3481; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; margin-top: 20px;'>
                    Accedi a Gestya
                </a>
            </div>

            <div style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #e9ecef; color: #666; font-size: 12px; text-align: center;'>
                <p>
                    Questa è una notifica automatica, si prega di non rispondere a questa email.<br>
                    Per assistenza, contattare il supporto Gestya.
                </p>
            </div>
        </div>";

            // Versione testo plain per client email che non supportano HTML
            $mail->AltBody = "
        Nuova Fattura Elettronica Ricevuta
        
        Gentile {$azienda->ragione_sociale},
        è stata ricevuta una nuova fattura elettronica con i seguenti dettagli:
        
        Fornitore: {$fornitore}
        Numero Fattura: {$numero_fattura}
        Data: {$data_fattura}
        Importo Totale: € {$importo}
        
        Accedi a Gestya.it per visualizzare i dettagli completi della fattura.";

            $mail->send();
            return true;

        } catch (\Exception $e) {
            \Log::error('Errore nell\'invio della notifica email: ' . $e->getMessage());
            return false;
        }
    }

}
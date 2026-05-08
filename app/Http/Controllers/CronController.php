<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use PHPMailer\PHPMailer\PHPMailer;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TariffeImport;
use phpseclib3\Net\SFTP;
use phpseclib3\Crypt\PublicKeyLoader;


class CronController extends Controller {

    public function invia_lista_attrezzature($token){

        if($token = 'skjhdjkasldajdlajk') {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = 'smtps.aruba.it;smtps.aruba.it';
            $mail->SMTPAuth = true;
            $mail->Username = 'gestionale@officinariparazionirotabili.com';
            $mail->Password = 'Aliperti2022?';
            $mail->SMTPSecure = 'ssl';
            $mail->CharSet = 'utf-8';
            $mail->Port = 465;
            $mail->setFrom('gestionale@officinariparazionirotabili.com');
            $mail->addAddress('info@officinariparazionirotabili.com');
            $mail->addBCC('giovinefabio@gmail.com');
            $mail->isHTML(true);
            $mail->Subject = 'Report Situazioni Attrezzature ' . date('d/m/Y');

            $attrezzature = DB::select('SELECT *,TIMESTAMPDIFF(MONTH,ultima_revisione,NOW()) AS scadenza_revisione,TIMESTAMPDIFF(MONTH,data,NOW()) AS scadenza_mese from attrezzature order by ultima_revisione asc,data asc');

            $html = View::make('stampa.attrezzature', compact('attrezzature'));
            $mpdf = new \Mpdf\Mpdf(['format' => 'A4-L', 'mode' => 'utf-8', 'margin_left' => 5, 'margin_right' => 5, 'margin_top' => 0, 'margin_bottom' => 10, 'margin_header' => 0, 'margin_footer' => 0]);
            $mpdf->WriteHTML($html);
            $mpdf->Output('attrezzature_' . date('Y_m_d') . '.pdf', 'F');
            $mail->AddAttachment('attrezzature_' . date('Y_m_d') . '.pdf', 'Lista Attrezzature ' . (date('d/m/Y') . '.pdf'));
            $mail->Body = 'Lista Attrezzature In Scadenza al ' . date('d/m/Y');
            $mail->send();
        }
    }

    public function scarica_fatture($ore){

        SdiController::scarica_esiti($ore);
    }


    public function get_risposte_sogei(){
        // Configurazione SFTP
        $sftpHost = '135.125.180.188';
        $sftpPort = 22; // Porta SFTP standard
        $sftpUser = 'sftp_user';
        $ppk_path = 'CERTS/ingenia_private.ppk';
        $remotePath = 'DatiDaSdITest/';

        // Carica la chiave privata
        $key = PublicKeyLoader::load(file_get_contents($ppk_path));

        // Crea connessione SFTP
        $sftp = new SFTP($sftpHost);

        // Login con la chiave
        if (!$sftp->login($sftpUser, $key)) {
            throw new Exception('Login fallito');
        }


        // Recupera la lista dei file XML nella directory
        $files = $sftp->nlist($remotePath);
        if (!$files) {
            throw new \Exception('Nessun file trovato nella directory remota');
        }

        // Cerca il primo file XML disponibile
        $xmlFile = null;
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'enc') {
                $xmlFile = $file;

                $tempFile = 'fatture_ricevute/'.$xmlFile;
                if (!$sftp->get($remotePath . $xmlFile, $tempFile)) {
                    throw new Exception("Errore durante il download del file XML");
                }


                $inputFile = $tempFile;    // File cifrato
                $outputFile = $inputFile;

                // File decifrato
                $certFile = 'CERTS/CIFRA.PEM';  // Certificato
                $password = '123456';          // Password del certificato


                $command = sprintf(
                    'openssl smime -decrypt -in %s -inform der -binary -out %s -recip %s -passin pass:%s',
                    escapeshellarg($inputFile),
                    escapeshellarg(str_replace('.enc','',$outputFile)),
                    escapeshellarg($certFile),
                    escapeshellarg($password)
                );

                // Esegui il comando e cattura output ed errori
                $output = shell_exec($command . ' 2>&1');

                echo $output;


                $caFile = 'CERTS/CAEntrate.pem';

                $inputFile = str_replace('.enc','',$outputFile);
                $outputFile = $inputFile;

                $command = sprintf(
                    'openssl smime -verify -in %s -inform der -binary -out %s -CAfile %s 2>&1',
                    escapeshellarg($inputFile),
                    escapeshellarg(str_replace('.p7m','',$outputFile)),
                    escapeshellarg($caFile)
                );

                $zipFile = str_replace('.p7m','',$outputFile);

                // Esegui il comando e cattura l'output
                $output = shell_exec($command);



                $zip = new \ZipArchive;
                $zipFile = $zipFile;
                $extractPath = "fatture_ricevute/estrazione";

                if ($zip->open($zipFile) === TRUE) {
                    $zip->extractTo($extractPath);
                    $zip->close();
                    echo "File extracted successfully";

                    $files = scandir($extractPath);

                    foreach($files as $file) {
                        // Ignora . e ..
                        if($file != "." && $file != "..") {
                            // Controlla se il nome del file contiene "RC" (case sensitive)
                            if(strpos($file, "RC") !== false) {
                                echo "File trovato: " . $file . "\n";


                                $xml = new \SimpleXMLElement(file_get_contents($extractPath .'/'. $file));

                                $xml->registerXPathNamespace('ns3', 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fattura/messaggi/v1.0');

                                $nomeFile = $xml->NomeFile;
                                $dataRicezione = $xml->DataOraRicezione;
                                $dataConsegna = $xml->DataOraConsegna;

                                $esiste = DB::select('SELECT * from dotes where nome_file_fattura = "'.$nomeFile.'"');

                                if(sizeof($esiste) > 0){

                                    $update['rc_ricezione'] = $dataRicezione;
                                    $update['rc_consegna'] = $dataConsegna;
                                    $update['stato'] = 3;

                                    DB::table('dotes')->where('id',$esiste[0]->id)->update($update);
                                }
                            }    // Controlla se il nome del file contiene "RC" (case sensitive)

                            if(strpos($file, "NS") !== false) {
                                echo "File trovato: " . $file . "\n";


                                $xml = new \SimpleXMLElement(file_get_contents($extractPath .'/'. $file));

                                $xml->registerXPathNamespace('ns3', 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fattura/messaggi/v1.0');

                                $errore = $xml->ListaErrori->Errore;

                                $esiste = DB::select('SELECT * from dotes where nome_file_fattura = "'.$nomeFile.'"');

                                if(sizeof($esiste) > 0){

                                    $update['ns_codice'] = (string)$errore->Codice;
                                    $update['ns_descrizione'] = (string)$errore->Descrizione;
                                    $update['ns_suggerimento'] = (string)$errore->Suggerimento;
                                    $update['stato'] = 2;

                                    DB::table('dotes')->where('id',$esiste[0]->id)->update($update);
                                }
                            }
                            unlink($extractPath .'/'. $file);
                        }


                    }

                } else {
                    echo "Failed to extract file";
                }

                // crea_documento

                unlink($zipFile);
                unlink($zipFile.'.p7m');
                rename($zipFile.'.p7m.enc','fatture_ricevute/backup/'.$file.'.p7m.enc');

                if ($sftp->delete($remotePath . $xmlFile)) {
                    echo "File deleted successfully";
                } else {
                    echo "Failed to delete file";
                }

            }
        }

    }

    public function importa_fatture_xml(){
        // Configurazione SFTP
        $sftpHost = '135.125.180.188';
        $sftpPort = 22; // Porta SFTP standard
        $sftpUser = 'sftp_user';
        $ppk_path = 'CERTS/ingenia_private.ppk';
        $remotePath = 'DatiDaSdI/';
        $backupPath = 'DatiDaSdIBackup/';



        // Carica la chiave privata
        $key = PublicKeyLoader::load(file_get_contents($ppk_path));

        // Crea connessione SFTP
        $sftp = new SFTP($sftpHost);

        // Login con la chiave
        if (!$sftp->login($sftpUser, $key)) {
            throw new Exception('Login fallito');
        }


        // Recupera la lista dei file XML nella directory
        $files = $sftp->nlist($remotePath);
        if (!$files) {
            throw new Exception('Nessun file trovato nella directory remota');
        }

        // Cerca il primo file XML disponibile
        $fo_file = null;
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'enc') {
                $fo_file = $file;

                $tempFile = 'fatture_ricevute/'.$fo_file;
                if (!$sftp->get($remotePath . $fo_file, $tempFile)) {
                    throw new \Exception("Errore durante il download del file XML");
                }


                $inputFile = $tempFile;    // File cifrato

                $outputFile = str_replace('.enc','',$inputFile);

                // File decifrato
                $certFile = 'CERTS/CIFRA.PEM';  // Certificato
                $password = '123456';          // Password del certificato


                $command = sprintf(
                    'openssl smime -decrypt -in %s -inform der -binary -out %s -recip %s -passin pass:%s',
                    escapeshellarg($inputFile),
                    escapeshellarg($outputFile),
                    escapeshellarg($certFile),
                    escapeshellarg($password)
                );

                // Esegui il comando e cattura output ed errori
                $output = shell_exec($command . ' 2>&1');

                echo $output;


                $caFile = 'CERTS/CAEntrate.pem';

                $inputFile = $outputFile;
                $outputFile = str_replace('.p7m','',$outputFile);

                $command = sprintf(
                    'openssl smime -verify -in %s -inform der -binary -out %s -CAfile %s 2>&1',
                    escapeshellarg($inputFile),
                    escapeshellarg($outputFile),
                    escapeshellarg($caFile)
                );

                // Esegui il comando e cattura l'output
                $output = shell_exec($command);

                // Verifica se il file di output esiste
                if (!file_exists($outputFile)) {
                    echo "Errore: File di output non creato\n";
                    echo "Output comando: " . $output;
                    return false;
                }


                CronController::importa_fatture_ricevute($outputFile);

                unlink($outputFile);
                unlink($outputFile.'.p7m');
                unlink($outputFile.'.p7m.enc');

                if (!$sftp->rename($remotePath . $fo_file, $backupPath . $fo_file)) {
                    throw new \Exception("Impossibile spostare il file nel backup");
                }

            }
        }

        if (!$fo_file) {
            throw new \Exception('Nessun file XML trovato');
        }


    }

    public static function importa_fatture_ricevute($file_zip) {

            $zip_name = basename($file_zip);
            $parts = explode('.', $zip_name);
            if (count($parts) < 2) {
                throw new \Exception('Formato nome file non valido');
            }
            $partita_iva = $parts[1];

            $azienda = DB::select("SELECT * FROM aziende WHERE partita_iva = ? LIMIT 1", [$partita_iva]);

            if (empty($azienda)) {
                throw new \Exception('Azienda non trovata per la partita IVA: ' . $partita_iva);
            } else {
                $azienda = $azienda[0];
            }

            $id_azienda = $azienda->id;

            $documento = DB::select("SELECT cd_do FROM do WHERE id_azienda = ? AND fatturazione_ingresso = 1 LIMIT 1", [$id_azienda]);

            if (empty($documento)) {
                throw new \Exception('Nessun documento configurato per fatture in ingresso per l\'azienda ID: ' . $id_azienda);
            }

            $cd_do = $documento[0]->cd_do;

            // Percorso dove estrarre i file
            $extraction_path = public_path('fatture_ricevute/'.$partita_iva);

            // Crea la directory se non esiste
            if (!file_exists($extraction_path)) {
                mkdir($extraction_path, 0777, true);
            }

            // Estrai il file ZIP

            $zip = new \ZipArchive;
            if ($zip->open($file_zip) === TRUE) {
                $zip->extractTo($extraction_path);
                $zip->close();

                // Scansiona la directory per i file XML
                $files = scandir($extraction_path);

                foreach ($files as $file) {

                    echo $file.'<br>';


                    if(strpos($file, "_RC_") !== false) {


                            echo "File trovato: " . $file . "\n";


                            $xml = new \SimpleXMLElement(file_get_contents('fatture_ricevute/' . $partita_iva . '/' . $file));

                            $xml->registerXPathNamespace('ns3', 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fattura/messaggi/v1.0');

                            $nomeFile = $xml->NomeFile;
                            $dataRicezione = $xml->DataOraRicezione;
                            $dataConsegna = $xml->DataOraConsegna;

                            $esiste = DB::select('SELECT * from dotes where nome_file_fattura = "' . $nomeFile . '"');

                            if (sizeof($esiste) > 0) {

                                $update['rc_ricezione'] = $dataRicezione;
                                $update['rc_consegna'] = $dataConsegna;
                                $update['stato'] = 1;

                                DB::table('dotes')->where('id', $esiste[0]->id)->update($update);
                            }

                        rename('fatture_ricevute/'.$partita_iva.'/'.$file,'fatture_ricevute/'.$partita_iva.'/backup/ricevute/'.$file);

                    } else if(strpos($file, "_MT_") !== false) {

                        echo "File trovato: " . $file . "\n";

                        rename('fatture_ricevute/'.$partita_iva.'/'.$file,'fatture_ricevute/'.$partita_iva.'/backup/MT/'.$file);

                    } else if(strpos($file, "FO.") !== false) {

                        echo "File trovato: " . $file . "\n";

                        rename('fatture_ricevute/'.$partita_iva.'/'.$file,'fatture_ricevute/'.$partita_iva.'/backup/FO/'.$file);

                    } else if(strpos($file, "_NS_") !== false) {

                        echo "File trovato: " . $file . "\n";


                        $xml = new \SimpleXMLElement(file_get_contents('fatture_ricevute/'.$partita_iva.'/'.$file));

                        $xml->registerXPathNamespace('ns3', 'http://ivaservizi.agenziaentrate.gov.it/docs/xsd/fattura/messaggi/v1.0');

                        $nomeFile = $xml->NomeFile;
                        $errore = $xml->ListaErrori->Errore;

                        $esiste = DB::select('SELECT * from dotes where nome_file_fattura = "'.$nomeFile.'"');

                        if(sizeof($esiste) > 0){

                            $update['ns_codice'] = (string)$errore->Codice;
                            $update['ns_descrizione'] = (string)$errore->Descrizione;
                            $update['ns_suggerimento'] = (string)$errore->Suggerimento;
                            $update['stato'] = 2;

                            DB::table('dotes')->where('id',$esiste[0]->id)->update($update);
                        }

                        rename('fatture_ricevute/'.$partita_iva.'/'.$file,'fatture_ricevute/'.$partita_iva.'/backup/scarti/'.$file);

                    }  else if ((preg_match('/^IT|^NL/i', $file)) && strpos($file, '_MT_') === false) {

                        $outputFile = 'fatture_ricevute/'.$partita_iva.'/'.$file;
                        $xml_path = $outputFile;

                        if(strtolower(pathinfo($file, PATHINFO_EXTENSION)) == 'p7m') {
                            $inputFile = $outputFile;

                            $outputFile = str_replace('.p7m', '', $outputFile);
                            $outputFile = str_replace('.XML', '.xml', $outputFile);

                            $file = str_replace('.p7m', '', $file);
                            $file = str_replace('.XML', '.xml', $file);

                            $command = sprintf('openssl smime -verify -inform DER -in ' . $inputFile . ' -noverify -out ' . $outputFile);
                            $output = shell_exec($command . ' 2>&1');

                            if(strpos($output, 'Error') !== false) {
                                $command = sprintf('openssl smime -verify -inform PEM -in ' . $inputFile . ' -noverify -out ' . $outputFile);
                                $output = shell_exec($command . ' 2>&1');
                            }


                            if(file_exists($outputFile)) {
                                $content = file_get_contents($outputFile);
                            } else {
                                $content = file_get_contents($inputFile);
                            }

                            // Prova prima ad estrarre manualmente l'XML
                            $xml_start = strpos($content, '<?xml');
                            if($xml_start !== false) {
                                $xml_content = substr($content, $xml_start);
                                // Cerca il tag di apertura per determinare il namespace
                                $opening_tag = '';
                                if (strpos($xml_content, '<ns3:FatturaElettronica') !== false) {
                                    $opening_tag = 'ns3';
                                } elseif (strpos($xml_content, '<p:FatturaElettronica') !== false) {
                                    $opening_tag = 'p';
                                }elseif (strpos($xml_content, '<b:FatturaElettronica') !== false) {
                                    $opening_tag = 'b';
                                }  elseif (strpos($xml_content, '<n0:FatturaElettronica') !== false) {
                                $opening_tag = 'n0';
                            }
                                
                                // Determina il tag di chiusura in base a quello di apertura
                                $xml_end = $opening_tag ? 
                                    strpos($xml_content, '</'.$opening_tag.':FatturaElettronica>') : 
                                    strpos($xml_content, '</FatturaElettronica>');
                                if($xml_end !== false) {
                                    $xml_content = substr($xml_content, 0, $xml_end + strlen('</'.$opening_tag.':FatturaElettronica>'));

                                    $filtered_content = CronController::pulisciXML($xml_content);

                                    file_put_contents($outputFile, $filtered_content);
                                    new \SimpleXMLElement($filtered_content);
                                    $xml_path = $outputFile;
                                    unlink($inputFile);
                                } else {
                                    $xml_path = $inputFile;
                                }
                            } else {
                                $xml_path = $inputFile;
                            }
                        }


                        $xml_content = file_get_contents($xml_path);


                        $existing_fattura = DB::select("SELECT id FROM dotes WHERE nome_file_fattura = ? AND id_azienda = ? AND cd_do = '".$cd_do."' LIMIT 1", [$file, $id_azienda]);
                        if (empty($existing_fattura)) {

                            $utils = new UtilsController();


                            $risultato = $utils->crea_documento_da_xml($xml_content, $id_azienda,$cd_do);

                            // Documento creato con successo
                            $id_dotes = $risultato['id_dotes'];
                            DB::update("UPDATE dotes SET nome_file_fattura = ? WHERE id = ? AND id_azienda = ?", [$file, $id_dotes, $id_azienda]);

                            $testate = DB::select('SELECT * from dotes where id_azienda = '.$id_azienda.' and id = '.$id_dotes);
                            foreach($testate as $testata) {
                                UtilsController::invia_notifica_fattura($azienda, $testata, $xml_content);
                            }
                        }


                        rename('fatture_ricevute/'.$partita_iva.'/'.$file,'fatture_ricevute/'.$partita_iva.'/backup/fatture/'.$file);
                    }
                }


                return [
                    'success' => true,
                    'message' => 'Importazione completata con successo'
                ];

            } else {
                return [
                    'success' => false,
                    'message' => 'Errore nell\'apertura del file ZIP'
                ];
            }



    }


    public static function pulisciXML($xml) {
        // 1. Rimuovi tutti i caratteri di controllo non stampabili
        // EOT (0x04), NUL (0x00), ecc.

        $xmlPulito = preg_replace('/[\x00-\x09\x0B-\x1F\x7F-\x9F]/u', '', $xml);

        // 2. Correggi specificamente i tag danneggiati identificati
        $xmlPulito = str_replace('ataIVA>', 'taIVA>', $xmlPulito);
        $xmlPulito = str_replace('ato>', 'to>', $xmlPulito);


        // 3. Pulisci spazi problematici nelle descrizioni
        $xmlPulito = preg_replace_callback('/<Descrizione>([\s\S]*?)<\/Descrizione>/u', function($matches) {
            // Mantieni le interruzioni di riga ma normalizza gli spazi
            $descrizione = $matches[1];
            $descrizione = preg_replace('/\s+/u', ' ', $descrizione);
            $descrizione = trim($descrizione);
            return "<Descrizione>$descrizione</Descrizione>";
        }, $xmlPulito);


        // 3. Rimuovi sequenze EOT+NUL e caratteri adiacenti
        $xmlPulito = preg_replace('/EOT..NUL/u', '', $xmlPulito);

        // 4. Correggi i tag POD/PDR danneggiati
        $xmlPulito = preg_replace('/<TipoDato>[^<]*<\/Tip[^>]*>/u', '<TipoDato>POD/PDR</TipoDato>', $xmlPulito);

        // 5. Correggi i tag RiferimentoTesto danneggiati
        $xmlPulito = preg_replace('/<RiferimentoTesto>([^<]+)<\/Riferimento[^>]*>/u', '<RiferimentoTesto>$1</RiferimentoTesto>', $xmlPulito);

        // 6. Rimuovi altri caratteri non validi
       // $xmlPulito = preg_replace('/[^\x20-\x7E\xA0-\xFF\s<>\/="\':.;,\-_()[\]{}]/u', '', $xmlPulito);

        // 7. Elimina tag con caratteri non validi
        //$xmlPulito = preg_replace('/<[^>]*[^\w\s\/>][^>]*>/u', '', $xmlPulito);

        // 8. Correggi coppie di tag danneggiati
        $tagMapping = [
            'TipoDato' => '</TipoDato>',
            'RiferimentoTesto' => '</RiferimentoTesto>',
            'AltriDatiGestionali' => '</AltriDatiGestionali>',
            'AliquotaIVA' => '</AliquotaIVA>'
        ];

        foreach ($tagMapping as $tagOpen => $tagClose) {
            $pattern = '/<' . $tagOpen . '>([^<]*)<\/[^>]*>/u';
            $replacement = '<' . $tagOpen . '>$1' . $tagClose;
            $xmlPulito = preg_replace($pattern, $replacement, $xmlPulito);
        }

        return $xmlPulito;
    }

}

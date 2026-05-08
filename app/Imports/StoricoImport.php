<?php
namespace App\Imports;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;


use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class StoricoImport implements ToCollection,WithCalculatedFormulas
{
    public function collection(Collection $rows)
    {
        $i = 1;
        foreach ($rows as $row) {

            if($i > 3) {

                if (isset($row) && isset($row[0])) {


                    $insert['entity'] = $row[0];
                    $insert['codice_articolo'] = $row[1];
                    $insert['ol'] = $row[2];
                    $insert['private_label'] = $row[3];
                    $insert['formato'] = $row[4];
                    $insert['descrizione_articolo'] = $row[5];
                    $insert['frutto_def'] = $row[6];
                    $insert['descrizione_articolo_def'] = $row[7];
                    $insert['descrizione_articolo_inglese'] = $row[8];
                    $insert['clissaificazione_bilancio'] = $row[9];
                    $insert['packaging'] = $row[10];
                    $insert['data_documento'] = $row[11];
                    $insert['anno_doc'] = $row[12];
                    $insert['mese_doc'] = $row[13];
                    $insert['data_documento_pn'] = Date::excelToDateTimeObject($row[14]);
                    $insert['anno_pn'] = $row[15];
                    $insert['mese_pn'] = $row[16];
                    $insert['tipo_documento'] = $row[17];
                    $insert['serie'] = $row[18];
                    $insert['numero_documento'] = $row[19];
                    $insert['conto_coge'] = $row[20];
                    $insert['descrizione_conto_coge'] = $row[21];
                    $insert['gruppo_coge'] = $row[22];
                    $insert['codice_bp'] = $row[23];
                    $insert['descrizione_bp'] = $row[24];
                    $insert['descrizione_bp_def'] = $row[25];
                    $insert['cliente'] = $row[26];
                    $insert['b2b_b2c'] = $row[27];
                    $insert['tipo_clienti'] = $row[28];
                    $insert['um'] = $row[29];
                    $insert['quantita'] = floatval($row[30]);
                    $insert['senza_registrazione_quantita'] = $row[31];
                    $insert['prezzo_originario'] = $row[32];
                    $insert['valute'] = $row[33];
                    $insert['prezzo_in_euro'] = floatval($row[34]);
                    $insert['imponibile_in_euro'] = floatval($row[35]);
                    $insert['Imponibile_de'] = floatval($row[36]);
                    $insert['quantita_kg_ver2'] = floatval($row[37]);
                    $insert['quantita_kg'] = floatval($row[38]);
                    $insert['prezzo_euro_kg'] = floatval($row[39]);
                    $insert['bio_declassato'] = $row[40];
                    $insert['guscio'] = $row[41];
                    $insert['sgusciato'] = $row[42];
                    $insert['pelato'] = $row[43];
                    $insert['tostato'] = $row[44];
                    $insert['crudo'] = $row[45];
                    $insert['farina'] = $row[46];
                    $insert['granella'] = $row[47];
                    $insert['pasta'] = $row[48];
                    $insert['pralina_latte'] = $row[49];
                    $insert['pralina_fondente'] = $row[50];
                    $insert['pralina_fondente_peperoncino'] = $row[51];
                    $insert['pralina_fondente_cannella'] = $row[52];
                    $insert['limone'] = $row[53];
                    $insert['arancia'] = $row[54];
                    $insert['sale'] = $row[55];
                    $insert['fritto'] = $row[56];
                    $insert['amaro'] = $row[57];
                    $insert['chicobella'] = $row[58];
                    $insert['fairtrade'] = $row[59];
                    $insert['fogliame'] = $row[60];
                    $insert['cannella'] = $row[61];
                    $insert['peperoncino'] = $row[62];
                    $insert['merce_sfusa'] = $row[63];
                    $insert['prodotto_finito'] = $row[64];
                    $insert['biosussie'] = $row[65];
                    $insert['ibd'] = $row[66];
                    $insert['dop'] = $row[67];
                    $insert['frutto'] = $row[68];
                    $insert['nazione'] = $row[69];
                    $insert['categoria_bp'] = $row[70];
                    $insert['paese_x_esteso'] = $row[71];
                    $insert['gruppo_paese'] = $row[72];
                    $insert['famiglia_prodotto'] = $row[73];
                    $insert['canale_distributivo_principale'] = $row[74];
                    $insert['dipendenti'] = $row[75];
                    $insert['consulenti_interni'] = $row[76];
                    $insert['codice_gruppo_articolo'] = $row[77];
                    $insert['gruppo_articolo'] = $row[78];
                    $insert['um_vendita'] = $row[79];
                    $insert['codice_um_vendita'] = $row[80];
                    $insert['nome_um_vendita'] = $row[81];
                    $insert['creme'] = $row[82];
                    $insert['conv_kg'] = $row[83];
                    $insert['capacita'] = $row[84];
                    $insert['capacita_um'] = $row[85];
                    $insert['canale_distributivo_oai'] = $row[86];
                    $insert['fine'] = $row[87];
                    $insert['note'] = $row[88];
                    $insert['key_magazzino'] = $row[89];
                    $insert['costo_mese'] = $row[90];
                    $insert['costo_del_venduto'] = $row[91];
                    $insert['margine'] = $row[92];
                    $insert['costo_venduto_da_sap'] = $row[93];
                    $insert['margine_da_sap'] = $row[94];
                    $insert['sap'] = $row[95];
                    $insert['check'] = $row[96];
                    $insert['mercato'] = $row[97];
                    $insert['go_to_mkt'] = $row[98];
                    $insert['canale_distributivo'] = $row[99];
                    $insert['bp_consolidamento'] = $row[100];
                    $insert['customer_service'] = $row[101];
                    $insert['responsabile'] = $row[102];
                    DB::table('storico')->insert($insert);

                }
            }

        $i++;}
    }
}
?>
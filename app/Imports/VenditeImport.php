<?php
namespace App\Imports;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;

use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use \PhpOffice\PhpSpreadsheet\Shared\Date;

class VenditeImport implements ToCollection,WithCalculatedFormulas
{
    public function collection(Collection $rows)
    {
        $i = 1;
        foreach ($rows as $row) {

            if($i > 1) {
                if (isset($row) && isset($row[0])) {

                    $insert['id'] = $row[0];
                    $insert['codice_articolo'] = $row[1];
                    $insert['descrizione_articolo'] = $row[2];
                    $insert['classificazione_bilancio'] = $row[3];
                    $insert['packaging'] = $row[4];
                    $insert['data_documento'] = Date::excelToDateTimeObject($row[5]);

                    $insert['tipo_documento'] = $row[6];
                    $insert['data_di_consegna'] = Date::excelToDateTimeObject($row[7]);
                    $insert['data_emv'] = Date::excelToDateTimeObject($row[8]);
                    $insert['anno_emv'] = $row[9];
                    $insert['mese_emv'] = $row[10];
                    $insert['stato_documento'] = $row[11];
                    $insert['serie'] = $row[12];
                    $insert['numero_documento'] = intval($row[13]);
                    $insert['conto_coge'] = $row[14];
                    $insert['descrizione_conto_coge'] = $row[15];
                    $insert['gruppo_coge'] = $row[16];
                    $insert['codice_bp'] = $row[17];
                    $insert['descrizione_bp'] = $row[18];
                    $insert['tipo_cliente'] = $row[19];
                    $insert['um'] = $row[20];
                    $insert['quantita'] = floatval($row[21]);
                    $insert['senza_registrazione_quantita'] = $row[22];
                    $insert['prezzo_originario'] = floatval($row[23]);
                    $insert['valuta'] = $row[24];
                    $insert['prezzo_in_euro'] = floatval($row[25]);
                    $insert['imponibile_in_euro'] = floatval($row[26]);
                    $insert['quantita_in_kg'] = floatval($row[27]);
                    $insert['prezzo_euro_kg'] = floatval($row[28]);
                    $insert['bio_declassato'] = $row[29];
                    $insert['guscio'] = $row[30];
                    $insert['sgusciato'] = $row[31];
                    $insert['pelato'] = $row[32];
                    $insert['tostato'] = $row[33];
                    $insert['crudo'] = $row[34];
                    $insert['farina'] = $row[35];
                    $insert['granella'] = $row[36];
                    $insert['pasta'] = $row[37];
                    $insert['pralina_latte'] = $row[38];
                    $insert['pralina_fondente'] = $row[39];
                    $insert['praloina_fondente_peperoncino'] = $row[40];
                    $insert['pralina_fondente_cannella'] = $row[41];
                    $insert['limone'] = $row[42];
                    $insert['arancia'] = $row[43];
                    $insert['sale'] = $row[44];
                    $insert['fritto'] = $row[45];
                    $insert['amaro'] = $row[46];
                    $insert['chicobella'] = $row[47];
                    $insert['fairtrade'] = $row[48];
                    $insert['fogliame'] = $row[49];
                    $insert['cannella'] = $row[50];
                    $insert['peperoncino'] = $row[51];
                    $insert['merce_sfusa'] = $row[52];
                    $insert['prodotto_finito'] = $row[53];
                    $insert['biosussie'] = $row[54];
                    $insert['ibd'] = $row[55];
                    $insert['dop'] = $row[56];
                    $insert['frutto'] = $row[57];
                    $insert['nazione'] = $row[58];
                    $insert['categoria_bp'] = $row[59];
                    $insert['paese_x_esteso'] = $row[60];
                    $insert['gruppo_paese'] = $row[61];
                    $insert['famiglia_prodotto'] = $row[62];
                    $insert['canale_distributivo'] = $row[63];
                    $insert['dipendenti'] = $row[64];
                    $insert['consulenti_interni'] = $row[65];
                    $insert['codice_gruppo_articolo'] = $row[66];
                    $insert['gruppo_articolo'] = $row[67];
                    $insert['um_vendita'] = $row[68];
                    $insert['codice_um_vendite'] = $row[69];
                    $insert['nome_um_vendite'] = $row[70];

                    $vendite = DB::select('SELECT * from vendite where id=' . $row[0]);
                    if (sizeof($vendite) > 0) {
                        DB::table('vendite')->where('id', $row[0])->update($insert);
                    } else {
                        DB::table('vendite')->insert($insert);
                    }
                }
            }

        $i++;}
    }
}
?>
<?php
namespace App\Imports;

use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class ArticoliImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $i = 1;
        DB::delete('DELETE from articoli');
        foreach ($rows as $row) {

            if(isset($row) && isset($row[0])) {
                $insert['codice'] = $row[0];
                $insert['descrizione'] = $row[1];
                $insert['al_magazzino'] = $row[2];
                $insert['inattivo'] = $row[3];
                $insert['frutto'] = $row[4];
                $insert['val_vecchi_codici'] = $row[5];
                $insert['incluso_val_magazzino'] = $row[6];
                $insert['val_nuovi_codici'] = $row[7];
                $insert['private_label'] = $row[8];
                $insert['famiglia_man_pel'] = $row[9];
                $insert['famiglia_man_pel_bio'] = $row[10];
                $insert['famiglia_man_sgu'] = $row[11];
                $insert['famiglia_man_sgu_bio'] = $row[12];
                $insert['bio'] = $row[13];
                $insert['classificazione_bilancio'] = $row[14];
                $insert['vecchio_codice_articolo'] = $row[15];
                $insert['fase'] = $row[16];
                $insert['um'] = $row[17];
                $insert['um_magazzino'] = $row[18];
                $insert['gruppo_articoli'] = $row[19];
                $insert['famiglia_prodotto'] = $row[20];
                $insert['pr_pasta'] = $row[21];
                $insert['pr_sgiusciato'] = $row[22];
                $insert['pr_pelato'] = $row[23];
                $insert['pr_tostato'] = $row[24];
                $insert['pr_crudo'] = $row[25];
                $insert['pr_farina'] = $row[26];
                $insert['pr_granella'] = $row[27];
                $insert['pr_fogliame'] = $row[28];
                $insert['shelf_product'] = $row[29];
                $insert['codici_a_barre'] = $row[30];
                $insert['shelf_product'] = $row[31];
                $insert['pr_pralina_latte'] = $row[32];
                $insert['pr_pralina_fondente'] = $row[33];
                $insert['pr_pralina_fondente_peperoncin'] = $row[34];
                $insert['pr_pralina_fondente_cannella'] = $row[35];
                $insert['pr_fairtrade'] = $row[36];
                $insert['pr_biosussie'] = $row[37];
                $insert['pr_ibd'] = $row[38];
                $insert['pr_filiera'] = $row[39];
                $insert['pr_oia'] = $row[40];
                $insert['pr_dop'] = $row[41];
                $insert['prodotto_finito'] = $row[42];
                $insert['merce_sfusa'] = $row[43];
                DB::table('articoli')->insert($insert);

            }

        $i++;}
    }
}
?>
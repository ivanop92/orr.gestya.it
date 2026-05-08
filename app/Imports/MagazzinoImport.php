<?php
namespace App\Imports;

use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class MagazzinoImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        DB::delete('DELETE from magazzino');

        $ultimo_codice = '';
        $ultima_descrizione = '';
        foreach ($rows as $row) {

            if(isset($row) && isset($row[0])) {

                if($row[0] != 'Totale' && $row[0] != '' && $ultimo_codice != $row[0]) {
                    $ultimo_codice = $row[0];
                    $ultima_descrizione = $row[5];
                }

                if($row[7] > 0) {
                    $insert['codice_articolo'] = $ultimo_codice;
                    $insert['descrizione'] = $ultima_descrizione;
                    if ($row[0] == 'Totale') {
                        $insert['lotto'] = $row[5];
                    }
                    $insert['quantita'] = $row[7];
                    DB::table('magazzino')->insert($insert);
                }

            }

        }
    }
}
?>
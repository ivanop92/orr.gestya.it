<?php
namespace App\Imports;

use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class BOMImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $i = 1;
        DB::delete('delete from bom');

        foreach ($rows as $row) {

            if($i > 1) {
                if (isset($row) && isset($row[0])) {
                    $insert['id'] = intval($row[0]);
                    $insert['struttura'] = $row[1];
                    $insert['livello'] = $row[2];
                    $insert['diba'] = $row[3];
                    $insert['rev_diba'] = $row[4];
                    $insert['componente'] = $row[5];
                    $insert['rev_componente'] = $row[6];
                    $insert['quantita_in_struttura'] = floatval($row[7]);
                    $insert['um'] = $row[8];
                    $insert['valore_unitario'] = floatval($row[9]);
                    $insert['valore'] = floatval($row[10]);
                    DB::table('bom')->insert($insert);
                }
            }

        $i++;}
    }
}
?>
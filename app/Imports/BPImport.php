<?php
namespace App\Imports;

use App\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;

class BPImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $i = 1;
        DB::delete('delete from bp');
        foreach ($rows as $row) {

            if(isset($row) && isset($row[0])) {
                $insert['codice'] = $row[0];
                $insert['nome'] = $row[1];
                $insert['localita'] = $row[2];
                $insert['inattivo'] = $row[3];
                DB::table('bp')->insert($insert);
            }

        $i++;}
    }
}
?>
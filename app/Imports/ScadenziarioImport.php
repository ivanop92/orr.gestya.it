<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ScadenziarioImport implements ToCollection, WithHeadingRow
{
    protected $id_utente;
    protected $id_azienda;

    public function __construct($id_azienda)
    {
        $this->id_azienda = $id_azienda;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {

            // Converti la data dal formato Excel
            $data = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['data'])->format('Y-m-d');



            DB::table('scadenziario')->insert([
                'id_azienda' => $this->id_azienda,
                'data_scadenza' => $data,
                'importo' => $row['importo'],
                'tipo_movimento' => strtolower($row['tipo']), // converte in minuscolo
                'note' => $row['note'] ?? '',
            ]);
        }
    }
}
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SearchResultExport implements FromArray,ShouldAutoSize
{

    // Excel data
    private $data;

    public function __construct($data) {
        $this->data = $data;
    }

    /**
     * Excel Data
     */
    public function array(): array
    {
        return $this->data;
    }
}
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class MassiveViewExport implements ShouldAutoSize,FromView,WithStyles
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

    public function view(): View
    {
        $righe = $this->data;
        return view('exports.massive',compact('righe'));
    }

    public function styles(Worksheet $sheet)
    {

        $sheet->getStyle('A1:P1')->getFill()->applyFromArray(['fillType' => 'solid','rotation' => 0, 'color' => ['rgb' => '	FFFF00'],]);

    }


}
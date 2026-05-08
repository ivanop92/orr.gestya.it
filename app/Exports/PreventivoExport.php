<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class PreventivoExport implements FromView,WithStyles
{

    // Excel data
    private $cliente;
    private $testata;
    private $righe_documento;

    public function __construct($cliente,$testata,$righe_documento) {
        $this->cliente = $cliente;
        $this->testata = $testata;
        $this->righe_documento = $righe_documento;
    }


    public function view(): View
    {
        $cliente = $this->cliente;
        $testata = $this->testata;
        $righe_documento = $this->righe_documento;
        return view('exports.preventivo',compact('cliente','testata','righe_documento'));
    }

    public function styles(Worksheet $sheet)
    {

        $sheet->getStyle('A23:I24')->getAlignment()->setWrapText(true);
        $sheet->getStyle('E25:E60')->getAlignment()->setWrapText(true);
        //$sheet->getStyle('A1:P1')->getFill()->applyFromArray(['fillType' => 'solid','rotation' => 0, 'color' => ['rgb' => 'D9D9D9'],]);

    }


}
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class MassiveViewExportGTS implements ShouldAutoSize,FromView,WithStyles
{

    // Excel data
    private $data;
    private $extra;

    public function __construct($data,$extra) {
        $this->data = $data;
        $this->extra = $extra;
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
        $data = $this->extra;
        return view('exports.massivegts',compact('righe','data'));
    }

    public function styles(Worksheet $sheet)
    {

        //$sheet->getStyle('A1:P1')->getFill()->applyFromArray(['fillType' => 'solid','rotation' => 0, 'color' => ['rgb' => '	FFFF00'],]);

    }


}
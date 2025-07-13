<?php

namespace App\Exports\Shipment;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class SummaryExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $shipmentRecord;

    public function __construct($shipmentRecord)
    {
        $this->shipmentRecord = $shipmentRecord;
    }


    public function collection()
    {
        if ($this->shipmentRecord->isEmpty()) {
            return collect([['検索結果はありません']]);
        }
        return $this->shipmentRecord;
    }

    public function headings(): array
    {
        return [
            '部門CD',
            '部門名',
            '課名',
            '組名',
            'ラインCD.',
            'ライン名',
            '製品品番',
            '品名',
            '納入数',

        ];
    }

    public function map($shipmentRecord): array
    {
        if ($this->shipmentRecord->isEmpty()) {
            return ['検索結果はありません'];
        }
        return [
            $shipmentRecord?->department_code ?? '',
            $shipmentRecord?->department_name ?? '',
            $shipmentRecord?->section_name ?? '',
            $shipmentRecord?->group_name ?? '',    
            $shipmentRecord?->line_code ?? '',
            $shipmentRecord?->line_name ?? '',
            $shipmentRecord?->product_number ?? '',
            $shipmentRecord?->product_name ?? '',
            $shipmentRecord?->quantity ?? '0',
        ] ?? '';
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells A2 to the last column if no records exist
        if ($this->shipmentRecord->isEmpty()) {
            $highestColumn = $sheet->getHighestColumn();
            $sheet->mergeCells("A2:{$highestColumn}2");
            $sheet->getStyle("A2")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A2")->getFont()->setBold(true);
        }

        // Centers the text horizontally for the range A1:Z1000 in the spreadsheet
        $sheet->getStyle('A1:Z1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:Z1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $sheet->getStyle('G1:G1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('H1:H1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('I1:I1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

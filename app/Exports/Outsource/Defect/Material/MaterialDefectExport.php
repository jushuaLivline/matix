<?php

namespace App\Exports\Outsource\Defect\Material;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class MaterialDefectExport implements FromCollection, WithHeadingRow, WithHeadings, WithMapping, WithStyles
{
    protected $outsourceMaterialDefects, $reasons;

    public function __construct($outsourceMaterialDefects, $reasons)
    {
        $this->outsourceMaterialDefects = $outsourceMaterialDefects;
        $this->reasons = $reasons;
    }

    public function collection()
    {
        if ($this->outsourceMaterialDefects->isEmpty()) {
            return ['検索結果はありません'];
        }
        return $this->outsourceMaterialDefects;
    }

    public function headings(): array
    {
        return [
            '返却日',
            '製品品番',
            '品名',
            '材料仕入先',
            '工程名',
            '理由',
            '数量',
            '加工単価',
            '加工率',
            '金額',
            '伝票No'
        ];
    }

    public function map($outsourceMaterialDefects): array
    {
        if ($this->outsourceMaterialDefects->isEmpty()) {
            return ['検索結果はありません'];
        }
        $reason = "";

        foreach ($this->reasons as $reason){
            if($outsourceMaterialDefects->reason_code == $reason->code){
                $reason = $reason->name;
                break;
            }
        }
        return [
            $outsourceMaterialDefects->return_date->format('Ymd'),
            "\t ". $outsourceMaterialDefects->product_number,
            $outsourceMaterialDefects?->product?->product_name ?? null,
            $outsourceMaterialDefects?->supplier_code ?? null,
            $outsourceMaterialDefects?->process?->abbreviation_process_name ?? null,
            $reason,
            $outsourceMaterialDefects->quantity,
            $outsourceMaterialDefects->product?->processUnitPrice?->processing_unit_price ?? null,
            $outsourceMaterialDefects->processing_rate,
            round($outsourceMaterialDefects->quantity * $outsourceMaterialDefects->product?->processUnitPrice?->processing_unit_price * ($outsourceMaterialDefects->processing_rate / 100)),
            $outsourceMaterialDefects->slip_no
        ];
        
    }

    public function styles(Worksheet $sheet)
    {
         // Merge cells A2 to the last column if no records exist
         if ($this->outsourceMaterialDefects->isEmpty()) {
            $highestColumn = $sheet->getHighestColumn();
            $sheet->mergeCells("A2:{$highestColumn}2");
            $sheet->getStyle("A2")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A2")->getFont()->setBold(true);
        }

        // Centers the text horizontally for the range A1:Z1000 in the spreadsheet
        $sheet->getStyle('A1:Z1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        return [
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }
}

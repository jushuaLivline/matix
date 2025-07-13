<?php

namespace App\Exports\Outsource\Defect;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProcessDefectExport implements FromCollection, WithMapping, WithHeadings, WithHeadingRow
{
    protected $outsourceProcessDefects;

    public function __construct($outsourceProcessDefects)
    {
        $this->outsourceProcessDefects = $outsourceProcessDefects;
    }

    public function collection()
    {
        return $this->outsourceProcessDefects;
    }

    public function headings(): array
    {
        return [
            '廃却日',
            '仕入先名',
            '製品品番',
            '品名',
            '数量',
            '単価',
            '金額',
            '伝票No'
        ];
    }

    public function map($outsourceProcessDefects): array
    {
        return [
            $outsourceProcessDefects->disposal_date->format('Ymd'),
            $outsourceProcessDefects->product?->customer?->supplier_name_abbreviation ?? null,
            "\t ". $outsourceProcessDefects->part_number ?? null,
            $outsourceProcessDefects->product?->product_name ?? null,
            $outsourceProcessDefects->quantity ?? null,
            $outsourceProcessDefects->product?->processUnitPrice?->processing_unit_price ?? null,
            round($outsourceProcessDefects->quantity * $outsourceProcessDefects->product?->processUnitPrice?->processing_unit_price),
            $outsourceProcessDefects->slip_no ?? null
        ];
        
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }
}

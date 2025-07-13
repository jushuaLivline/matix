<?php

namespace App\Exports\Material;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class ReturnExcelExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $supplyMaterialArrivals;

    public function __construct($supplyMaterialArrivals)
    {
        $this->supplyMaterialArrivals = $supplyMaterialArrivals;
    }


    public function collection()
    {
        return $this->supplyMaterialArrivals;
    }

    public function headings(): array
    {
        return [
            '返却日',
            '伝票No',
            '材料メーカーコード',
            '品番',
            '品名',
            '数量',
            '加工率',
            '伝票区分'
        ];
    }

    public function map($supplyMaterialArrivals): array
    {
        $voucherClass = [
            1 => '支給',
            2 => '返品',
            3 => '材不返品',
        ];
        return [
            $supplyMaterialArrivals->arrival_day->format('Y-m-d'),
            $supplyMaterialArrivals->delivery_no,
            $supplyMaterialArrivals->material_manufacturer_code,
            $supplyMaterialArrivals->part_number ?? $supplyMaterialArrivals->material_no,
            $supplyMaterialArrivals->product?->product_name ?? null,
            $supplyMaterialArrivals->arrival_quantity ?? null,
            $supplyMaterialArrivals->processing_rate ?? null,
            $voucherClass[$supplyMaterialArrivals->voucher_class] ?? null
        ];
    }


    public function styles(Worksheet $sheet)
    {
        // center the values in cell
        $sheet->getStyle('A1:Z1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        return [
            1 => [
                'font' => ['bold' => true],
            ],
        ];
    }
}

<?php

namespace App\Exports\Material;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ReturnSummaryExcelExport implements FromCollection, WithHeadings, WithMapping
{
    protected $supplyMaterialArrivals;

    public function __construct($supplyMaterialArrivals, $request)
    {
        $this->supplyMaterialArrivals = $supplyMaterialArrivals;
        $this->request = $request;
    }


    public function collection()
    {
        return $this->supplyMaterialArrivals;
    }

    public function headings(): array
    {
        $heading = [
            '部門CD',
            '部門名',
            'ラインCD.',
            'ライン名',
            '製品品番',
            '品名',
            '数量',
            '加工費'

        ];
        return $heading;
    }

    public function map($supplyMaterialArrivals): array
    {
        return [
            $supplyMaterialArrivals->product?->department_code,
            $supplyMaterialArrivals->product?->department->name,
            $supplyMaterialArrivals->product?->line_code,
            $supplyMaterialArrivals->product?->line->line_name,
            $supplyMaterialArrivals->edited_part_number,
            $supplyMaterialArrivals->product?->product_name,
            $supplyMaterialArrivals->max_arrival_quantity,
            $supplyMaterialArrivals->max_processing_rate
        ];
    }
}

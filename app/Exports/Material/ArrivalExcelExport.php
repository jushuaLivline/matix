<?php

namespace App\Exports\Material;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Carbon\Carbon;

class ArrivalExcelExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
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
            '納入No',
            '材料品番',
            '品名',
            '入荷日',
            '便No.',
            '入荷数',
            '伝票区分'
        ];
    }

    public function map($supplyMaterialArrivals): array
    {
        // Since voucher_Class is varchar on database, lets make sure it'll be int
        $voucherClass = (int) $supplyMaterialArrivals->voucher_class;
        $voucherLabel = $this->getArrivalClassificationLabel($voucherClass);

        return [
            $supplyMaterialArrivals->delivery_no,
            $supplyMaterialArrivals->material_no,
            $supplyMaterialArrivals->product_name ?? null,
            $supplyMaterialArrivals->arrival_day ?? null,
            $supplyMaterialArrivals->flight_no ?? null,
            $supplyMaterialArrivals->arrival_quantity ?? null,
            $this->getArrivalClassificationLabel($supplyMaterialArrivals->voucher_class)
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

    protected function getArrivalClassificationLabel($voucherClass)
    {
        return match ((int)$voucherClass) {
            1 => '支給',
            2 => '返品',
            3 => '材不返品',
            default => null,
        };
    }

}
<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;

class ArrivalResultExport implements FromCollection, WithMapping, WithHeadings
{
    protected $arrivalResultLists;

    public function __construct($arrivalResultLists)
    {
        $this->arrivalResultLists = $arrivalResultLists;
    }


    public function collection()
    {
        return $this->arrivalResultLists;
    }

    public function headings(): array
    {
        return [
            '発注No',
            '製品品番',
            '品名',
            '仕入先名',
            '入荷日.',
            '便No',
            '指示数',
            '入荷数'
        ];
    }

    public function map($arrivalResultLists): array
    {
        return [
            $arrivalResultLists->order_no,
            $arrivalResultLists->product_code,
            $arrivalResultLists->product?->product_name ?? null,
            $arrivalResultLists->supplier?->supplier_name_abbreviation ?? null,
            $arrivalResultLists->arrival_day?->format('Ymd') ?? null,
            $arrivalResultLists->incoming_flight_number ?? null,
            $arrivalResultLists->instruction_number ?? null,
            $arrivalResultLists->arrival_quantity ?? null,
        ];
    }
}

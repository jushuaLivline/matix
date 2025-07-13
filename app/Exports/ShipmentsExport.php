<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ShipmentsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $shipments;

    public function __construct($shipments)
    {
        $this->shipments = $shipments;
    }

    public function collection()
    {
        return collect($this->shipments);
    }

    public function headings(): array
    {
        return [
            '納入日',
            '便No',
            '納入先',
            '伝票No',
            '受入',
            '直送先',
            '製品品番',
            '品名',
            '納入数',
            '部門',
            '備考',
        ];
    }
}
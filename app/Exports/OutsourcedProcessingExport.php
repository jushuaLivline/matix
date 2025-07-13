<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;

class OutsourcedProcessingExport implements FromCollection
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
            'order_number',
            'management_no',
            'branch_number',
            'supplier_code',
            'instruction_date',
            'instruction_number',
            'instruction_kanban_quantity'
        ];
    }
}

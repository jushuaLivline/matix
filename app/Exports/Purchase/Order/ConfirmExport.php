<?php

namespace App\Exports\Purchase\Order;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ConfirmExport implements FromCollection, WithMapping, WithHeadings
{
    protected $purchaseRequisition;

    public function __construct($purchaseRequisition)
    {
        $this->purchaseRequisition = $purchaseRequisition;
    }

    public function collection()
    {
        return $this->purchaseRequisition;
    }

    public function headings(): array
    {
        return [
            '購買依頼No',
            'ライン',
            '品番・品名・規格',
            '数量',
            '単位',
            '単価',
            '金額',
            '依頼者',
            '納期',
            '備考',
        ];
    }

    public function map($purchaseRequisition): array
    {
        return [
            $purchaseRequisition->requisition_number,
            $purchaseRequisition->line?->line_name,
            implode("・", array_filter([$purchaseRequisition->part_number, $purchaseRequisition->product_name, $purchaseRequisition->standard])),
            $purchaseRequisition->quantity,
            $purchaseRequisition->unit?->name,
            $purchaseRequisition->unit_price,
            $purchaseRequisition->amount_of_money,
            $purchaseRequisition->employee?->employee_name,
            $purchaseRequisition->deadline?->format('Y-m-d'),
            $purchaseRequisition->remarks,
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

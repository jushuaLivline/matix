<?php

namespace App\Exports\Purchase\Order;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ListExport implements FromCollection, WithHeadings, WithMapping
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
            '部門',
            'ライン',
            '発注先',
            '品番・品名・規格',
            '数量',
            '単価',
            '単位',
            '金額',
            '発注日',
            '納期',
            '入荷日',
            '入荷数',
            '受入日',
            '依頼者',
            '購買依頼No.',
            '注文書No.',
        ];
    }

    public function map($purchaseRequisition): array
    {
        return [
            $purchaseRequisition->department?->name,
            $purchaseRequisition->line?->line_name,
            $purchaseRequisition->supplier?->customer_name,
            implode(", ", [$purchaseRequisition->part_number, $purchaseRequisition->product_name, $purchaseRequisition->standard]),
            $purchaseRequisition->quantity,
            $purchaseRequisition->unit_price,
            $purchaseRequisition->unit?->name,
            $purchaseRequisition->amount_of_money,
            $purchaseRequisition->order_date?->format('Y-m-d'),
            $purchaseRequisition->deadline?->format('Y-m-d'),
            $purchaseRequisition->arrival_day?->format('Y-m-d'),
            $purchaseRequisition->total_arrival_quantity,
            $purchaseRequisition->purchase_receipt_date?->format('Y-m-d'),
            $purchaseRequisition->employee?->employee_name,
            $purchaseRequisition->requisition_number,
            $purchaseRequisition->purchase_order_number,
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

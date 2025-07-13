<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PurchaseOrderFormReissue implements FromCollection, WithHeadings, WithMapping
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            '購買依頼No.',
            'ライン',
            '品番・品名・規格',
            '数量',
            '単位',
            '単価',
            '金額',
            '依頼者',
            '納期',
            '備考'
        ];
        // return [
        //     '注文書No',
        //     '発注日',
        //     '発注先'
        // ];
    }

    public function map($data): array
    {

        return [
            $data->requisition_number,
            $data->line_name ?? '',
            $data->part_number . '*' . $data->product_name . '*' . $data->standard,
            $data->quantity,
            $data->name,
            $data->unit_price,
            $data->amount_of_money,
            $data->employee_name,
            $data->deadline,
            $data->remarks
        ];
        // return [
        //     $data->purchase_order_number,
        //     $data->order_date->format('Y-m-d'),
        //     $data->supplier->customer_name
        // ];
        
    }
}

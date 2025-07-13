<?php

namespace App\Exports\Purchase;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ActualExport implements FromCollection, WithHeadings
{
    protected $datas;

    public function __construct($datas)
    {
        $this->datas = collect($datas);
    }

    public function collection()
    {
        return $this->datas->map(function ($data) {
            return [
                '入荷日' => $data->arrival_date?->format('Y-m-d'),
                '仕入先名' => $data->supplier?->customer_name,
                '製品品番' => $data->part_number,
                '品名' => $data->product_name,
                '数量' => number_format($data->quantity, 0, '.', ','),
                '単位' => $data->unit_name ?? '',
                '単価' => number_format($data->unit_price, 0, '.', ','),
                '金額' => number_format($data->amount_of_money, 0, '.', ','),
                '伝票種類' => (($data->slip_type == 1) ? '納入伝票' : (($data->slip_type == 2) ? '外注加工伝票' : '購入材伝票')),
                '伝票No' => $data->slip_no,
            ];
        });
    }

    public function headings(): array
    {
        return [
            '入荷日',
            '仕入先名',
            '製品品番',
            '品名',
            '数量',
            '単位',
            '単価',
            '金額',
            '伝票種類',
            '伝票No',
        ];
    }
}

<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseDataExport implements FromCollection, WithHeadings
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
            '部門コード',
            '部門名',
            'ラインコード',
            'ライン名',
            '品番',
            '品名',
            '数量',
            '単位',
            '単価',
            '金額',
            '伝票No.',
            '仕入先名',
            '得意先名',
            '入荷日',
        ];
    }
}


<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ListLineExport implements FromCollection, WithHeadings
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
            '購入材料費',
            '設備材料費',
            '外注費',
            '設備外注費',
            '外注設計費',
            '外注工事費',
            '刃具費（その他）',
            '接待交際費',
            '修繕費',
            '消耗品費',
            '事務用品費',
            '会議費',
            '雑費',
            '合計',
        ];
    }
}


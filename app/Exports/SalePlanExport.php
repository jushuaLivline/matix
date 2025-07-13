<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\FromCollection;

class SalePlanExport implements WithHeadings, WithMapping, FromCollection
{
    protected $sum_a = 0;
    protected $sum_d = 0;

    public function __construct(
        protected $data,
        protected $type,
    ) {
        $this->sum_a = $data['sum_a'];
        $this->sum_d = $this->sum_a - $data['sum_b'] - $data['sum_c'];
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return $this->data['sales'];
    }

    /**
     * @param  mixed  $row
     * @return array
     */
    public function map($row): array
    {
        $col1 = match ($this->type) {
            '1' => $row->customer_name,
            '2', '3' => $row->code,
            default => $row->line_code,
        };

        $col2 = match ($this->type) {
            '1' => $row->branch_factory_name,
            '4', '5' => $row->line_name,
            default => $row->name,
        };

        $col3 = $row->price_a;
        $col4 = number_format($this->sum_a > 0 ? ($col3 / $this->sum_a) * 100 : 0, 2);
        $col5 = $row->price_b;
        $col6 = $row->price_c;
        $col7 = $col3 - $col5 - $col6;
        $col8 = number_format($this->sum_d > 0 ? ($col7 / $this->sum_d) * 100 : 0, 2);
        $col9 = $row->price_e;
        $col10 = $col7 - $col9;
        $col11 = number_format($col3 > 0 ? $col10 / $col3 : 0, 2);

        return [$col1, $col2, $col3, $col4, $col5, $col6, $col7, $col8, $col9, $col10, $col11];
    }

    public function headings(): array
    {
        $cols = match ($this->type) {
            '2', '3' => ['部門CD', '部門名'],
            '4', '5' => ['ラインCD', 'ライン名'],
            default => ['得意先名', '得意先工場'],
        };

        return [
            ...$cols,
            '売上高 A',
            '売上高比率(%)',
            '支給材料費 B',
            '購入材料費 C',
            '加工費 A-B-C=D',
            '加工費比率(%)',
            '外注加工費 E',
            '付加価値 D-E=F',
            '付加価値比率(%) F/A',
        ];
    }
}

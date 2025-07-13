<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class LinesExport implements FromCollection, WithHeadings
{
    protected $lines;

    public function __construct($lines)
    {
        $this->lines = $lines;
    }

    public function collection()
    {
        return $this->lines->map(function ($line) {
            return [
                'ラインコード' => $line->line_code,
                'ライン名' => $line->line_name,
                '部門コード' => $line->department_code,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'ラインコード',
            'ライン名',
            '部門コード',
        ];
    }
}
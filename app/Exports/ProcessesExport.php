<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ProcessesExport implements FromCollection, WithHeadings
{
    protected $processes;

    public function __construct($processes)
    {
        $this->processes = $processes;
    }

    public function collection()
    {
        return $this->processes;
    }

    public function headings(): array
    {
        return [
            '工程コード',
            '工程名',
            '工程名略',
            '内外区分',
            '取引先コード',
            '入荷待ち日数',
        ];
    }
}
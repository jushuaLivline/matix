<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class DepartmentsExport implements FromCollection, WithHeadings
{
    protected $departmemts;

    public function __construct($departmemts)
    {
        $this->departmemts = $departmemts;
    }

    public function collection()
    {
        return $this->departmemts;
    }

    public function headings(): array
    {
        return [
            '部門コード',
            '部門名',
            '部門名略',
            '部名',
            '課名',
            '組名',
            '有効/無効'
        ];
    }
}
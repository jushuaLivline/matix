<?php

namespace App\Exports\Master;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EmployeeExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $employees;

    public function __construct($employees)
    {
        $this->employees = $employees;
    }

    public function collection()
    {
        return $this->employees;
    }

    public function headings(): array
    {
        return [
            '社員コード',
            '社員名',
            '部門名',
            '権限'
        ];
    }
}
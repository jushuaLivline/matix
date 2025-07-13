<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PurchaseBreakdownExport implements FromCollection, WithHeadings
{
    protected $breakdown_datas;

    public function __construct($breakdown_datas)
    {
        $this->breakdown_datas = $breakdown_datas;
    }

    public function collection()
    {
        return $this->breakdown_datas;
    }

    public function headings(): array
    {
        return [
            '費目',
            '費目名',
            '勘定科目',
            '勘定科目名',
            '補助科目',
            '補助科目名',
            '金額'
        ];
    }
}


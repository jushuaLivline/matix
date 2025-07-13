<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KanbanMasterExport implements FromCollection, WithHeadings
{
    protected $kanban;

    public function __construct($kanban)
    {
        $this->kanban = $kanban;
    }

    public function collection()
    {
        return $this->kanban;
    }

    public function headings(): array
    {
        return [
            '管理No.',
            '品番',
            'かんばん区分',
        ];
    }
}
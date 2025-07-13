<?php
namespace App\Exports\Master;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class KanbanExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $kanbans;

    public function __construct($kanbans)
    {
        $this->kanbans = $kanbans;
    }

    public function collection()
    {
        return collect($this->kanbans);
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

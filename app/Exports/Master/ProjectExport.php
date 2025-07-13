<?php
namespace App\Exports\Master;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class ProjectExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $projects;

    public function __construct($projects)
    {
        $this->projects = $projects;
    }

    public function collection()
    {
        return collect($this->projects);
    }

    public function headings(): array
    {
        return [
            'プロジェクトNo.',
            'プロジェクト名'
        ];
    }
}

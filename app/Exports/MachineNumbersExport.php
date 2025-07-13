<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Constants\MachineNumberConstant;
use Carbon\Carbon;

class MachineNumbersExport implements FromCollection, WithHeadings
{
    protected $machineNumbers;

    public function __construct($machineNumbers)
    {
        $this->machineNumbers = $machineNumbers;
    }

    public function collection()
    {
        return $this->machineNumbers->map(function ($machineNumber) {
            return [
                '機番' => $machineNumber->machine_number,
                '機械名' => $machineNumber->machine_number_name,
                'プロジェクトNo.' => $machineNumber->project_number,
                'ライン名' => $machineNumber->line_name,
                '機械区分' => MachineNumberConstant::MACHINE_DIVISION[$machineNumber->machine_division] ?? $machineNumber->machine_division,
                '登録日' => $machineNumber->created_at ? Carbon::parse($machineNumber->created_at)->format('Y/m/d') : '',
                '出図日' => $machineNumber->drawing_date ? Carbon::parse($machineNumber->drawing_date)->format('Y/m/d') : '',
                '完成日' => $machineNumber->completion_date ? Carbon::parse($machineNumber->completion_date)->format('Y/m/d') : '',
                '担当者' => $machineNumber->manager,
                '備考' => $machineNumber->remarks
            ];
        });
    }

    public function headings(): array
    {
        return [
            '機番',
            '機械名',
            'プロジェクトNo.',
            'ライン名',
            '機械区分',
            '登録日',
            '出図日',
            '完成日',
            '担当者',
            '備考'
        ];
    }
}


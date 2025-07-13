<?php

namespace App\Exports\Outsource;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;
use Carbon\Carbon;

class SupplyExcelExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $outsourcedProcesses;

    public function __construct($outsourcedProcesses) {
        $this->outsourcedProcesses = $outsourcedProcesses;
    }

    public function collection() {
        if ($this->outsourcedProcesses->isEmpty()) {
            return collect([['検索結果はありません」']]);
        }
        return $this->outsourcedProcesses;
    }

    public function headings(): array
    {
        return [
            '支給No.',
            '支給日',
            '便No.',
            '管理No.',
            '枝番',
            '製品品番',
            '品名',
            '支給先コード',
            '仕入先名',
            '背番号',
            'サイクル',
            '枚数',
            '収容数',
            '数量',
        ];
    }

    public function map($outsourcedProcess): array
    {
        if ($this->outsourcedProcesses->isEmpty()) {
            return ['検索結果はありません'];
        }

        return [
            $outsourcedProcess->subcontract_supply_no ?? '',
            $outsourcedProcess->supply_date ? Carbon::parse($outsourcedProcess->supply_date)->format('Y-m-d') : '',
            $outsourcedProcess->supply_flight_no ?? '',
            $outsourcedProcess->management_no ?? '',
            $outsourcedProcess->branch_number ?? '',
            $outsourcedProcess->product_code ?? '',
            optional($outsourcedProcess->product_number)->product_name ?? '',
            $outsourcedProcess->supplier_process_code ?? '',
            optional($outsourcedProcess->customer)->customer_name,
            optional($outsourcedProcess->product_number)->uniform_number,
            '',
            $outsourcedProcess->supply_kanban_quantity ?? 0,
            optional($outsourcedProcess->kanban)->number_of_accomodated ?? 0,
            $outsourcedProcess->supply_quantity ?? 0,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells A2 to the last column if no records exist
        if ($this->outsourcedProcesses->isEmpty()) {
            $highestColumn = $sheet->getHighestColumn();
            $sheet->mergeCells("A2:{$highestColumn}2");
            $sheet->getStyle("A2")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A2")->getFont()->setBold(true);
        }

        // Centers the text horizontally for the range A1:Z1000 in the spreadsheet
        $sheet->getStyle('A1:Z1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
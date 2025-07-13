<?php

namespace App\Exports\Outsource;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\OutsourcedProcessing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class OrderExcelExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $outsourcedProcesses;

    public function __construct($outsourcedProcesses)
    {
        $this->outsourcedProcesses = $outsourcedProcesses;
    }

    public function collection()
    {
        if ($this->outsourcedProcesses->isEmpty()) {
            return collect([['検索結果はありません」']]);
        }
        return $this->outsourcedProcesses;
    }

    public function headings(): array
    {
        return [
            '指示日',
            '便No',
            '管理No',
            '枝番',
            '製品品番',
            '品名',
            '仕入先コード',
            '仕入先名',
            '発注区分',
            '背番号',
            '枚数',
            '数量',
            '操作',
        ];
    }

    public function map($outsourcedProcess): array
    {
        if ($this->outsourcedProcesses->isEmpty()) {
            return ['検索結果はありません'];
        }
        $orderClassification = [
            1 => '通常かんばん',
            2 => '臨時かんばん',
            3 => '指示',
            4 => '随時',
        ];
        return [
            optional($outsourcedProcess->instruction_date)->format('Y-m-d') ?? '（該当なし）',
            $outsourcedProcess->incoming_flight_number ?? '（該当なし）',
            $outsourcedProcess->management_no ?? '（該当なし）',
            $outsourcedProcess->branch_number ?? '（該当なし）',
            optional($outsourcedProcess->product)->customer_edited_product_number ?? '（該当なし）',
            optional($outsourcedProcess->product)->product_name ?? '（該当なし）',
            $outsourcedProcess->supplier_code ?? '（該当なし）',
            optional($outsourcedProcess->supplier)->customer_name ?? '（該当なし）',
            $orderClassification[$outsourcedProcess->order_classification] ?? null,
            optional($outsourcedProcess->kanbanMaster)->printed_jersey_number ?? '（該当なし）',
            $outsourcedProcess->instruction_kanban_quantity ?? 0,
            $outsourcedProcess->arrival_number ?? 0,
            $outsourcedProcess->arrival_quantity ?? 0,
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
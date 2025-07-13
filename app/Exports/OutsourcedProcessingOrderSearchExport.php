<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use App\Models\OutsourcedProcessing;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class OutsourcedProcessingOrderSearchExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $outsourcedProcesses;

    public function __construct($outsourcedProcesses)
    {
        $this->outsourcedProcesses = $outsourcedProcesses;
    }

    public function collection()
    {
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
            '背番号',
            '発注区分',
            '枚数',
            '数量',
            '操作',
        ];
    }

    public function map($outsourcedProcess): array
    {
        return [
            optional($outsourcedProcess->instruction_date)->format('Y-m-d') ?? '（該当なし）',
            $outsourcedProcess->instruction_number ?? '（該当なし）',
            $outsourcedProcess->management_no ?? '（該当なし）',
            $outsourcedProcess->branch_number ?? '（該当なし）',
            optional($outsourcedProcess->product)->customer_edited_product_number ?? '（該当なし）',
            optional($outsourcedProcess->product)->product_name ?? '（該当なし）',
            $outsourcedProcess->supplier_code ?? '（該当なし）',
            optional($outsourcedProcess->supplier)->customer_name ?? '（該当なし）',
            optional($outsourcedProcess->kanbanMaster)->printed_jersey_number ?? '（該当なし）',
            $this->getOrderClassificationLabel($outsourcedProcess->order_classification),
            $outsourcedProcess->instruction_kanban_quantity ?? 0,
            optional($outsourcedProcess->kanbanMaster)->number_of_accomodated ?? 0,
            $outsourcedProcess->arrival_quantity ?? 0,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    protected function getOrderClassificationLabel($orderClassification)
    {
        return match ($orderClassification) {
            1 => '通常かんばん',
            2 => '臨時かんばん',
            3 => '指示',
            default => '随時',
        };
    }
}
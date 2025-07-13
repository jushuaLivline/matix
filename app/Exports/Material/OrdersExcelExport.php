<?php

namespace App\Exports\Material;

use App\Models\SupplyMaterialOrder;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;

class OrdersExcelExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $supplyMaterialOrders;

    public function __construct($supplyMaterialOrders)
    {
        $this->supplyMaterialOrders = $supplyMaterialOrders;
    }

    public function collection()
    {
        if ($this->supplyMaterialOrders->isEmpty()) {
            return collect([['検索結果はありません」']]);
        }
        return $this->supplyMaterialOrders;
    }

    public function headings(): array
    {
        return [
            '管理No.',
            '枝番',
            '材料品番',
            '材料品名',
            '材料メーカーコード',
            '材料メーカー名',
            '背番号',
            '指示日',
            '便No.',
            '枚数',
            '収容数',
            '数量'
        ];
    }

    public function map($supplyMaterialOrders): array
    {
        if ($this->supplyMaterialOrders->isEmpty()) {
            return ['検索結果はありません'];
        }
        return [
            $supplyMaterialOrders->management_no,
            "\t" . $supplyMaterialOrders->branch_number,
            "\t" . $supplyMaterialOrders->material_number,
            $supplyMaterialOrders->product->product_name ?? null,
            $supplyMaterialOrders->supplier_code ?? null,
            $supplyMaterialOrders->supplier?->customer_name ?? null,
            //$this->getOrderClassificationLabel($supplyMaterialOrders->order_classification),
            $supplyMaterialOrders->kanban->printed_jersey_number ?? null,
            $supplyMaterialOrders->instruction_date?->format('Ymd'),
            $supplyMaterialOrders->instruction_no ?? null,
            $supplyMaterialOrders->instruction_kanban_quantity ?? null,
            $supplyMaterialOrders->kanban->number_of_accomodated ?? null,
           "\t" . ($supplyMaterialOrders->arrival_quantity ?? 0)

        ];
    }


    public function styles(Worksheet $sheet)
    {
        // Merge cells A2 to the last column if no records exist
        if ($this->supplyMaterialOrders->isEmpty()) {
            $highestColumn = $sheet->getHighestColumn();
            $sheet->mergeCells("A2:{$highestColumn}2");
            $sheet->getStyle("A2")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A2")->getFont()->setBold(true);
        }
        
        // Centers the text horizontally for the range A1:Z1000 in the spreadsheet
        $sheet->getStyle('A1:Z1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        return [
            1 => ['font' => ['bold' => true]], // Keep the header row bold
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


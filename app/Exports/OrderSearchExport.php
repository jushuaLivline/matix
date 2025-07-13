<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class OrderSearchExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $data;
    protected $deliveryNos;
    protected $category;

    public function __construct(Collection $data, array $deliveryNos, $category)
    {
        $this->data = $data;
        $this->deliveryNos = $deliveryNos;
        $this->category = $category;
    }

    public function collection()
    {
        return $this->data;
    }

    public function styles(Worksheet $sheet)
    {
        // $boldStyle = [
        //     'font' => [
        //         'bold' => true,
        //     ],
        // ];
        // $sheet->getStyle('A1:Z1')->applyFromArray($boldStyle);
        return [
            'A' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            'B' => ['alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
        ];
    }

    public function map($item): array
    {
        if (!is_array($item)) {
            return [];
        }

        $rows = [];
        foreach ($item as $partData) {
            if (!is_array($partData)) {
                continue;
            }

            $classification = $partData['classification'] ?? null;
            $dailyReports = $partData['daily_reports'] ?? [];

            // Initialize sums
            $kanbanNumberSum = array_sum(array_column($dailyReports, 'kanban_number')) ?? 0;
            $instructionNumberSum = array_sum(array_column($dailyReports, 'instruction_number')) ?? 0;

            // Initialize late data
            $lateKanban = $classification == 1 ? ($kanbanNumberSum - array_sum(array_column($dailyReports, 'shipment_kanban_number'))) : '';
            $lateInstruction = $classification == 2 ? ($instructionNumberSum - array_sum(array_column($dailyReports, 'shipment_instruction_number'))) : '';

            // Initialize first row data
            $firstRow = [
                $partData['acceptance'] ?? '',
                $partData['plant'] ?? '',
                $partData['part_number'] ?? '',
                $partData['product_name'] ?? '',
                $partData['uniform_number'] ?? '',
                $partData['number_of_accommodated'] ?? 0,
                $lateKanban,
                $lateInstruction,
            ];

            // Add delivery numbers columns based on category
            foreach ($this->deliveryNos as $deliveryNo) {
                $dailyReport = $dailyReports[$deliveryNo] ?? [];
                
                if ($this->category == 0) {
                    $kanbanNumber = $dailyReport['kanban_number'] ?? 0;
                    $instructionNumber = $dailyReport['instruction_number'] ?? 0;
                    $firstRow[] = $kanbanNumber;
                    $firstRow[] = $instructionNumber;
                }

                if ($this->category == 1) {
                    $kanbanNumber = $dailyReport['kanban_number'] ?? 0;
                    $firstRow[] = $kanbanNumber;
                }
                
                if ($this->category == 2) {
                }
                
            }

            // Add sums and calculations
            if ($this->category == 0) {
                $firstRow[] = $kanbanNumberSum;
                $firstRow[] = $instructionNumberSum;
                $firstRow[] = $kanbanNumberSum * ($partData['number_of_accommodated'] ?? 0);
            }
            if ($this->category == 1) {
                $firstRow[] = $kanbanNumberSum;
                $firstRow[] = $kanbanNumberSum * ($partData['number_of_accommodated'] ?? 0);
            }
            if ($this->category == 2) {
                $firstRow[] = $instructionNumberSum;
            }

            $rows[] = $firstRow;
        }

        return $rows;
    }

    public function headings(): array
    {
        $headings = ['受入', '工場', '品番', '品名', '背番号', '収容数', '遅れ 枚', '遅れ 個'];

        // Add delivery numbers columns based on category
        foreach ($this->deliveryNos as $deliveryNo) {
            if ($this->category == 0) {
                $headings[] = "{$deliveryNo}便 枚";
                $headings[] = "{$deliveryNo}便 個";
            }
            if ($this->category == 1) {
                $headings[] = "{$deliveryNo}便 枚";
            }
            if ($this->category == 2) {
                $headings[] = "{$deliveryNo}便 個";
            }
        }

        if ($this->category == 0) {
            $headings = array_merge($headings, ['かんばん枚合計','指示数計', '個数計']);
        }
        if ($this->category == 1) {
            $headings = array_merge($headings, ['かんばん枚合計', '個数計']);
        }
        if ($this->category == 2) {
            $headings = array_merge($headings, ['指示数計']);
        }

        return $headings;
    }
}
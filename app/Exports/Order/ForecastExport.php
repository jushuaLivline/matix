<?php

namespace App\Exports\Order;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class ForecastExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $unofficialNotices;

    public function __construct($unofficialNotices)
    {
        $this->unofficialNotices = $unofficialNotices;
    }


    public function collection()
    {
        if ($this->unofficialNotices->isEmpty()) {
            return ['検索結果はありません'];
        }
        return $this->unofficialNotices;
    }

    public function headings(): array
    {
        return [
            '納入先CD',
            '納入先名',
            '部門CD',
            '部門名',
            'ラインCD',
            'ライン名',
            '製品品番',
            '品名',
            '指示区分',
            '受入',
            '当月',
            '翌月',
            '翌々月',

        ];
    }

    public function map($unofficialNotices): array
    {
        if ($this->unofficialNotices->isEmpty()) {
            return ['検索結果はありません'];
        }
        $classification = [
            1 => 'かんばん',
            2 => '指示 ',
        ];

        return [
            $unofficialNotices?->product?->customer?->customer_code ?? '',
            $unofficialNotices?->product?->customer?->customer_name ?? '',
            $unofficialNotices?->product?->department_code ?? '',
            $unofficialNotices?->product?->department->name ?? '',
            $unofficialNotices?->product?->line_code ?? '',
            $unofficialNotices?->product?->line->line_name ?? '',
            $unofficialNotices?->product_number ?? '',
            $unofficialNotices?->product?->product_name ?? '',
            $classification[$unofficialNotices->instruction_class],
            $unofficialNotices->acceptance ?? 0,
            $unofficialNotices->current_month ?? 0,
            $unofficialNotices->next_month ?? 0,
            $unofficialNotices->two_months_later ?? 0
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells A2 to the last column if no records exist
        if ($this->unofficialNotices->isEmpty()) {
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

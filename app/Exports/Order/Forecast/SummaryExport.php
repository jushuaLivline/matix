<?php

namespace App\Exports\Order\Forecast;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class SummaryExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $unofficialNotices;

    public function __construct($unofficialNotices)
    {
        $this->unofficialNotices = $unofficialNotices;
    }


    public function collection()
    {
        if ($this->unofficialNotices->isEmpty()) {
            return collect([['検索結果はありません']]);
        }
        return $this->unofficialNotices;
    }

    public function headings(): array
    {
        return [
            '部門CD',
            '部門名',
            '課名',
            '組名',
            'ラインCD',
            'ライン名',
            '製品品番',
            '品名',
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
        return [
            $unofficialNotices?->department_code ?? '',
            $unofficialNotices?->department_name ?? '',
            $unofficialNotices?->section_name ?? '',
            $unofficialNotices?->group_name ?? '',
            $unofficialNotices?->line_code ?? '',
            $unofficialNotices?->line_name ?? '',
            $unofficialNotices?->product_number ?? '',
            $unofficialNotices?->product_name ?? '',
            $unofficialNotices?->current_month ?? '0',
            $unofficialNotices?->next_month ?? '0',
            $unofficialNotices?->two_months_later ?? '0',
        ] ?? '';
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
        $sheet->getStyle('A2:Z1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);

        $sheet->getStyle('G1:G1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('H1:H1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('I1:I1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

<?php

namespace App\Exports\Outsource\Arrival;


use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithStyles;

class PendingExcelExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    protected $searchResults;

    public function __construct($searchResults)
    {
        $this->searchResults = $searchResults;
    }


    public function collection()
    {
        if ($this->searchResults->isEmpty()) {
            return ['検索結果はありません」'];
        }
        return $this->searchResults;
    }

    public function headings(): array
    {
        return [
            '発注No',
            '製品品番',
            '品名',
            '仕入先名',
            '入荷日.',
            '指示日.',
            '便No',
            '指示数',
        ];
    }

    public function map($searchResults): array
    {
        if ($this->searchResults->isEmpty()) {
            return ['検索結果はありません」'];
        }
        return [
            $searchResults->order_no,
            $searchResults?->product_code,
            $searchResults?->product?->product_name ?? null,
            $searchResults?->supplier?->supplier_name_abbreviation ?? null,
            $searchResults->arrival_day?->format('Ymd') ?? null,
            $searchResults->instruction_date?->format('Ymd') ?? null,
            $searchResults->incoming_flight_number ?? null,
            $searchResults->instruction_number ?? null,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Merge cells A2 to the last column if no records exist
        if ($this->searchResults->isEmpty()) {
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
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}

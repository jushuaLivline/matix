<?php
namespace App\Exports\Master;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ProductNumberExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $product_numbers;

    public function __construct($product_numbers)
    {
        $this->product_numbers = $product_numbers;
    }

    public function collection()
    {
        return collect($this->product_numbers);
    }

    public function headings(): array
    {
        return [
            '品番',
            '品名',
            'ライン',
            '部門',
            '得意先',
            '仕入先',
            '製品区分' 
        ];
    }
}
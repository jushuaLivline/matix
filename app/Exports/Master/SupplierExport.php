<?php
namespace App\Exports\Master;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SupplierExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $customers;

    public function __construct($customers)
    {
        $this->customers = $customers;
    }

    public function collection()
    {
        return collect($this->customers);
    }

    public function headings(): array
    {
        return [
            '取引先コード',
            '取引先名',
            '郵便番号',
            '住所1',
            '電話番号',
            '得意先',
            '仕入先' 
        ];
    }
}

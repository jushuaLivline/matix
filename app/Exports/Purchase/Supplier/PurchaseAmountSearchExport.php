<?php

namespace App\Exports\PUrchase\Supplier;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class PurchaseAmountSearchExport implements FromView
{
    public $datas = [];

    public function __construct($datas)
    {
        $this->datas = $datas;
    }

    public function view(): View
    {
        return view('export-template.purchase.supplier.purchase-amount-search-export', [
            'datas' => $this->datas
        ]);
    }
}

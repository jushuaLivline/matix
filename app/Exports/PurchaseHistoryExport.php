<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class PurchaseHistoryExport implements FromView
{
    public $datas = [];

    public function __construct($datas)
    {
        $this->datas = $datas;
    }

    public function view(): View
    {
        return view('export-template.purchase.purchase-history-export', [
            'datas' => $this->datas
        ]);
    }
}

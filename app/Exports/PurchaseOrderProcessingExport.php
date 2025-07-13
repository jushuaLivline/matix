<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\FromCollection;

class PurchaseOrderProcessingExport implements FromView
{
    protected $purchaseRequisitions;

    public function __construct($purchaseRequisitions)
    {
        $this->purchaseRequisitions = $purchaseRequisitions;
    }

    public function view(): View
    {
        return view('export-template.purchase.order-processing', [
            'purchaseRequisitions' => $this->purchaseRequisitions
        ]);
    }

}

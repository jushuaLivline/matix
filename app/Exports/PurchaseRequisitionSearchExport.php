<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PurchaseRequisitionSearchExport implements FromView
{
    protected $purchaseRequisitions;

    public function __construct($purchaseRequisitions)
    {
        $this->purchaseRequisitions = $purchaseRequisitions;
    }

    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view(): View
    {
        return view('export-template.purchase.purchase-requisition-search', [
            'purchaseRequisitions' => $this->purchaseRequisitions
        ]);
    }
}

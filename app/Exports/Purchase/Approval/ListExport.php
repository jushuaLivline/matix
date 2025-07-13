<?php

namespace App\Exports\Purchase\Approval;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ListExport implements FromView, ShouldAutoSize
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
        return view('export-template.purchase.approval.listExport', [
            'purchaseRequisitions' => $this->purchaseRequisitions
        ]);
    }
}

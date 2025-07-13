<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PurchaseRequisitionContentExport implements FromView
{
    protected $purchaseRequisitionDetails;

    public function __construct($purchaseRequisitionDetails)
    {
        $this->purchaseRequisitionDetails = $purchaseRequisitionDetails;
    }
    public function view(): View
    {
        $view = 'export-template.purchase.purchase-requisition-content-pdf';
        $cellSize = [
            'normal' => '60px',
            'large'  => '70px',
            'xlarge'  => '100px',
            'small'  => '50px',
            'xs'  => '40px',
        ];

        return view($view, [
            'requisition' => $this->purchaseRequisitionDetails,
            'cellSize' => $cellSize,
            'exportType' => $this->purchaseRequisitionDetails['exportType']
        ]);
    }

}
 
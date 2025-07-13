<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PurchaseOrderReissueExport implements FromView
{
    protected $purchaseOrderDetail;

    public function __construct($purchaseOrderDetail)
    {
        $this->purchaseOrderDetail = $purchaseOrderDetail;
    }
    public function view(): View
    {
        if($this->purchaseOrderDetail['exportType'] == 'pdf') {
            $view = 'export-template.purchase.purchase-order-reissue-pdf';
            $cellSize = [
                'normal' => '60px',
                'large'  => '70px',
                'xlarge'  => '100px',
                'small'  => '50px',
                'xs'  => '40px',
            ];
        } else {
            $view = 'export-template.purchase.purchase-order-reissue';

            $cellSize = [
                'normal' => '90px',
                'large'  => '130px',
                'xlarge'  => '290px',
                'small'  => '60px',
                'xs'  => '50px',
            ];
        }

        $view = $this->purchaseOrderDetail['exportType'] == 'pdf' ? 'export-template.purchase.purchase-order-reissue-pdf' : 'export-template.purchase.purchase-order-reissue';
        
        return view($view, [
            'purchaseOrderItemDetails' => $this->purchaseOrderDetail['purchaseOrderItemDetails'],
            'purchaseOrderItem' => $this->purchaseOrderDetail['purchaseOrderItem'],
            'cellSize' => $cellSize,
            'exportType' => $this->purchaseOrderDetail['exportType']
        ]);
    }

}
 
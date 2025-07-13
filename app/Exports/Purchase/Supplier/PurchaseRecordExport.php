<?php

namespace App\Exports\Purchase\Supplier;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class PurchaseRecordExport implements FromView
{
    public $datas = [];

    public function __construct($purchaseRecord)
    {
      // Initialize the purchase records data for export
      $this->purchaseRecord = $purchaseRecord;
    }

    public function view(): View
    {
      // Return the view with the data to be exported
      return view('export-template.purchase.supplier.purchase-amount-detail-export', [
          'datas' => $this->purchaseRecord
      ]);
    }
}

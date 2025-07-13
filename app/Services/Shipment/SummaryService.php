<?php

namespace App\Services\Shipment;

use App\Mail\Purchase\PurchaseApproverNotification;
use App\Models\Employee;
use App\Models\ProductNumber;
use App\Models\ShipmentRecord;
use App\Models\Department;

use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class SummaryService
{

  public function __construct()
  {
    $this->shipmentRecord = new ShipmentRecord();
  }

  public function getSummaryRecord($request)
  {
    $query = $this->shipmentRecord->getSummaryRecord($request);

    if ($request->category == '1') {
      $results = $query->groupBy('departments.section_name');
    } elseif ($request->category == '2') {
      $results = $query->groupBy('departments.group_name');
    } elseif ($request->category == '3') {
      $results = $query->groupBy(groups: 'lines.line_code');
    } elseif ($request->category == '4') {
      $results = $query->groupBy('product_no');
    } else {
      $results = $query->groupBy('product_no');
    }
    return $results;
  }
}

<?php

namespace App\Services\Order\Forecast;

use App\Mail\Purchase\PurchaseApproverNotification;
use App\Models\Employee;
use App\Models\ProductNumber;
use App\Models\UnofficialNotice;
use App\Models\Department;

use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class SummaryService
{

  public function __construct()
  {
    $this->unofficialNotice = new UnofficialNotice();
  }

  public function getSummaryRecord($request)
  {
    $query = $this->unofficialNotice->getSummaryRecord($request);

    if ($request->category == 'department') {
      $results = $query->groupBy('departments.section_name');
    } elseif ($request->category == 'group') {
      $results = $query->groupBy('departments.group_name');
    } elseif ($request->category == 'line') {
      $results = $query->groupBy('lines.line_code');
    } elseif ($request->category == 'product') {
      $results = $query->groupBy('product_number');
    } else {
      $results = $query->groupBy('product_number');
    }
    return $results;
  }
}

<?php

namespace App\Services\Material;

use App\Mail\Purchase\PurchaseApproverNotification;
use App\Models\Employee;
use App\Models\ProductNumber;
use App\Models\SupplyMaterialArrival;
use App\Models\Department;

use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;

class ReturnSummaryService
{

  public function __construct()
  {
    $this->supplyMaterialArrival = new SupplyMaterialArrival();
  }

  public function getData($request)
  {
    $query = $this->supplyMaterialArrival->getReturnSummary($request);
    $paginationThreshold = Constant::PAGINATION_THRESHOLD;
    if ($request->category == 'division' || $request->category == 'department') {
      $results = $query->groupBy('department_code')
        ->paginate($paginationThreshold);
    } elseif ($request->category == 'line') {
      $results = $query->groupBy('line_code')
        ->paginate($paginationThreshold);
    } elseif ($request->category == 'product') {
      $results = $query->groupBy('product_number')->paginate($paginationThreshold);
    } else {
      $results = $query->groupBy('product_number')->paginate($paginationThreshold);
    }

   
    // $sql = vsprintf(str_replace('?', "'%s'", $query->toSql()), $query->getBindings());
    // dd($sql);
    return $results;
  }

  public function excel_export($request)
  {
    $query = $this->supplyMaterialArrival->getReturnSummary($request);
    if ($request->category == 'division' || $request->category == 'department') {
      $results = $query->groupBy('department_code', 'product_number')
        ->get();
    } elseif ($request->category == 'line') {
      $results = $query->groupBy('line_code', 'product_number')
        ->get();
    } elseif ($request->category == 'product_code') {
      $results = $query->groupBy('product_number', 'product_number')->get();
    } else {
      $results = $query->groupBy('product_number')->get();
    }
    return $results;
  }
}

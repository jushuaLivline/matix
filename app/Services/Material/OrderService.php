<?php

namespace App\Services\Material;

use App\Mail\Purchase\PurchaseApproverNotification;
use App\Models\Employee;
use App\Models\ProductNumber;
use App\Models\SupplyMaterialOrder;
use App\Models\Customer;

use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class OrderService
{

  public function __construct()
  {
    $this->supplyOrder = new SupplyMaterialOrder();
  }

  public function excel_export($request)
  {
    $query =$this->supplyOrder->query();

    $supplyMaterialOrders = $query
        ->when($request->manufacturer_code, function ($query) use ($request) {
          return $query->where('supplier_code', $request->manufacturer_code);
        })
        ->when($request->instruction_date_start, function ($query) use ($request) {
          return $request->instruction_date_end
            ? $query->whereBetween('instruction_date', [
                Carbon::parse($request->instruction_date_start)->format('Y-m-d'),
                Carbon::parse($request->instruction_date_end)->format('Y-m-d')
            ])
            : $query->where('instruction_date', Carbon::parse($request->instruction_date_start)->format('Y-m-d'));
        })
        ->when($request->arrival_day_from, function ($query) use ($request) {
          return $request->arrival_day_to
            ? $query->whereBetween('instruction_date', [
                Carbon::parse($request->arrival_day_from)->format('Y-m-d'),
                Carbon::parse($request->arrival_day_to)->format('Y-m-d')
            ])
            : $query->where('instruction_date', Carbon::parse($request->arrival_day_from)->format('Y-m-d'));
        })
        ->when($request->instruction_no_from && $request->instruction_no_to, function ($query) use ($request) {
          return $query->whereBetween('instruction_no', [$request->instruction_no_from, $request->instruction_no_to]);
        })
        ->with('kanban', 'product', 'supplier')
        ->get();
    return  $supplyMaterialOrders;
  }
}

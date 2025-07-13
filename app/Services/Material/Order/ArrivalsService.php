<?php

namespace App\Services\Material\Order;

use App\Models\ProductNumber;
use App\Models\SupplyMaterialOrder;
use App\Models\Material\SupplyArrival;

use Exception;
use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;


class ArrivalsService
{

  public function __construct()
  {
    $this->supplyArrival = new SupplyArrival();
    $this->productNumber = new ProductNumber();
  }

  public function excel_export($request)
  {
    $supplyMaterialArrivals = DB::table('supply_material_arrivals')
    ->join('product_numbers', 'product_numbers.part_number', '=', 'supply_material_arrivals.material_no')
    ->when($request->input('manufacturer_code'), function ($query, $manufacturerCode) {
        return $query->where('supply_material_arrivals.material_manufacturer_code', $manufacturerCode);
    })
    ->when($request->input('arrival_day_from'), function ($query) use ($request) {
        $arrivalStart = Carbon::parse($request->input('arrival_day_from'))->format('Y-m-d');
        $arrivalEnd = $request->input('arrival_day_to') 
            ? Carbon::parse($request->input('arrival_day_to'))->format('Y-m-d') 
            : $arrivalStart;
        return $query->whereBetween('arrival_day', [$arrivalStart, $arrivalEnd]);
    })
    ->when($request->input('flight_from') && $request->input('flight_to'), function ($query) use ($request) {
        return $query->whereBetween('supply_material_arrivals.flight_no', [
            $request->input('flight_from'),
            $request->input('flight_to')
        ]);
    })
    ->when($request->input('material_no'), function ($query, $materialNo) {
        return $query->where('supply_material_arrivals.material_no', $materialNo);
    })
    ->when($request->input('delivery_no'), function ($query, $deliveryNo) {
        return $query->where('supply_material_arrivals.delivery_no', 'LIKE', "%$deliveryNo%");
    })
    ->select([
        'supply_material_arrivals.delivery_no',
        'supply_material_arrivals.material_no',
        'product_numbers.product_name',
        'supply_material_arrivals.arrival_day',
        'supply_material_arrivals.flight_no',
        'supply_material_arrivals.arrival_quantity',
        'supply_material_arrivals.voucher_class'
    ])
    ->where('voucher_class', 1)
    ->orderBy('arrival_day', 'DESC')
    ->get();


      return $supplyMaterialArrivals;
  }
}
<?php

namespace App\Services\Material;

use App\Mail\Purchase\PurchaseApproverNotification;
use App\Models\Employee;
use App\Models\ProductNumber;
use App\Models\Customer;
use App\Models\Material\SupplyArrival;

use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class ReturnService
{
  protected $supplyArrival;

  public function __construct()
  {
    $this->supplyArrival = new SupplyArrival();
  }

  public function store($request)
  {
    $supplyMaterialNoLatest = $this->supplyArrival->generateSupplyMaterialReceiptNo();
    $serialNumberLatest = $this->supplyArrival->generateSerialNumber();
    $yearMonths = date('ym');
    $mothDays = date('md');
    // Transform the request data into an array suitable for database insertion.
    // 1. Iterate over each `dataId` entry using `collect()->map()` to maintain indexing.
    // 3. Use `array_map()` to dynamically extract indexed values for all other request fields,
    // 4. Convert the resulting collection to an array using `toArray()` for database insertion.
    $fields = collect($request)->except('')->toArray();
    $insertData = collect($request['supplier_code'])->map(function ($_, $index) use ($fields, $yearMonths, $supplyMaterialNoLatest, $mothDays, $serialNumberLatest, $request) {
      return [
          'supply_material_receipt_no' => $yearMonths . sprintf("%06d", substr($supplyMaterialNoLatest, 4) + $index),
          'serial_number' => $mothDays . sprintf("%02d", substr($serialNumberLatest, 4) + $index),
          'arrival_day' => Carbon::parse($request['arrival_day'])->format('Y-m-d'),
      ] + array_map(fn($field) => $field[$index] ?? null, $fields);
    })->toArray();
   
    // Create a new record in mst_supply_material_orders table
    return $this->supplyArrival->insert($insertData);
  }

  public function update($request, $id)
  {
    $request['arrival_day'] = Carbon::parse($request['arrival_day'])->format('Y-m-d');
    $arrivalRecord = $this->supplyArrival->findOrFail($id);
    $updateData = $arrivalRecord->update($request);
    return $updateData;
  }

  public function convertArraysToAssociative($arrayFields)
  {
    // Ensure all fields are arrays
    $fields = array_map(fn($value) => (array) $value, $arrayFields);

    // Transpose the array to group values by index
    $structuredData = array_map(null, ...array_values($fields));

    // Convert grouped values into associative arrays
    $insertData = array_map(fn($values) => array_combine(array_keys($fields), $values), $structuredData);

    return $insertData;
  }

}
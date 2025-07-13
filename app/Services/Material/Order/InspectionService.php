<?php

namespace App\Services\Material\Order;

use App\Models\ProductNumber;
use App\Models\SupplyMaterialOrder;
use App\Models\Material\SupplyArrival;

use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class InspectionService
{

  public function __construct()
  {
    $this->supplyArrival = new SupplyArrival();
    $this->productNumber = new ProductNumber();
  }

  public function store($request)
  {
    // Retrieve session data
    $sessionData = session('sessionReceiptAndInspectionData', []);

    // Generate a starting order number
    $supplyMaterialNoLatest = $this->supplyArrival->generateSupplyMaterialReceiptNo();
    $serialNumberLatest = $this->supplyArrival->generateSerialNumber();
    $yearMonths = date('ym');
    $mothDays = date('md');

    // Prepare data for bulk insertion
    $insertData = collect($sessionData)->map(fn($data, $index) => [
      'supply_material_receipt_no' => $yearMonths . sprintf("%06d", substr($supplyMaterialNoLatest, 4) + $index),
      'serial_number' => $mothDays . sprintf("%02d", substr($serialNumberLatest, 4) + $index),
      'arrival_day' => Carbon::parse($data['arrival_day'])->format('Y-m-d'),
    ] + array_diff_key($data, array_flip(['id', 'product_name'])) )->toArray();

      // dd($insertData, $sessionData);
    // Bulk insert into the database
    return $this->supplyArrival->insert($insertData);

  }
  public function store_session($request)
  {
    // Extract input data
    $inputData = $request->only([
      'arrival_day',
      'flight_no',
      'delivery_no',
      'arrival_quantity',
      'material_no',
      'voucher_class',
      'product_name'
    ]);

    // Fetch supplier details based on material number
    $supplier = $this->productNumber->where('part_number', $inputData['material_no'])
      ->first(['supplier_code', 'material_manufacturer_code']);

    // Prepare structured session data
    $sessionData = array_merge($inputData, [
      'id' => uniqid(),
      'supplier_code' => $supplier->supplier_code ?? null,
      'material_manufacturer_code' => $supplier->material_manufacturer_code ?? null,
      'product_number' => $inputData['material_no'],
      'product_name' => $inputData['product_name'],
      'creator' => $request->user()->id,
    ]);

    return $sessionData;
  }

  public function update($request, $id)
  {
    // Extract input data
    $inputData = $request->only([
      'data_id',
      'arrival_day',
      'flight_no',
      'delivery_no',
      'arrival_quantity',
      'material_no',
      'product_name'
    ]);

    // Retrieve session data
    $sessionData = collect(session('sessionReceiptAndInspectionData', []));

    // Fetch supplier details based on material number
    $supplier = $this->productNumber->where('part_number', $inputData['material_no'])
      ->first(['supplier_code', 'material_manufacturer_code']);

    // Update the session data
    $sessionData = $sessionData->map(function ($data) use ($inputData, $supplier, $id) {
      if ($data['id'] === $id) {
        return [
          ...$data,
          'arrival_day' => $inputData['arrival_day'],
          'flight_no' => $inputData['flight_no'],
          'delivery_no' => $inputData['delivery_no'],
          'material_no' => $inputData['material_no'],
          'supplier_code' => $supplier->supplier_code ?? $data['supplier_code'],
          'material_manufacturer_code' => $supplier->material_manufacturer_code ?? $data['material_manufacturer_code'],
          'arrival_quantity' => $inputData['arrival_quantity'],
          'product_name' => $inputData['product_name'],
        ];
      }
      return $data;
    })->toArray();

    return $sessionData;
  }
}
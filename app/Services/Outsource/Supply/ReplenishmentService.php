<?php

namespace App\Services\Outsource\Supply;

use App\Mail\Purchase\PurchaseApproverNotification;
use App\Models\KanbanMaster;
use App\Models\ProductNumber;
use App\Models\Material\SupplyOrder;
use App\Models\Customer;
use App\Models\Outsource\SubcontractSupply;

use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class ReplenishmentService
{

  public function __construct()
  {
    $this->subcontractSupply = new SubcontractSupply();
  }

  public function store($request)
  {
    // Retrieve session data from the validated request
    $sessionData = session('sessionSupplyReplenishmentData', []); // Ensure 'session_data' exists

    if (empty($sessionData)) {
      return response()->json(['error' => 'No session data provided'], 400);
    }

    // Generate a starting order number
    $baseOrderNo = $this->subcontractSupply->generateSubcontractSupplyNo();

    // Prepare data for bulk insertion
    $insertData = collect($sessionData)->map(function ($data, $index) use ($baseOrderNo) {
        return [
            'subcontract_supply_no' => $baseOrderNo + $index,  // Assign unique subcontract_supply_no
            'supply_date' => Carbon::parse($data['supply_date'])->format('Y-m-d'),
        ] + array_diff_key($data, array_flip(['id', 'product_name'])); // Exclude 'id' & 'product_name'
    })->toArray();

    // Bulk insert into the database
    return $this->subcontractSupply->insert($insertData);
  }
  public function store_session($request)
  {
   
    // Extract input data
    $inputData = $request->only([
      'management_no',
      'product_code',
      'supply_classification',
      'supply_date',
      'supply_flight_no',
      'supply_quantity',
      'product_name',
      'payment_classification',
    ]);

    // Fetch supplier code based on material number
    $supplierCode = ProductNumber::where('part_number', $inputData['product_code'])->value('supplier_code');
    $kanbanMaster = KanbanMaster::where('part_number', $inputData['product_code'])->first();
    
    // Prepare structured session data
    $sessionData = [
      'id' => uniqid(),
      'management_no' => $kanbanMaster?->management_no ?? '',
      'product_code' => $inputData['product_code'],
      'supply_classification' => $inputData['supply_classification'],
      'supplier_process_code' => $supplierCode,
      'supply_date' => $inputData['supply_date'],
      'supply_quantity' => $inputData['supply_quantity'],
      'payment_classification' => $inputData['payment_classification'],
      'supply_flight_no' => $inputData['supply_flight_no'],
      'product_name' => $inputData['product_name'],
      'creator' => $request->user()->employee_code,
      'created_at' => now()->format('Y-m-d H:i:s'),
    ];

    return $sessionData;
  }

  public function update($request, $id)
  {
    // Retrieve session data and convert it into a collection for easier manipulation
    $getSessionData = collect(session('sessionSupplyReplenishmentData', []));

    // Iterate over each session data entry and update the matching record
    $sessionData = $getSessionData->map(function ($data) use ($request, $id) {
      // Check if the current session data entry matches the provided temp_data_id
      if ($data['id'] === $id) {
        // Fetch the updated management_no based on the new product_code
        $kanbanMaster = KanbanMaster::where('part_number', $request->input('product_code'))->first();
        
        // Update only the specified fields while keeping other data unchanged
        return array_replace($data, $request->only([
          'product_code',
          'product_name',
          'supply_date',
          'supply_flight_no',
          'supply_quantity',
          'payment_classification'
        ]), [
          'management_no' => $kanbanMaster?->management_no ?? '' // Update management_no
        ]);
      }
      // Return the data unchanged if no match is found
      return $data;
    })->toArray(); // Convert the collection back to an array for session storage

    return $sessionData;
  }


  /**
   * Helper: Clear session if user navigated to a new page.
   */
  public function clearSessionIfNewPage(Request $request)
  {
    if (URL::previous() !== $request->fullUrl()) {
      session()->forget('sessionSupplyReplenishmentData');
    }
  }

  /**
   * Helper: Get latest supplier from session data.
   */
  public function getLatestSupplier(array $supplyReplenishment)
  {
    $latestSupplierCode = collect($supplyReplenishment)
      ->pluck('supplier_process_code')
      ->filter()
      ->last();

    return $latestSupplierCode
      ? Customer::select('customer_code', 'customer_name', 'supplier_name_abbreviation')
        ->where('customer_code', $latestSupplierCode)
        ->first()
      : null;
  }
}

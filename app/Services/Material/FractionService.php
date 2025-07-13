<?php

namespace App\Services\Material;

use App\Mail\Purchase\PurchaseApproverNotification;
use App\Models\Employee;
use App\Models\ProductNumber;
use App\Models\Material\SupplyOrder;
use App\Models\Customer;

use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class FractionService
{

  public function __construct()
  {
    $this->SupplyOrder = new SupplyOrder();
  }

  public function store($request)
  {
    // Retrieve session data
    $sessionData = session('sessionSupplyInstructionData', []);

    // Generate a starting order number
    $baseOrderNo = $this->SupplyOrder->generateSupplyMaterialOrderNo();

    // Prepare data for bulk insertion
    $insertData = collect($sessionData)->map(fn($data, $index) => [
      'supply_material_order_no' => $baseOrderNo + $index,  // Assign unique supply_material_order_no
      'instruction_date' => Carbon::parse($data['instruction_date'])->format('Y-m-d'),
    ] + array_diff_key($data, array_flip(['id', 'product_name'])) )->toArray(); // Merge existing $data array into new array
    
    // Bulk insert into the database
    return $this->SupplyOrder->insert($insertData);
  }
  public function store_session($request)
  {
    // Extract input data
    $inputData = $request->only([
      'material_number',
      'order_classification',
      'instruction_date',
      'instruction_no',
      'instruction_kanban_quantity',
      'product_name',
      // 'supplier_code_request'
    ]);

    // Fetch supplier code based on material number
    $supplierCode = ProductNumber::where('part_number', $inputData['material_number'])->value('supplier_code');
    // Prepare structured session data
    $sessionData = [
      'id' => uniqid(),
      'material_number' => $inputData['material_number'],
      'order_classification' => $inputData['order_classification'],
      'supplier_code' => $supplierCode,
      'instruction_date' => $inputData['instruction_date'],
      'instruction_no' => $inputData['instruction_no'],
      'instruction_kanban_quantity' => $inputData['instruction_kanban_quantity'],
      'instruction_number' => $inputData['instruction_no'], // Avoid duplicate key names
      'product_name' => $inputData['product_name'],
      // 'supplier_code_request' => $inputData['supplier_code_request'],
      'creator' => $request->user()->id,
    ];

    return $sessionData;
  }

  public function update($request, $id)
  {
    // Retrieve session data and convert it into a collection for easier manipulation
    $getSessionData = collect(session('sessionSupplyInstructionData', []));

    // Iterate over each session data entry and update the matching record
    $sessionData = $getSessionData->map(function ($data) use ($request, $id) {
      // Check if the current session data entry matches the provided temp_data_id
      if ($data['id'] === $id) {
        // Update only the specified fields while keeping other data unchanged
        return array_replace($data, $request->only([
          'material_number',
          'product_name',
          'instruction_date',
          'instruction_no',
          'instruction_kanban_quantity'
        ]));
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
      session()->forget('sessionSupplyInstructionData');
    }
  }

  /**
   * Helper: Get latest supplier from session data.
   */
  public function getLatestSupplier(array $SupplyOrders)
  {
    $latestSupplierCode = collect($SupplyOrders)
      ->pluck('supplier_code')
      ->filter()
      ->last();

    return $latestSupplierCode
      ? Customer::select('customer_code', 'customer_name', 'supplier_name_abbreviation')
        ->where('customer_code', $latestSupplierCode)
        ->first()
      : null;
  }
}

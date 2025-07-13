<?php

namespace App\Services\Outsource\Defect;

use App\Mail\Purchase\PurchaseApproverNotification;
use App\Models\KanbanMaster;
use App\Models\OutsourceProcessFailure;
use App\Models\ProductPrice;
use App\Models\Process;
use App\Models\Outsource\SubcontractSupply;

use Carbon\Carbon;
use App\Constants\Constant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Log;

class ProcessService
{

  public function __construct()
  {
    $this->outsourceProcessFailure = new OutsourceProcessFailure();
  }

  public function store($request)
  {
    // Retrieve session data from the validated request
    $sessionData = session('sessionDefectProcessData', []); // Ensure 'session_data' exists

    if (empty($sessionData)) {
      return response()->json(['error' => 'No session data provided'], 400);
    }
   
    // Generate a starting order number
    $registrationNo = $this->outsourceProcessFailure->generateRegistrationNo();

    // Prepare data for bulk insertion
    $insertData = collect($sessionData)->map(function ($data, $index) use ($registrationNo) {
        $index = $index + 1; // Increment index for each item
        return [
          'registration_no' => $registrationNo + $index, 
          'part_number' => $data['product_code'],  
          'serial_number' => 1,  
          'disposal_date' => Carbon::parse($data['disposal_date'])->format('Y-m-d'),
        ] + array_diff_key($data, array_flip(['id', 'product_code','product_name', 'process_name', 'processing_unit_price', 'subTotal'])); // Exclude 'id' & 'product_name'
    })->toArray();

    // Bulk insert into the database
    return $this->outsourceProcessFailure->insert($insertData);
  }
  public function store_session($request)
  {
   
    // Extract input data
    $inputData = $request->only([
      'process_code',
      'process_name',
      'product_code',
      'product_name',
      'disposal_date',
      'quantity',
      'slip_no',
      'processing_unit_price',
      'subTotal',
    ]);

    // Fetch supplier code based on material number
    // $supplierCode = ProductNumber::where('part_number', $inputData['product_code'])->value('supplier_code');
    
    // Prepare structured session data
    $sessionData = [
      'id' => uniqid(),
      'process_code' => $inputData['process_code'],
      'process_name' => $inputData['process_name'],
      'product_code' => $inputData['product_code'],
      'product_name' => $inputData['product_name'],
      'disposal_date' => $inputData['disposal_date'],
      'quantity' => $inputData['quantity'],
      'slip_no' => $inputData['slip_no'],
      'processing_unit_price' => $inputData['processing_unit_price'],
      'subTotal' => $inputData['subTotal'],
      'creator' => $request->user()->id,  // type: bigint
      'created_at' => now()->format('Y-m-d H:i:s'),
    ];

    return $sessionData;
  }

  public function update_session($request, $id)
  {
    // Retrieve session data and convert it into a collection for easier manipulation
    $getSessionData = collect(session('sessionDefectProcessData', []));

    // Iterate over each session data entry and update the matching record
    $sessionData = $getSessionData->map(function ($data) use ($request, $id) {
      // Check if the current session data entry matches the provided temp_data_id
      if ($data['id'] === $id) {
        // Fetch the updated management_no based on the new product_code
        $productPrice = ProductPrice::where('part_number', $request->input('product_code'))->orderByDesc('id')->first();
        
        // Update only the specified fields while keeping other data unchanged
        return array_replace($data, $request->only([
          'product_code',
          'product_name',
          'disposal_date',
          'quantity',
          'slip_no',
          'subTotal',
        ]), [
          'processing_unit_price' => $productPrice?->unit_price ?? 0
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
      session()->forget('sessionDefectProcessData');
    }
  }

  /**
   * Helper: Get latest supplier from session data.
   */
  public function getLatestSupplier(array $sessionDefectProcessData)
  {
    $latestSupplierCode = collect($sessionDefectProcessData)
      ->pluck('process_code')
      ->filter()
      ->last();

    return $latestSupplierCode
      ? Process::select('process_code', 'process_name', 'abbreviation_process_name')
        ->where('process_code', $latestSupplierCode)
        ->first()
      : null;
  }

  public function getProductUnitPrice($productCode)
  {
    $productPrice = ProductPrice::where('part_number', $productCode)->orderByDesc('id')->first();
    return $productPrice?->unit_price?? 0;
  }
}

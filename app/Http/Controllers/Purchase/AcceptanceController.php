<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\PurchaseArrival;
use App\Models\PurchaseRequisition;
use App\Models\Code;
use App\Models\Customer;
use App\Models\PurchaseRecord;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\Purchase\AcceptanceService;
use App\Http\Requests\Purchase\AcceptanceRequest;

class AcceptanceController extends Controller
{
  protected $acceptanceService;

  public function __construct(AcceptanceService $acceptanceService)
  {
    $this->acceptanceService = $acceptanceService;
  }

  public function edit(Request $request, $id)
  {
    // Retrieve order details from the request
    $orderNumber = $request->order_number;
    $orderDetailNumber = $request->order_details_number;

    // Fetch the corresponding purchase requisition
    $requisition = PurchaseRequisition::where('purchase_order_number', $orderNumber)
      ->where('purchase_order_details_number', $orderDetailNumber)
      ->with('employee')
      ->first();

    // Get all purchase arrivals related to the order
    $items = PurchaseArrival::where('purchase_order_no', $orderNumber)
      ->where('purchase_order_details_no', $orderDetailNumber)
      ->get();

    // Retrieve unit codes from the Code model where division is '単位' (units)
    $units = Code::selectRaw('division, code, name')
      ->where('division', '単位')
      ->get();

    // Fetch customer name based on the requisition's `where_used_code`, if available
    $requisition->customer = Customer::where('customer_code', $requisition->where_used_code)
      ->value('customer_name') ?? '';

    // Load the edit view with the required data
    return view('pages.purchases.acceptance.edit', compact('items', 'requisition', 'units'));
  }

  //Purchase 80 - update requisition
  public function update(AcceptanceRequest $request, $id)
  {
    // Begin a database transaction to ensure data integrity
    DB::beginTransaction();
    try {
      $purchase = PurchaseRequisition::findOrFail($id);

      $validatedData = $request->validated();

      $purchase->update($validatedData);

      // Commit the transaction if no errors occur
      DB::commit();
      return response()->json([
          'status' => 'success',
          'message' => '発注データ情報が更新されました',
          'purchase' => $purchase,
      ], 201);
    } catch (\Exception $e) {
      DB::rollBack();

      // Log the error with request details for debugging
      Log::error('Error occurred while updating the data.', [
        'error' => $e->getMessage(),
        'validated_data' => $validatedData ?? [],
        'timestamp' => now(),
        'exception_trace' => $e->getTraceAsString(),
    ]);

      return response()->json([
          'status' => 'error',
          'message' => $e->getMessage(),
      ], 500);
    }
  }

  //Purchase 80 store
  public function store(Request $request)
  {
    // Begin database transaction to ensure atomicity
    DB::beginTransaction();
    try {
      // Delegate the store logic to the AcceptanceService class
      $this->acceptanceService->store($request);

      // Commit the transaction if no errors occur
      DB::commit();
      return back()->with('success', '入荷・受入情報が更新されました');
    } catch (\Exception $e) {
      // Rollback the transaction in case of an error
      DB::rollBack();

      // Log the error with request details for debugging
      Log::error('Error occurred while updating the data.', [
        'error' => $e->getMessage(),
        'request_data' => $request->all(),
        'timestamp' => now(),
      ]);

      // Redirect back with an error message
      return back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
    }
  }

  //Purchase 80 delete
  public function destroy(Request $request, $id)
  {
    DB::beginTransaction();
    try {
      // Find the record
      $acceptance = PurchaseArrival::findOrFail($id);
      // Delete the record
      $acceptance->delete();

      PurchaseRecord::where('purchase_record_no', $acceptance['purchase_record_no'])->first()->delete();

      DB::commit();
      // Return a success response
      return response()->json(['message' => '削除が完了しました'], 200);
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error occurred while storing the data.', [
        'error' => $e->getMessage(),
        'request_data' => $request->all(),
        'timestamp' => now(),
      ]);
      return response()->json(['error' => '削除に失敗しました: ' . $e->getMessage()], 500);
    }
  }
}

<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\PurchaseArrival;
use App\Models\PurchaseRequisition;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ReceiptController extends Controller
{
  /**
   * Handles the input for purchase arrival.
   *
   * This method retrieves the purchase requisition and purchase arrival records
   * based on the provided order number and order details number from the request.
   * It then returns the 'purchase_arrival_input' view with the retrieved data.
   *
   * @param \Illuminate\Http\Request $request The incoming request instance containing order number and order details number.
   * @return \Illuminate\View\View The view displaying the purchase arrival input form with the requisition and items data.
   */
  public function edit(Request $request)
  {
    $orderNumber = $request->order_number;
    $orderDetailNumber = $request->order_details_number;

    $requisition = PurchaseRequisition::getRecordByOrderNumberOrderDetailsNumber($orderNumber, $orderDetailNumber);
    $items = PurchaseArrival::getRecordByOrderNumberOrderDetailsNumber($orderNumber, $orderDetailNumber);

    return view('pages.purchases.receipt.edit', compact('requisition', 'items'));
  }

  public function store(Request $request)
  {
    DB::beginTransaction();
    try {
      $orderNumber = $request->purchase_order_no;
      $orderDetailNumber = $request->purchase_order_details_no;
      $arrivalDay = $request->input('arrival_day');

      // Fetch latest record with required fields
      $latest = PurchaseArrival::where('purchase_order_no', $orderNumber)
        ->where('purchase_order_details_no', $orderDetailNumber)
        ->latest('created_at')
        ->first();

      // Determine new branch number and record number
      $branchNo = optional($latest)->branch_number + 1 ?? 1;
      $lastSixDigits = optional($latest)->purchase_record_no
        ? intval(substr($latest->purchase_record_no, -6)) + 1
        : 1;

      $newPurchaseRecordNo = sprintf('%04d%06d', now()->format('ym'), $lastSixDigits);

      // Create a new record
      PurchaseArrival::createRecord($request->all(), $branchNo, $arrivalDay, $newPurchaseRecordNo);

      DB::commit();
      return back()->with('success', 'データは正常に登録されました');

    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error occurred while storing the data.', [
        'error' => $e->getMessage(),
        'request_data' => $request->all(),
        'timestamp' => now(),
      ]);

      return back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
    }
  }

  public function destroy($id)
  {
    DB::beginTransaction();
    try {
      PurchaseArrival::destroy($id);
      DB::commit();
      return back()->with('delete', 'データは正常に削除されました');
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error occurred while deleting the data.', [
        'error' => $e->getMessage(),
        'timestamp' => now(),
      ]);

      return back()->with('error', 'エラーが発生しました: ' . $e->getMessage());
    }
  }
}

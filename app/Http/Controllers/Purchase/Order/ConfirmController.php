<?php

namespace App\Http\Controllers\Purchase\Order;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequisition;

use App\Exports\Purchase\Order\ConfirmExport;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Helpers\RequestHelper;
use Maatwebsite\Excel\Facades\Excel;

class ConfirmController extends Controller
{

  public function __construct()
  {
    
  }

  public function index(Request $request)
  {
    $selectedItems = $request->selected_items;

    if ($selectedItems) {
      $items = PurchaseRequisition::whereIn('id', $selectedItems)->get();
      $firstSupplierData = PurchaseRequisition::whereIn('id', $selectedItems)
        ->first();
    } else {
      $items = null;
      $firstSupplierData = null;
    }
    return view('pages.purchases.order.confirm.index', compact(
      'items',
      'firstSupplierData',
    ));
  }

  public function store(Request $request)
  {
    // Start a database transaction
    DB::beginTransaction();

    try {
      $selectedItems = $request->selected_items;

      foreach ($selectedItems as $itemId) {
        $item = PurchaseRequisition::find($itemId);

        if ($item) {
          $item->update([
            'state_classification' => 3,
            'purchase_order_number' => now()->format('ymd') . 'AA',
            'purchase_order_details_number' => 1,
            'order_date' => now(),
          ]);
        }
      }
      // Commit the database transaction to save all changes
      DB::commit();
      return redirect()->route('purchase.orderProcess.index', $request->query())->with('success', '発注登録が完了しました');

    } catch (\Exception $e) {
      // Rollback the transaction if an error occurs to prevent partial updates
      DB::rollBack();

      // Log the error details for debugging and tracking issues
      Log::error('Error occurred.', [
        'error' => $e->getMessage(),
        'request_data' => $request->all(),
        'timestamp' => now(),
      ]);

      // Redirect back with an error message to inform the user
      return redirect()->back()->with('error', 'Error occurred.');
    }
  }

  public function excel_export(Request $request)
  {
    $selectedItems = $request->selected_items;
    $items = PurchaseRequisition::whereIn('id', $selectedItems)->get();

    $fileName = '発注内容確認_'.now()->format('Ymd').'.xlsx';
    return Excel::download(new ConfirmExport($items), $fileName , \Maatwebsite\Excel\Excel::XLSX);
  }

}

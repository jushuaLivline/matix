<?php

namespace App\Http\Controllers\Purchase\Order;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequisition;
use App\Services\Purchase\PurchaseOrderFormService;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Helpers\RequestHelper;
use Maatwebsite\Excel\Facades\Excel;

class ReissueController extends Controller
{

  public function __construct(PurchaseOrderFormService $purchaseOrderFormService)
  {
    $this->purchaseOrderFormService = $purchaseOrderFormService;
  }

  public function index(Request $request)
  {
    $items = PurchaseRequisition::getAllOrderFormReissue($request);

    return view('pages.purchases.order.reissue.index', compact('items'));
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

  public function excel_export(Request $request, $id)
  {
      // Set the default type to xlsx if the GET parameter 'type' does not exist
      $type = $request->query('type', 'xlsx');
      
      // Check if the type is supported
      if (!in_array($type, ['pdf', 'xlsx'], true)) {
          throw new \InvalidArgumentException('Invalid export type. Supported types are: pdf, xlsx');
      }
  
      $item = PurchaseRequisition::getRecordWithSupplier($id);
      
      $fileName = '注文書再発行_' . 
        $request->query('purchase_order_number', '') . '_' . 
        now()->format('Ymd') . '.' . 
        $type;
  
      $purchaseOrderNumberItems = PurchaseRequisition::getAllRecordsForExport($item->purchase_order_number);
  
      $purchaseOrderDetails = [
          'purchaseOrderItemDetails' => $purchaseOrderNumberItems,
          'purchaseOrderItem' => $item,
          'exportType' => $type
      ];
      
      return $this->purchaseOrderFormService->downloadOrderReissue(
          $type,
          $purchaseOrderDetails, 
          $fileName
      );
  }

}

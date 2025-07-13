<?php

namespace App\Http\Controllers\Purchase\Order\Process;

use App\Http\Controllers\Controller;
use App\Models\PurchaseArrival;
use App\Models\PurchaseRequisition;
use App\Models\Code;
use App\Models\PurchaseApproval;
use App\Models\PurchaseApprovalHistory;

use App\Services\Purchase\Order\ProcessService;
use App\Http\Requests\Purchase\Order\Process\DetailRequest;
use App\Services\Purchase\PurchaseApproverEmailNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Helpers\RequestHelper;

class DetailController extends Controller
{
  protected $processService, $EmailNotification, $purchaseApprovalHistory;

  public function __construct(ProcessService $processService)
  {
    $this->processService = $processService;
    $this->purchaseApprovalHistory = new PurchaseApprovalHistory();
    $this->EmailNotification = new PurchaseApproverEmailNotification();
  }

  public function show(Request $request, $id)
  {
    RequestHelper::processRequest($request);
    $item = PurchaseRequisition::findOrFail($id);

    $units = Code::selectRaw('division,code,name')
      ->whereDivision('単位')
      ->get();
    $approvals = PurchaseApproval::where('purchase_record_no', $item->requisition_number)->get();
    $requestData = $request->all();

    return view('pages.purchases.order.process.detail.index', compact('units', 'item', 'approvals', 'requestData'));
  }

  public function update(DetailRequest $request, $id)
  {
    // Start a database transaction
    DB::beginTransaction();

    try {
      $requisitionNumber = data_get($request->validated(), 'requisition_number');
      $stateClassification = data_get($request->validated(), 'state_classification');

      $purchaseRequisition = PurchaseRequisition::where("requisition_number", $requisitionNumber)->first();
      
      //$purchaseRequisition->update(array_replace($request->validated(), ["state_classification" => (int) $request->state_classification[0]]));
      $purchaseRequisition->update(array_replace($request->validated(), [
        'state_classification' => $stateClassification
      ]));

      DB::commit();

      // create a new approval history entry
      $this->purchaseApprovalHistory->createRecord($request);

      DB::commit();

      return response()->json([
        'status' => 'success',
        'message' => '購入依頼情報が正常に更新されました',
    ], 201);

    } catch (\Exception $e) {
      // Rollback the transaction if an error occurs to prevent partial updates
      DB::rollBack();

      // Log the error details for debugging and tracking issues
      Log::error('Error occurred while approving the order.', [
          'error' => $e->getMessage(),
          'request_data' => $request->all(),
          'timestamp' => now(),
      ]);

      return response()->json([
        'status' => 'error',
        'message' => $e->getMessage(),
      ], 500);
    }
  }

  public function reject(DetailRequest $request, $id)
  {
    // Start a database transaction
    DB::beginTransaction();
    try {
      $purchaseRequisition = PurchaseRequisition::findOrFail($id);

      if (!$purchaseRequisition) {
        // Redirect back with an error message to inform the user
        return redirect()->back()->with('error', '購入依頼の差し戻し中にエラーが発生しました');
      }
      // update data from PurchaseRequisition
      $purchaseRequisition->update($request->validated());

      // get all the record from PurchaseRequisition table
      // update the following fields reason_for_denial, state_classification
      $getPurchaseRequisitionData = $purchaseRequisition->toArray();
      $getPurchaseRequisitionData['reason_for_denial'] = $request->reason_for_denial;

      // Inserted a new recordd to PurchaseApprovalHistory table
      $this->purchaseApprovalHistory->createRecord($getPurchaseRequisitionData, true);

      // Send email form the next_approver field
      if ($purchaseRequisition->next_approver) {
        $this->EmailNotification->rejectPurchaseNotification($request, $purchaseRequisition, true);
      }
      // Commit the database transaction to save all changes
      DB::commit();

      return redirect()->route('purchase.orderProcess.index', $request->query())
        ->with('success', '購買依頼の差し戻しが完了しました');

    } catch (\Exception $e) {
      // Rollback the transaction if an error occurs to prevent partial updates
      DB::rollBack();

      // Log the error details for debugging and tracking issues
      Log::error('Error occurred while rejecting the order.', [
        'error' => $e->getMessage(),
        'request_data' => $request->all(),
        'timestamp' => now(),
      ]);

      // Redirect back with an error message to inform the user
      return redirect()->back()->with('error', '購買依頼の差し戻し中にエラーが発生しました');
    }
  }

  public function destroy(Request $request, $id)
  {
    // Start a database transaction
    DB::beginTransaction();
    try {
      $purchaseRequisition = PurchaseRequisition::findOrFail($id);
      
      if($purchaseRequisition) {
        $purchaseRequisition->delete();
        PurchaseApprovalHistory::where("purchase_requisition_id", $purchaseRequisition->requisition_number)->delete();
      }

      // Commit the database transaction to save all changes
      DB::commit();
      return redirect()->route('purchase.orderProcess.index', $request->query())->with('success', '購買依頼の削除が完了しました');

    } catch (\Exception $e) {
      // Rollback the transaction if an error occurs to prevent partial updates
      DB::rollBack();

      // Log the error details for debugging and tracking issues
      Log::error('Error occurred while deleting the order.', [
          'error' => $e->getMessage(),
          'timestamp' => now(),
      ]);

      // Redirect back with an error message to inform the user
      return redirect()->back()->with('error', '購買依頼削除中にエラーが発生いたしました');
    }
  }
}

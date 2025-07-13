<?php

namespace App\Http\Controllers\Purchase\Approval;

use App\Http\Controllers\Controller;
use App\Services\Purchase\PurchaseRequisitionService;
use App\Http\Requests\PurchaseRequisition\RequisitionApprovalDetailsRequest;
use App\Services\Purchase\PurchaseApproverEmailNotification;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseApprovalHistory;
use App\Models\PurchaseApproval;
use App\Models\Employee;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
class DetailController extends Controller
{
  protected $purchaseRequisitionService, $purchaseApprovalHistory, $EmailNotification;
  public function __construct(PurchaseRequisitionService $purchaseRequisitionService)
  {
    $this->purchaseRequisitionService = $purchaseRequisitionService;
    $this->purchaseApprovalHistory = new PurchaseApprovalHistory();
    $this->EmailNotification = new PurchaseApproverEmailNotification();
  }

  public function showRequisitionApprovalDetails(Request $request, $id)
  {
    // Retrieve the requisition data or abort if not found
    $purchaseRecord = PurchaseRequisition::where('requisition_number', $id)->firstOrFail();
    $last_approver = $purchaseRecord->lastApprover();
    $request = $request->all();

    return view('pages.purchase.approval.detail.index', compact(
      'purchaseRecord',
      'last_approver',
      'request'
    ));
  }

  public function addApprovalUser(RequisitionApprovalDetailsRequest $request)
  {
    // Start a transaction
    DB::beginTransaction();
    try {
      // approve first the current approval
      $purchaseRequisition = PurchaseRequisition::where("requisition_number", $request['requisition_number'])->first();

      if ($purchaseRequisition) {
        // return the last inserted data from mst_purchase_approvals.approver_employee_code
        $lastApprover = $purchaseRequisition->lastApprover();

        if ($lastApprover) {
          // update first the current last approver's approval date as now()
          $lastApprover->update(['approval_date' => now(), 'denial_date' => null]);
          // Updat the next approver
          $purchaseRequisition->update(['next_approver' => $request['approver_employee_code']]);

          // create the additional approver for the purchase requisition
          PurchaseApproval::create($request->validated());
        }
      }
      // Commit the transaction
      DB::commit();
      return redirect()->back();

    } catch (\Exception $e) {
      // Rollback the transaction if something went wrong
      DB::rollBack();

      // Log the error with detailed information
      Log::error('Error occurred while adding new approver.', [
        'error' => $e->getMessage(),
        'request_data' => $request->all(),
        'timestamp' => now(),
      ]);

      // Handle the error, log it or display a custom error message
      return redirect()->back()->with('error', 'Error occurred while adding new approver.');
    }
  }

  public function removeApprovalUser(Request $request)
  {
    // Start a transaction
    DB::beginTransaction();
    try {
      if ($request->filled('approval_ids') && is_array($request->approval_ids)) {
        PurchaseApproval::whereIn('id', $request->approval_ids)->delete();
      }

      // Commit the transaction
      DB::commit();
      return redirect()->back()->with('success', '承認者の除外が完了いたしました');

    } catch (\Exception $e) {
      // Rollback the transaction if something went wrong
      DB::rollBack();

      // Log the error with detailed information
      Log::error('Error occurred while removing an approver.', [
        'error' => $e->getMessage(),
        'request_data' => $request->all(),
        'timestamp' => now(),
      ]);

      // Handle the error, log it or display a custom error message
      return redirect()->back()->with('error', 'Error occurred while removing an approver.');
    }
  }

  public function purchaseRequisitionApprove(Request $request, $requisitionNumber)
  {
    try {
      
      // Get the current is_checked values from the query string
      $is_checked = $request->query('is_checked') 
          ? explode(',', $request->query('is_checked')) 
          : [];

      // Get the validated requisition_number
      $requisition_number = $requisitionNumber;

      // Add requisition_number if it's not already in the array
      if (!in_array($requisition_number, $is_checked)) {
          $is_checked[] = $requisition_number;
      }

      // Update the request with the new is_checked values
      $request->merge(['is_checked' => implode(',', $is_checked)]);

      // Redirect back to the previous page with a success message
      return redirect()
          ->route('purchase.approval.list.index', $request->except('_token'))
          ->with('success', '対象の購買依頼にチェックを付与しました.');
      
    } catch (Exception $e) {
      // Log the error for debugging
      Log::error('Error occurred while approving purchase requisition.', [
          'error' => $e->getMessage(),
          'request_data' => $request->all(),
          'timestamp' => now(),
      ]);

      // Redirect back with an error message
      return redirect()->back()->with('error', 'Error occurred while approving the requisition.');
    }
  }

  public function purchaseRequisitionDenied(RequisitionApprovalDetailsRequest $request, $requisitionNumber)
  {
    // Start a database transaction
    DB::beginTransaction();

    try {
      // Fetch the purchase requisition based on the requisition number
      $purchaseRequisition = PurchaseRequisition::where('requisition_number', $requisitionNumber)
        ->firstOrFail(); // Ensure requisition is found, or 404 if not

      // Check if the current user is the next approver
      if ($purchaseRequisition->next_approver == $request->user()->employee_code) {

        // Fetch the corresponding approval record for the user
        $approval = PurchaseApproval::where([
          'purchase_record_no' => $requisitionNumber,
          'approver_employee_code' => $request->user()->employee_code,
        ])->first();

        // If the approval exists, update it with the validated request data
        if ($approval) {
          // Update approval record | denial_date
          $approval->update($request->validated());

          // Assign the next approver
          $employee = Employee::find($request->user()->id);
          $purchaseRequisition->assignNextApprover($employee);

          // Update requisition status or other details based on the request
          $purchaseRequisition->update($request->validated());
        }
        // Commit the transaction if everything succeeds
        DB::commit();
        
        // Notify the creator of the purchase requisition
        $this->EmailNotification->rejectPurchaseNotification($request, $purchaseRequisition);
      }
      
      // Redirect back with a success message
      return redirect()->back()->with('success', '要求は拒否され、次の承認者が正常に割り当てられました。');

    } catch (Exception $e) {
      // Rollback the transaction if any error occurs
      DB::rollBack();

      // Log the error details for debugging and tracking
      Log::error('Error occurred while denying a purchase requisition approval.', [
        'error' => $e->getMessage(),
        'request_data' => $request->all(),
        'timestamp' => now(),
      ]);

      // Redirect back with an error message
      return redirect()->back()->with('error', 'Error occurred while denying the requisition approval. Please try again.');
    }
  }
}
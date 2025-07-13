<?php

namespace App\Services\Purchase\Approval;

use Request;
use App\Models\Employee;
use App\Models\PurchaseApproval;
use Illuminate\Support\Facades\DB;
use App\Models\PurchaseRequisition;
use Illuminate\Support\Facades\Log;
use App\Services\Purchase\PurchaseApproverEmailNotification;

class ListService
{
    protected $emailNotification;

    public function __construct(PurchaseApproverEmailNotification $emailNotification) 
    {
        $this->emailNotification = $emailNotification;
    }

    public function purchaseRequisitionApprovalProcess($request)
    {
        DB::beginTransaction();
        try {
        // Get the current employee
        $employee = Employee::where('employee_code', $request->user()->employee_code)->first();

        // Check if the  requisition number is provided
        if (empty($request->requisitionNumbers)) {
            return back()->with('error', 'リクエスト番号が選択されていません。');
        }

        foreach ($request->requisitionNumbers ?? [] as $requisitionNumber) {
            if (!$employee) {
                continue;
            }

            $purchaseRequisition = PurchaseRequisition::where("requisition_number", $requisitionNumber)->first();
            $approval = PurchaseApproval::where('purchase_record_no', $requisitionNumber)
                ->where('approver_employee_code', $employee->employee_code)
                ->first();
            
            if ($approval && $purchaseRequisition) {
            $purchaseRequisition->processApproval($approval, $employee, $request->approval_type);
            $this->emailNotification->purchaseNotification($request, $purchaseRequisition);
            }
            $message = $request->approval_type == "unapprove" ? "承認取消処理が完了しました" : "該当の購買依頼を承認いたしました";
        }

        DB::commit();
        // Message based on approval type
        $message = $request->approval_type == "unapprove" ? "承認取消処理が完了しました。" : "対象の購買依頼にチェックを付与しました。";
        return [
            'status' => 'success',
            'message' => $message,
        ];
        
        } catch (\Exception $exception) {
        DB::rollBack();
        Log::info($exception->getMessage());

        return [
            'status' => 'error',
            'message' => $exception->getMessage(),
        ];
        }
    }
}
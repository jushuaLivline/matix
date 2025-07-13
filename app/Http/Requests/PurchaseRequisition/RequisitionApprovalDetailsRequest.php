<?php
namespace App\Http\Requests\PurchaseRequisition;
use Illuminate\Foundation\Http\FormRequest;
class RequisitionApprovalDetailsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules()
    {
        $action = $this->route()->getName(); // Get the current route name

        switch ($action) {
            case 'purchase.detail.purchaseAddApprovalUser':
                return $this->addApproverRule();
            case 'purchase.detail.purchaseRequisitionDenied':
                return $this->deniedRequestRule();
            case 'purchase.detail.purchaseRequisitionApprove':
                return $this->approveRequestRule();
            default:
                return [];
        }
    }

    /**
     * Rules for creating a new approver.
     */
    private function addApproverRule()
    {
        return [
            'requisition_number' => 'required',
            'purchase_record_no' => 'required',
            'order_of_approval' => 'required',
            'approver_employee_code' => 'required'
        ];
    }

    /**
     * Rules for denied purchase requisition request.
     */
    private function deniedRequestRule()
    {
        return [
            'reason_for_denial' => 'required',
            'state_classification' => 'required',
            'purchase_record_no' => 'nullable',
            'approver_employee_code' => 'nullable',
            'approval_date' => 'nullable',
            'denial_date' => 'required'
        ];
    }

    /**
     * Rules for approving a requisition.
     */
    private function approveRequestRule()
    {
        return [
            'denial_date' => 'nullable',
            'reason_for_denial' => 'nullable',
            'approval_date' => 'required',
            'state_classification' => 'nullable'
        ];
    }

    /**
     * Custom error messages for validation.
     */
    public function messages()
    {
        return [
            'requisition_number.required' => '請求番号は必須です',
            'purchase_record_no.required' => '購入記録番号は必須です',
            'order_of_approval.required' => '承認の順序は必須です',
            'approver_employee_code.required' => '従業員コードは必須です',
            
            'reason_for_denial.required' => '否認理由は必須です',
            'state_classification.required' => '州の分類は必須です',
            'denial_date.required' => '拒否日は必須です',

            'approval_date.required' => '承認日は必須です',
        ];
    }
}
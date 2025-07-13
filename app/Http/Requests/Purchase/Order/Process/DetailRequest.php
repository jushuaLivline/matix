<?php

namespace App\Http\Requests\Purchase\Order\Process;

use Illuminate\Foundation\Http\FormRequest;

class DetailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
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
            case 'purchase.purchaseAddApprovalUser':
                return $this->addApproverRule();
            case 'purchase.purchaseRequisitionDenied':
                return $this->deniedRequestRule();
            case 'purchase.purchaseRequisitionApprove':
                return $this->approveRequestRule();
            case 'purchase.orderProcessDetail.update':
                return $this->updatRule();
            case 'purchase.orderProcessDetail.reject':  //
                return $this->deniedDetailOrderRule();
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
     * Rules for creating a new approver.
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
     * Rules for approving order.
     */
    private function updatRule()
    {
        return [
            'requisition_number' => 'required',
            'requested_date' => 'nullable',
            'supplier_code' => 'nullable|string|exists:customers,customer_code',
            'department_code' => 'required|string|exists:departments,code',
            'creator' => 'nullable|string',
            'line_code' => 'nullable|string|exists:lines,line_code',
            'part_number' => 'required|nullable',
            'part_name' => 'nullable',
            'standard' => 'nullable|max:50',
            'reason' => 'nullable',
            'quantity' => 'required|numeric',
            'unit_code' => 'nullable',
            'unit_price' => 'required|numeric',
            'amount_of_money' => 'nullable|numeric',
            'expense_items' => 'nullable|string',
            'expense_items' => 'nullable|exists:items,expense_item',
            'deadline' => 'nullable',
            'approval_method_category' => 'nullable',
            'remarks' => 'nullable',
            'state_classification' => 'nullable',
            'purchase_order_number' => 'nullable',
            'purchase_order_details_number' => 'nullable',
            'order_date' => 'nullable',
            'updated_at' => 'nullable',
            'machine_number' => 'nullable',
            'reason_for_denial' => 'nullable',
            'product_name' => 'required',
            'quotation_existence_flag' => 'nullable',
        ];
    }
    /**
    * Rules for creating a new approver.
    */
   private function deniedDetailOrderRule()
   {
        return [
           'remarks' => 'nullable|string',
           'reason_for_denial' => 'nullable|string',
           'state_classification' => 'nullable'
       ];
   }

   public function messages()
    {
        return [
            // purchase.purchaseAddApprovalUser
            'requisition_number.required' => '購買依頼No.は必須です',
            'purchase_record_no.required' => '購入履歴番号は必須です',
            'order_of_approval.required' => '承認順序は必須です',
            'approver_employee_code.required' => '承認者は必須です',

            // purchase.purchaseRequisitionDenied
            'reason_for_denial.required' => '差し戻し理由は必須です',
            'state_classification.required' => '州の分類は必須です',
            'denial_date.required' => '未承認は必須です',

            // purchase.purchaseRequisitionApprove
            'approval_date.required' => '承認日は必須です',

            // purchase.orderProcessDetail.update
            'department_code.required' => '部門は必須です',
            'supplier_code.exists' => '発注先が存在しません',
            'department_code.exists' => '部門が存在しません',
            'line_code.exists' => 'ラインが存在しません',
            'expense_items.exists' => '費目が存在しません',

            'part_number.required' => '品番は必須です',
            'quantity.required' => '数量は必須です',
            'unit_price.required' => '単価は必須です',
            'unit_price.numeric' => '単価は数値でなければなりません',
            'amount_of_money.numeric' => '金額は数値でなければなりません',
            'product_name.required' => '品名は必須です',

            'standard.max' => '50文字以内で入力してください'
        ];
    }
}
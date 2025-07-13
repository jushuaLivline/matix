<?php

namespace App\Http\Requests\PurchaseRequisition;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseRequisitionRequest extends FormRequest
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
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'supplier_code' => 'required',
            'department_code' => 'required',
            'line_code' => 'nullable',
            'part_number' => 'required',
            'product_name' => 'nullable',
            'standard' => 'nullable',
            'reason' => 'nullable',
            'quantity' => 'required|numeric',
            'unit_code' => 'required',
            'unit_price' => 'required|numeric',
            'amount_of_money' => 'required|numeric',
            'expense_items' => 'required',
            'requested_date' => 'nullable|date_format:Ymd',
            'deadline' => 'nullable|date_format:Ymd',
            'approval_method_category' => 'required|in:1,2',
            'approval_route_number' => 'required_if:method,1',
            'remarks' => 'nullable',
            'state_classification' => 'nullable',
            'quotation_existence_flag' => 'required|boolean',
            'creator' => 'nullable',
            'updator' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attributeは必須です',
            'required_if' => ':attributeは必須です',
            'numeric' => ':attributeは数値で入力してください',
            'date_format' => '正しい形式で入力してください',
            'in' => ':attributeの値が不正です',
            'boolean' => ':attributeの値が不正です',
        ];
    }

    public function attributes()
    {
        return [
            'supplier_code' => '仕入先',
            'department_code' => '部門',
            'line_code' => 'ライン',
            'part_number' => '品番',
            'product_name' => '品名',
            'standard' => '規格',
            'reason' => '理由',
            'quantity' => '数量',
            'unit_code' => '単位',
            'unit_price' => '単価',
            'amount_of_money' => '金額',
            'expense_items' => '費目',
            'requested_date' => '申請日',
            'deadline' => '納期',
            'approval_method_category' => '承認方法',
            'approval_route_number' => '承認ルート番号',
            'quotation_existence_flag' => '見積書の有無',
            'remarks' => '備考',
            'state_classification' => '状態区分',
            'creator' => '作成者',
            'updator' => '更新者',
        ];
    }
}

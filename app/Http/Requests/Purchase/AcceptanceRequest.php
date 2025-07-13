<?php
namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AcceptanceRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */

     public function rules()
     {
         return [
             'purchase_order_number' => ['required', 'string'],
             'requisition_number' => ['required', 'string'],
             'creator' => ['nullable', 'string', Rule::exists('employees', 'employee_code')],
             'supplier_code' => ['required', 'string', Rule::exists('customers', 'customer_code')],
             'tax_classification' => ['required', 'string'],
             'order_date' => 'nullable|date_format:Ymd',
             'deadline' => 'nullable|date_format:Ymd',
             'department_code' => ['nullable', 'string', Rule::exists('departments', 'code')],
             'line_code' => ['nullable', 'string', Rule::exists('lines', 'line_code')],
             'expense_items' => ['required', 'numeric', Rule::exists('items', 'expense_item')],
             'machine_number' => ['nullable', 'string', Rule::exists('machine_numbers', 'machine_number')],
             'project_number' => ['nullable', 'string', Rule::exists('projects', 'project_number')],
             'part_number' => ['required', 'string', 'max:50'],
             'product_name' => ['nullable', 'string'],
             'standard' => ['nullable', 'string','max:50'],
             'quantity' => ['required', 'integer'],
             'unit_code' => ['nullable', 'string'],
             'unit_price' => ['nullable', 'string'],
             'amount_of_money' => ['nullable', 'string'],
             'where_used_code' => ['nullable', 'string', Rule::exists('customers', 'customer_code')],
             'reason' => ['nullable', 'string'],
             'remarks' => ['nullable', 'string'],
         ];
     }

    public function attributes()
    {
        return [
            'purchase_order_number' => '注文書No.',
            'requisition_number' => '購買依頼No.',
            'creator' => '作成者',
            'supplier_code' => '発注先',
            'tax_classification' => '課税区分',
            'order_date' => '発注日',
            'deadline' => '納期',
            'department_code' => '部門',
            'line_code' => 'ライン',
            'expense_items' => '費目',
            'machine_number' => '機番',
            'project_number' => 'プロジェクトNo.',
            'part_number' => '品番',
            'product_name' => '品名',
            'standard' => '規格',
            'quantity' => '数量',
            'unit_code' => '単位',
            'unit_price' => '単価',
            'where_used_code' => '使用先',
            'reason' => '購入理由',
            'remarks' => '依頼時備考',
            'amount_of_money' => '金額',
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attributeは必須です',
            'exists' => ':attributeが存在しません',
            'max' => [
                'string' => ':max文字以内で入力してください',
                'numeric' => ':max文字以内で入力してください',
                'integer' => ':max文字以内で入力してください',
            ],
            'integer' => ':attributeは整数で入力してください',
            'date_format' => '正しい形式で入力してください'  
        ];
    }
}

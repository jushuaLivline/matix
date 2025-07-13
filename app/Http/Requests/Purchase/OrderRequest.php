<?php

namespace App\Http\Requests\Purchase;
use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'supplier_name' => 'required|string|max:100',
            'department_code' => 'required|string|max:10|exists:departments,code',
            'line_code' => 'nullable|string|max:10|exists:lines,line_code',
            'part_number' => 'required|string|max:20',
            'product_name' => 'nullable|string|max:100',
            'standard' => 'nullable|string|max:100',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'nullable|numeric|min:0',
            'unit_code' => 'nullable|numeric|min:0',
            'amount_of_money' => 'nullable|numeric|min:0',
            'reason' => 'nullable|string|max:200',
            'expense_items' => 'nullable|string|max:10|exists:items,expense_item', 
            'deadline' => 'nullable|date',
            'remarks' => 'nullable|string|max:500',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes()
    {
        return [
            'supplier_name' => '仕入先名',
            'department_code' => '部門コード',
            'line_code' => 'ラインコード',
            'part_number' => '品番',
            'product_name' => '品名',
            'standard' => '規格',
            'quantity' => '数量',
            'unit_price' => '単価',
            'unit_code' => '単位',
            'amount_of_money' => '金額',
            'reason' => '理由',
            'expense_items' => '費目',
            'deadline' => '納期',
            'remark' => '備考'
        ];
    }

    /**
     * Custom error messages.
     *
     * @return array<string, string>
     */
    public function messages()
    {
        return [
            'required' => ':attributeは必須です',
            'string' => ':attributeは文字列で入力してください',
            'numeric' => ':attributeは数値で入力してください',
            'integer' => ':attributeは整数で入力してください',
            'date' => ':attributeは日付形式で入力してください',
            'min' => [
                'numeric' => ':attributeは:min以上で入力してください',
                'integer' => ':attributeは:min以上で入力してください'
            ],
            'max' => ':attributeは:max文字以内で入力してください',
            'in' => ':attributeは指定された値のいずれかである必要があります',
            'exists' => ':attributeが存在しません'
        ];
    }
}
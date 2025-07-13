<?php

namespace App\Http\Requests\Material;
use Illuminate\Foundation\Http\FormRequest;

class FractionRequest extends FormRequest
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

    public function rules(): array
    {
        $action = $this->route()->getName();
        switch ($action) {
            case 'material.storeData':
            case 'material.fractionCreate.update':
                return $this->storeRules();
            default:
                return [];
        }
        
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function storeRules()
    {
        return [
            'supplier_code' => 'required|string|max:10',
            'product_code' => 'required|string|max:100|exists:product_numbers,part_number',
            'supply_material_order_no' => 'nullable|string|max:10',
            'material_number' => 'nullable|string|max:20',
            'management_no' => 'nullable|string|max:15',
            'branch_number' => 'nullable|string|max:15',
            'supplier_code_request' => 'nullable|string|max:10',
            'order_classification' => 'nullable|integer|min:1',
            'instruction_no' => 'required|integer|min:1',
            'instruction_number' => 'nullable|integer|max:5',
            'instruction_kanban_quantity' => 'required|integer|min:10',
            'instruction_date' => 'required|date_format:Ymd'
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
            'supplier_code' => '材料メーカー',
            'product_code' => '材料品番',
            'instruction_date' => '指示日',
            'instruction_no' => '便',
            'instruction_kanban_quantity' => '数量',
            'instruction_number' => '便',
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
            'required' => ':attributeは必須です。',
            'string' => ':attributeは文字列で入力してください。',
            'numeric' => ':attributeは数値で入力してください。',
            'integer' => ':attributeは整数で入力してください。',
            'date' => ':attributeは日付形式で入力してください。',
            'date_format' => ':attributeは正しい形式で入力してください。', 
            'min' => [
                'numeric' => ':attributeは:min以上で入力してください。',
                'integer' => ':attributeは:min以上で入力してください。'
            ],
            'exists' => ':attributeが存在しません。',
            'max' => ':attributeは:max文字以内で入力してください。',
            'in' => ':attributeは指定された値のいずれかである必要があります。'
        ];
    }
}
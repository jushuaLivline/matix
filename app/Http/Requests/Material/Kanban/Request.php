<?php

namespace App\Http\Requests\Material\Kanban;
use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
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
            'supply_material_order_no' => 'nullable|array',
            'supply_material_order_no.*' => 'nullable|string|max:255',

            'management_no' => 'nullable|array',
            'management_no.*' => 'nullable|string|max:255',

            'material_number' => 'nullable|array',
            'material_number.*' => 'nullable|string|max:255',

            'order_classification' => 'nullable|array',
            'order_classification.*' => 'nullable|string|max:255',

            'supplier_code' => 'nullable|array',
            'supplier_code.*' => 'nullable|string|max:255',

            'material_manufacturer_code' => 'nullable|array',
            'material_manufacturer_code.*' => 'nullable|string|max:255',

            'instruction_date' => 'required|array',
            'instruction_date.*' => 'required|digits:8|date_format:Ymd',

            'instruction_no' => 'required|array',
            'instruction_no.*' => 'required|string|max:2',

            'instruction_kanban_quantity' => 'nullable|array',
            'instruction_kanban_quantity.*' => 'nullable|numeric|min:0',

            'instruction_number' => 'nullable|array',
            'instruction_number.*' => 'nullable|string|max:255',

            'arrival_quantity' => 'nullable|array',
            'arrival_quantity.*' => 'nullable|numeric|min:0',

            'created_at' => 'nullable|array',
            'created_at.*' => 'nullable|date',

            'creator' => 'nullable|array',
            'creator.*' => 'nullable|string|max:255',
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
            'supply_material_order_no.*' => '資材発注番号',
            'management_no.*' => '管理番号',
            'material_number.*' => '品目番号',
            'order_classification.*' => '発注区分',
            'supplier_code.*' => '仕入先コード',
            'material_manufacturer_code.*' => 'メーカーコード',
            'instruction_date.*' => '指示日',
            'instruction_no.*' => '便',
            'instruction_kanban_quantity.*' => 'かんばん指示数',
            'instruction_number.*' => '指示番号',
            'arrival_quantity.*' => '入荷数',
            'created_at.*' => '作成日',
            'creator.*' => '作成者',
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
            'exist' => ':attributeは:が存在しません',
            'max' => ':attributeは:max文字以内で入力してください。',
            'in' => ':attributeは指定された値のいずれかである必要があります。'
        ];
    }
}
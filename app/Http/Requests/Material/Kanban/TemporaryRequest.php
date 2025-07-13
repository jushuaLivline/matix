<?php

namespace App\Http\Requests\Material\Kanban;

use Illuminate\Foundation\Http\FormRequest;

class TemporaryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        $action = $this->route()->getName();
        switch ($action) {
            case 'material.kanbanTemporary.store':
                return $this->storeRules();
            case 'material.saveTemporaryData':
            case 'material.updateTemporaryData':
                return $this->storeUpdateRules();
            case 'material.fetch.kanban.details':
                return $this->kanbanRule();
            default:
                return [];
        }
        
    }

    public function storeRules()
    {
        return [
            'session_data' => 'required|array',

            'session_data.*.management_no' => 'required|string|max:5',
            'session_data.*.order_classification' => 'nullable|integer',
            'session_data.*.supplier_code' => 'nullable|string|max:10',
            'session_data.*.material_number' => 'nullable|string|max:100',
            'session_data.*.product_code' => 'required|string|max:100|exists:product_numbers,part_number',
            'session_data.*.material_manufacturer_code' => 'nullable|string|max:20',
            'session_data.*.instruction_date' => 'required|date_format:Ymd',
            'session_data.*.instruction_no' => 'required|string|max:20',
            'session_data.*.lot' => 'nullable|string|max:20',
            'session_data.*.instruction_kanban_quantity' => 'nullable|integer',
            'session_data.*.instruction_number' => 'nullable|integer',
            'session_data.*.arrival_quantity' => 'nullable|integer',
            'session_data.*.supply_material_order_no' => 'nullable|integer',
        ];
    }

    public function storeUpdateRules()
    {
        return [
            'management_no' => 'required|string|max:5|exists:kanban_masters,management_no',
            'order_classification' => 'nullable|integer',
            'supplier_code' => 'nullable|string|max:10',
            'material_number' => 'nullable|string|max:100',
            'product_code' => 'required|string|max:100|exists:product_numbers,part_number',
            'material_manufacturer_code' => 'nullable|string|max:20',
            'instruction_date' => 'required|date_format:Ymd',
            'instruction_no' => 'required|string|max:20',
            'lot' => 'nullable|string|max:20',
            'instruction_kanban_quantity' => 'required|integer',
            'number_of_accomodated' => 'required|integer',
            'instruction_number' => 'nullable|integer',
            'arrival_quantity' => 'nullable|integer',
            'supply_material_order_no' => 'nullable|integer',
            'creator' => 'nullable|string',
        ];
    }

    public function kanbanRule()
    {
        return [
            'management_no' => 'required|string|max:5|exists:kanban_masters,management_no'
        ];
    }
    public function messages(): array
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

    public function attributes(): array
    {
        return [
            'management_no' => '管理No.',
            'order_classification' => '発注区分',
            'supplier_code' => '仕入先コード',
            'material_number' => '材料品番',
            'product_code' => '材料品番',
            'material_manufacturer_code' => '材料メーカーコード',
            'instruction_date' => '指示日',
            'instruction_no' => '便',
            'lot' => 'ロット',
            'instruction_kanban_quantity' => '枚数',
            'number_of_accomodated' => '収容数',
            'instruction_number' => '指示数',
            'arrival_quantity' => '入荷数',
            'supply_material_order_no' => '資材発注番号',
        ];
    }
}

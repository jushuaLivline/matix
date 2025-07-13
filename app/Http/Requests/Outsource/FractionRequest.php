<?php

namespace App\Http\Requests\Outsource;

use Illuminate\Foundation\Http\FormRequest;

class FractionRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'process_code' => 'required|string|max:5|exists:processes,process_code',
            'product_code' => 'required|string|max:100|exists:product_numbers,part_number',
            'product_name' => 'nullable|string',
            'instruction_date' => 'required|date_format:Ymd',
            'instruction_number' => 'required|integer',
            'instruction_kanban_quantity' => 'required|integer',
            'creator' => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'process_code' => '仕入先',
            'product_code' => '製品品番',
            'product_name' => '品名',
            'instruction_date' => '指示日',
            'instruction_number' => '便',
            'instruction_kanban_quantity' => '数量',
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
            'date_format' => '正しい形式で入力してください。', 
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

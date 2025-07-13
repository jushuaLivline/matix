<?php

namespace App\Http\Requests\Outsource\Order;

use Illuminate\Foundation\Http\FormRequest;

class SlipRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_code' => 'required|integer|max:100',
            'instruction_date_from' => 'required|date_format:Ymd',
            'instruction_date_to' => 'required|date_format:Ymd',
            'instruction_number_from' => 'nullable|integer',
            'instruction_number_to' => 'nullable|integer',
            'supplier_name' => 'nullable|string',
            'creator' => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'supplier_code' => '仕入先',
            'instruction_date_from' => '指示日',
            'instruction_date_to' => '指示日',
            'instruction_number_from' => '便No',
            'instruction_number_to' => '便No',
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

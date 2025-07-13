<?php

namespace App\Http\Requests\Outsource;

use Illuminate\Foundation\Http\FormRequest;

class TemporaryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'management_no' => 'required|string|max:5|exists:kanban_masters,management_no',
            'product_code' => 'required|string|max:100|exists:product_numbers,part_number',
            'product_name' => 'nullable|string',
            'uniform_number' => 'nullable|string',
            'instruction_date' => 'required|date_format:Ymd',
            'instruction_number' => 'required|integer',
            'number_of_accomodated' => 'nullable|integer',
            'instruction_kanban_quantity' => 'required|integer',
            'arrival_quantity' => 'nullable|integer',
            'creator' => 'nullable|string',
        ];
    }

    public function attributes(): array
    {
        return [
            'management_no' => '管理No.',
            'product_code' => '製品品番',
            'product_name' => '品名',
            'uniform_number' => '背番号',
            'instruction_number' => '便',
            'number_of_accomodated' => '収容数',
            'instruction_kanban_quantity' => '枚数',
            'instruction_date' => '指示日',
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

<?php

namespace App\Http\Requests\Outsource\Inspection;
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
            'order_no' => 'nullable|array',
            'management_no' => 'nullable|array',
            'branch_number' => 'nullable|array',
            'product_code' => 'nullable|array',
            'supplier_process_code' => 'nullable|array',
            'order_classification' => 'nullable|array',
            'instruction_date' => 'nullable|array',
            'instruction_number' => 'nullable|array',
            'lot' => 'nullable|array',
            'instruction_kanban_quantity' => 'nullable|array',
            'arrival_number' => 'nullable|array',
            'arrival_day' => 'nullable|array',
            'incoming_flight_number' => 'nullable|array',
            'arrival_quantity' => 'nullable|array',
            'document_issue_date' => 'nullable|array',
            'supplier_code' => 'nullable|array',
            'created_at' => 'nullable|array',
            'creator' => 'nullable|array',
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
            'min' => [
                'numeric' => ':attributeは:min以上で入力してください。',
                'integer' => ':attributeは:min以上で入力してください。'
            ],
            'max' => ':attributeは:max文字以内で入力してください。',
            'in' => ':attributeは指定された値のいずれかである必要があります。'
        ];
    }
}
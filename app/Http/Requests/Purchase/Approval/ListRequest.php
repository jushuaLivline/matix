<?php

namespace App\Http\Requests\Purchase\Approval;

use Illuminate\Foundation\Http\FormRequest;

class ListRequest extends FormRequest
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
            'purpose' => 'nullable',
            'request_date_from' => 'nullable|date',
            'request_date_to' => 'nullable|date',
            'deadline_from' => 'nullable|date',
            'deadline_to' => 'nullable|date',
            'department_code_start' => 'nullable|string',
            'department_code_end' => 'nullable|string',
            'line_code_start' => 'nullable|string',
            'line_code_end' => 'nullable|string',
            'employee_code' => 'nullable|string',
            'supplier_code' => 'nullable|string',
            'part_number' => 'nullable|string',
            'product_name' => 'nullable|string',
            'standard' => 'nullable|string',
            'approval_method_category' => 'nullable|array',
            'state_classification' => 'nullable|array',
            'purchase_requisition_no' => 'nullable|string',
        ];
    }
}
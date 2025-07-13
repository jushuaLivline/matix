<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KanbanRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $management_no = $this->route('kanban'); 

        return [
            'management_no' => [
                'required',
                'numeric',
                Rule::unique('kanban_masters', 'management_no')->ignore($management_no),
            ],
            'kanban_classification' => ['required', 'string'],
            
            // If not null, must exist in product_numbers table
            'part_number' => ['nullable', 'string', 'exists:product_numbers,part_number'],
            
            // If not null, must exist in processes table
            'process_code' => ['nullable', 'string', 'exists:processes,process_code'],
            'next_process_code' => ['nullable', 'string', 'exists:processes,process_code'],
            
            'customer_acceptance' => ['nullable', 'string'],
            'process_order' => ['nullable', 'string'],
            'cycle_day' => ['nullable', 'numeric'],
            'number_of_cycles' => ['nullable', 'numeric'],
            'cycle_interval' => ['nullable', 'numeric'],

            // nullable is unnecessary with required, so removed
            'number_of_accomodated' => ['required', 'string'],

            'box_type' => ['nullable', 'string'],
            'acceptance' => ['nullable', 'string'],
            'shipping_location' => ['nullable', 'string'],
            'printed_jersey_number' => ['nullable', 'string'],
            'remark_1' => ['nullable', 'string'],
            'remark_2' => ['nullable', 'string'],
            'remark_qr_code' => ['nullable', 'string'],
            'issued_sequence_number' => ['nullable', 'numeric'],
            'paid_category' => ['nullable', 'numeric'],
            'delete_flag' => ['nullable', 'numeric'],
            'creator' => ['nullable','numeric'],
            'updator' => ['nullable','numeric'],
        ];
    }

    public function messages()
    {
        return [
            'management_no.required' => '管理番号は必須です',
            'management_no.numeric' => '管理番号は数字でなければなりません',
            'management_no.unique' => 'この管理番号はすでに使用されています',
            'management_no.digits' => '管理番号は6桁である必要があります',

            'kanban_classification.required' => 'カンバン分類が必要です',

            'part_number.exists' => '品番が存在しません',
            'process_code.exists' => '工程コードが存在しません',
            'next_process_code.exists' => '次工程コードが存在しません',

            'number_of_accomodated.required' => '収容人数は必須です',
        ];
    }
}

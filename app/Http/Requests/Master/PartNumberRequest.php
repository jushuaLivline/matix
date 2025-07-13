<?php
namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PartNumberRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */

    public function rules()
    {
        $productId = $this->route('part'); 

        return [
            'part_number' => ['required', 'string', Rule::unique('product_numbers', 'part_number')->ignore($productId),],
            'part_number_editing_format' => ['numeric', 'nullable'],
            'edited_part_number' => ['string', 'nullable'],
            'product_name' => ['required', 'string', 'max:255'],
            'product_category' => ['numeric', 'nullable'],
            'production_division' => ['numeric', 'nullable'],
            'instruction_class' => ['numeric', 'nullable'],
            'customer_code' => ['numeric', 'nullable'],
            'supplier_code' => ['numeric', 'nullable'],
            'department_code' => ['required','numeric', 'nullable'],
            'line_code' => ['numeric', 'nullable'],
            'standard' => ['string', 'nullable'],
            'material_manufacturer_code' => ['string', 'nullable'],
            'unit_code' => ['string', 'nullable'],
            'customer_part_number' => ['string', 'nullable'],
            'customer_part_number_edit_format' => ['numeric', 'nullable'],
            'customer_edited_product_number' => ['string', 'nullable'],
            'delete_flag' => ['numeric', 'nullable'],
        ];
    }

    public function messages ()
    {
        return [
            'part_number.unique' => 'この製品番号はすでに使用されています',
            'part_number.required' => '商品番号は必須です',
            'product_name.required' => '製品名は必須です',
            'department_code.required' => '部門は必須です',
        ];
    }
}

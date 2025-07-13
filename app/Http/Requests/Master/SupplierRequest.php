<?php
namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */

    public function rules()
    {
        $customerId = $this->route('supplier'); 

        return [
            'customer_code' => ['required', 'numeric', 'digits:6', Rule::unique('customers', 'customer_code')->ignore($customerId),],
            'customer_name' => ['required', 'string', 'max:255'],
            'supplier_name_abbreviation' => ['required', 'string', 'max:100'],
            'business_partner_kana_name' => ['string', 'nullable'],
            'branch_factory_name' => ['string', 'nullable'],
            'kana_name_of_branch_factory' => ['string', 'nullable'],
            'post_code' => ['numeric', 'nullable'],
            'address_1' => ['string', 'nullable'],
            'address_2' => ['string', 'nullable'],
            'telephone_number' => ['string', 'nullable'],
            'fax_number' => ['string', 'nullable'],
            'capital' => ['string', 'nullable'],
            'representative_name' => ['string', 'nullable'],
            'customer_flag' => ['string', 'nullable'],
            'supplier_tag' => ['string', 'nullable'],
            'supplier_classication' => ['string', 'nullable'],
            'purchase_report_apply_flag' => ['string', 'nullable'],
            'sales_amount_rounding_indicator' => ['string', 'nullable'],
            'purchase_amount_rounding_indicator' => ['string', 'nullable'],
            'transfer_source_bank_code' => ['string', 'nullable'],
            'transfer_source_bank_branch_code' => ['string', 'nullable'],
            'transfer_source_account_number' => ['string', 'nullable'],
            'transfer_source_account_clarification' => ['string', 'nullable'],
            'payee_bank_code' => ['string', 'nullable'],
            'transfer_destination_bank_branch_code' => ['string', 'nullable'],
            'transfer_account_number' => ['string', 'nullable'],
            'transfer_account_clasification' => ['string', 'nullable'],
            'transfer_fee_burden_category' => ['string', 'nullable'],
            'bill_ratio' => ['required', 'numeric'],
            'transfer_fee_condition_amount' => ['string', 'nullable'],
            'amount_less_than_transfer_fee_conditions' => ['string', 'nullable'],
            'transfer_fee_condition_or_more_amount' => ['string', 'nullable'],
            'delete_flag' => ['string', 'nullable'],
        ];
    }

    public function prepareForValidation()
    {
        // If delete_flag is null, set it to 0
        if ($this->input('delete_flag') === null) {
            $this->merge(['delete_flag' => '0']);
        }

        // Remove commas from the capital field
        if ($this->has('capital')) {
            $this->merge([
                'capital' => str_replace(',', '', $this->input('capital'))
            ]);
        }
    }

    public function messages ()
    {
        return [
            'customer_code.required' => '取引先コードは必須です',
            'customer_code.numeric' => '取引先コードは数字でなければなりません',
            'customer_code.digits' => '取引先コードは6桁である必要があります',
            'customer_code.unique' => 'この取引先コードはすでに使用されています',
            'customer_name.required' => '取引先名は必須です',
            'supplier_name_abbreviation.required' => '取引先略名は必須です',
            'bill_ratio.required' => '請求比率は必須です',
        ];
    }
}

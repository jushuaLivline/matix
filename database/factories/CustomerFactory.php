<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Customer>
 */
class CustomerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "customer_code" => $this->faker->unixTime(),
            "customer_name" => $this->faker->name(),
            "business_partner_kana_name" => $this->faker->name(),
            "branch_factory_name" => $this->faker->name(),
            "kana_name_of_branch_factory" => $this->faker->name(),
            "supplier_name_abbreviation" => $this->faker->name(),
            "factory_classification_code" => $this->faker->name(),
            "post_code" => $this->faker->randomDigit(),
            "address_1" => $this->faker->address(),
            "address_2" => $this->faker->address(),
            "telephone_number" => $this->faker->phoneNumber(),
            "fax_number" => $this->faker->phoneNumber(),
            "representative_name" => $this->faker->name(),
            "capital" => $this->faker->randomDigit(),
            "customer_flag" => $this->faker->randomElement([1, 0]),
            "supplier_tag" => $this->faker->randomElement([1, 0]),
            "supplier_classication" => $this->faker->randomElement([1, 2, 3]),
            "purchase_report_apply_flag"  => $this->faker->randomElement([1, 0]),
            "sales_amount_rounding_indicator"  => $this->faker->randomElement([1, 2, 3]),
            "purchase_amount_rounding_indicator"  => $this->faker->randomElement([1, 2, 3]),
            "bill_ratio" => $this->faker->randomNumber(),
            "transfer_source_bank_code" => $this->faker->countryCode(),
            "transfer_source_bank_branch_code"=> $this->faker->countryCode(),
            "transfer_source_account_number"=> $this->faker->countryCode(),
            "transfer_source_account_clarification"=> $this->faker->randomElement([1, 2]),
            "payee_bank_code"=> $this->faker->countryCode(),
            "transfer_destination_bank_branch_code"=> $this->faker->countryCode(),
            "transfer_account_number"=> $this->faker->randomDigit(),
            "transfer_account_clasification" => $this->faker->randomElement([1, 2]),
            "transfer_fee_burden_category" => $this->faker->randomElement([1, 2]),
            "transfer_fee_condition_amount" => $this->faker->randomNumber(),
            "amount_less_than_transfer_fee_conditions" => $this->faker->randomNumber(),
            "transfer_fee_condition_or_more_amount" => $this->faker->randomNumber(),
            "delete_flag" => $this->faker->randomElement([1, 0]),
            "created_at" => now(),
            "creator"  => $this->faker->randomDigit(),
            "updated_at" => now(),
            "updator"=> $this->faker->randomDigit(),
        ];
    }
}

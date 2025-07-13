<?php

namespace Database\Factories;

use App\Models\HistoryTemporaryOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HistoryTemporaryOrder>
 */
class HistoryTemporaryOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = HistoryTemporaryOrder::class;

    public function definition()
    {
        return [
            'item_code' => $this->faker->unique()->regexify('[A-Z]{2}\d{4}'),
            'accessioning_code' => $this->faker->regexify('[A-Z]{2}'),
            'customer_code' => $this->faker->regexify('[A-Z]{6}'),
            'product_code' => $this->faker->regexify('[A-Z]{3}\d{2}'),
            'department_code' => $this->faker->regexify('[A-Z]{3}\d{2}'),
            'line_code' => $this->faker->regexify('[A-Z]{3}\d{2}'),
            'delivery_destination_code' => $this->faker->optional()->regexify('[A-Z]{2}\d{2}'),
            'acceptance' => $this->faker->randomElement(['all', 'partial', 'none']),
            'order_yearmonth' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Ym'),
            'day_1_amount' => $this->faker->numberBetween(0, 500),
            'day_2_amount' => $this->faker->numberBetween(0, 500),
            'day_3_amount' => $this->faker->numberBetween(0, 500),
            'day_4_amount' => $this->faker->numberBetween(0, 500),
            'day_5_amount' => $this->faker->numberBetween(0, 500),
            'day_6_amount' => $this->faker->numberBetween(0, 500),
            'day_7_amount' => $this->faker->numberBetween(0, 500),
            'day_8_amount' => $this->faker->numberBetween(0, 500),
            'day_9_amount' => $this->faker->numberBetween(0, 500),
            'day_10_amount' => $this->faker->numberBetween(0, 500),
            'day_11_amount' => $this->faker->numberBetween(0, 500),
            'day_12_amount' => $this->faker->numberBetween(0, 500),
            'day_13_amount' => $this->faker->numberBetween(0, 500),
            'day_14_amount' => $this->faker->numberBetween(0, 500),
            'day_15_amount' => $this->faker->numberBetween(0, 500),
            'day_16_amount' => $this->faker->numberBetween(0, 500),
            'day_17_amount' => $this->faker->numberBetween(0, 500),
            'day_18_amount' => $this->faker->numberBetween(0, 500),
            'day_19_amount' => $this->faker->numberBetween(0, 500),
            'day_20_amount' => $this->faker->numberBetween(0, 500),
            'day_21_amount' => $this->faker->numberBetween(0, 500),
            'day_22_amount' => $this->faker->numberBetween(0, 500),
            'day_23_amount' => $this->faker->numberBetween(0, 500),
            'day_24_amount' => $this->faker->numberBetween(0, 500),
            'day_25_amount' => $this->faker->numberBetween(0, 500),
            'day_26_amount' => $this->faker->numberBetween(0, 500),
            'day_27_amount' => $this->faker->numberBetween(0, 500),
            'day_28_amount' => $this->faker->numberBetween(0, 500),
            'day_29_amount' => $this->faker->numberBetween(0, 500),
            'day_30_amount' => $this->faker->numberBetween(0, 500),
            'day_31_amount' => $this->faker->numberBetween(0, 500),
            'currentmonth_amount' => $this->faker->randomNumber(),
            'nextmonth_amount' => $this->faker->randomNumber(),
            'monthafternext_amount' => $this->faker->randomNumber(),
            'instruction_classification' => $this->faker->randomLetter(),
            'direct_destination' => $this->faker->randomNumber(4),
            'back_number' => $this->faker->randomNumber(4),
            'cycle' => $this->faker->randomElement(['week', 'month']),
            'capacity' => $this->faker->randomNumber(),
            'aisin_plant_code' => $this->faker->randomLetter(),
            'charge_code' => $this->faker->randomNumber(2),
            'minimum_delivery_unit' => $this->faker->randomNumber(),
            'number_per_day' => $this->faker->randomNumber(),
            'number_of_cards' => $this->faker->randomNumber(),
            'number_of_kanban' => $this->faker->randomNumber(),
            'standard_inventory' => $this->faker->randomNumber(),
            'sptp_classification' => $this->faker->randomLetter(),
            'factory_src' => $this->faker->randomLetter(),
            'factory_dest' => $this->faker->randomLetter(),
            'data_classification' => $this->faker->randomLetter(),
            'current_month_order_rate_weighted_decomposition_number' => $this->faker->randomNumber(),
            'color_code' => $this->faker->hexColor(),
            'customer_item_code' => $this->faker->word(),
            'aisin_customer_code' => $this->faker->randomNumber(4),
            'variation_code' => $this->faker->randomLetter(),
            'input_classification' => $this->faker->randomLetter(),
        ];
    }
}

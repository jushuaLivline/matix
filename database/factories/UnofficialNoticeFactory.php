<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UnofficialNotice>
 */
class UnofficialNoticeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {

        $productIds = Product::pluck('id')->all();
        $productId = $this->faker->randomElement($productIds);

        // Generate year_and_month within the range of March to May 2023
        $yearAndMonth = $this->faker->dateTimeBetween('2023-03-01', '2023-05-31')->format('Y-m');
        return [
            'product_number' => $productId,
            'acceptance' => $this->faker->numberBetween(1, 50),
            'delivery_destination_code' => $this->faker->unixTime(),
            'year_and_month' => $yearAndMonth,
            'day_1' => $this->faker->numberBetween(0, 500),
            'day_2' => $this->faker->numberBetween(0, 500),
            'day_3' => $this->faker->numberBetween(0, 500),
            'day_4' => $this->faker->numberBetween(0, 500),
            'day_5' => $this->faker->numberBetween(0, 500),
            'day_6' => $this->faker->numberBetween(0, 500),
            'day_7' => $this->faker->numberBetween(0, 500),
            'day_8' => $this->faker->numberBetween(0, 500),
            'day_9' => $this->faker->numberBetween(0, 500),
            'day_10' => $this->faker->numberBetween(0, 500),
            'day_11' => $this->faker->numberBetween(0, 500),
            'day_12' => $this->faker->numberBetween(0, 500),
            'day_13' => $this->faker->numberBetween(0, 500),
            'day_14' => $this->faker->numberBetween(0, 500),
            'day_15' => $this->faker->numberBetween(0, 500),
            'day_16' => $this->faker->numberBetween(0, 500),
            'day_17' => $this->faker->numberBetween(0, 500),
            'day_18' => $this->faker->numberBetween(0, 500),
            'day_19' => $this->faker->numberBetween(0, 500),
            'day_20' => $this->faker->numberBetween(0, 500),
            'day_21' => $this->faker->numberBetween(0, 500),
            'day_22' => $this->faker->numberBetween(0, 500),
            'day_23' => $this->faker->numberBetween(0, 500),
            'day_24' => $this->faker->numberBetween(0, 500),
            'day_25' => $this->faker->numberBetween(0, 500),
            'day_26' => $this->faker->numberBetween(0, 500),
            'day_27' => $this->faker->numberBetween(0, 500),
            'day_28' => $this->faker->numberBetween(0, 500),
            'day_29' => $this->faker->numberBetween(0, 500),
            'day_30' => $this->faker->numberBetween(0, 500),
            'day_31' => $this->faker->numberBetween(0, 500),
            'current_month' => $this->faker->numberBetween(0, 500),
            'next_month' => $this->faker->numberBetween(0, 500),
            'two_months_later' => $this->faker->numberBetween(0, 500),
            'instruction_class' => $this->faker->randomElement([1,2]),
            'direct_shipping_destination' => $this->faker->unixTime(),
            'uniform_number' => $this->faker->text,
            'cycle' => $this->faker->text,
            'number_of_accomodated' => $this->faker->text,
            'aisin_factory' => $this->faker->text,
            'responsible_person' => $this->faker->text,
            'minimum_delivery_unit' => $this->faker->randomElement([1, 500]),
            'number_per_day' => $this->faker->randomElement([1, 500]),
            'number_of_cards' => $this->faker->randomElement([1, 500]),
            'kanban_number' => $this->faker->randomElement([1, 500]),
            'standard_stock' => $this->faker->randomElement([1, 500]),
            'sp_tp_classification' => $this->faker->text,
            'manufactorer_factory' => $this->faker->text,
            'manufactorer_factory_destination' => $this->faker->text,
            'data_partition' => $this->faker->text,
            'current_month_order_rate_factored_number' => $this->faker->randomElement([1,500]),
            'color_mode' => $this->faker->text,
            'customer_part_number' => $this->faker->unixTime(),
            'customer' => $this->faker->unixTime(),
            'design_change_code' => $this->faker->text,
            'input_category' => $this->faker->randomElement([1,2,3,4]),
            'creator' => $this->faker->randomNumber(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

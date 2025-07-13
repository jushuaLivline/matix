<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FirmOrder>
 */
class FirmOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $deliveryNos = [12345, 54321, 54123, 12354, 12223, 10010];
        $partNos = [1222, 1333, 1444, 1555, 1666, 1777];

        return [
            'delivery_no' => $this->faker->randomElement($deliveryNos),
            'due_date' => $this->faker->dateTimeBetween('2023-05-19', '2023-06-01'),
            'part_no' => $this->faker->randomElement($partNos),
            'confirm_order_no' => $this->faker->unique()->randomNumber(6),
            'customer_code' => $this->faker->unique()->regexify('[A-Z]{2}\d{4}'),
            'classification' => $this->faker->randomElement(['kanban', 'instruction']),
            'plant' => $this->faker->numberBetween(5, 50),
            'acceptance' => $this->faker->unique()->randomNumber(6),
            'uniform_no' => $this->faker->unique()->randomNumber(6),
            'accomodation_no' => $this->faker->numberBetween(5, 50),
            'kanban_no' => $this->faker->numberBetween(1, 10), //random number from 1 to 10
            'instruction_no' => $this->faker->numberBetween(10, 90), // random number from 10 to 90
            'instruction_printed_flag' => $this->faker->randomElement(['Not Printed', 'Printed']),
            'ai_delivery_no' => $this->faker->unique()->randomNumber(6),
            'ai_jersey_no' => $this->faker->unique()->randomNumber(6)
        ];
    }
}

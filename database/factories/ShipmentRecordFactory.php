<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ShipmentRecord>
 */
class ShipmentRecordFactory extends Factory
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
            'shipment_no' => $this->faker->word,
            'serial_number' => $this->faker->numberBetween(1, 99),
            'slip_no' => $this->faker->word,
            'voucher_class' => $this->faker->word,
            'customer_code' => $this->faker->unique()->regexify('[A-Z]{2}\d{4}'),
            'acceptance' => $this->faker->numberBetween(5, 50),
            'plant' => $this->faker->numberBetween(5, 50),
            'drop_ship_code' => $this->faker->word,
            'line_code' => $this->faker->word,
            'department_code' => $this->faker->word,
            'quantity' => $this->faker->randomNumber(),
            'unit_price' => $this->faker->randomNumber(),
            'remarks' => $this->faker->optional()->sentence,
            'closing_date' => $this->faker->dateTime,
            'ai_slip_type' => $this->faker->word,
            'classification' => $this->faker->randomElement(['kanban', 'instruction']),
            'uniform_no' => $this->faker->word,
            'accomodation_no' => $this->faker->numberBetween(5, 50),
            'kanban_no' => $this->faker->numberBetween(5, 20),
            'instruction_no' => $this->faker->word,
            'ai_delivery_no' => $this->faker->unique()->randomNumber(6),
            'ai_jersey_no' => $this->faker->unique()->randomNumber(6),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Process>
 */
class ProcessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "process_code" => $this->faker->numberBetween(1, 500),
            "process_name" => $this->faker->word,
            "abbreviation_process_name" => $this->faker->word,
            "inside_and_outside_division" => $this->faker->randomElement([1, 2]),
            "customer_code" => $this->faker->unixTime(),
            "backorder_days" => $this->faker->randomDigit(),
            "material_receiving_classification" => $this->faker->randomElement([1, 2]),
            "delete_flag" => $this->faker->randomElement([1, 0]),
            "created_at" => now(),
            "creator" => $this->faker->randomDigit(),
            "updated_at" => now(),
            "updator" => $this->faker->randomDigit(),
        ];
    }
}

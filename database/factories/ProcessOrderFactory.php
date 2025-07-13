<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProcessOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "part_number" => $this->faker->unixTime(),
            "process_order" => null,
            "process_code" => null,
            "process_details" => $this->faker->randomLetter(),
            "packing" => $this->faker->word(),
            "created_at" => now(),
            "creator" => $this->faker->randomDigit(),
            "updated_at" => now(),
            "updator" => $this->faker->randomDigit(),
        ];
    }
}

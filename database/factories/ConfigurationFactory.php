<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Configuration>
 */
class ConfigurationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            "parent_part_number" => $this->faker->unixTime(),
            "child_part_number" => $this->faker->unixTime(),
            "number_used" => $this->faker->randomDigit(),
            "material_classification" => $this->faker->randomElement([1.2 ]),
            "delete_flag" => $this->faker->randomElement([1, 0]),
            "created_at" => now(),
            "creator" => $this->faker->randomDigit(),
            "updated_at" => now(),
            "updator" => $this->faker->randomDigit(),
        ];
    }
}

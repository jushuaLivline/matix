<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'item_code' => $this->faker->regexify('[A-Z0-9]{20}'),
            'item_name' => $this->faker->text(20),
            'name' => $this->faker->text(20),
            'product_name_abbreviation' => $this->faker->regexify('[A-Z0-9]{5}'),
            'product_category' => $this->faker->randomElement(['A', 'B', 'C']),
            'customer_code' => $this->faker->regexify('[A-Z0-9]{6}'),
            'supplier_code' => $this->faker->regexify('[A-Z0-9]{6}'),
            'department_code' => $this->faker->regexify('[A-Z0-9]{6}'),
            'line_code' => $this->faker->regexify('[A-Z0-9]{3}'),
            'sub_line_code' => $this->faker->regexify('[A-Z0-9]{2}'),
            'standard' => $this->faker->text(40),
            'material_manufacturer_code' => $this->faker->regexify('[A-Z0-9]{4}'),
            'unit_code' => $this->faker->text(10),
            'uniform_number' => $this->faker->numberBetween(1, 50),
            'back_number' => $this->faker->regexify('[A-Z0-9]{4}'),
            'part_number_edit_format' => $this->faker->regexify('[A-Z0-9]{5}'),
            'edit_part_number' => $this->faker->text(24),
            'instruction_classification' => $this->faker->randomElement(['A', 'B', 'C']),
            'customer_part_number' => $this->faker->text(20),
            'customer_part_number_edit_format' => $this->faker->regexify('[A-Z0-9]{5}'),
            'customer_edited_part_number' => $this->faker->text(24),
            'production_classification' => $this->faker->randomElement(['A', 'B', 'C']),
            'delete_flag' => $this->faker->boolean,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

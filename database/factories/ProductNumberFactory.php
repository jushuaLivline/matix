<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductNumber>
 */
class ProductNumberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'part_number' => $this->faker->unixTime(),
            'product_name' => $this->faker->name(),
            'name_abbreviation' => $this->faker->name(),
            'product_category' => $this->faker->randomElement([1,0]),
            'customer_code'  => $this->faker->unixTime(),
            'supplier_code'  => $this->faker->unixTime(),
            'department_code' => $this->faker->unixTime(),
            'line_code' => $this->faker->unixTime(),
            'secondary_line_code' => $this->faker->unixTime(),
            'standard' => $this->faker->word(),
            'material_manufacturer_code' => null,
            'unit_code' => $this->faker->unixTime(),
            'uniform_number' => $this->faker->unixTime(),
            'part_number_editing_format' => $this->faker->unixTime(),
            'edited_part_number' => $this->faker->unixTime(),
            'instruction_class' => $this->faker->randomElement([1, 2]),
            'customer_part_number' => $this->faker->unixTime(),
            'customer_part_number_edit_format' => $this->faker->unixTime(),
            'customer_edited_product_number' => $this->faker->unixTime(),
            'production_division' => $this->faker->randomElement([0, 1, 2]),
            'delete_flag' => $this->faker->randomElement([1,0])
        ];
    }
}

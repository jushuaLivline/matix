<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'code' => $this->faker->unixTime(),
            'name' => $this->faker->word(),
            'name_abbreviation' => $this->faker->word(),
            'department_name' => $this->faker->word(),
            'section_name' => $this->faker->word(),
            'group_name' => $this->faker->word(),
            'delete_flag' => $this->faker->randomElement([1,0]),
        ];
    }
}

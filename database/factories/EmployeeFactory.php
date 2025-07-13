<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Employee>
 */
class EmployeeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'employee_code' => $this->faker->unixTime(),
            'employee_name' => $this->faker->name(),
            'department_code' => null,
            'password' => Hash::make("password"),
            'authorization_code' => null,
            'mail_address' => $this->faker->safeEmail(),
            'purchasing_approval_request_email_notification_flag' => $this->faker->randomElement([1,0]),
            'delete_flag' => $this->faker->randomElement([1,0])
        ];
    }
}

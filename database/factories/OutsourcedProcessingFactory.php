<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OutsourcedProcessing>
 */
class OutsourcedProcessingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $managementNos = [
            '11111-11111',
            '22222-22222',
            '33333-33333',
            '44444-44444',
            '55555-55555',
            '66666-66666',
            '77777-77777',
            '88888-88888',
            '99999-99999',
            '12345-12345',
        ];

        shuffle($managementNos);

        $productIds = Product::pluck('id')->all();
        $productId = $this->faker->randomElement($productIds);

        return [
            'product_id' => $productId,
            'order_number' => $this->faker->numberBetween(1, 50),
            'management_no' => array_shift($managementNos),
            'branch_number' => $this->faker->randomNumber(),
            // 'product_number' => $this->faker->randomNumber(),
            'supplier_process_code' => $this->faker->regexify('[A-Z0-9]{5}'),
            'order_classification' => $this->faker->numberBetween(1, 4),
            // 'instruction_date' => $this->faker->optional()->date(),
            'instruction_date' => now(),
            'instruction_number' => $this->faker->optional()->numberBetween(1, 10),
            'lot' => $this->faker->word,
            'instruction_kanban_quantity' =>$this->faker->numberBetween(1, 50),
            'arrival_number' => null,
            'arrival_day' => null,
            'incoming_flight_number' => null,
            'arrival_quantity' => null,
            // 'arrival_number' => $this->faker->randomNumber(),
            // 'arrival_day' => $this->faker->date(),
            // 'incoming_flight_number' => $this->faker->word,
            // 'arrival_quantity' => $this->faker->randomNumber(),
            'document_issue_date' => now(),
            'supplier_code' => $this->faker->regexify('[A-Z0-9]{5}'),
            'creation_date' => $this->faker->date(),
            'creator_code' => $this->faker->word,
        ];
    }
}

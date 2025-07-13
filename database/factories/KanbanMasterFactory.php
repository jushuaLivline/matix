<?php

namespace Database\Factories;

use App\Models\Process;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\KanbanMaster>
 */
class KanbanMasterFactory extends Factory
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
        $processIds = Process::pluck('id')->all();
        $productId = $this->faker->randomElement($productIds);
        $processId = $this->faker->randomElement($processIds);
        
        return [
            'product_id' => $productId,
            'process_id' => $processId,
            'management_no' => array_shift($managementNos),
            'kanban_classification' =>  $this->faker->randomElement([1, 2, 3]),
            'part_number' => $this->faker->bothify('???###'),
            'process_order' => $this->faker->bothify('????'),
            'next_process_code' => $this->faker->bothify('????'),
            'cycle_day' => $this->faker->randomElement(['01', '02', '03']),
            'number_of_cycles' => $this->faker->numberBetween(5, 20),
            'cycle_interval' => $this->faker->numberBetween(1, 5),
            'number_of_accomodated' => $this->faker->numberBetween(1, 10),
            'box_type' => $this->faker->randomElement(['Small', 'Medium', 'Large']),
            'acceptance' => $this->faker->randomElement(['Accept', 'Reject']),
            'shipping_location' => $this->faker->bothify('LOC##'),
            'printed_jersey_number' => $this->faker->randomNumber(3),
            'remark_1' => $this->faker->sentence,
            'remark_2' => $this->faker->sentence,
            'remark_qr_code' => $this->faker->bothify('QR###'),
            'issued_sequence_number' => $this->faker->bothify('ISS###'),
            'paid_category' => $this->faker->randomElement(['1', '2', '3']),
            'delete_flag' => false,
            'creator' => 1,
        ];
        
    }
}

<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductPrice>
 */
class ProductPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        // Get random product ID from the products table
        $productIds = Product::pluck('id')->all();
        $productId = $this->faker->randomElement($productIds);

        return [
            'part_number' => $productId,
            'effective_date' => $this->faker->date(),
            'unit_price' => $this->faker->randomFloat(2, 0, 9999999),
            'sell_price' => $this->faker->randomFloat(2, 0, 9999999),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}

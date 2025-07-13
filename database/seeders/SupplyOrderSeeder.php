<?php

namespace Database\Seeders;

use App\Models\Material\SupplyOrder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplyOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SupplyOrder::factory()->times(20)->create();
    }
}

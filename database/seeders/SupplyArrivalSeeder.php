<?php

namespace Database\Seeders;

use App\Models\Material\SupplyArrival;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SupplyArrivalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        SupplyArrival::factory()->times(20)->create();
    }
}

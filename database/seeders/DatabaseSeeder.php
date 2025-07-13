<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\FirmOrder;
use App\Models\HistoryTemporaryOrder;
use App\Models\KanbanMaster;
use App\Models\OutsourcedProcessing;
use App\Models\Process;
use App\Models\Product;
use App\Models\ProductPrice;
use App\Models\UnofficialNotice;
use Illuminate\Database\Seeder;
use App\Models\ShipmentRecord;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // HistoryTemporaryOrder::factory()->times(50)->create();

        Product::factory()->times(20)->create();
        Process::factory()->times(20)->create();
        ProductPrice::factory()->times(20)->create();
        KanbanMaster::factory()->count(10)->create();
        OutsourcedProcessing::factory()->count(10)->create();
        UnofficialNotice::factory()->times(50)->create();
        HistoryTemporaryOrder::factory()->times(100)->create();
        FirmOrder::factory()->times(100)->create();
        ShipmentRecord::factory()->times(100)->create();
    }
}

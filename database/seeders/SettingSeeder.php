<?php

namespace Database\Seeders;

use App\Imports\SettingsImport;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Maatwebsite\Excel\Facades\Excel;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::truncate();
        Excel::import(new SettingsImport, base_path("storage/database/system-settings.xlsx"));
    }
}

<?php

namespace Database\Seeders;

use App\Models\KanbanMaster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class KanbanMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
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


        foreach ($managementNos as $managementNo) {
            KanbanMaster::create([
                'management_no' => $managementNo,
                'kanban_classification' => 'Material Supplied',
                'part_number' => 'ABC123', // Replace with appropriate part number
                'process_code' => 'PROC',
                'process_order' => 'ORDR',
                'next_process_code' => 'NEXT',
                'cycle_day' => '01',
                'number_of_cycles' => '10',
                'cycle_interval' => '2',
                'number_of_accomodated' => '5',
                'box_type' => 'Small',
                'acceptance' => 'Accept',
                'shipping_location' => 'LOC',
                'printed_jersey_number' => '123',
                'remark_1' => 'Remark 1',
                'remark_2' => 'Remark 2',
                'remark_qr_code' => 'QR123',
                'issued_sequence_number' => 'ISS123',
                'paid_category' => '1',
                'delete_flag' => false,
                'creator' => 1, // Replace with appropriate creator ID
            ]);
        }
    }
}

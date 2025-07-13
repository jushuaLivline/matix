<?php

namespace App\Imports;

use App\Models\Setting;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class SettingsImport implements ToModel, WithStartRow
{
    /**
     * @return int
     */
    public function startRow(): int
    {
        return 2;
    }

    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $data)
    {
        return new Setting([
            "setting_category" => $data[0],
            "setting_id" => $data[1],
            "number_1" => ($data[2] == "NULL" ? NULL : $data[2]),
            "number_2" => ($data[3] == "NULL" ? NULL : $data[3]),
            "number_3" => ($data[4] == "NULL" ? NULL : $data[4]),
            "number_4" => ($data[5] == "NULL" ? NULL : $data[5]),
            "string_1" => ($data[6] == "NULL" ? NULL : $data[6]),
            "string_2" => ($data[7] == "NULL" ? NULL : $data[7]),
            "string_3" => ($data[8] == "NULL" ? NULL : $data[8]),
            "string_4" => ($data[9] == "NULL" ? NULL : $data[9]),
            "remarks" => ($data[10] == "NULL" ? NULL : $data[10]),
        ]);
    }
}

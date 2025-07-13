<?php
namespace App\Services;

use App\Models\Configuration;
use Illuminate\Support\Facades\Auth;

class ConfigurationService{

    public function createConfiguration($data)
    {
        $data['creator'] = Auth::user()->id;

        return Configuration::create($data);
    }

    public function editConfiguration($data, $orig_id)
    {
        $conf = Configuration::find($orig_id);
        $fieldsToUpdate = $data;

        // if (isset($fieldsToUpdate['delete_flag'])) {
        //     $conf->delete_flag = $fieldsToUpdate['delete_flag'] ? 1 : 0;
        // } else {
        //     $conf->delete_flag = 0; // Assuming unchecked checkbox means value should be 0
        // }

        foreach ($fieldsToUpdate as $field => $value) {
            if ($conf->$field != $value) {
                $conf->$field = $value;
            }
        }

        $conf->updator = Auth::user()->id;

        $updated = $conf->save();

        return $updated;
    }

    public function softDelete ($id)
    {
        $conf = Configuration::find($id);
        $conf->delete_flag = 1;
        $conf->updator = Auth::user()->id;
        
        $updated = $conf->save();

        return $updated;
    }

    // public function updateLine($data, $id)
    // {
    //     $line = Line::find($id);
    //     $fieldsToUpdate = $data->except(['_token', 'department_name']);

    //     if (isset($fieldsToUpdate['delete_flag'])) {
    //         $line->delete_flag = $fieldsToUpdate['delete_flag'] ? 1 : 0;
    //     } else {
    //         $line->delete_flag = 0; // Assuming unchecked checkbox means value should be 0
    //     }

    //     foreach ($fieldsToUpdate as $field => $value) {
    //         if ($line->$field != $value) {
    //             $line->$field = $value;
    //         }
    //     }

    //     $line->updator = Auth::user()->id;

    //     $updated = $line->save();

    //     return $updated;
    // }

}
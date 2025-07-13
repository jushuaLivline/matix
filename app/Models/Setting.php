<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = "system_setting_masters";

    public $timestamps = false;
    
    protected $fillable = [
        "setting_category",
        "setting_id",
        "number_1",
        "number_2",
        "number_3",
        "number_4",
        "string_1",
        "string_2",
        "string_3",
        "string_4",
        "remarks",
    ];
}

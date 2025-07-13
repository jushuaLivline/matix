<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityWorkAmountResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'year_and_month',
        'machine_number',
        'branch_number',
        'working_code',
        'data_partition',
        'working_hours',
        'unit_price',
        'amount_of_work',
        'creation_date',
        'creator_code',
        'updated_at',
        'updater_code',
    ];
}

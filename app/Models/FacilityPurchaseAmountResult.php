<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacilityPurchaseAmountResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'year_and_month',
        'machine_number',
        'branch_number',
        'expense_item',
        'subsidy_items',
        'data_partition',
        'purchase_amount',
        'creation_date',
        'creator_code',
        'updated_at',
        'updater_code',
    ];
}

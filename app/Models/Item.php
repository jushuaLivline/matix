<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_item',
        'subsidy_items',
        'item_name',
        'acount',
        'acount_name',
        'supplementary_subjects',
        'auxiliary_course_name',
        'delete_flag'
    ];
}

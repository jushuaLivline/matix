<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;

    protected  $fillable = [
        'calendar_date',
        'creator_code',
        'created_at',
    ];
}

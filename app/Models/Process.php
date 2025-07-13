<?php

namespace App\Models;

use App\Traits\HasModelUtility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Process extends Model
{
    use HasFactory, HasModelUtility;

    protected $guarded = [];

    public function process_order()
    {
        return $this->hasMany(ProcessOrder::class, 'process_code', 'process_code');
    }
}

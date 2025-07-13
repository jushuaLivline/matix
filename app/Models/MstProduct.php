<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Eloquent\SoftDeletes;

class MstProduct extends Model
{
    use HasFactory;
    // NOTE: Comment out softDeletes as deleted_at column is missing from current migration

    // use HasFactory, SoftDeletes;

    const ACTIVE = 1;
    const DEACTIVE = 0;
    const DELETE = 1;

    // Remove mst_ prefix to follow database config
    protected $table = 'product_numbers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'part_number', 'product_name', 'name_abbreviation',
        'department_code', 'line_code', 'material_manufacturer_code',
        // 'created_by', 'updated_by',
        // 'deleted_at'
        'delete_flag'
    ];

    // public function updater()
    // {
    //     return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    // }

    // public function creater()
    // {
    //     return $this->belongsTo('App\Models\User', 'created_by', 'id');
    // }

    // public function line()
    // {
    //     return $this->belongsTo(MstLine::class, 'line_code', 'line_code');
    // }
}

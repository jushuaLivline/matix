<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MstDepartment extends Model
{
    use HasFactory;

    const ACTIVE = 1;
    const DEACTIVE = 0;
    const DELETE = 1;

    protected $table = 'departments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code', 'name', 'delete_flag',
        // 'created_by', 'updated_by',
        // 'deleted_at'
    ];

    // public function updater()
    // {
    //     return $this->belongsTo('App\Models\User', 'updated_by', 'id');
    // }

    // public function creater()
    // {
    //     return $this->belongsTo('App\Models\User', 'created_by', 'id');
    // }
}

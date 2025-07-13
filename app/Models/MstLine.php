<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MstLine extends Model
{
    use HasFactory, SoftDeletes;

    const ACTIVE = 1;
    const DEACTIVE = 0;
    const DELETE = 1;

    protected $table = 'facility_lines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'line_id',
        'line_code', 'line_name',
        'json_data',
        'created_by', 'updated_by',
        'deleted_at'
    ];

    public function line()
    {
        return $this->belongsTo('App\Models\Line', 'line_id', 'id');
    }

    public function updater()
    {
        // return $this->belongsTo('App\Models\User', 'updated_by', 'id');
        return $this->belongsTo('App\Models\Employee', 'updated_by', 'id');
    }

    public function creater()
    {
        // return $this->belongsTo('App\Models\User', 'created_by', 'id');
        return $this->belongsTo('App\Models\Employee', 'created_by', 'id');
    }

    // public function machines() {
    //     return $this->hasMany(MstMachine::class, 'inspection_line_id', 'id');
    // }
}

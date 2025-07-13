<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InspectionItem extends Model
{
    use HasFactory, SoftDeletes;

    const ACTIVE = 1;
    const DEACTIVE = 0;
    const DELETE = 1;

    protected $table = 'basic_inspection_item_set';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'inspection_item_set', 'json_data',
        'created_by', 'updated_by',
        'deleted_at'
    ];

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
}

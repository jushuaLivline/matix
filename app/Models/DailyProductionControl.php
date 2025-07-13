<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DailyProductionControl extends Model
{
    use HasFactory, SoftDeletes;

    const ACTIVE = 1;
    const DEACTIVE = 0;
    const DELETE = 1;

    protected $table = 'daily_production_control';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mst_department_id', 'mst_line_id', 'line_id', 'equipment_inspection_id',
        'year', 'month',
        'ct_input', 'json_data',
        'created_by', 'updated_by',
        'deleted_at'
    ];
    // note: line_id is id of mst_lines table
    // note: mst_line_id is id of mst_facility_machines table

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

    public function department()
    {
        return $this->belongsTo('App\Models\MstDepartment', 'mst_department_id', 'id');
    }
}

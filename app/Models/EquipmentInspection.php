<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EquipmentInspection extends Model
{
    use HasFactory, SoftDeletes;

    const ACTIVE = 1;
    const DEACTIVE = 0;
    const DELETE = 1;

    protected $table = 'equipment_inspection';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'mst_department_id', 'inspection_line_id', 'line_image',
        'year', 'month',
        'mst_basic_id', 'process_id', 'json_data', 'file_id',
        'created_by', 'updated_by',
        'confirmed_by', 'approved_by',
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

     public function confirmed()
    {
        return $this->belongsTo('App\Models\Employee', 'confirmed_by', 'id');
    }

    public function approved()
    {
        return $this->belongsTo('App\Models\Employee', 'approved_by', 'id');
    }

    public function completed()
    {
        return $this->belongsTo('App\Models\Employee', 'completed_by', 'id');
    }

    public function department()
    {
        return $this->belongsTo('App\Models\MstDepartment', 'mst_department_id', 'id');
    }

    public function facilityLine()
    {
        return $this->belongsTo('App\Models\MstLine', 'inspection_line_id', 'id');
    }

    public function inspectionItem()
    {
        return $this->belongsTo('App\Models\InspectionItem', 'mst_basic_id', 'id');
    }

    public function getBase64Image()
    {
        $base64 = '';
        $id = isset($this->id) ? $this->id : '';
        $id = (empty($id) AND isset($this->attributes['id'])) ? $this->attributes['id'] : $id;
        $image = isset($this->line_image) ? $this->line_image : '';
        $image = (empty($image) AND isset($this->attributes['line_image'])) ? $this->attributes['line_image'] : $image;
        $path = storage_path('app/public/'. $image);
        if ($id AND $image AND file_exists($path)) {
            // $type = pathinfo($path, PATHINFO_EXTENSION);
            $type = 'png';
            $data = file_get_contents($path);
            $encodedData = base64_encode($data);
            $base64 = 'data:image/' . $type . ';base64,' . str_replace('dataimage/jpegbase64', '', $encodedData);
            // $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        return $base64;
    }
}

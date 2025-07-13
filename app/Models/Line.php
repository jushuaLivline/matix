<?php

namespace App\Models;

use App\Traits\HasModelUtility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PaginateWithLimit;

class Line extends Model
{
    use HasFactory, HasModelUtility;

    use PaginateWithLimit;
    protected $fillable = [
        'line_code',
        'line_name',
        'line_name_abbreviation',
        'department_code',
        'delete_flag',
        'creator',
        'updator'
    ];

    public function shipments() {
        return $this->hasMany(ShipmentRecord::class, 'line_code', 'line_code');
    }

    public function department() {
        return $this->belongsTo(Department::class, 'department_code', 'code');
    }

    public function salePlans()
    {
        return $this->hasMany(SalePlan::class, 'line_code', 'line_code');
    }

    public function salePerformances()
    {
        return $this->hasMany(SalePerformance::class, 'line_code', 'line_code');
    }

    public function scopeSearch($query, $request)
    {
        if ($request->filled('line_code')) {
            $query->where('line_code', $request->input('line_code'));
        }

        if ($request->filled('department_code')) {
            $query->where('department_code', $request->input('department_code'));
        }

        if ($request->filled('delete_flag') && ($request->input('delete_flag') <> "all")) {
            $query->where('delete_flag', $request->input('delete_flag'));
        }

        $query->orderByDesc('created_at');
        return $query;
    }

    public static function getLastLineByCreator($creator) {
        
        $data = self::where('creator', $creator)
                ->orderBy('created_at', 'desc')
                ->first();
        
        if (!$data) {
            return null;
        }
        
        $data->load('department');
        $data["department_name"] = $data->department->name;
        
        return $data;
    }

    public static function getLineById($id) {
        
        $data = self::findOrFail($id);
        
        if (!$data) {
            return null;
        }
        
        $data->load('department');
        $data["department_name"] = $data->department->name;
        
        return $data;
    }

    public static function checkIfLineCodeExists($line_code): bool
    {
        return self::where('line_code', $line_code)->exists();
    }

}

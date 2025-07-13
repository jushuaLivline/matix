<?php

namespace App\Models;

use App\Traits\HasModelUtility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory, HasModelUtility;

    /**
     * @var string $table
     */
    // protected $table = 'mst_departments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    // protected $fillable = [];
    protected $guarded = [];

    public function shipments() {
        return $this->hasMany(ShipmentRecord::class, 'department_code', 'code');
    }

    public function salePlans()
    {
        return $this->hasMany(SalePlan::class, 'department_code', 'id');
    }

    public function salePerformances()
    {
        return $this->hasMany(SalePerformance::class, 'department_code', 'id');
    }

    public function scopeSearch($query, $request)
    {
        if (($request->department_code ?? '') != '') {
            $query->where('code', $request->department_code);
        }
    }
}

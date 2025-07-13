<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyMaterialGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_number',
        'supply_material_group',
        'created_at',
        'creator',
        'updated_at',
        'updator'
    ];

    public function product()
    {
        return $this->belongsTo(ProductNumber::class, 'part_number', 'part_number');
    }

}

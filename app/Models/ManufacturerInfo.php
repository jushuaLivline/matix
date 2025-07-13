<?php

namespace App\Models;

use App\Traits\HasModelUtility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManufacturerInfo extends Model
{
    use HasFactory, HasModelUtility;

    /**
     * @var string $table
     */
    // protected $table = 'mst_manufacturer_info';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */

    protected $fillable = [
        'material_manufacturer_code',
        'person_in_charge',
        'creator_code',
        'updator_code',
    ];
    protected $guarded = [];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipmentFileUpload extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'equipment_inspection_files';

    function file(): BelongsTo
    {
        return $this->belongsTo(EquipmentInspection::class, 'file_id');
    }
}

<?php

namespace App\Models;

use App\Traits\HasModelUtility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PaginateWithLimit;

class Project extends Model
{
    use HasFactory, HasModelUtility;
    use PaginateWithLimit;

    public $timestamps = false;

    protected $fillable = [
        'project_number',
        'project_name',
        'delete_flag',
        'created_at',
        'creator',
        'updated_at',
        'updator',
    ];

    public static function boot()
    {
        parent::boot();

        self::creating(function($model){
            $model->created_at = date('Y-m-d H:i:s');
            $model->creator = auth()->user()->id;
        });

        self::updating(function($model){
            $model->updated_at = date('Y-m-d H:i:s');
            $model->updator = auth()->user()->id;
        });

    }
}

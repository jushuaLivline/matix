<?php

namespace App\Models;

use App\Traits\HasModelUtility;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PaginateWithLimit;
use App\Constants\MachineNumberConstant;

class MachineNumber extends Model
{
    use HasFactory, HasModelUtility;
    
    use PaginateWithLimit;

    public $timestamps = false;

    use PaginateWithLimit;

    protected $fillable = [
        'sign',
        'branch_number',
        'machine_number',
        'machine_number_name',
        'project_number',
        'project_name',
        'line_name',
        'machine_division',
        'drawing_date',
        'completion_date',
        'manager',
        'remarks',
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
            $model->creator = auth()->user()->id;
        });

        self::updating(function($model){
            $model->updated_at = date('Y-m-d H:i:s');
            $model->updator = auth()->user()->id;
        });

    }

    public function project() {
        return $this->belongsTo(Project::class, 'project_number', 'project_number');
    }


    public function scopeSearch($query, $request)
    {
        return $query
            ->when($request->filled('machine_number_from'), fn($q) =>
                $q->where('machine_number', '>=', $request->machine_number_from)
            )
            ->when($request->filled('machine_number_to'), fn($q) =>
                $q->where('machine_number', '<=', $request->machine_number_to)
            )
            ->when($request->filled('machine_number_name'), fn($q) =>
                $q->where('machine_number_name', 'like', '%' . $request->machine_number_name . '%')
            )
            ->when($request->filled('project_number'), fn($q) =>
                $q->where('project_number', 'like', '%' . $request->project_number . '%')
            )
            ->when($request->filled('line_name'), fn($q) =>
                $q->where('line_name', 'like', '%' . $request->line_name . '%')
            )
            ->when($request->has('machine_division') && $request->machine_division !== 'all', fn($q) =>
                $q->where('machine_division', $request->machine_division)
            )
            ->when($request->filled('remarks'), fn($q) =>
                $q->where('remarks', 'like', '%' . $request->remarks . '%')
            )
            ->when($request->filled('completion_date') && $request->completion_date !== 'all', function ($q) use ($request) {
                if ($request->completion_date == 1) {
                    $q->whereNotNull('completion_date');
                } elseif ($request->completion_date == 2) {
                    $q->whereNull('completion_date');
                }
            })
            ->when($request->filled('delete_flag') && $request->delete_flag !== 'all', fn($q) =>
                $q->where('delete_flag', $request->delete_flag)
            )
            ->orderByDesc('created_at');
    }
}

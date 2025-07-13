<?php

namespace App\Models\Outsource;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\KanbanMaster;
use App\Models\ProductNumber;
use App\Models\Process;

class SubcontractSupply extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'subcontract_supply';

    protected $fillable = [
        'subcontract_supply_no',
        'management_no',
        'branch_number',
        'supplier_process_code',
        'product_code',
        'supply_classification',
        'supply_date',
        'supply_flight_no',
        'lot',
        'supply_kanban_quantity',
        'supply_quantity',
        'payment_classification',
        'issuance_date',
        'creator',
    ];

    public function kanban()
    {
        return $this->belongsTo(KanbanMaster::class, 'management_no');
    }

    public function product()
    {
        return $this->belongsTo(ProductNumber::class, 'product_code', 'part_number');
    }

    public function process()
    {
        return $this->belongsTo(Process::class, 'supplier_process_code', 'process_code');
    }

    public static function boot()
    {
        parent::boot();

        self::creating(function ($model) {
            $model->created_at = date('Y-m-d H:i:s');
            $model->creator = auth()->user()->id;
        });
    }
    
    public function generateSubcontractSupplyNo()
    {
        $latest = self::whereRaw('LENGTH(subcontract_supply_no) = 10')->orderBy('id', 'DESC')->first();
        $prefix = date('ym'); // YYMM format

        return ($latest && substr($latest->subcontract_supply_no, 0, 4) == $prefix)
            ? $prefix . sprintf("%06d", substr($latest->subcontract_supply_no, 4) + 1)
            : $prefix . '000001';
    }
}
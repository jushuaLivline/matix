<?php

namespace App\Models\Purchase;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Approval extends Model
{
    use HasFactory;

    protected $table = 'purchase_approvals';
    protected $guarded = [];

    // Relation to mst_purchase_requisitions
    public function requisition()
    {
        return $this->belongsTo(Requisition::class, 'purchase_record_no', 'requisition_number');
    }
}

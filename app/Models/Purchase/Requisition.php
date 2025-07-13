<?php

namespace App\Models\Purchase;

use Illuminate\Database\Eloquent\Model;

class Requisition extends Model
{
    protected $table = 'purchase_requisitions';

    public function approvals()
    {
        return $this->hasMany(
            Approval::class,
            'purchase_record_no',
            'requisition_number'
        );
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseApproval extends Model
{
    use HasFactory;

    protected $casts = [
        'approval_date'        => 'date',
        'denial_date'        => 'date',
        'created_at'        => 'date'
    ];

    public $timestamps = false;

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'approver_employee_code', 'employee_code');
    }

    function nextApproval(){
        return self::where([
            "order_of_approval" => $this->order_of_approval+1,
            "purchase_record_no" => $this->purchase_record_no,
            ])->first();
    }

    protected $fillable = [
        'purchase_record_no',
        'order_of_approval',
        'approver_employee_code',
        "approval_date",
        'denial_date',
    ];
}

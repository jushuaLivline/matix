<?php

namespace App\Models;

use App\Traits\HasAttachment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimateReplyDetail extends Model
{
    use HasFactory, HasAttachment;

    protected $casts = [
        'reply_estimate_d' => 'date',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'estimate_id',
        'monthly_standard_amount',
        'attachment_file',
        'delete_flag',
    ];

    function estimate(){
        return $this->belongsTo(Estimate::class);
    }

    function employee(){
        return $this->belongsTo(Employee::class, 'employee_code');
    }

    function replyQuations(){
        return $this->hasMany(EstimateReplyQuotation::class);
    }
}

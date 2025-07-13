<?php

namespace App\Models;

use App\Traits\HasAttachment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EstimateReply extends Model
{
    use HasFactory, HasAttachment;

    protected $casts = [
        'estimate_reply_date' => 'date',
    ];

    protected $table = 'estimate_reply';
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'estimate_id',
        'employee_code',
        'estimate_reply_date',
        'reply_content',
        'decline_flag',
        'delete_flag',
    ];

    function estimate(){
        return $this->belongsTo(Estimate::class, 'estimate_id',  'id');
    }

    function employee(){
        return $this->belongsTo(Employee::class, 'employee_code', 'employee_code');
    }

    function replyQuations(){
        return $this->hasMany(EstimateReplyQuotation::class, 'estimate_reply_detail_id', 'id');
    }
}

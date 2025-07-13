<?php

namespace App\Models;

use App\Traits\HasAttachment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Traits\PaginateWithLimit;

class Estimate extends Model
{
    use HasFactory, HasAttachment, PaginateWithLimit;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'estimate_request_date' => 'date',
        'reply_due_date' => 'date',
        'sop' => 'date',
        'monthly_standard_amount' => 'integer',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_code',
        'customer_contact_person',
        'estimate_request_date',
        'reply_due_date',
        'base_product_code',
        'product_code',
        'part_name',
        'model_type',
        'monthly_standard_amount',
        'sop',
        'request_content',
        'attachment_file',
        'delete_flag',
        'created_at',
        'updated_at',
        'creator',
        'updator',
    ];

    function replies(){
        return $this->hasMany(EstimateReply::class, 'estimate_id',  'id')
                    ->where('delete_flag', 0)
                    ->latest();
    }

    public function lastReply()
    {
        return $this->hasOne(EstimateReply::class, 'estimate_id',  'id')->orderByDesc('id');
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_code', ownerKey: 'customer_code');
    }
    public function attachments()
    {
        return $this->hasMany(EstimateReplyDetail::class, 'estimate_id', 'id');
    }


    public function scopeSearch($query, $request)
    {
        $query = $this
                    ->withCount(['replies'])
                    ->with(['replies','lastReply', 'customer', 'attachments'])
                    ->when(filled($request->unanswered), fn($q) => $q->doesntHave("replies"))
                    ->when(filled($request->answered), fn($q) => $q->has("replies"))
                    ->when(filled($request->declined), fn($q) => 
                        $q->whereHas("replies", fn($subQ) => $subQ->where("decline_flag", 1))
                    )
                    ->when(filled($request->customer_code), function ($q) use ($request) {
                        $q->whereHas("customer", fn($subQ) => $subQ->where("customer_code", $request->customer_code));
                    })
                    ->when(filled($request->reply_id), function ($q) use ($request) {
                        $q->whereHas("replies", fn($subQ) => $subQ->where("estimate_reply.id", $request->reply_id));
                    });


        // Apply direct filters when values exist
        $filterMappings = [
            // 'customer_code' => 'customer_code',
        ];

        foreach ($filterMappings as $filterKey => $dbColumn) {
            if (!empty($request[$filterKey])) {
                $query->where($dbColumn, $request[$filterKey]);
            }
        }

        // Apply partial search
        $searchableFields  = [
            'product_code' => 'product_code', // db_field => input_field name
            'part_name' => 'part_name',
            'model_type' => 'model_type',
            'id' => 'id',
         ];
 
         foreach ($searchableFields  as $filterKey => $dbColumn) {
            if (!empty($request[$filterKey])) {
                $query->where($dbColumn, 'like', '%' . $request[$filterKey] . '%');
            }
         }

        // Apply date range filters
        $dateFilters = [
            'estimate_request_date' => ['estimate_request_date_start', 'estimate_request_date_end'], // db_field => input_field name
            'reply_due_date' => ['reply_due_date_start', 'reply_due_date_end'],
        ];

        foreach ($dateFilters as $column => [$fromKey, $toKey]) {
            $fromDate = !empty($request[$fromKey]) ? Carbon::parse($request[$fromKey])->startOfDay() : null;
            $toDate = !empty($request[$toKey]) ? Carbon::parse($request[$toKey])->endOfDay() : null;
        
            if ($fromDate && $toDate) {
                $query->whereBetween($column, [$fromDate, $toDate]);
            } elseif ($fromDate) {
                $query->where($column, '>=', $fromDate);
            } elseif ($toDate) {
                $query->where($column, '<=', $toDate);
            }
        }

        // Apply numeric range filter for order number
        $numericRanges = [
            'product_number' => ['start' => 'product_code_from', 'end' => 'product_code_to'],
        ];

        foreach ($numericRanges as $column => $rangeKeys) {
            $from = $request[$rangeKeys['start']] ?? null;
            $to = $request[$rangeKeys['end']] ?? null;

            if ($from !== null && $to !== null) {
                $query->whereBetween($column, [$from, $to]);
            } elseif ($from !== null) {
                $query->where($column, '>=', $from);
            } elseif ($to !== null) {
                $query->where($column, '<=', $to);
            }
        }
        
        $query->orderByDesc('created_at');
        return $query;
    }
}

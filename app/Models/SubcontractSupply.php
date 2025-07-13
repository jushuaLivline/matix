<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Traits\PaginateWithLimit;

class SubcontractSupply extends Model
{
    use HasFactory;

    // Trait for paginating with a record limit.
    use PaginateWithLimit;


    /**
     * @var string $table
     */
    // protected $table = 'mst_role_permissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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
        'payment_classification	',
        'issuance_date',
        'created_at',
        'creator',
        'updated_at',
        'updator',
    ];

    public function kanban() {
        return $this->belongsTo(KanbanMaster::class, 'management_no', 'management_no');
    }

    public function customer() {
        return $this->belongsTo(Customer::class, 'supplier_process_code', 'customer_code');
    }

    public function product_number() {
        return $this->belongsTo(ProductNumber::class, 'product_code', 'part_number');
    }

    public function scopeSearch($query, $filters)
    {
        $query->with(['kanban', 'customer', 'product_number'])
            ->orderByDesc('created_at');

        // Apply direct filters when values exist
        $filterMappings = [
            'subcontract_supply_no' => 'subcontract_supply_no',
            'supplier_process_code' => 'supplier_process_code',
        ];

        foreach ($filterMappings as $filterKey => $dbColumn) {
            if (!empty($filters[$filterKey])) {
                $query->where($dbColumn, $filters[$filterKey]);
            }
        }

        // Apply date range filters
        $dateFilters = [
            'supply_date' => ['supply_date_from', 'supply_date_to']
        ];

        foreach ($dateFilters as $column => [$fromKey, $toKey]) {
            if (!empty($filters[$fromKey])) {
                $fromDate = Carbon::parse($filters[$fromKey])->format('Y-m-d');
                $toDate = !empty($filters[$toKey]) ? Carbon::parse($filters[$toKey])->format('Y-m-d') : null;

                $query->whereBetween($column, $toDate ? [$fromDate, $toDate] : [$fromDate, $fromDate]);
            }
        }

        // Apply numeric range filter for order number
        if (!empty($filters['supply_flight_number_from'])) {
            $from = $filters['supply_flight_number_from'];
            $to = !empty($filters['supply_flight_number_to']) ? $filters['supply_flight_number_to'] : $from;

            $query->whereBetween('supply_flight_no', [$from, $to]);
        }


        return $query;
    }
}

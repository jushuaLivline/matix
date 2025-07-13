<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Traits\PaginateWithLimit;

class FirmOrder extends Model
{
    use HasFactory, PaginateWithLimit;


    public function shipmentRecords()
    {
        return $this->hasMany(ShipmentRecord::class, 'product_no', 'part_number');
    }

    public function product()
    {
        return $this->belongsTo(ProductNumber::class, 'part_number', 'part_number');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'delivery_destination_code', 'customer_code');
    }

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

    public function scopeCreateSearch($query, $filters)
    {
        $query->with([
            'product',
            'customer'
        ])
        ->orderByDesc('created_at');

        // Apply direct filters when values exist
        $filterMappings = [
            'delivery_no' => 'delivery_no',
            'delivery_destination_code' => 'delivery_destination_code',
            'plant' => 'plant',
            'acceptance' => 'acceptance',
            'classification' => 'classification',
            'part_number'  => 'part_number'
        ];

        foreach ($filterMappings as $filterKey => $dbColumn) {
            if (!empty($filters[$filterKey])) {
                $query->where($dbColumn, $filters[$filterKey]);
            }
        }

        if (!empty($filters['created_at'])) {
            $formattedDate = \Carbon\Carbon::createFromFormat('Ymd', $filters['created_at'])->format('Y-m-d');
        
            $startOfDay = \Carbon\Carbon::createFromFormat('Y-m-d', $formattedDate)->startOfDay();
            $endOfDay = \Carbon\Carbon::createFromFormat('Y-m-d', $formattedDate)->endOfDay();
            
            $query->whereBetween('created_at', [$startOfDay, $endOfDay]);
        }
        
        // Apply additional filtering for part_number
        if (!empty($filters['part_number'])) {
            $query->whereHas('product', function ($q) use ($filters) {
                $q->where('product_category', 1)
                ->where('part_number', $filters['part_number']);
            });
        }

        return $query;
    }

    public function scopeSearch($query, $request)
    {
        if (($request->part_number_first ?? '') != '') {
            $query->where('part_number', '<=', $request->part_number_first);
        }

        if (($request->part_number_second ?? '') != '') {
            $query->where('part_number', '>=', $request->part_number_second);
        }

        if (($request->order_date ?? '') != '') {
            $query->where('due_date', $request->order_date);
        }

        if (($request->customer_code ?? '') != '') {
            $query->where('delivery_destination_code', $request->customer_code);
        }

        if (($request->order_plant ?? '') != '') {
            $query->where('plant', $request->order_plant);
        }

        if (($request->acceptance ?? '') != '') {
            $query->where('acceptance', $request->acceptance);
        }
        
        if (($request->class_type ?? '1') == '1') {
            $query->where('kanban_number', '<>', '');
        } elseif (($request->class_type ?? '1') == '2') {
            $query->where('instruction_number', '<>', '');
        }
    }

    public function getConfirmedOrder($request)
    {
        $orderDate = Carbon::parse($request->order_date)->format('Y-m-d');
        $query = $this->select(
            'due_date',
            'delivery_destination_code',
            'classification',
            'part_number',
            'plant',
            'acceptance',
            'uniform_number',
            'number_of_accommodated',
            'delivery_no',
            'kanban_number',
            'instruction_number'
          )
            ->with(['product:id,part_number,product_name,customer_edited_product_number'])
            ->where('due_date', $orderDate)
            ->when($request->input('acceptance'), fn($q, $acceptance) => $q->where('acceptance', $acceptance))
            ->when($request->input('customer_code'), fn($q, $deliveryDestination) => $q->where('delivery_destination_code', $deliveryDestination))
            ->when($request->input('delivery_no'), fn($q, $deliveryNo) => $q->where('delivery_no', $deliveryNo))
            ->when($request->input('plant'), fn($q, $plant) => $q->where('plant', $plant))
            ->when($request->input('category'), fn($q, $category) => $q->where('classification', $category))
            ->when($request->input('department_code'), function ($query, $departmentCode) {
              $query->whereHas('product', fn($productQuery) => $productQuery->where('department_code', $departmentCode));
            })
            ->when($request->input('supplier_code'), function ($query, $supplierCode) {
              $query->whereHas('product', fn($productQuery) => $productQuery->where('supplier_code', $supplierCode));
            })
            ->when($request->input('line_code'), function ($query, $lineCode) {
              $query->whereHas('product', fn($productQuery) => $productQuery->where('line_code', $lineCode));
            })
            ->where(function ($query) {
              $query->whereNotNull('kanban_number')
                ->orWhereNotNull('instruction_number')
                ->orWhereNotNull('part_number');
            })
            ->groupBy('part_number')
            ->orderByDesc('created_at');
        return $query;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Purchase extends Model
{
    use HasFactory;

    public $timestamps = false;

    /**
     * Item
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'expense_item', 'expense_item');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_code', 'code');
    }

    public function line()
    {
        return $this->belongsTo(Line::class, 'line_code', 'line_code');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_code', 'customer_code');
    }

    /**
     * Scope a query to filter purchases by a given year and month.
     */
    public function scopeFilterByYearMonth($query, $year, $month)
    {
        return $query->whereYear('date', $year)->whereMonth('date', $month);
    }

    /**
     * Scope a query to aggregate purchase amount grouped by expense items.
     */
    public function scopeWithAggregatedAmounts($query)
    {
        return $query->select('expense_item', 'subsidy_items')
                    ->selectRaw('SUM(COALESCE(amount_of_money, 0)) as amount')
                    ->groupBy('expense_item', 'subsidy_items');
    }

     /**
     * The "booted" method of the model.
     * Register cache clearing logic directly within the model.
     */
    protected static function booted()
    {
        // Clear cache on created event
        static::created(function ($purchase) {
            static::clearCache();
        });

        // Clear cache on updated event
        static::updated(function ($purchase) {
            static::clearCache();
        });

        // Clear cache on deleted event
        static::deleted(function ($purchase) {
            static::clearCache();
        });
    }

    /**
     * Clear cache based on specific cache keys related to the Purchase model.
     */
    protected static function clearCache()
    {
        Cache::forget('purchases_*');
    }
}

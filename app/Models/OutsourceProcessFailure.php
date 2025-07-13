<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\PaginateWithLimit;

class OutsourceProcessFailure extends Model
{
    use HasFactory;
    
    // Trait for paginating with a record limit.
    use PaginateWithLimit;
    protected $fillable = [
        'registration_no',
        'serial_number',
        'process_code',
        'disposal_date',
        'part_number',
        'quantity',
        'slip_no',
    ];

    protected $casts = [
        'disposal_date' => 'date',
        'created_at' => 'date'
    ];

    public function product()
    {
        return $this->belongsTo(ProductNumber::class, 'part_number', 'part_number');
    }

    public function process()
    {
        return $this->belongsTo(Process::class, 'process_code', 'process_code');
    }

    /**
     * Scope a query to filter outsource process failures based on various criteria.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * Filters:
     * - product_code: Filter by product code (part number).
     * - process_code: Filter by process code.
     * - slip_no: Filter by slip number.
     * - disposal_date_from: Filter by disposal date range start.
     * - disposal_date_to: Filter by disposal date range end.
     * - input_date_from: Filter by input date range start.
     * - input_date_to: Filter by input date range end.
     *
     * Relationships loaded:
     * - product
     * - product.customer
     * - product.processUnitPrice
     * - process
     */
    public function scopeFilter($query, $filters)
    {
        return $query->with(['product', 'product.customer', 'product.processUnitPrice', 'process'])
            ->when($filters['product_code'] ?? null, fn($q, $partNumber) => $q->where('part_number', $partNumber))
            ->when($filters['process_code'] ?? null, fn($q, $processCode) => $q->where('process_code', $processCode))
            ->when($filters['slip_no'] ?? null, fn($q, $slipNo) => 
                $q->where('slip_no', 'like', "%{$slipNo}%")
            )
            ->when($filters['disposal_date_from'] ?? null, function ($q) use ($filters) {
                $from = Carbon::parse($filters['disposal_date_from'])->format('Y-m-d');
                $to = isset($filters['disposal_date_to']) ? Carbon::parse($filters['disposal_date_to'])->format('Y-m-d') : null;
                return $to ? $q->whereBetween('disposal_date', [$from, $to]) : $q->whereDate('disposal_date', $from);
            })
            ->when($filters['input_date_from'] ?? null, function ($q) use ($filters) {
                $from = Carbon::parse($filters['input_date_from'])->startOfDay();
                $to = isset($filters['input_date_to']) 
                    ? Carbon::parse($filters['input_date_to'])->endOfDay()
                    : $from->copy()->endOfDay(); // Same day, full range
            
                return $q->whereBetween('created_at', [$from, $to]);
            })
            ->orderByDesc('created_at');
    }

    /**
     * Updates a defect item record with the provided data.
     *
     * @param array $data An associative array containing the following keys:
     *                    - 'id' (int): The ID of the record to update.
     *                    - 'part_number' (string): The part number to update.
     *                    - 'quantity' (int): The quantity to update.
     *                    - 'slip_no' (string): The slip number to update.
     * @return bool True if the update was successful, false otherwise.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException If the record with the given ID is not found.
     */
    public static function updateDefectItem($data)
    {
        $record = self::findOrFail($data['id']);

        return $record->update([
            'part_number' => $data['part_number'],
            'quantity' => $data['quantity'],
            'slip_no' => $data['slip_no'],
        ]);
    }

    /**
     * Scope a query to filter machining defects for export based on various filters.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * Filters:
     * - product_code: Filter by product code (part number).
     * - process_code: Filter by process code.
     * - slip_no: Filter by slip number.
     * - disposal_date_from: Filter by disposal date range start.
     * - disposal_date_to: Filter by disposal date range end.
     * - input_date_from: Filter by input date range start.
     * - input_date_to: Filter by input date range end.
     */
    public function scopeMachiningDefectExport($query, $filters)
    {
        return $query->with(['product', 'process'])
            ->when($filters['product_code'] ?? null, fn($q, $partNumber) => $q->where('part_number', $partNumber))
            ->when($filters['process_code'] ?? null, fn($q, $processCode) => $q->where('process_code', $processCode))
            ->when($filters['slip_no'] ?? null, fn($q, $slipNo) => $q->where('slip_no', $slipNo))
            ->when($filters['disposal_date_from'] ?? null, function ($q) use ($filters) {
                $from = Carbon::parse($filters['disposal_date_from'])->format('Y-m-d');
                $to = $filters['disposal_date_to'] ?? null;
                return $to ? $q->whereBetween('disposal_date', [$from, Carbon::parse($to)->format('Y-m-d')])
                        : $q->whereDate('disposal_date', $from);
            })
            ->when($filters['input_date_from'] ?? null, function ($q) use ($filters) {
                $from = Carbon::parse($filters['input_date_from'])->format('Y-m-d');
                $to = $filters['input_date_to'] ?? null;
                return $to ? $q->whereBetween('created_at', [$from, Carbon::parse($to)->format('Y-m-d')])
                        : $q->whereDate('created_at', $from);
            });
    }
    
    public function productPrice()
    {
        return $this->belongsTo(ProductPrice::class, 'part_number', 'part_number');
    }

    public function generateRegistrationNo()
    {
      $latest = self::whereRaw('LENGTH(registration_no) = 10')->orderBy('id', 'DESC')->first();
      $prefix = date('ym'); // YYMM format
  
      return ($latest && substr($latest->registration_no, 0, 4) == $prefix)
        ? $prefix . sprintf("%06d", substr($latest->registration_no, 4) + 1)
        : $prefix . '000001';
    }
}

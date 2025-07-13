<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseArrival extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_no',
        'purchase_order_details_no',
        'branch_number',
        'arrival_day',
        'arrival_quantity',
        'slip_no',
        'unable_to_resharpen_flag',
        'remarks',
        'purchase_receipt_date',
        'purchase_record_no',
    ];

    protected $casts = [
        'arrival_day'               => 'date',
        'purchase_receipt_date'     => 'date',
        'created_at'                => 'date'
    ];

    public function requisition()
    {
        return $this->belongsTo(PurchaseRequisition::class, 'purchase_order_no', 'purchase_order_number');
    }

    /**
     * Retrieve records by order number and order details number.
     *
     * @param string $orderNumber The purchase order number.
     * @param string $orderDetailNumber The purchase order details number.
     * @return \Illuminate\Database\Eloquent\Collection The collection of matching records.
     */
    public static function getRecordByOrderNumberOrderDetailsNumber($orderNumber, $orderDetailNumber)
    {
        return self::where('purchase_order_no', $orderNumber)
                    ->where('purchase_order_details_no', $orderDetailNumber)
                    ->get();
    }

    public function scopeWhereDateRangeOrEqual($query, $columnName, $from, $to)
    {
        if ($from && $to) {
            return $query->whereBetween($columnName, [
                Carbon::parse($from)->format('Y-m-d'),
                Carbon::parse($to)->format('Y-m-d')
            ]);
        } elseif ($from) {
            return $query->whereDate($columnName, '>=', Carbon::parse($from)->format('Y-m-d'));
        } elseif ($to) {
            return $query->whereDate($columnName, '<=', Carbon::parse($to)->format('Y-m-d'));
        }
        return $query;
    }

    public static function createRecord($data, $branchNo, $arrivalDay, $newPurchaseRecordNo)
    {
        return self::create([
            'purchase_order_no' => $data['purchase_order_no'],
            'purchase_order_details_no' => $data['purchase_order_details_no'],
            'branch_number' => $branchNo,
            'arrival_day'   =>  Carbon::parse($arrivalDay),
            'arrival_quantity'  => $data['arrival_quantity'],
            'slip_no'   => $data['slip_no'],
            'unable_to_resharpen_flag' => $data['unable_to_resharpen_flag'] ?? 0,
            'remarks'   => $data['remarks'],
            'purchase_record_no'    => $newPurchaseRecordNo
        ]);
    }
}

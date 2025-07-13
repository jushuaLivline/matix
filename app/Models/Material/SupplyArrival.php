<?php

namespace App\Models\Material;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Configuration;
use App\Models\ProductNumber;
use Carbon\Carbon;
use App\Traits\PaginateWithLimit;


class SupplyArrival extends Model
{
  use HasFactory;
  
  // Trait for paginating with a record limit.
  use PaginateWithLimit;

  protected $guarded = [];
  protected $table = 'supply_material_arrivals';
  protected $casts = [
    'arrival_day' => 'date',
  ];

  protected $fillable = [
    'supply_material_receipt_no',
    'serial_number',
    'delivery_no',
    'voucher_class',
    'arrival_day',
    'flight_no',
    'supplier_code',
    'product_number',
    'material_manufacturer_code',
    'material_no',
    'line_code',
    'department_code',
    'arrival_quantity',
    'processing_rate',
  ];

  public function product()
  {
    return $this->belongsTo(ProductNumber::class, 'product_number', 'part_number');
  }

  public function configuration()
  {
    return $this->belongsTo(Configuration::class, 'product_number', 'child_part_number');
  }

  public static function boot()
  {
    parent::boot();

    self::creating(function ($model) {
      $model->created_at = date('Y-m-d H:i:s');
      $model->creator = auth()->user()->id;
    });

    // self::updating(function($model){
    //     $model->updated_at = date('Y-m-d H:i:s');
    //     $model->updator = auth()->user()->id;
    // });
  }

  public static function generateSupplyMaterialReceiptNo()
  {
    $latest = self::whereRaw('LENGTH(supply_material_receipt_no) = 10')->orderBy('id', 'DESC')->first();
    $prefix = date('ym'); // YYMM format

    return ($latest && substr($latest->supply_material_receipt_no, 0, 4) == $prefix)
      ? $prefix . sprintf("%06d", substr($latest->supply_material_receipt_no, 4) + 1)
      : $prefix . '000001';
  }
  public function generateSerialNumber()
  {
    $latest = self::whereRaw('LENGTH(serial_number) = 6')->orderBy('id', 'DESC')->first();
    $prefix = date('md'); // MMDD format (e.g., "0214" for Feb 14)

    return ($latest && substr($latest->serial_number, 0, 4) == $prefix)
      ? $prefix . sprintf("%02d", substr($latest->serial_number, 4) + 1)
      : $prefix . '01'; // Start from '001' if no existing record
  }

  public static function filterReturnRecords($request)
  {
    $voucherClass = $request['voucher_class'];
    $arrivalDateFrom = $request['arrival_day_from'];
    $arrivalDateTo = $request['arrival_day_to'];
    $deliveryNo = $request['delivery_no'];
    $materialManufacturer = $request['material_manufacturer_code'];
    $materialNo = $request['product_code'];
    $query = self::query()
      ->with('product')
      ->when($voucherClass == 2, function ($query) {
        return $query->where('voucher_class', 2);
      })
      ->when($voucherClass == 3, function ($query) {
        return $query->where('voucher_class', 3);
      })
      ->when($voucherClass == 1, function ($query) {
        return $query->whereIn('voucher_class', [2, 3]);
      })
      ->when($arrivalDateFrom, function ($query) use ($arrivalDateFrom, $arrivalDateTo) {
        if ($arrivalDateFrom) {
          return $query->whereBetween('arrival_day', [Carbon::parse($arrivalDateFrom)->format('Y-m-d'), Carbon::parse($arrivalDateTo)->format('Y-m-d')]);
        } else {
          return $query->where('arrival_day', Carbon::parse($arrivalDateFrom)->format('Y-m-d'));
        }
      })
      ->when(isset($deliveryNo), function ($query) use ($deliveryNo) {
        $query->where('delivery_no', 'LIKE', '%' . trim($deliveryNo) . '%');
      })
      ->when(isset($materialManufacturer), function ($query) use ($materialManufacturer) {
        $query->where('material_manufacturer_code', 'LIKE', "%{$materialManufacturer}%");
      })
      ->when(isset($materialNo), function ($query) use ($materialNo) {
        $query->where('material_no', $materialNo);
      })
      ->orderByDesc('created_at');

      return $query;
  }

}

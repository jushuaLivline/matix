<?php

namespace App\Services\Master;

use App\Models\ProductNumber;
use App\Models\ProductPrice;
use App\Models\ProcessUnitPrice;
use App\Models\Code;
use App\Models\Configuration;
use Illuminate\Support\Carbon;

class PartService
{
  public function getProductData($id)
  {
    return ProductNumber::with(['customer', 'supplier', 'department', 'line', 'manufacturer'])
      ->findOrFail($id);
  }

  public function getCodes()
  {
    return Code::where('division', '単位')->get();
  }

  public function getLatestProductPrice($part_number)
  {
    $today = now()->startOfDay()->toDateTimeString();

    return ProductPrice::where('effective_date', '<=', $today)
      ->where('part_number', $part_number)
      ->latest('effective_date')
      ->first()?->toArray();
  }

  public function getProcessPrices($part_number, $division)
  {
    $today = now()->startOfDay()->toDateTimeString();
    $processPrices = ProcessUnitPrice::getUnitPriceByDivision($today, $part_number);
    return $processPrices->has($division) ? $processPrices[$division]->first()?->toArray() : null;
  }

  public function getConfigurations($part_number)
  {
    return Configuration::where('parent_part_number', $part_number)
      ->where('delete_flag', '!=', 1)
      ->get();
  }
}

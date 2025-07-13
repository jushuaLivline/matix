<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Config;

trait PaginateWithLimit
{
  public function scopePaginateResults($query, $pageSize = null)
  {
    // Allow overriding max_records via URL parameter, defaulting to config value
    $maxRecords = request()->input('max_records', Config::get('search.max_records', 100));

    // Allow overriding page_size via URL parameter, defaulting to config value
    $pageSize = request()->input('page_size', $pageSize ?? Config::get('search.page_size', 20));

    $records = $query->limit($maxRecords)->get();
    $currentPage = Paginator::resolveCurrentPage() ?: 1;
    $recordsCollection = new Collection($records);
    $slicedRecords = $recordsCollection->slice(($currentPage - 1) * $pageSize, $pageSize)->values();

    return new LengthAwarePaginator(
      $slicedRecords,
      $recordsCollection->count(),
      $pageSize,
      $currentPage,
      [
        'path' => Paginator::resolveCurrentPath(),
        'query' => request()->query(),
      ]
    );
  }
}

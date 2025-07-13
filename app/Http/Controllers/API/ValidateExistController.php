<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ValidateExistController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        $model = $request->model;
        $reference = $request->reference;
        $column = $request->column;
        $value = $request->$reference;

        $extralQuery = match($model) {
            'supplier' => ['supplier_tag' => 1],
            'customer' => ['customer_flag' => 1],
            'ProductMaterial' => ['product_category' => 0],
            default => []
        };

        $databaseTable = match($model){ //override the actual table name
            'department'    => 'departments',
            'supplier'      => "customers",
            'customer'      => "customers",
            'ProductMaterial' => "product_numbers",
            default         => Str::of($model)->snake()->lower()->plural()
        };

        $hasDeleteFlag = Schema::hasColumn($databaseTable, 'delete_flag') ? true : false;
        $exists =  DB::table($databaseTable)
                        ->where($column, $value)
                        ->when($hasDeleteFlag, fn($query) => $query->where("delete_flag", 0))
                        ->when($extralQuery, fn($query) => $query->where($extralQuery))
                        ->exists();

        return response()->json($exists ? true : false);
    }
}

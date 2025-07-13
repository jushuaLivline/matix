<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LookUpAutoSearchController extends Controller
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
        $column = $request->column;
        $columnReturn = $request->name;
        $searchValue = $request->searchValue;

        $extralQuery = match($model) {
            'supplier' => ['supplier_tag' => 1],
            'customer' => ['customer_flag' => 1],
            default => []
        };

        $databaseTable = match($model){ //override the actual table name
            'supplier'      => "customers",
            'customer'      => "customers",
            default         => Str::of($model)->snake()->lower()->plural()
        };

       try{
            $hasDeleteFlag = Schema::hasColumn($databaseTable, 'delete_flag') ? true : false;
            $result = DB::table($databaseTable)
                            ->where($column, $searchValue)
                            ->when($hasDeleteFlag, fn($query) => $query->where("delete_flag", 0))
                            ->when($extralQuery, fn($query) => $query->where($extralQuery))
                            ->first();
                            
            return response()->json([
                'value' => $result && $columnReturn ? $result->$columnReturn : ""
            ], 200);
       }catch (Exception $exception){
            return response()->json([
                'autosearch_error' => $exception->getMessage()
            ], status: 422);
       }
        
    }
}

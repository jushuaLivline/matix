<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Material\SupplyArrival as SupplyMaterialArrival;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SupplyMaterialArrivalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'columns' => 'required|array',
        ]);

        $columns = $data['columns'];

        // Parse the arrival_day if it exists in the columns
        if (isset($columns['arrival_day'])) {
            $columns['arrival_day'] = Carbon::parse($columns['arrival_day'])->format('Y-m-d');
        }

        $supplyMaterialArrival = SupplyMaterialArrival::create($columns);

        return response()->json([
            'message' => '支給材情報は正常に更新されました',
            'data' => $supplyMaterialArrival
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'columns' => 'required|array',
        ]);

        $supplyMaterialArrival = SupplyMaterialArrival::findOrFail($id);

        if (!$supplyMaterialArrival) {
            return response()->json(['error' => 'SupplyMaterialArrival not found'], 404);
        }
    
        $columns = $data['columns'];
    
        // Parse the arrival_day if it exists in the columns
        if (isset($columns['arrival_day'])) {
            $columns['arrival_day'] = Carbon::parse($columns['arrival_day'])->format('Y-m-d');
        }
    
        $supplyMaterialArrival->update($columns);

        // Return the updated SupplyMaterialArrival as the API response
        return response()->json([
            'message' => '支給材情報は正常に更新されました',
            'data' => $supplyMaterialArrival
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $supplyMaterialArrival = SupplyMaterialArrival::findOrFail($id);

        if (!$supplyMaterialArrival) {
            return response()->json(['error' => 'SupplyMaterialArrival not found'], 404);
        }

        $supplyMaterialArrival->delete();

        return response()->json(['message' => 'SupplyMaterialArrival deleted successfully']);
    }
}

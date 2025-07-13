<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Material\SupplyOrder as SupplyMaterialOrder;
use App\Models\KanbanMaster;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SupplyMaterialOrderController extends Controller
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
        //
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
    
        $supplyMaterialOrder = SupplyMaterialOrder::findOrFail($id);
    
        if (!$supplyMaterialOrder) {
            return response()->json(['error' => 'SupplyMaterialOrder not found'], 404);
        }
    
        $columns = $data['columns'];

        if (isset($columns['management_no'])) {
            $exists = KanbanMaster::where('management_no', $columns['management_no'])->exists();
            if (!$exists) {
                return response()->json(['management_number_error' => '管理番号が存在しません!'], 404);
            }
        }

        // Parse the arrival_day if it exists in the columns
        if (isset($columns['instruction_date'])) {
            $columns['instruction_date'] = Carbon::parse($columns['instruction_date'])->format('Y-m-d');
        }
    
        $supplyMaterialOrder->update($columns);

        if (isset($columns['number_of_accomodated'])) {
            $kanbanMaster = KanbanMaster::where('management_no', $supplyMaterialOrder->management_no)->first();
            
            if ($kanbanMaster) {
                $kanbanMaster->update(['number_of_accomodated' => $columns['number_of_accomodated']]);
            }
        }

        // Return the updated SupplyMaterialOrder as the API response
        return response()->json([
            'message' => '支給材情報は正常に更新されました',
            'data' => $supplyMaterialOrder
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
        $supplyMaterialOrder = SupplyMaterialOrder::findOrFail($id);

        if (!$supplyMaterialOrder) {
            return response()->json(['error' => 'SupplyMaterialOrder not found'], 404);
        }

        $supplyMaterialOrder->delete();

        return response()->json(['message' => 'SupplyMaterialOrder deleted successfully']);
    }
}

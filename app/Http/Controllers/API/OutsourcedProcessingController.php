<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\OutsourcedProcessing;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OutsourcedProcessingController extends Controller
{/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $outsourcedProcessings = OutsourcedProcessing::all();
        return response()->json($outsourcedProcessings);
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
        
        // Remove the fields you want to disregard
        unset($columns['number_of_accomodated']);
        unset($columns['uniform_number']);
        
        $outsourcedProcessing = OutsourcedProcessing::create($columns);

        return response()->json([
            'message' => 'stored successfully',
            'data' => $outsourcedProcessing
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
        $outsourcedProcessing = OutsourcedProcessing::findOrFail($id);
        return response()->json($outsourcedProcessing);
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
        // Retrieve the updated column values from the request
        $columns = $request->input('columns');

        // Remove unwanted columns
        unset($columns['number_of_accomodated']);
        unset($columns['uniform_number']);
        unset($columns['product_name']);
        unset($columns['supplier_name']);

        // Format instruction_date as Carbon date (Y-m-d)
        if (isset($columns['instruction_date'])) {
            $instructionDate = Carbon::createFromFormat('Ymd', $columns['instruction_date']);
            $columns['instruction_date'] = $instructionDate->format('Y-m-d');
        }
        
        // Update the specific record with the new values
        $record = OutsourcedProcessing::findOrFail($id);
        if ($record) {
            foreach ($columns as $column => $value) {
                $record->$column = $value;
            }
            
            $record->save();

            // Return a success response
            return response()->json(['message' => 'Data updated successfully'], 200);
        }

        // Return an error response if the record is not found
        return response()->json(['error' => 'Record not found'], 404);
    }
    
    public function updateData(Request $request, $id)
    {
         // Retrieve the updated column values from the request
         $columns = $request->input('columns');
    
         // Update the specific record with the new values
         $record = OutsourcedProcessing::findOrFail($id);
         if ($record) {
             foreach ($columns as $column => $value) {
                 $record->$column = $value;
             }
             
             $record->save();
     
             // Return a success response
             return response()->json(['message' => 'Data updated successfully'], 200);
         }
     
         // Return an error response if the record is not found
         return response()->json(['error' => 'Record not found'], 404);
    }


    public function destroy($id)
    {
        $outsourcedProcess = OutsourcedProcessing::findOrFail($id);

        if (!$outsourcedProcess) {
            return response()->json(['error' => 'OutsourcedProcessing not found'], 404);
        }

        $outsourcedProcess->delete();

        return response()->json([
            'message' => 'OutsourcedProcessing has been successfully deleted.',
        ]);
    }

}

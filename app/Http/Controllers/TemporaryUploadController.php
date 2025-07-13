<?php

namespace App\Http\Controllers;

use App\Models\EquipmentFileUpload;
use App\Models\TemporaryUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TemporaryUploadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (str_contains($request->form, "equipment-inspection")) {
            $equipmentFile = EquipmentFileUpload::where('user_id', $request->user_id)->where('id', $request->file_id)->first();

            if (!empty($equipmentFile)) {
                return [
                    'name' => $equipmentFile->file_name,
                    'size' => $equipmentFile->size,
                    'file' => $equipmentFile->file,
                ];
            } else {
                return [];
            }
        } else {
            return TemporaryUpload::query()
                ->select('file_name', DB::raw("LENGTH(file) as file_size"))
                ->where([
                    'form' => $request->form,
                    'user_id' => $request->user_id,
                ])->get()->map(function($q){
                    return [
                        'name' => $q->file_name,
                        'size' => $q->file_size
                    ];
                }) ?? [];
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        $temp = TemporaryUpload::create([
            'file_name' =>  $request->file->getClientOriginalName(),
            'file' => base64_encode(file_get_contents($request->file)),
            'form' => $request->form,
            'user_id' => $request->user_id,
        ]);

        return response()->json([
            'file_name' => $temp->file_name,
            'form' => $temp->form,
            'user_id' => $temp->user_id,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\TemporaryUpload  $temporaryUpload
     * @return \Illuminate\Http\Response
     */
    public function show(TemporaryUpload $temporaryUpload)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\TemporaryUpload  $temporaryUpload
     * @return \Illuminate\Http\Response
     */
    public function edit(TemporaryUpload $temporaryUpload)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TemporaryUpload  $temporaryUpload
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TemporaryUpload $temporaryUpload)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\TemporaryUpload  $temporaryUpload
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id = 0)
    {
        TemporaryUpload::where('file_name', $request->file_name)
                ->where("user_id", $request->user_id)
                ->delete();
        
        return response([
            'message' => 'success'
        ], 200);
    }
}

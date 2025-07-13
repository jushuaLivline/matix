<?php

namespace App\Http\Controllers\Estimate\Request;

use App\Http\Controllers\Controller;
use App\Http\Requests\Estimate\Request\RequestCreate;
use App\Models\Estimate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateController extends Controller
{
    public function index(Request $request)
    {
        return redirect()->route('estimate.requestCreate.create');
    }
    
    public function create(Request $request)
    {
        return view("pages.estimate.request.create.index");
    }
    
    public function store(RequestCreate $request)
    {
        DB::beginTransaction();
        try {
            Estimate::insert($request->validated());
            DB::commit();

            return redirect()->route('estimate.requestCreate.create')->with('success', '見積依頼の登録が完了しました');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error occurred while storing estimate.', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'timestamp' => now(),
            ]);

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function store_file(RequestCreate $request)
    {

        $file = $request->file('file');
        $filename = now()->format('YmdHis') . '.' . $file->extension();
        $path = $file->storeAs('public/estimate', $filename);

        return response()->json(['name' => $filename, 'path' => $path]);
    }
}

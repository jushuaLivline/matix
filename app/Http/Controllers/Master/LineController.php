<?php

namespace App\Http\Controllers\Master;

use App\Exports\LinesExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Master\LineRequest;
use App\Models\Line;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;

class LineController extends Controller
{
    public function index(Request $request)
    {
        $lines = Line::query()->search($request)->paginateResults(20);

        return view('pages.master.line.index', compact('lines'));
    }
    public function excel_export(Request $request)
    {
        $lineResults = Line::query()->search($request)->paginate($request->per_page ?? 20);

        $fileName = 'ラインマスタ一覧_'.now()->format('Ymd').'.xlsx';
        return Excel::download(new LinesExport($lineResults), $fileName);  
  
    }
    
    public function create()
    {
        $line = null;
        return view("pages.master.line.create", compact('line'));
    }

    public function check_line_code(Request $request)
    {
        $line_code = $request->query('line_code');
        $exists = Line::checkIfLineCodeExists($line_code);

        return response()->json(!$exists);
    }

    public function store(LineRequest $request)
    {
        DB::beginTransaction();
        try {
            if (empty($request->input('id'))) {
                Line::insert($request->validated());
                $route = route('master.masterLine.create');
                $message = 'ラインマスタの登録が完了しました';
            } else {
                $line = Line::findOrFail($request->input('id'));
                $line->update($request->validated());
                $route = route('master.masterLine.edit', $request->input('id'));
                $message = 'ラインマスタの更新が完了しました';
            }

            DB::commit();

            return redirect()->to($route)->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error occurred while storing line master.', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'timestamp' => now(),
            ]);

            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function copy_previous_input() {
        $creator = request()->user()->employee_code;
        $line = Line::getLastLineByCreator($creator);
    
        return view('pages.master.line.create', compact('line'));
    }

    public function edit($id)
    {
        $line = Line::getLineById($id);
        return view("pages.master.line.edit", compact('line'));
    }
    public function delete(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            if ($id) {
                $line = Line::find($id);
                $line->delete();

                DB::commit();
            }

            if ($request->ajax()) {
                session()->flash('success', 'ラインマスタの削除が完了しました');
                return response()->json(['status' => 'success', 'message' => 'ラインマスタの削除が完了しました']);
            }
            return redirect()->route('master.masterLine.index')->with('success', 'ラインマスタの削除が完了しました');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error occurred while deleting line master.', [
                'error' => $e->getMessage(),
                'request' => $request->all(),
                'timestamp' => now(),
            ]);

            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
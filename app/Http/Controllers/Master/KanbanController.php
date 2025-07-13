<?php
namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Exports\Master\KanbanExport;
use App\Models\KanbanMaster;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Master\KanbanRequest;
use Illuminate\Support\Facades\Auth;

class KanbanController extends Controller
{
    public function index(Request $request)
    {
        $filter = array_filter([
            'management_no' => $request->input('management_no'),
            'part_number' => $request->input('part_number'),
            'kanban_classification' => $request->input('kanban_classification'),
            'delete_flag' => $request->input('delete_flag'),
        ], fn($value) => $value !== null); // Exlude only null since some false value is = 0

        $kanban_records = KanbanMaster::where($filter)
                                ->orderByDesc('created_at')
                                ->paginateResults(10);
        //Log::info('Records Fetched:', ['data' => $kanban_records->toArray()]);

        return view('pages.master.kanban.index', compact('kanban_records'));
    }

    public function excel_export(Request $request)
    {
        $filter = array_filter([
            'management_no' => $request->input('management_no'),
            'part_number' => $request->input('part_number'),
            'kanban_classification' => $request->input('kanban_classification'),
            'delete_flag' => $request->input('delete_flag'),
        ], fn($value) => $value !== null);

        $kanban_records = KanbanMaster::where($filter)->paginateResults(10);

        $exportData = $kanban_records->map(function ($kanban) {
            $cleanedPartNumber = preg_replace('/\D/', '', $kanban->part_number);
            $partChunks = [
                substr($cleanedPartNumber, 0, 4),
                substr($cleanedPartNumber, 4, 6),
                substr($cleanedPartNumber, 10, 4)
            ];
            $formattedPartNumber = implode('-', array_filter($partChunks));

            switch ($kanban->kanban_classification) {
                case 1:
                    $formattedKanbanClassification = "支給材";
                    break;
                case 2:
                    $formattedKanbanClassification = "外注加工";
                    break;
                case 3:
                    $formattedKanbanClassification = "外注支給";
                    break;
                case 4:
                    $formattedKanbanClassification = "社内";
                    break;
                default:
                    $formattedKanbanClassification = "";
            }

            return [
                $kanban->management_no,
                $formattedPartNumber,
                $formattedKanbanClassification,
            ];
        });

        $fileName = 'かんばんマスタ一覧' . now()->format('Ymd') . '.xlsx';
        return Excel::download(new KanbanExport($exportData), $fileName, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function create(Request $request) 
    {
        return view('pages.master.kanban.edit');
    }

    public function store(KanbanRequest $request)
    {
        try {
            DB::beginTransaction(); 

            $data = $request->validated();
            $data['creator'] = auth()->id();
            $kanban = KanbanMaster::create($data);
            
            DB::commit(); 

            return response()->json([
                'status' => 'success',
                'message' => 'かんばんマスタが正常に登録されました。',
                'kanban' => $kanban,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Kanban master creation failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id) {
        $data = KanbanMaster::where('id',$id)
            ->with('product','process','next_process')
            ->get()->first();

        //Log::info('Kanban Master Info:', $data->toArray());

        return view('pages.master.kanban.edit', compact('data'));
    }

    public function update(KanbanRequest $request, $id) {
        try {
            DB::beginTransaction(); 
            
            $kanban = KanbanMaster::findOrFail($id);

            $data = $request->validated();
            $data['updator'] = auth()->id();

            $kanban->update($data);

            DB::commit(); 

            return response()->json([
                'status' => 'success',
                'message' => 'かんばんマスタが正常に更新されました',
                'kanban' => $kanban,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kanban master update failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function get_previous_input(Request $request){

        $id = Auth::id();
        $data = KanbanMaster::where('creator', $id)
        ->with('product', 'process', 'next_process')
        ->latest('created_at')
        ->first();

        return response()->json([
            'status' => 'success',
            'kanban' => $data,
        ], 201);
    }

    public function destroy(KanbanRequest $request, $id){
        try {
           
            DB::beginTransaction(); 
            
            $kanban = KanbanMaster::findOrFail($id);

            $data = $request->validated();
            $data['updator'] = auth()->id();

            $kanban->update($data);

            DB::commit(); 

            return response()->json([
                'status' => 'success',
                'message' => 'カンバンマスターが正常に削除されました',
                'kanban' => $kanban,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kanban master deletion failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
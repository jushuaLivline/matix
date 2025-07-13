<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Customer;

use App\Exports\Master\ProjectExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Project;
use App\Http\Requests\Master\ProjectRequest;

class ProjectController extends Controller
{
    public function index(Request $request)
    {

        $project_name = $request->input('project_name');
        $delete_flag = $request->input('delete_flag');

        $project_records = Project::when($project_name, function ($query, $name) {
                $query->where('project_name', 'like',  $name . '%');
            })
            ->when($delete_flag !== null, function ($query) use ($delete_flag) {
                $query->where('delete_flag', $delete_flag);
            })
            ->orderByDesc('created_at')
            ->paginateResults(10);


        //Log::info('Records Fetched:', ['data' => $project_records]);

        return view('pages.master.project.index', compact('project_records'));
    }

    public function excel_export(Request $request)
    {
        $filter = array_filter([
            'project_name' => $request->input('project_name'),
            'delete_flag' => $request->input('delete_flag'),
        ], function ($value) {
            return $value !== null || $value === 0;
        });

        $project_records = Project::where($filter)->paginateResults(10);

        $currentPageRecords = collect($project_records->items());

        $exportData = $currentPageRecords->map(function ($project) {
            return [
                $project->project_number,
                $project->project_name,
            ];
        });
        
        $fileName = 'プロジェクトマスタ一覧' . now()->format('Ymd') . '.xlsx';
        return Excel::download(new ProjectExport($exportData), $fileName, \Maatwebsite\Excel\Excel::XLSX);
    }

    public function create(Request $request) 
    {
        return view('pages.master.project.edit');
    }

    public function store(ProjectRequest $request)
    {
        try {
            DB::beginTransaction(); 

            $project = Project::create($request->validated());

            DB::commit(); 

            return response()->json([
                'status' => 'success',
                'message' => 'プロジェクトが正常に作成されました。',
                'project' => $project,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Project creation failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id) {
        $data = Project::where('id',$id)
          ->get()->first();

        return view('pages.master.project.edit', compact('data'));
    }

    public function update(ProjectRequest $request, $id) {
        try {
            DB::beginTransaction(); 
            
            $project = Project::findOrFail($id);
    
            $project->update($request->validated());
    
            DB::commit(); 
    
            return response()->json([
                'status' => 'success',
                'message' => 'プロジェクトが正常に更新されました',
                'project' => $project,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Project update failed: ' . $e->getMessage());
    
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Project $project){
        try {
            DB::beginTransaction(); 

            $project->delete();

            DB::commit(); 

            return redirect()->route("master.project.index")->with("success", 'プロジェクトは正常に削除されました');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Project delete flag update failed: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
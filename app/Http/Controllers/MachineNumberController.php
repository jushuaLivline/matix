<?php

namespace App\Http\Controllers;

use App\Constants\MachineNumberConstant;
use App\Exports\MachineNumbersExport;
use App\Models\MachineNumber;
use App\Models\Project;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MachineNumberController extends Controller
{
    public function index(Request $request)
    {
        if($request->session()->has('mn_last_input'))
        {
            $request->session()->forget('mn_last_input');
        }
        $data = [];
        $count = 0;
        if ($request->all()) {
            if ($request->project_number != null && $request->project_name == null) {
                $project_model = Project::where('project_number', $request->project_number)->where('delete_flag', '0')->first();
                $request->merge(['project_name' => $project_model->project_name]);
            } else if ($request->project_number == null)  {
                $request->merge(['project_name' => null]);
            }

            $query = MachineNumber::query()
                    ->when($request->machine_number_name, function($query) use ($request) {
                        $query->where("machine_number_name", "LIKE", "%". $request->machine_number_name . "%");
                    })
                    ->when($request->project_number, function($query) use ($request) {
                        $query->where("project_number", "LIKE", "%". $request->project_number . "%");
                    })
                    ->when($request->line_name, function($query) use ($request) {
                        $query->where("line_name", "LIKE", "%". $request->line_name . "%");
                    })
                    ->when($request->machine_division, function($query) use ($request) {
                        $machine_division = $request->machine_division - 1;
                        $query->where("machine_division", $machine_division);
                    })
                    ->when($request->remarks, function($query) use ($request) {
                        $query->where("remarks", "LIKE", "%". $request->remarks . "%");
                    })
                    ->when($request->completion_date, function($query) use ($request) {
                        if ($request->completion_date == 1) {
                            $query->whereRaw("completion_date <= NOW()");
                        } else if ($request->completion_date == 2) {
                            $query->whereRaw("completion_date >= NOW()");
                        }
                    })
                    ->where(function ($query) use ($request) {
                        $branch_number_from = $request->branch_number_from != null ? $request->branch_number_from : 0;
                        $branch_number_to = $request->branch_number_to != null ? $request->branch_number_to : 0;
                        if ($request->machine_number_from != null && $request->machine_number_to != null) {
                            $query->whereRaw('(machine_number + branch_number BETWEEN ('.$request->machine_number_from.' + '.$request->branch_number_from.') AND ('.$request->machine_number_to.' + '.$request->branch_number_to.'))');
                        } else if ($request->machine_number_from != null && $request->machine_number_to == null) {
                            $query->whereRaw('machine_number + branch_number = '.$request->machine_number_from.' + '.$branch_number_from.'');
                        } else if ($request->machine_number_from == null && $request->machine_number_to != null) {
                            $query->whereRaw('machine_number + branch_number = '.$request->machine_number_to.' + '.$branch_number_to.'');
                        }
                    })
                    ->where(function($query) use ($request) {
                        if ($request->delete_flag != '2') {
                            $query->where("delete_flag", $request->delete_flag);
                        }
                    });
                    // 18AI2010
            $count = (clone $query)->count();

            $data = $query->paginate(20);
            foreach ($data as $key => $value) {
                $value->created_at = date('Y/m/d', strtotime($value->created_at));
                $value->drawing_date = $value->drawing_date != null ? date('Y/m/d', strtotime($value->drawing_date)) : '';
                $value->completion_date = $value->completion_date != null ? date('Y/m/d', strtotime($value->completion_date)) : '';
                $value->machine_division = MachineNumberConstant::MACHINE_DIVISION[$value->machine_division];
            }
            $parameters = [
                'machine_number_name' => $request->machine_number_name,
                'project_name' => $request->project_name,
                'project_number' => $request->project_number,
                'branch_number_from' => $request->branch_number_from,
                'branch_number_to' => $request->branch_number_to,
                'machine_number_from' => $request->machine_number_from,
                'machine_number_to' => $request->machine_number_to,
                'line_name' => $request->line_name,
                'machine_division' => $request->machine_division,
                'remarks' => $request->remarks,
                'completion_date' => $request->completion_date,
                'delete_flag' => $request->delete_flag
            ];
            $data->appends($parameters);
        }
        $compact = [
            'data' => $data,
            'count' => $count,
            'machineDivision' => [
                1 => '自社製設備機械・工具',
                2 => '購入機械',
                3 => 'その他',
                4 => '試作・ライン治具',
                5 => '外販機械'
            ]
        ];
        return view('pages.master.machine_numbers.index', $compact);
    }

    public function machineNumberSearch(Request $request)
    {
        return redirect()->route('master.machineNumbers.index', [
            'machine_number_name' => $request->machine_number_name,
            'project_name' => $request->project_name,
            'project_number' => $request->project_number,
            'branch_number_from' => $request->branch_number_from,
            'branch_number_to' => $request->branch_number_to,
            'machine_number_from' => $request->machine_number_from,
            'machine_number_to' => $request->machine_number_to,
            'line_name' => $request->line_name,
            'machine_division' => $request->machine_division,
            'remarks' => $request->remarks,
            'completion_date' => $request->completion_date,
            'delete_flag' => $request->delete_flag
        ]);
    }

    public function exportCSV(Request $request)
    {
        $machineNumber = MachineNumber::query()
                        ->when($request->machine_number_name, function($query) use ($request) {
                            $query->where("machine_number_name", "LIKE", "%". $request->machine_number_name . "%");
                        })
                        ->when($request->project_number, function($query) use ($request) {
                            $query->where("project_number", "LIKE", "%". $request->project_number . "%");
                        })
                        ->when($request->line_name, function($query) use ($request) {
                            $query->where("line_name", "LIKE", "%". $request->line_name . "%");
                        })
                        ->when($request->machine_division, function($query) use ($request) {
                            $query->where("machine_division", "LIKE", "%". $request->machine_division . "%");
                        })
                        ->when($request->remarks, function($query) use ($request) {
                            $query->where("remarks", "LIKE", "%". $request->remarks . "%");
                        })
                        ->when($request->delete_flag, function($query) use ($request) {
                            $query->where("delete_flag", $request->delete_flag);
                        })
                        ->selectRaw('
                            machine_number,
                            machine_number_name,
                            project_number,
                            line_name,
                            machine_division,
                            created_at,
                            drawing_date,
                            completion_date,
                            manager,
                            remarks
                            ')
                        ->get();
        foreach ($machineNumber as $key => $value) {
            $value->created_at = date('Y/m/d', strtotime($value->created_at));
            $value->drawing_date = $value->drawing_date != null ? date('Y/m/d', strtotime($value->drawing_date)) : '';
            $value->completion_date = $value->completion_date != null ? date('Y/m/d', strtotime($value->completion_date)) : '';
            $value->machine_division = MachineNumberConstant::MACHINE_DIVISION[$value->machine_division];
        }
        $fileName = '機番マスタ一覧.xlsx';
        return Excel::download(new MachineNumbersExport($machineNumber), $fileName);
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Models\MstLine;
use App\Models\Line;
use App\Models\InspectionItem;
use App\Models\MstDepartment;
use App\Models\DailyProductionControl;
use App\Models\MstProduct;
use Illuminate\Database\Query\JoinClause;

class DailyProductionControlController extends Controller
{
    protected $limit = 24;
    protected $adminPrefix = 'admin';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dailyReportEdit()
    {
        $year = date('Y');
        $month = date('m');
        $user = Auth::user();
        $user_name = $user->employee_name;
        $user_id = $user->user_id;
        $admin = $user->id;
        $post = DailyProductionControl::where('year', $year)
            ->where('month', $month)
            ->first();
        if (! $post) {
            $dataInsert = [
                'year' => $year,
                'month' => $month,
                'created_by' => $admin,
                'updated_by' => $admin,
            ];
            $post = DailyProductionControl::create($dataInsert);
        }
        $listDaily = array();
        $year = $post->year;
        $month = $post->month;
        $id = $post->id;
        $line_id = $post->line_id;
        $daily_date = $year . '年 '.$month.' 月度';
        for ($d = 1; $d <= 31; $d++) {
            $time = mktime(12, 0, 0, $month, $d, $year);
            if (date('m', $time) == $month) {
                $listDaily[] = [
                    'index' => $d, 'day' => date('Y-m-d-D', $time),
                    'num_calculator' => '', 'num_input' => '',
                ];
            }
        }
        // dd($listDaily);
        $breadcrumb = [
            'h1' => '日々生産管理表 入力',
            // 'li' => [
            //     'link' => '/' . $this->adminPrefix . '/equipment-inspection/list',
            //     'name' => '設備点検票 一覧'
            // ],
            // 'active' => '設備点検票 登録',
        ];
        
        $inspectionItem = InspectionItem::select('id', 'inspection_item_set')
            ->orderBy('id', 'ASC')->limit(200)->get();
        $lines = Line::select('id', 'line_code', 'line_name')
            ->where('delete_flag', 0)
            ->orderBy('id', 'ASC')->limit(1)->get();
        $departments = MstDepartment::select('id', 'code', 'name')->where('delete_flag', 0)
            ->orderBy('id', 'ASC')->limit(1)->get();
        $products = MstProduct::select('id', 'part_number as model', 'product_name as name', 'name_abbreviation as short_name', 'material_manufacturer_code as code')
            ->where('delete_flag', 0)
            ->orderBy('id', 'ASC')->limit(1)->get();
        return view('backend.daily-production-control-table-edit', compact('breadcrumb', 'id', 'user_name', 'inspectionItem', 'lines', 'departments', 'products', 'listDaily', 'daily_date', 'year', 'month', 'line_id'));
    }

    protected function dailyReportList(Request $request)
    {
        $method = $request->method;
        if ($method === 'SEARCH') {
            return $this->dailyReportSearch($request);
        }

        if ($method === 'SEARCH-PRODUCT') {
            return $this->searchProduct($request);
        }

        if ($method === 'SEARCH-LINE') {
            return $this->searchLine($request);
        }

        if ($method === 'SEARCH-DEPARTMENT') {
            return $this->searchDepartment($request);
        }
        $items = [];
        $links = '';
        $status = 'error';
        $message = 'error';
        return response()->json(compact('status', 'message'));
        // $blogs = EquipmentInspection::with('updater')
        //     ->orderBy('id', 'ASC')->paginate($this->limit);
        // if (count($blogs)) {
        //     $blogs->withPath('/'.$this->adminPrefix.'/equipment-inspection/list');
        //     $status = 'success';
        //     $message = 'success';
        //     foreach($blogs as $key => $value) {
        //         $line = $value->facilityLine;
        //         $confirmed = $value->confirmed;
        //         $approved = $value->approved;
        //         $line_code = isset($line->line_code) ? $line->line_code : '';
        //         $line_name = isset($line->line_name) ? $line->line_name : '';
        //         $status = '作成'; // created
        //         $confirmed_name = '';
        //         $approved_name = '';
        //         if (isset($confirmed->employee_name)) {
        //             $status = '確認'; // confirmed
        //             $confirmed_name = $confirmed->employee_name;
        //         }
        //         if (isset($approved->employee_name)) {
        //             $status = '承認'; // approved
        //             $approved_name = $approved->employee_name;
        //         }
        //         $item = [
        //             'id' => $value->id,
        //             'mst_department_id' => $value->mst_department_id,
        //             'inspection_line_id' => $value->inspection_line_id,
        //             'mst_basic_id' => $value->mst_basic_id,
        //             'process_id' => $value->process_id,
        //             'line_image' => $value->line_image,
        //             'year' => $value->year,
        //             'month' => $value->month,
        //             'line_code' => $line_code,
        //             'line_name' => $line_name,
        //             'confirmed_name' => $confirmed_name,
        //             'approved_name' => $approved_name,
        //             'status' => $status,
        //             'updated_name' => isset($value->updater->employee_name) ? $value->updater->employee_name : '',
        //             'created_name' => isset($value->creater->employee_name) ? $value->creater->employee_name : '',
        //             'updated_at' => date('Y-m-d H:i:s', strtotime($value->updated_at)),
        //             'created_at' => date('Y-m-d H:i:s', strtotime($value->created_at)),
        //         ];
        //         $items[] = $item;
        //     }
        //     // $items = $blogs->items();
        //     $links = (string) $blogs->links('partials.pagination-bootstrap-4');
        // }
        // $data = ['links'=> $links, 'items' => $items];
        // return response()->json(compact('status', 'message', 'data'));
    }

    protected function dailyReportSearch(Request $request)
    {
        $id = $request->get('id');
        $year = $request->get('year');
        $month = $request->get('month');
        $line_id = $request->get('line_id');
        $line_id = $line_id ? intval($line_id) : null;
        $data = [];
        $status = 'error';
        $message = 'Not found equipment inspection';
        $user = Auth::user();
        $user_name = $user->employee_name;
        $user_id = $user->user_id;
        $admin = $user->id;
        $blogs = null;
        $blogs = DailyProductionControl::where('line_id', $line_id)
            ->where('year', $year)
            ->where('month', $month);
        // if ($id) {
        //     $blogs = $blogs->where('id', $id);
        // }
        $blogs = $blogs->first();
        if (!$blogs) {
            $blogs = DailyProductionControl::where('line_id', 0)
                ->where('year', $year)
                ->where('month', $month)->first();
            if (!$blogs) {
                $blogs = DailyProductionControl::whereNull('line_id')
                    ->where('year', $year)
                    ->where('month', $month)->first();
                // lvt@20230704 fix initial line_id empty
                if ($blogs && $line_id) {
                    $blogs->line_id = $line_id;
                    $blogs->save();
                }
            } else {
                if ($blogs && $line_id) {
                    $blogs->line_id = $line_id;
                    $blogs->save();
                }
            }
        }
        if (!$blogs) {
            $dataInsert = [
                'line_id' => $line_id,
                'year' => $year,
                'month' => $month,
                'created_by' => $admin,
                'updated_by' => $admin,
            ];
            $blogs = DailyProductionControl::create($dataInsert);
        }
        if ($blogs) {
            $status = 'success';
            $message = 'success';
            $json_data = $this->dailyProductionControlResult($request, $blogs);
            $data = [
                'id' => $blogs->id,
                'year' => $blogs->year,
                'month' => $blogs->month,
                // 'base64_image' => $blogs->getBase64Image(),
                'json_data' => $json_data,
                'created_name' => isset($blogs->creater->employee_name) ? $blogs->creater->employee_name : '',
                // 'confirmed_name' => isset($blogs->confirmed->employee_name) ? $blogs->confirmed->employee_name : '',
                // 'approved_name' => isset($blogs->approved->employee_name) ? $blogs->approved->employee_name : '',
                // 'completed_name' => isset($blogs->completed->employee_name) ? $blogs->completed->employee_name : '',
                'updated_at' => date('Y-m-d H:i:s', strtotime($blogs->updated_at)),
            ];
        }
        return response()->json(compact('status', 'message', 'data'));
    }

    protected function searchProduct(Request $request)
    {
        $search = $request->get('search');
        $results = [];
        $status = 'error';
        $message = 'Not found product';
        $products = MstProduct::select('product_numbers.id', 
                                    'part_number as model', 
                                    'product_name as name', 
                                    'product_numbers.name_abbreviation as short_name', 
                                    'material_manufacturer_code as code', 
                                    'departments.name as department_name')
                                ->leftjoin('departments', 'product_numbers.department_code', '=', 'departments.code');

        if ($request->search) {
            $products = $products->where(function ($query) use ($search) {
                return $query->where('part_number', 'LIKE', '%'.trim($search).'%')
                    ->orWhere('product_name', 'LIKE', '%'.trim($search).'%');
            });
        }

        if ($request->line_code) {
            $products = $products->where('line_code', $request->line_code);
        }

        $products = $products->where('product_numbers.delete_flag', 0)
            ->orderBy('id', 'ASC')->get();

        if (count($products)) {
            $status = 'success';
            $message = 'Product list';
            foreach ($products as $product) {
                $results[] = [
                    'id' => $product->id,
                    'text' => '['.$product->model . ']' . $product->name,
                    'source' => json_encode(
                        [
                            'code' => $product->code, 
                            'model' => $product->model, 
                            'name' => $product->name, 
                            'short_name' => $product->short_name,
                            'department_name' => $product->department_name
                        ]),
                ];
            }
        }
        // $pagination = ['more' => true];
        return response()->json(compact('results'));
    }

    protected function searchLine(Request $request)
    {
        $search = $request->get('search');
        $results = [];
        $status = 'error';
        $message = 'Not found line';
        $lines = Line::select('id', 'line_code as code', 'line_name as name');
        if ($request->search) {
            $lines = $lines->where(function ($query) use ($search) {
                return $query->where('line_code', 'LIKE', '%'.trim($search).'%')
                    ->orWhere('line_name', 'LIKE', '%'.trim($search).'%');
            });
        }
        $lines = $lines->where('delete_flag', 0)
            ->orderBy('id', 'ASC')->limit(200)->get();
        if (count($lines)) {
            $status = 'success';
            $message = 'Line list';
            foreach ($lines as $line) {
                $results[] = [
                    'id' => $line->id,
                    'text' => '['.$line->code . ']' . $line->name,
                    'source' => json_encode(['code' => $line->code, 'name' => $line->name]),
                ];
            }
        }
        // $pagination = ['more' => true];
        return response()->json(compact('results'));
    }

    protected function searchDepartment(Request $request)
    {
        $search = $request->get('search');
        $results = [];
        $status = 'error';
        $message = 'Not found department';
        $departments = MstDepartment::select('id', 'code', 'name');
        if ($request->search) {
            $departments = $departments->where(function ($query) use ($search) {
                return $query->where('code', 'LIKE', '%'.trim($search).'%')
                    ->orWhere('name', 'LIKE', '%'.trim($search).'%');
            });
        }
        $departments = $departments->where('delete_flag', 0)
            ->orderBy('id', 'ASC')->limit(1)->get();
        if (count($departments)) {
            $status = 'success';
            $message = 'Department list';
            foreach ($departments as $department) {
                $results[] = [
                    'id' => $department->id,
                    'text' => '['.$department->code . ']' . $department->name,
                    'source' => json_encode(['code' => $department->code, 'name' => $department->name]),
                ];
            }
        }
        // $pagination = ['more' => true];
        return response()->json(compact('results'));
    }

    protected function dailyProductionControlResult(Request $request, $daily)
    {
        $id = $daily->id;
        $lineId = null;
        $machines = null;
        $line_code = null;
        $line_name = null;
        $json_data = [];

        if ($daily && isset($daily->json_data) && $daily->json_data) {
            $daily_data = json_decode($daily->json_data, true);
            if (isset($daily_data['dailyDataTable'])) {
                $json_data = $daily_data['dailyDataTable'];
                unset($daily_data['dailyDataTable']);
                foreach ($daily_data as $key => $value) {
                    if ($key === 'deadtime' && isset($value['cleaning_endofwork)'])) {
                        $value['cleaning_endofwork'] = $value['cleaning_endofwork)'];
                        unset($value['cleaning_endofwork)']);
                    }
                    
                    $json_data[$key] = $value;
                }
            }
        }

        if ($daily && isset($daily->line_id) && $daily->line_id) {
            $lineId = $daily->line_id;
            $lines = Line::where('id', $lineId)->where('delete_flag', 0)->first();
            $line_code = isset($lines->line_code) ? $lines->line_code : null;
            $line_name = isset($lines->line_name) ? $lines->line_name : null;
            $lines = MstLine::where('line_id', $lineId)->first();
            if ($lines && isset($lines->json_data) && $lines->json_data) {
                $machines = json_decode($lines->json_data, true);
            }

        }

            

        $ct_input = isset($daily->ct_input) ? $daily->ct_input : null;

        $list = array();
        $year = $daily->year;
        $month = $daily->month;

        for ($d = 1; $d <= 31; $d++) {
            $time = mktime(12, 0, 0, $month, $d, $year);
            if (date('m', $time) == $month) {
                $list[] = [
                    'index' => $d, 'day' => date('Y-m-d-D', $time),
                    'comment' => '', 'num_input' => '',
                    'confirmed_name' => '',
                    'ct_input' => '',
                ];
            }
        }
        $datas = [
            'achievement' => [
                'number_of_prod',
                'finished_prod',
                'no_materials',
                // 'material_failure',
                'judgment_prod',
                'add_or_remove',
                // 'judgment_rate',
                // 'rate_of_addition',
            ],
            'deadtime' => [
                'ht15_activities',
                'tool_exchange',
                'step_change',
                'short_stop',
                'mechanical_failure',
                'quality_trouble',
                'dimension_adjustment',
                'cleaning_endofwork',
                'other_sudden',
                // 'sum',
            ],
            'actual_time_hours',
            'total_time_hours',
            // 'operating_time_hours',
            // 'actual_working_hours',
            // 'availability',
            // 'performance',
            'man_hours_per_machine',
            'supervisor_confirmation',
            'remarks',
        ];
        if (!(isset($json_data['ct_input']) && $json_data['ct_input'])) {
            $json_data['ct_input'] = $ct_input;
        }

        if (!(isset($json_data['line_id']) && $json_data['line_id'])) {
            $json_data['line_id'] = $lineId;
        }

        if (!(isset($json_data['line_code']) && $json_data['line_code'])) {
            $json_data['line_code'] = $line_code;
        }
        if (!(isset($json_data['line_name']) && $json_data['line_name'])) {
            $json_data['line_name'] = $line_name;
        }
        $json_data['line_id'] = $lineId;
        $json_data['line_code'] = $line_code;
        $json_data['line_name'] = $line_name;
        if (!(isset($json_data['year']) && $json_data['year'])) {
            $json_data['year'] = $year;
        }
        if (!(isset($json_data['month']) && $json_data['month'])) {
            $json_data['month'] = $month;
        }

        if (!(isset($json_data['machines']) && $json_data['machines'])) {
            $json_data['machines'] = $machines;
        }

        if (!(isset($json_data['arr_ct_input']) && $json_data['arr_ct_input'])) {
            $json_data['arr_ct_input'] = $list;
        }
        foreach($datas as $key => $value) {
            if (is_array($value)) {
                foreach($value as $item) {
                    if (!(isset($json_data[$key][$item]) && $json_data[$key][$item])) {
                        $json_data[$key][$item] = $list;
                    }
                }
            } else {
                if (!(isset($json_data[$value]) && $json_data[$value])) {
                    $json_data[$value] = $list;
                }
            }
        }
        return $json_data;
    }

    // protected function dailyReportResult(Request $request, $blogs)
    // {
    //     $id = $blogs->id;
    //     $lineId = $blogs->inspection_line_id;
    //     $equipment_inspection_data = ($blogs->json_data) ? json_decode($blogs->json_data, true) : null;
    //     $json_data = [];

    //     $daily = DailyProductionControl::where('equipment_inspection_id', $id)->first();
    //     if ($daily && isset($daily->json_data) && $daily->json_data) {
    //         $daily_data = json_decode($daily->json_data, true);
    //         if (isset($daily_data['dailyDataTable'])) {
    //             $json_data = $daily_data['dailyDataTable'];
    //             unset($daily_data['dailyDataTable']);
    //             foreach ($daily_data as $key => $value) {
    //                 if ($key === 'deadtime' && isset($value['cleaning_endofwork)'])) {
    //                     $value['cleaning_endofwork'] = $value['cleaning_endofwork)'];
    //                     unset($value['cleaning_endofwork)']);
    //                 }
                    
    //                 $json_data[$key] = $value;
    //             }
    //         }
    //     }

    //     if ($daily && isset($daily->mst_line_id) && $daily->mst_line_id) {
    //         $lineId = $daily->mst_line_id;
    //     }

    //     $lines = MstLine::where('id', $lineId)->first();
    //     $machines = null;
    //     if ($lines && isset($lines->json_data) && $lines->json_data) {
    //         $machines = json_decode($lines->json_data, true);
    //     }

    //     $ct_input = isset($daily->ct_input) ? $daily->ct_input : null;

    //     $list = array();
    //     $year = $blogs->year;
    //     $month = $blogs->month;

    //     for ($d = 1; $d <= 31; $d++) {
    //         $time = mktime(12, 0, 0, $month, $d, $year);
    //         if (date('m', $time) == $month) {
    //             $list[] = [
    //                 'index' => $d, 'day' => date('Y-m-d-D', $time),
    //                 'comment' => '', 'num_input' => '',
    //                 'confirmed_name' => '',
    //                 'ct_input' => '',
    //             ];
    //         }
    //     }
    //     $datas = [
    //         'achievement' => [
    //             'number_of_prod',
    //             'finished_prod',
    //             'no_materials',
    //             // 'material_failure',
    //             'judgment_prod',
    //             'add_or_remove',
    //             // 'judgment_rate',
    //             // 'rate_of_addition',
    //         ],
    //         'deadtime' => [
    //             'ht15_activities',
    //             'tool_exchange',
    //             'step_change',
    //             'short_stop',
    //             'mechanical_failure',
    //             'quality_trouble',
    //             'dimension_adjustment',
    //             'cleaning_endofwork',
    //             'other_sudden',
    //             // 'sum',
    //         ],
    //         'actual_time_hours',
    //         'total_time_hours',
    //         // 'operating_time_hours',
    //         // 'actual_working_hours',
    //         // 'availability',
    //         // 'performance',
    //         'man_hours_per_machine',
    //         'supervisor_confirmation',
    //         'remarks',
    //     ];
    //     if (!(isset($json_data['ct_input']) && $json_data['ct_input'])) {
    //         $json_data['ct_input'] = $ct_input;
    //     }

    //     if (!(isset($json_data['line_id']) && $json_data['line_id'])) {
    //         $json_data['line_id'] = $equipment_inspection_data['line_id'] ? $equipment_inspection_data['line_id'] : null;
    //     }

    //     if (!(isset($json_data['line_code']) && $json_data['line_code'])) {
    //         $json_data['line_code'] = $equipment_inspection_data['line_code'] ? $equipment_inspection_data['line_code'] : null;
    //     }
    //     if (!(isset($json_data['line_name']) && $json_data['line_name'])) {
    //         $json_data['line_name'] = $equipment_inspection_data['line_name'] ? $equipment_inspection_data['line_name'] : null;
    //     }
    //     if (!(isset($json_data['year']) && $json_data['year'])) {
    //         $json_data['year'] = $equipment_inspection_data['year'] ? $equipment_inspection_data['year'] : null;
    //     }
    //     if (!(isset($json_data['month']) && $json_data['month'])) {
    //         $json_data['month'] = $equipment_inspection_data['month'] ? $equipment_inspection_data['month'] : null;
    //     }

    //     if (!(isset($json_data['machines']) && $json_data['machines'])) {
    //         $json_data['machines'] = $machines;
    //     }

    //     if (!(isset($json_data['arr_ct_input']) && $json_data['arr_ct_input'])) {
    //         $json_data['arr_ct_input'] = $list;
    //     }
    //     foreach($datas as $key => $value) {
    //         if (is_array($value)) {
    //             foreach($value as $item) {
    //                 if (!(isset($json_data[$key][$item]) && $json_data[$key][$item])) {
    //                     $json_data[$key][$item] = $list;
    //                 }
    //             }
    //         } else {
    //             if (!(isset($json_data[$value]) && $json_data[$value])) {
    //                 $json_data[$value] = $list;
    //             }
    //         }
    //     }
    //     return $json_data;
    // }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function dailyReportStore(Request $request)
    {
        $type = 'Created';
        $id = $request->daily_production_control_id;
        $year = $request->year;
        $month = $request->month;
        $ct_input = intval($request->ct_input);
        $status = 'error';
        $message = 'Not found daily production control';
        $post = DailyProductionControl::where('id', $id)
            ->where('year', $year)
            ->where('month', $month)->first();
        if (!$post) {
            return response()->json(compact('status', 'message'));
        }
        $oldLineId = $post->line_id;

        $dataReq = $request->all();
        $dataReq['id'] = $id;
        $year = $post->year;
        $month = $post->month;
        $dataReq['year'] = $year;
        $dataReq['month'] = $month;
        // $dataReq['department_id'] = $post->mst_department_id;
        unset($dataReq['_token']);
        unset($dataReq['_type']);
        $newLineId = $dataReq['line_id'] ? intval($dataReq['line_id']) : null;
        if ($oldLineId != $newLineId) {
            if (isset($dataReq['dailyDataTable']['machines'])) {
                unset($dataReq['dailyDataTable']['machines']);
            }
        }

        if ($request->product_id) {
            $part_number = MstProduct::find($request->product_id)->value('part_number');
        }
        $user = Auth::user();
        $admin = $user->id;
        $data = [
            // 'mst_department_id' => $post->mst_department_id,
            'line_id' => $newLineId,
            // 'equipment_inspection_id' => $id,
            'part_number' => $part_number ?? NULL,
            'year' => $year,
            'month' => $month,
            'ct_input' => $ct_input,
            'json_data' => json_encode($dataReq),
            'created_by' => $admin,
            'updated_by' => $admin,
        ];
        
        $check = false;
        $post = DailyProductionControl::where('line_id', $newLineId)
            ->where('year', $year)
            ->where('month', $month)->first();
        if (! $post) {
            $post = DailyProductionControl::whereNull('line_id')
                ->where('year', $year)
                ->where('month', $month)->first();
        }

        DB::beginTransaction();
        try {
            if ($post) {
                $id = $post->id;
                // $oldLineId = $post->mst_line_id;
                $type = 'Updated';
                unset($data['created_by']);
                foreach ($data as $key => $value) {
                    $post->$key = $value;
                }
                $check = $post->save();
            } else {
                $check = DailyProductionControl::create($data);
                $id = $check->id ? $check->id : $id;
            }
            if ($check) {
                DB::commit();
                $status = 'success';
                $message = $type.' equipment inspection success';
                if ($oldLineId != $newLineId) {
                    $dataRes = ['refesh_line_machine' => true, 'id' => $id];
                    return response()->json(['status' => $status, 'message' => $message, 'data' => $dataRes]);
                }
            } else {
                DB::rollback();
                $message = $type.' equipment inspection failed';
            }

            return response()->json(['status' => $status, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(['DailyProductionControlController@dailyReportStore' => $e->getMessage()]);
            // return response()->json(['status' => $status, 'message' => 'System error']);
            return response()->json(['status' => $status, 'message' => 'System error',
                'message_error' => $e->getMessage()]);
        }
    }

    // public function dailyReportStore(Request $request)
    // {
    //     $type = 'Created';
    //     $id = $request->equipment_inspection_id;
    //     $ct_input = intval($request->ct_input);
    //     $status = 'error';
    //     $message = 'Not found equipment inspection';
    //     $post = EquipmentInspection::where('id', $id)
    //         ->first();
    //     if (!$post) {
    //         return response()->json(compact('status', 'message'));
    //     }
    //     $oldLineId = $post->inspection_line_id;

    //     $dataReq = $request->all();
    //     $dataReq['year'] = $post->year;
    //     $dataReq['month'] = $post->month;
    //     $dataReq['department_id'] = $post->mst_department_id;
    //     unset($dataReq['_token']);
    //     unset($dataReq['_type']);
    //     $newLineId = intval($dataReq['line_id']);
    //     if ($oldLineId != $newLineId) {
    //         if (isset($dataReq['dailyDataTable']['machines'])) {
    //             unset($dataReq['dailyDataTable']['machines']);
    //         }
    //     }

    //     $user = Auth::user();
    //     $admin = $user->id;
    //     $data = [
    //         'mst_department_id' => $post->mst_department_id,
    //         'mst_line_id' => $newLineId,
    //         'equipment_inspection_id' => $id,
    //         'year' => $post->year,
    //         'month' => $post->month,
    //         'ct_input' => $ct_input,
    //         'json_data' => json_encode($dataReq),
    //         'created_by' => $admin,
    //         'updated_by' => $admin,
    //     ];
        
    //     // dd($data);
    //     $check = false;
    //     DB::beginTransaction();
    //     try {
    //         $post = DailyProductionControl::where('equipment_inspection_id', $data['equipment_inspection_id'])->first();
    //         if ($post) {
    //             $oldLineId = $post->mst_line_id;
    //             $type = 'Updated';
    //             unset($data['created_by']);
    //             foreach ($data as $key => $value) {
    //                 $post->$key = $value;
    //             }
    //             $check = $post->save();
    //         } else {
    //             $check = DailyProductionControl::create($data);
    //         }
    //         if ($check) {
    //             DB::commit();
    //             $status = 'success';
    //             $message = $type.' equipment inspection success';
    //             if ($oldLineId != $newLineId) {
    //                 $dataRes = ['refesh_line_machine' => true];
    //                 return response()->json(['status' => $status, 'message' => $message, 'data' => $dataRes]);
    //             }
    //         } else {
    //             DB::rollback();
    //             $message = $type.' equipment inspection failed';
    //         }

    //         return response()->json(['status' => $status, 'message' => $message]);
    //     } catch (\Exception $e) {
    //         DB::rollback();
    //         Log::error(['DailyProductionControlController@dailyReportStore' => $e->getMessage()]);
    //         // return response()->json(['status' => $status, 'message' => 'System error']);
    //         return response()->json(['status' => $status, 'message' => 'System error',
    //             'message_error' => $e->getMessage()]);
    //     }
    // }


    // public function dailyReport($id)
    // {
    //     $post = EquipmentInspection::where('id', $id)
    //         ->first();
    //     if (! $post) {
    //         abort(404);
    //     }
    //     $listDaily = array();
    //     $year = $post->year;
    //     $month = $post->month;
    //     $daily_date = $year . '年 '.$month.' 月度';
    //     for ($d = 1; $d <= 31; $d++) {
    //         $time = mktime(12, 0, 0, $month, $d, $year);
    //         if (date('m', $time) == $month) {
    //             $listDaily[] = [
    //                 'index' => $d, 'day' => date('Y-m-d-D', $time),
    //                 'num_calculator' => '', 'num_input' => '',
    //             ];
    //         }
    //     }
    //     // dd($listDaily);
    //     $breadcrumb = [
    //         'h1' => '日々生産管理表 入力',
    //         // 'li' => [
    //         //     'link' => '/' . $this->adminPrefix . '/equipment-inspection/list',
    //         //     'name' => '設備点検票 一覧'
    //         // ],
    //         // 'active' => '設備点検票 登録',
    //     ];
    //     $user = Auth::user();
    //     $user_name = $user->employee_name;
    //     $user_id = $user->user_id;
    //     $inspectionItem = InspectionItem::select('id', 'inspection_item_set')
    //         ->orderBy('id', 'ASC')->limit(200)->get();
    //     $lines = MstLine::select('id', 'line_code', 'line_name')
    //         ->orderBy('id', 'ASC')->limit(200)->get();
    //     $departments = MstDepartment::select('id', 'code', 'name')
    //         ->orderBy('id', 'ASC')->limit(200)->get();
    //     $products = MstProduct::select('id', 'item_code as code', 'item_name as name', 'product_name_abbreviation as short_name', 'material_manufacturer_code as model')
    //         ->orderBy('id', 'ASC')->limit(200)->get();
    //     return view('backend.daily-production-control-table-edit', compact('breadcrumb', 'id', 'user_name', 'inspectionItem', 'lines', 'departments', 'products', 'listDaily', 'daily_date'));
    // }

    public function dailyReferenceIndex(Request $request)
    {
        $id = $request->get('id');
        $id = intval($id);
        $year = date('Y');
        $month = intval(date('m'));
        $post = DailyProductionControl::query();
        if ($id) {
            $post = $post->where('id', $id);
        } else {
            $post = $post->where('year', $year)->where('month', $month);
        }
        $post = $post->first();
        if (! $post) {
            abort(404);
        }
        return $this->dailyReference($post->id);
    }

    public function dailyReferenceList(Request $request)
    {
        $yearMonth = $request->input('year_month');
        $lineCode = $request->input('line_code');
        $partNumber = $request->input('part_number');
        $departmentCode = $request->input('department_code');
        $currentYearMonth = Carbon::now()->format('Ym');

        $query = DailyProductionControl::join('product_numbers as pn', function (JoinClause $join) {
            $join->on('pn.part_number', 'daily_production_control.part_number')
                ->where('pn.delete_flag', '0');
        })->join('lines as l', function (JoinClause $join) use ($departmentCode, $lineCode) {
            $join->on('l.id', 'daily_production_control.line_id')
                ->where('l.delete_flag', '0');
            if ($lineCode) $join->where('l.line_code', $lineCode);
            if ($departmentCode) $join->where('l.department_code', $departmentCode);
        })->select([
            'daily_production_control.id',
            'daily_production_control.year',
            'daily_production_control.month',
            'l.line_code',
            'l.line_name',
            'pn.part_number',
            'pn.product_name',
        ]);

        $year = substr($yearMonth, 0, 4);
        $month = substr($yearMonth, 4);

        if ($year && $month) {
            $query->where([
                'year' => $year,
                'month' => $month,
            ]);
        }

        if ($partNumber) $query->where('daily_production_control.part_number', $partNumber);

        $result = $query->paginate(20);

        $result->getCollection()->map(function ($production) {
            $decoded = json_decode($production->json_data);
            $production->department_name = $decoded->product_department_name ?? null;
            return $production;
        });

        return view('backend.daily-production-control-list', [
            'daily_prod' => $result,
            'yearMonth' => $currentYearMonth,
        ]);
    }

    // public function dailyProdSearch (Request $request)
    // {
    //     return redirect()->route('daily.reference.list', [
    //         'year_month' => $request->input('year_month'),
    //         'line_code' => $request->input('line_code'),
    //         'part_number' => $request->input('part_number'),
    //         'department_code' => $request->input('department_code'),
    //     ]);
    // }

    public function dailyReference($id)
    {
        $post = DailyProductionControl::where('id', $id)
                ->first();

        if (! $post) {
            abort(404);
        }

        $user = Auth::user();
        $user_name = $user->employee_name;
        $user_id = $user->user_id;
        $admin = $user->id;
        $listDaily = array();
        $year = $post->year;
        $month = $post->month;
        $id = $post->id;
        $line_id = $post->line_id;
        $daily_date = $year . '年 '.$month.' 月度';
        for ($d = 1; $d <= 31; $d++) {
            $time = mktime(12, 0, 0, $month, $d, $year);
            if (date('m', $time) == $month) {
                $listDaily[] = [
                    'index' => $d, 'day' => date('Y-m-d-D', $time),
                    'num_calculator' => '', 'num_input' => '',
                ];
            }
        }
        // dd($listDaily);
        $breadcrumb = [
            'h1' => '日々生産管理表 参照',
            // 'li' => [
            //     'link' => '/' . $this->adminPrefix . '/equipment-inspection/list',
            //     'name' => '設備点検票 一覧'
            // ],
            // 'active' => '設備点検票 登録',
        ];
        
        $inspectionItem = InspectionItem::select('id', 'inspection_item_set')
            ->orderBy('id', 'ASC')->limit(200)->get();
        $lines = Line::select('id', 'line_code', 'line_name')
            ->where('delete_flag', 0)
            ->orderBy('id', 'ASC')->limit(1)->get();
        $departments = MstDepartment::select('id', 'code', 'name')
            ->where('delete_flag', 0)
            ->orderBy('id', 'ASC')->limit(1)->get();
        $products = MstProduct::select('id', 'part_number as code', 'product_name as name', 'name_abbreviation as short_name', 'material_manufacturer_code as code')
            ->where('delete_flag', 0)
            ->orderBy('id', 'ASC')->limit(1)->get();
        return view('backend.daily-production-control-table-reference', compact('breadcrumb', 'id', 'user_name', 'inspectionItem', 'lines', 'departments', 'products', 'listDaily', 'daily_date', 'year', 'month', 'line_id'));
    }
}

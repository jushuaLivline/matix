<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use App\Apollo\Helpers\Helper;
use App\Models\Employee;
use App\Models\EquipmentFileUpload;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\MstLine;
use App\Models\Line;
use App\Models\MstMachine;
use App\Models\InspectionItem;
use App\Models\MstDepartment;
use App\Models\EquipmentInspection;
use App\Services\TemporaryUploadService;

class EquipmentInspectionController extends Controller
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

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function equipmentInspectionIndex(Request $request)
    {
        $breadcrumb = ['h1' => '設備点検票 一覧'];
        $blogs = [];
        // $blogs = EquipmentInspection::with('updater')
        //     ->orderBy('id', 'ASC')->paginate($this->limit);
        return view('backend.equipment-inspection-list', compact('blogs', 'breadcrumb'));
    }

    protected function equipmentInspectionList(Request $request)
    {
        $method = $request->method;
        if ($method === 'SEARCH') {
            return $this->equipmentInspectionSearch($request);
        }
        $items = [];
        $links = '';
        $status = 'error';
        $message = 'error';
        $blogs = EquipmentInspection::with('updater')
            ->orderBy('inspection_line_id', 'ASC')->paginate($this->limit);
        if (count($blogs)) {
            $blogs->withPath('/'.$this->adminPrefix.'/equipment-inspection/list');
            $status = 'success';
            $message = 'success';
            foreach($blogs as $key => $value) {
                $line = $value->facilityLine;
                $confirmed = $value->confirmed;
                $approved = $value->approved;
                $line_code = isset($line->line_code) ? $line->line_code : '';
                $line_name = isset($line->line_name) ? $line->line_name : '';
                $status = '作成'; // created
                $confirmed_name = '';
                $approved_name = '';
                if (isset($confirmed->employee_name)) {
                    $status = '確認'; // confirmed
                    $confirmed_name = $confirmed->employee_name;
                }
                if (isset($approved->employee_name)) {
                    $status = '承認'; // approved
                    $approved_name = $approved->employee_name;
                }
                $item = [
                    'id' => $value->id,
                    'mst_department_id' => $value->mst_department_id,
                    'inspection_line_id' => $value->inspection_line_id,
                    'mst_basic_id' => $value->mst_basic_id,
                    'process_id' => $value->process_id,
                    'line_image' => $value->line_image,
                    'year' => $value->year,
                    'month' => $value->month,
                    'line_code' => $line_code,
                    'line_name' => $line_name,
                    'confirmed_name' => $confirmed_name,
                    'approved_name' => $approved_name,
                    'status' => $status,
                    'file_id' => $value->file_id,
                    'updated_name' => isset($value->updater->employee_name) ? $value->updater->employee_name : '',
                    'created_name' => isset($value->creater->employee_name) ? $value->creater->employee_name : '',
                    'updated_at' => date('Y-m-d H:i:s', strtotime($value->updated_at)),
                    'created_at' => date('Y-m-d H:i:s', strtotime($value->created_at)),
                ];
                $items[] = $item;
            }
            // $items = $blogs->items();
            $links = (string) $blogs->links('partials.pagination-bootstrap-4');
        }
        $data = ['links'=> $links, 'items' => $items];
        return response()->json(compact('status', 'message', 'data'));
    }

    protected function equipmentInspectionSearch(Request $request)
    {
        $id = $request->get('id');
        $data = [];
        $status = 'error';
        $message = 'Not found equipment inspection';
        $blogs = EquipmentInspection::where('id', $id)
            ->first();

        if ($blogs) {
            $status = 'success';
            $message = 'success';
            $json_data = $this->equipmentInspectionResult($request, $blogs);
            $data = [
                'id' => $blogs->id,
                'file_id' => $blogs->file_id,
                'base64_image' => $blogs->getBase64Image(),
                'json_data' => $json_data,
                'created_name' => isset($blogs->creater->employee_name) ? $blogs->creater->employee_name : '',
                'confirmed_name' => isset($blogs->confirmed->employee_name) ? $blogs->confirmed->employee_name : '',
                'approved_name' => isset($blogs->approved->employee_name) ? $blogs->approved->employee_name : '',
                'completed_name' => isset($blogs->completed->employee_name) ? $blogs->completed->employee_name : '',
                'updated_at' => date('Y-m-d H:i:s', strtotime($blogs->updated_at)),
            ];
        }
        return response()->json(compact('status', 'message', 'data'));
    }

    protected function equipmentInspectionResult(Request $request, $blogs)
    {
        $json_data = ($blogs->json_data) ? json_decode($blogs->json_data, true) : null;
        if (! $json_data) {
            return $json_data;
        }
        $list = array();
        $year = $blogs->year;
        $month = $blogs->month;

        for ($d = 1; $d <= 31; $d++) {
            $time = mktime(12, 0, 0, $month, $d, $year);
            if (date('m', $time) == $month) {
                $list[] = [
                    'index' => $d, 'day' => date('Y-m-d-D', $time),
                    'admission_decision' => '', 'num_input' => '',
                    'confirmed_name' => '',
                ];
            }
        }
        if (isset($json_data['dataItems']) && is_array($json_data['dataItems'])
            && count($json_data['dataItems'])
        ) {
            $dataItems = $json_data['dataItems'];
            foreach ($dataItems as $key => $value) {
                if (! isset($dataItems[$key]['inspection_daily'])) {
                    $dataItems[$key]['inspection_daily'] = $list;
                }
            }
            $json_data['dataItems'] = $dataItems;
        }
        // 点　検　実　施　者
        if (! isset($json_data['inspector_daily'])) {
            $json_data['inspector_daily'] = $list;
        }
        // 監　督　者　確　認　欄
        if (! isset($json_data['s_confirmation_daily'])) {
            $json_data['s_confirmation_daily'] = $list;
        }
        return $json_data;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function equipmentInspectionCreate()
    {
        $breadcrumb = [
            'h1' => '設備点検票 登録',
            // 'li' => [
            //     'link' => '/' . $this->adminPrefix . '/equipment-inspection/list',
            //     'name' => '設備点検票 一覧'
            // ],
            // 'active' => '設備点検票 登録',
        ];
        $inspectionItem = InspectionItem::select('id', 'inspection_item_set')
            ->orderBy('id', 'ASC')->limit(200)->get();
        $lines = MstLine::select('id', 'line_code', 'line_name')
            ->orderBy('id', 'ASC')->limit(200)->get();
        $departments = MstDepartment::select('id', 'code', 'name')->where('delete_flag', 0)
            ->orderBy('id', 'ASC')->limit(200)->get();
        return view('backend.equipment-inspection-create', compact('breadcrumb', 'inspectionItem', 'lines', 'departments'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function equipmentInspectionStore(Request $request, TemporaryUploadService $temporaryUploadService)
    {
        $id = $request->id;
        if ($id) {
            $id = intval($id);
            return $this->equipmentInspectionUpdate($request, $id, $temporaryUploadService);
        }

        $status = 'error';
        $message = 'error';
        $year = $request->year;
        $month = $request->month;

        if (! $year) {
            $message = 'Request year is require';
            return response()->json(compact('status', 'message'));
        }
        if (! $month) {
            $message = 'Request month is require';
            return response()->json(compact('status', 'message'));
        }
        if (strlen($month) == 1) {
            $month = '0' . $month;
        }
        $day = $year . '-' . $month . '-01';
        if (! strtotime($day)) {
            $message = 'Request year, month is not trust';
            return response()->json(compact('status', 'message'));
        }

        $dataReq = $request->all();
        // $base64_image = $dataReq['base64_image'];
        unset($dataReq['_token']);
        // unset($dataReq['base64_image']);
        $checkExist = EquipmentInspection::where('inspection_line_id', $dataReq['line_id'])
            ->where('mst_department_id', $dataReq['department_id'])
            ->where('year', $dataReq['year'])
            ->where('month', $dataReq['month'])
            ->where('mst_basic_id', $dataReq['basic_set_id'])
            ->count();

        if ($checkExist) {
            $message = 'Exists equipment inspection';
            return response()->json(compact('status', 'message'));
            // return redirect()->back()->with($status, $message);
        }

        $user = Auth::user();
        $admin = $user->id;
        $data = [
            'mst_department_id' => $dataReq['department_id'],
            'inspection_line_id' => $dataReq['line_id'],
            'year' => $dataReq['year'],
            'month' => $dataReq['month'],
            'mst_basic_id' => $dataReq['basic_set_id'],
            'process_id' => $dataReq['process_id'],
            'json_data' => json_encode($dataReq),
            'created_by' => $admin,
            'updated_by' => $admin,
        ];
        $post = EquipmentInspection::create($data);

        if ($post) {
            $tempFiles = $temporaryUploadService->getTemporaryFiles([
                'form' => $request->currentUrl,
                'user_id' => $request->user()->id
            ]);

            if ($tempFiles) {
                foreach ($tempFiles as $file) {
                    \Log::info($file->id);
                    $attachment = new EquipmentFileUpload;
                    $attachment->file_name = $file->file_name;
                    $attachment->extension = $file->extension();
                    $attachment->size = strlen($file->file);
                    $attachment->user_id = $file->user_id;
                    $attachment->form = $file->form;
                    $attachment->file = $file->file;

                    $attachment->save();

                    $post->file_id = $attachment->id;

                    $post->save();

                    $file->delete();

                }
            }
        }

        if ($post) {
            $status = 'success';
            $message = 'Created equipment inspection success';
        } else {
            $message = 'Created equipment inspection failed';
        }
        return response()->json(['status' => $status, 'message' => $message]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function equipmentInspectionEditIndex(Request $request)
    {
        $id = $request->get('id');
        $id = intval($id);
        $year = date('Y');
        $month = intval(date('m'));
        $post = EquipmentInspection::query();
        if ($id) {
            $post = $post->where('id', $id);
        } else {
            $post = $post->where('year', $year)->where('month', $month);
        }
        $post = $post->first();
        if (! $post) {
            abort(404);
        }
        return $this->equipmentInspectionEdit($post->id);
    }

    public function equipmentInspectionEdit($id)
    {
        $equipmentInspection = EquipmentInspection::where('id', $id)
            ->first();
        // NOTE: Comment out to access page without record
        // if (! $checkExist) {
        //     abort(404);
        // }

        $file_id = $equipmentInspection->file_id;

        $breadcrumb = [
            'h1' => '設備点検票 登録',
            // 'li' => [
            //     'link' => '/' . $this->adminPrefix . '/equipment-inspection/list',
            //     'name' => '設備点検票 一覧'
            // ],
            // 'active' => '設備点検票 登録',
        ];
        $user = Auth::user();
        $user_name = $user->employee_name;
        $user_id = $user->user_id;

        $inspectionItem = InspectionItem::select('id', 'inspection_item_set')
            ->orderBy('id', 'ASC')->limit(200)->get();
        $lines = MstLine::select('id', 'line_code', 'line_name')
            ->orderBy('id', 'ASC')->limit(200)->get();
        $departments = MstDepartment::select('id', 'code', 'name')
            ->orderBy('id', 'ASC')->limit(200)->get();

        $employee = Employee::select('id', 'employee_code', 'employee_name')
            ->where('delete_flag', 0)->orderBy('id', 'asc')->get();

        return view('backend.equipment-inspection-edit',
            compact('breadcrumb', 'id', 'inspectionItem', 'lines', 'departments', 'user_name', 'employee', 'file_id'));
    }

    public function equipmentInspectionTabletIndex(Request $request)
    {
        $id = $request->get('id');
        $id = intval($id);
        $year = date('Y');
        $month = intval(date('m'));
        $post = EquipmentInspection::query();
        if ($id) {
            $post = $post->where('id', $id);
        } else {
            $post = $post->where('year', $year)->where('month', $month);
        }
        $post = $post->first();
        if (! $post) {
            abort(404);
        }
        return $this->equipmentInspectionTablet($post->id);
    }

    public function equipmentInspectionTablet($id)
    {
        $checkExist = EquipmentInspection::where('id', $id)
            ->count();
        // NOTE: Comment out to access page without record
        // if (! $checkExist) {
        //     abort(404);
        // }
        $breadcrumb = [
            'h1' => '設備点検票 登録',
            // 'li' => [
            //     'link' => '/' . $this->adminPrefix . '/equipment-inspection/list',
            //     'name' => '設備点検票 一覧'
            // ],
            // 'active' => '設備点検票 登録',
        ];
        $user = Auth::user();
        $user_name = $user->employee_name;
        $user_id = $user->user_id;
        $inspectionItem = InspectionItem::select('id', 'inspection_item_set')
            ->orderBy('id', 'ASC')->limit(200)->get();
        $lines = MstLine::select('id', 'line_code', 'line_name')
            ->orderBy('id', 'ASC')->limit(200)->get();
        $departments = MstDepartment::select('id', 'code', 'name')
            ->orderBy('id', 'ASC')->limit(200)->get();
        return view('backend.equipment-inspection-tablet',
            compact('id', 'inspectionItem', 'lines', 'departments', 'user_name'));
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function equipmentInspectionUpdate(Request $request, $id, $temporaryUploadService)
    {
        $method = $request->method;
        if ($method === 'DELETE') {
            return $this->equipmentInspectionDestroy($request, $id);
        } else if ($method === 'CONFIRM') {
            return $this->equipmentInspectionConfirm($request, $id);
        } else if ($method === 'TABLET') {
            return $this->equipmentInspectionTabletUpdate($request, $id);
        }
        $year = $request->year;
        $month = $request->month;
        if (! $year) {
            $message = 'Request year is require';
            return response()->json(compact('status', 'message'));
        }
        if (! $month) {
            $message = 'Request month is require';
            return response()->json(compact('status', 'message'));
        }
        if (strlen($month) == 1) {
            $month = '0' . $month;
        }
        $day = $year . '-' . $month . '-01';
        if (! strtotime($day)) {
            $message = 'Request year, month is not trust';
            return response()->json(compact('status', 'message'));
        }
        $post = EquipmentInspection::where('id', $id)->first();
        $status = 'error';
        $message = 'error';
        if (! $post) {
            $message = 'Not found equipment inspection';
            return response()->json(compact('status', 'message'));
            // return redirect()->back()->with($status, $message);
        }

        $dataReq = $request->all();
        $base64_image = $dataReq['base64_image'];
        unset($dataReq['_token']);
        unset($dataReq['base64_image']);

        $checkExist = EquipmentInspection::where('id', '<>', $id)
            ->where('inspection_line_id', $dataReq['line_id'])
            ->where('mst_department_id', $dataReq['department_id'])
            ->where('year', $dataReq['year'])
            ->where('month', $dataReq['month'])
            ->where('mst_basic_id', $dataReq['basic_set_id'])
            ->count();

        if ($checkExist) {
            $message = 'Exists equipment inspection';
            return response()->json(compact('status', 'message'));
            // return redirect()->back()->with($status, $message);
        }

        $user = Auth::user();
        $admin = $user->id;
        $data = [
            'mst_department_id' => $dataReq['department_id'],
            'inspection_line_id' => $dataReq['line_id'],
            'year' => $dataReq['year'],
            'month' => $dataReq['month'],
            'mst_basic_id' => $dataReq['basic_set_id'],
            'process_id' => $dataReq['process_id'],
            'json_data' => json_encode($dataReq),
            'updated_by' => $admin,
        ];
        // dd($data);
        $check = EquipmentInspection::where('id', $id)->update($data);

        if ($check) {
            $equipmentInspection = EquipmentInspection::find($id);

            $tempFiles = $temporaryUploadService->getTemporaryFiles([
                'form' => $request->currentUrl,
                'user_id' => $request->user()->id
            ]);

            if ($tempFiles) {
                foreach ($tempFiles as $file) {
                    $attachedFile = EquipmentFileUpload::where('id', $dataReq['fileID'])->first();

                    if (!empty($attachedFile)) {
                        $attachedFile->file_name = $file->file_name;
                        $attachedFile->extension = $file->extension();
                        $attachedFile->size = strlen($file->file);
                        $attachedFile->user_id = $file->user_id;
                        $attachedFile->form = $file->form;
                        $attachedFile->file = $file->file;

                        $attachedFile->save();
                    } else {
                        $attachment = new EquipmentFileUpload;
                        $attachment->file_name = $file->file_name;
                        $attachment->extension = $file->extension();
                        $attachment->size = strlen($file->file);
                        $attachment->user_id = $file->user_id;
                        $attachment->form = $file->form;
                        $attachment->file = $file->file;
    
                        $attachment->save();

                        $equipmentInspection->file_id = $attachment->id;

                        $equipmentInspection->save();

                        $file->delete();
                    }

                    $file->delete();
                }
            }
        }

        if ($check) {
            $status = 'success';
            $message = 'Updated equipment inspection success';
        } else {
            $message = 'Updated equipment inspection failed';
        }
        return response()->json(['status' => $status, 'message' => $message]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function equipmentInspectionDestroy(Request $request, $id)
    {
        $post = EquipmentInspection::where('id', $id)
            ->first();
        $data = [
            'status' => 'error',
            'message' => '',
            'data' => '',
        ];
        if (!$post) {
            $data['message'] = 'Not found equipment inspection';
            return response()->json($data);
        }
        $check = $post->delete();
        if ($check) {
            $data['status'] = 'success';
            $data['message'] = 'Equipment inspection deleted success!';
        } else {
            $data['message'] = 'Equipment inspection deleted failed';
        }
        return response()->json($data);
    }

    public function equipmentInspectionConfirm(Request $request, $id)
    {
        $os = ['confirmed_name', 'approved_name', 'completed_name'];
        $cols = ['confirmed_name' => 'confirmed_by', 'approved_name' => 'approved_by',
            'completed_name' => 'completed_by'];
        $type = $request->type;
        $data = [
            'status' => 'error',
            'message' => '',
            'data' => '',
        ];
        if (! in_array($type, $os)) {
            $data['message'] = 'Request type not trust';
            return response()->json($data);
        }
        $col = $cols[$type];
        $confirmed_name = '';
        $approved_name = '';
        $completed_name = '';
        if ($type === 'confirmed_name') {
            $confirmed_name = $request->confirmed_name;
        }
        if ($type === 'approved_name') {
            $approved_name = $request->approved_name;
        }
        if ($type === 'completed_name') {
            $completed_name = $request->completed_name;
        }
        $post = EquipmentInspection::where('id', $id)
            ->first();
        if (!$post) {
            $data['message'] = 'Not found equipment inspection';
            return response()->json($data);
        }
        $user = Auth::user();
        $admin = $user->id;
        $name = $user->employee_name;

        $post->$col = $$type ? null : $admin;
        $post->updated_by = $admin;
        $check = $post->save();

        if ($check) {
            $data['status'] = 'success';
            $data['message'] = 'データは正常に登録されました';
            $data['data'] = [$type => $$type ? '' : $name];
        } else {
            $data['message'] = '更新された機器検査に失敗しました';
        }
        return response()->json($data);
    }

    protected function equipmentInspectionTabletUpdate(Request $request, $id)
    {
        $year = $request->year;
        $month = $request->month;
        $dataItems = $request->dataItems;
        $confirmation_daily = $request->confirmation_daily;
        $inspector_daily = $request->inspector_daily;
        if (! $year) {
            $message = 'Request year is require';
            return response()->json(compact('status', 'message'));
        }
        if (! $month) {
            $message = 'Request month is require';
            return response()->json(compact('status', 'message'));
        }
        if (strlen($month) == 1) {
            $month = '0' . $month;
        }
        $day = $year . '-' . $month . '-01';
        if (! strtotime($day)) {
            $message = 'Request year, month is not trust';
            return response()->json(compact('status', 'message'));
        }
        $post = EquipmentInspection::where('id', $id)->first();
        $status = 'error';
        $message = 'error';
        if (! $post) {
            $message = 'Not found equipment inspection';
            return response()->json(compact('status', 'message'));
            // return redirect()->back()->with($status, $message);
        }
        $json_data = ($post->json_data) ? json_decode($post->json_data, true) : null;
        if ($json_data && is_array($json_data)) {
            if (is_array($dataItems) && count($dataItems)) {
                $json_data['dataItems'] = $dataItems;
            }
            if (is_array($inspector_daily) && count($inspector_daily)) {
                $json_data['inspector_daily'] = $inspector_daily;
            }
            if (is_array($confirmation_daily) && count($confirmation_daily)) {
                $json_data['s_confirmation_daily'] = $confirmation_daily;
            }
        }
        $user = Auth::user();
        $admin = $user->id;
        $data = [
            'json_data' => json_encode($json_data),
            'updated_by' => $admin,
        ];
        // dd($data);
        $check = EquipmentInspection::where('id', $id)->update($data);
        if ($check) {
            $status = 'success';
            $message = 'Updated equipment inspection success';
        } else {
            $message = 'Updated equipment inspection failed';
        }
        return response()->json(['status' => $status, 'message' => $message]);
    }
}

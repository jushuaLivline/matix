<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Apollo\Helpers\Helper;
use Carbon\Carbon;
use App\Models\MstLine;
use App\Models\Line;
use App\Models\MstMachine;
use App\Models\MachineNumber;

class MstMachineController extends Controller
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
    public function machinesCreate(Request $request)
    {
        $breadcrumb = ['h1' => '設備管理マスタ 登録・編集'];
        $blogs = [];
        // $blogs = MstLine::with('updater')
        //     ->orderBy('id', 'ASC')->paginate($this->limit);
        return view('backend.machines-create', compact('blogs', 'breadcrumb'));
    }

    protected function machinesSearch(Request $request)
    {
        $id = $request->get('id');
        $data = [];
        $status = 'error';
        $message = 'Not found machine';
        $blogs = MachineNumber::where('machine_number', $id)->where('delete_flag', 0)
            ->first();
        // if (!$blogs) {
        //     $id = intval($id);
        //     $blogs = MstMachine::where('id', $id)
        //         ->first();
        // }
        if ($blogs) {
            $mstMachine = MstMachine::where('machine_number', $id)->first();
            $status = 'success';
            $message = 'success';
            $data = [
                'id' => $blogs->id,
                'machine_number' => $blogs->machine_number,
                'machine_name' => $blogs->machine_number_name,
                'number_of_maintenance' => $mstMachine->number_of_maintenance ?? null,
                'json_data' => isset($mstMachine->json_data) ? json_decode($mstMachine->json_data, true) : null,
                'updated_at' => date('Y-m-d H:i:s', strtotime($blogs->updated_at)),
            ];
        }
        return response()->json(compact('status', 'message', 'data'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function machinesStore(Request $request)
    {
        $method = $request->method;
        if ($method === 'SEARCH') {
            return $this->machinesSearch($request);
        }
        $id = $request->id;
        if ($id) {
            $id = intval($id);
            return $this->machinesUpdate($request, $id);
        }
        $status = 'error';
        $message = 'Please input line code correct and choose search button';
        return response()->json(['status' => $status, 'message' => $message]);
        // $code = $request->code;
        // $dataItems = $request->dataItems;
        // $post = MstLine::where('line_code', $code)
        //     // ->where('line_name', $name)
        //     ->first();
        // $status = 'error';
        // $message = 'Not exists line code';
        // if (! $post) {
        //     return response()->json(['status' => $status, 'message' => $message]);
        // }
        // $user = Auth::user();
        // $admin = $user->id;
        // DB::beginTransaction();
        // try {
        //     $type = 'Updated';
        //     if (!$post) {
        //         $data = [
        //             'line_code' => $code,
        //             'line_name' => $code,
        //             'created_by' => $admin,
        //             'updated_by' => $admin,
        //         ];
        //         // dd($data);
        //         $post = MstLine::create($data);
        //         $type = 'Created';
        //     }
        //     if ($post) {
        //         $json_data = null;
        //         if (is_array($dataItems) && count($dataItems)) {
        //             $json_data = json_encode($dataItems);
        //             $this->insertOrUpdateMachine($dataItems, $admin);
        //         }
        //         $post->json_data = $json_data;
        //         $check = $post->save();
        //         if ($check) {
        //             DB::commit();
        //             $status = 'success';
        //             $message = $type.' machine success';
        //         } else {
        //             DB::rollback();
        //             $message = $type.' machine failed';
        //         }
        //     } else {
        //         $message = $type.' machine failed';
        //     }
        //     return response()->json(['status' => $status, 'message' => $message]);
        // } catch (\Exception $e) {
        //     DB::rollback();
        //     Log::error(['MstMachineController@machinesStore' => $e->getMessage()]);
        //     // return response()->json(['status' => $status, 'message' => 'System error']);
        //     return response()->json(['status' => $status, 'message' => 'System error',
        //         'message_error' => $e->getMessage()]);
        // }
    }

    protected function insertOrUpdateMachine($dataItems, $admin)
    {
        $arrMachineName = [
            1 => 'M/C',
            2 => 'M/C',
            3 => 'M/C',
            4 => 'DR',
            5 => 'M/C',
            6 => 'M/C',
            7 => 'DR',
            8 => 'DR TP',
            9 => 'MIBS',
            10 => 'DR TP',
        ];

        if (is_array($dataItems) && count($dataItems)) {
            foreach ($dataItems as $key => $value) {
                if (isset($value['machine_number']) && $value['machine_number']) {
                    $machine_name = isset($value['machine_name']) ? $value['machine_name'] : '機番 '. $value['machine_number'];
                    if (isset($arrMachineName[intval($value['machine_number'])])) {
                        $machine_name = $arrMachineName[intval($value['machine_number'])];
                    }
                    $item = [
                        'machine_number' => intval($value['machine_number']),
                        'machine_name' => $machine_name,
                        'number_of_maintenance' => isset($value['number_of_maintenance']) ? intval($value['number_of_maintenance']) : null,
                        'json_data' => isset($value['json_data']) ? json_encode($value['json_data']) : null,
                        'created_by' => $admin,
                        'updated_by' => $admin,
                    ];
                    $blog = MstMachine::where('machine_number', $item['machine_number'])->first();
                    if ($blog) {
                        $blog->machine_name = $item['machine_name'];
                        $blog->number_of_maintenance = $item['number_of_maintenance'];
                        $blog->json_data = $item['json_data'];
                        $blog->updated_by = $item['updated_by'];
                        $blog->save();
                    } else {
                        MstMachine::create($item);
                    }
                }
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    protected function machinesUpdate(Request $request, $id)
    {
        $method = $request->method;
        // if ($method === 'DELETE') {
        //     return $this->machinesDestroy($request, $id);
        // }
        $line = Line::where('id', $id)->where('delete_flag', 0)
            // ->where('line_name', $name)
            ->first();
        $status = 'error';
        $message = 'Not exists line code';
        if (! $line) {
            return response()->json(['status' => $status, 'message' => $message]);
        }
        $post = MstLine::where('line_id', $line->id)->first();
        $user = Auth::user();
        $admin = $user->id;
        if (! $post) {
            $post = MstLine::create(['line_id' => $line->id, 'line_code' => $line->line_code, 'line_name' => $line->line_name, 'created_by' => $admin, 'updated_by' => $admin]);
        }
        $dataItems = $request->dataItems;

        DB::beginTransaction();
        try {
            $json_data = null;
            if (is_array($dataItems) && count($dataItems)) {
                foreach ($dataItems as $key => $value) {
                    $machine_number = isset($value['machine_number']) ? $value['machine_number'] : null;
                    $blogs = MachineNumber::where('machine_number', $machine_number)
                        ->where('delete_flag', 0)->first();
                    if (! $blogs) {
                        $message = 'Not exists machine number ' . $machine_number;
                        return response()->json(['status' => $status, 'message' => $message]);
                    }
                }
                $json_data = json_encode($dataItems);
                $this->insertOrUpdateMachine($dataItems, $admin);
            }
            $post->line_code = $line->line_code;
            $post->line_name = $line->line_name;
            $post->json_data = $json_data;
            $post->updated_by = $admin;
            $check = $post->save();
            if ($check) {
                DB::commit();
                $status = 'success';
                $message = 'Updated machine success';
            } else {
                $message = 'Updated machine failed';
            }
            return response()->json(['status' => $status, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error(['MstMachineController@machinesUpdate' => $e->getMessage()]);
            // return response()->json(['status' => $status, 'message' => 'System error']);
            return response()->json(['status' => $status, 'message' => 'System error',
                'message_error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // protected function linesDestroy(Request $request, $id)
    // {
    //     $post = MstLine::where('id', $id)
    //         ->first();
    //     $data = [
    //         'status' => 'error',
    //         'message' => '',
    //         'data' => '',
    //     ];
    //     if (!$post) {
    //         $data['message'] = 'Not found line code';
    //         return response()->json($data);
    //     }
    //     $check = $post->delete();
    //     if ($check) {
    //         $data['status'] = 'success';
    //         $data['message'] = 'Line code deleted success!';
    //     } else {
    //         $data['message'] = 'Line code deleted failed';
    //     }
    //     return response()->json($data);
    // }
}

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
use App\Models\MachineNumber;

class MstLineController extends Controller
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
    public function linesIndex(Request $request)
    {
        $breadcrumb = ['h1' => '設備管理マスタ 一覧'];
        $blogs = [];
        // $blogs = MstLine::with('updater')
        //     ->orderBy('id', 'ASC')->paginate($this->limit);
        return view('backend.lines-list', compact('blogs', 'breadcrumb'));
    }

    protected function linesList(Request $request)
    {
        $items = [];
        $links = '';
        $status = 'error';
        $message = 'error';
        $blogs = MstLine::with('updater')
            ->orderBy('id', 'ASC')->paginate($this->limit);
        if (count($blogs)) {
            $blogs->withPath('/'.$this->adminPrefix.'/facility-management-master/list');
            $status = 'success';
            $message = 'success';
            foreach($blogs as $key => $value) {
                $item = [
                    'id' => $value->id,
                    'line_id' => $value->line_id,
                    'line_code' => $value->line_code,
                    'line_name' => $value->line_name,
                    'updated_name' => isset($value->updater->employee_name) ? $value->updater->employee_name : '',
                    'updated_at' => date('Y-m-d H:i:s', strtotime($value->updated_at)),
                ];
                $items[] = $item;
            }
            // $items = $blogs->items();
            $links = (string) $blogs->links('partials.pagination-bootstrap-4');
        }
        $data = ['links'=> $links, 'items' => $items];
        return response()->json(compact('status', 'message', 'data'));
    }

    protected function linesSearch(Request $request)
    {
        $id = $request->get('id');
        $data = [];
        $status = 'error';
        $message = 'Not found line code';
        $blogs = Line::where('line_code', $id)->where('delete_flag', 0)
            ->first();
        // if (!$blogs) {
        //     $id = intval($id);
        //     $blogs = MstLine::where('id', $id)
        //         ->first();
        // }
        if ($blogs) {
            $mstLine = MstLine::where('line_id',$blogs->id)->first();
            $status = 'success';
            $message = 'success';
            $data = [
                'id' => $blogs->id,
                'line_code' => $blogs->line_code,
                'line_name' => $blogs->line_name,
                'json_data' => isset($mstLine->json_data) ? json_decode($mstLine->json_data, true) : null,
                'updated_at' => date('Y-m-d H:i:s', strtotime($blogs->updated_at)),
            ];
        }
        return response()->json(compact('status', 'message', 'data'));
    }
    protected function machineNumberSearch(Request $request)
    {
        $limit = 200;
        $model = $request->input('model');
        $search = $request->input('query');
        $data = [];
        $status = 'error';
        $message = 'Not found machine';
        $data = MachineNumber::select('machine_number as code', 'machine_number_name as name');
        if ($search) {
            $data = $data->where(function ($query) use ($search) {
                return $query->where('machine_number', 'LIKE', '%'.trim($search).'%')
                    ->orWhere('machine_number_name', 'LIKE', '%'.trim($search).'%');
            });
        }
        $data = $data->where('delete_flag', 0)->limit($limit)->get();
        return response()->json($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function linesStore(Request $request)
    {
        $method = $request->method;
        if ($method === 'LIST') {
            return $this->linesList($request);
        } else if ($method === 'SEARCH') {
            return $this->linesSearch($request);
        } else if ($method === 'SEARCH-MACHINE-NUMBER') {
            return $this->machineNumberSearch($request);
        } else if ($method === 'DELETE') {
            MstLine::find($request->input('id'))->delete();
            return $this->linesList($request);
        }

        $status = 'error';
        $message = 'Exists line code';

        return response()->json(['status' => $status, 'message' => $message]);

        // $id = $request->id;
        // if ($id) {
        //     $id = intval($id);
        //     return $this->linesUpdate($request, $id);
        // }
        // $code = $request->code;
        // $status = 'error';
        // $message = 'Exists line code';
        // if (! $code) {
        //     return response()->json(['status' => $status, 'message' => 'Line code is require']);
        // }
        // $name = $request->name;
        // $checkExist = MstLine::where('line_code', $code)
        //     // ->where('line_name', $name)
        //     ->count();
        // if ($checkExist) {
        //     return response()->json(['status' => $status, 'message' => $message]);
        // }
        // $user = Auth::user();
        // $admin = $user->id;

        // $data = [
        //     'line_code' => $code,
        //     'line_name' => $name,
        //     'created_by' => $admin,
        //     'updated_by' => $admin,
        // ];
        // // dd($data);
        // $post = MstLine::create($data);
        // if ($post) {
        //     $status = 'success';
        //     $message = 'Create line code success';
        // } else {
        //     $message = 'Create line code failed';
        // }
        // return response()->json(['status' => $status, 'message' => $message]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    // protected function linesUpdate(Request $request, $id)
    // {
    //     $method = $request->method;
    //     if ($method === 'DELETE') {
    //         return $this->linesDestroy($request, $id);
    //     } else if ($method === 'SEARCH') {
    //         return $this->linesSearch($request);
    //     }
    //     $code = $request->code;
    //     $status = 'error';
    //     $message = 'Not found line code';
    //     if (! $code) {
    //         return response()->json(['status' => $status, 'message' => 'Line code is require']);
    //     }
    //     $name = $request->name;
    //     $post = MstLine::where('id', $id)
    //         ->first();
    //     if (! $post) {
    //         return response()->json(['status' => $status, 'message' => $message]);
    //     }
    //     $checkExist = MstLine::where('id', '<>', $id)
    //         ->where('line_code', $code)
    //         // ->where('line_name', $name)
    //         ->count();
    //     if ($checkExist) {
    //         $message = 'Exists line code';
    //         return response()->json(['status' => $status, 'message' => $message]);
    //     }
    //     $user = Auth::user();
    //     $admin = $user->id;
    //     $post->line_code = $code;
    //     $post->line_name = $name;
    //     $post->updated_by = $admin;
    //     $check = $post->save();
    //     if ($check) {
    //         $status = 'success';
    //         $message = 'Update line code success';
    //     } else {
    //         $message = 'Update line code failed';
    //     }
    //     return response()->json(['status' => $status, 'message' => $message]);
    // }

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

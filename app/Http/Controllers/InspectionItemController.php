<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Apollo\Helpers\Helper;
use Carbon\Carbon;
use App\Models\MstLine;
use App\Models\MstMachine;
use App\Models\InspectionItem;

class InspectionItemController extends Controller
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
    public function inspectionItemIndex(Request $request)
    {
        $breadcrumb = ['h1' => '点検項目基本セット 一覧'];
        $blogs = [];
        // $blogs = MstLine::with('updater')
        //     ->orderBy('id', 'ASC')->paginate($this->limit);
        return view('backend.inspection-item-basic-set-list', compact('blogs', 'breadcrumb'));
    }

    public function inspectionItemList(Request $request)
    {
        $method = $request->method;
        if ($method === 'SEARCH') {
            return $this->inspectionItemSearch($request);
        }
        $items = [];
        $links = '';
        $status = 'error';
        $message = 'error';
        $blogs = InspectionItem::with('updater')
            ->orderBy('id', 'ASC')->paginate($this->limit);
        if (count($blogs)) {
            $blogs->withPath('/'.$this->adminPrefix.'/inspection-item-basic-set/list');
            $status = 'success';
            $message = 'success';
            foreach($blogs as $key => $value) {
                $item = [
                    'id' => $value->id,
                    'inspection_item_set' => $value->inspection_item_set,
                    'updated_name' => isset($value->updater->user_name) ? $value->updater->user_name : '',
                    'created_name' => isset($value->creater->user_name) ? $value->creater->user_name : '',
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

    protected function inspectionItemSearch(Request $request)
    {
        $id = $request->get('id');
        $data = [];
        $status = 'error';
        $message = 'Not found inspection item set';
        $blogs = InspectionItem::where('id', $id)
            ->first();

        if ($blogs) {
            $status = 'success';
            $message = 'success';
            $data = [
                'id' => $blogs->id,
                'inspection_item_set' => $blogs->inspection_item_set,
                'json_data' => ($blogs->json_data) ? json_decode($blogs->json_data, true) : null,
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
    public function inspectionItemStore(Request $request)
    {
        $method = $request->method;
        if ($method === 'LIST') {
            return $this->inspectionItemList($request);
        } else if ($method === 'SEARCH') {
            return $this->inspectionItemSearch($request);
        }
        $id = $request->id;
        if ($id) {
            $id = intval($id);
            return $this->inspectionItemUpdate($request, $id);
        }
        $name = $request->name;
        $checkExist = InspectionItem::where('inspection_item_set', $name)
            ->count();
        $status = 'error';
        $message = 'Exists inspection item set';
        if ($checkExist) {
            return response()->json(['status' => $status, 'message' => $message]);
        }
        $user = Auth::user();
        $admin = $user->id;
        $dataItems = $request->dataItems;
        $json_data = null;
        if (is_array($dataItems) && count($dataItems)) {
            $json_data = json_encode($dataItems);
        }
        $data = [
            'inspection_item_set' => $name,
            'json_data' => $json_data,
            'created_by' => $admin,
            'updated_by' => $admin,
        ];
        // dd($data);
        $post = InspectionItem::create($data);
        if ($post) {
            $status = 'success';
            $message = 'Created inspection item set success';
        } else {
            $message = 'Created inspection item set failed';
        }
        return response()->json(['status' => $status, 'message' => $message]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    protected function inspectionItemUpdate(Request $request, $id)
    {
        $method = $request->method;
        if ($method === 'DELETE') {
            return $this->inspectionItemDestroy($request, $id);
        }
        $name = $request->name;
        $post = InspectionItem::where('id', $id)
            ->first();
        $status = 'error';
        $message = 'Not found inspection item set';
        if (! $post) {
            return response()->json(['status' => $status, 'message' => $message]);
        }
        $checkExist = InspectionItem::where('id', '<>', $id)
            ->where('inspection_item_set', $name)
            ->count();
        if ($checkExist) {
            $message = 'Exists inspection item set';
            return response()->json(['status' => $status, 'message' => $message]);
        }
        $user = Auth::user();
        $admin = $user->id;
        $post->inspection_item_set = $name;
        $dataItems = $request->dataItems;
        $json_data = null;
        if (is_array($dataItems) && count($dataItems)) {
            $json_data = json_encode($dataItems);
        }
        $post->json_data = $json_data;
        $post->updated_by = $admin;
        $check = $post->save();
        if ($check) {
            $status = 'success';
            $message = 'Updated inspection item set success';
        } else {
            $message = 'Updated inspection item set failed';
        }
        return response()->json(['status' => $status, 'message' => $message]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    protected function inspectionItemDestroy(Request $request, $id)
    {
        $post = InspectionItem::where('id', $id)
            ->first();
        $data = [
            'status' => 'error',
            'message' => '',
            'data' => '',
        ];
        if (!$post) {
            $data['message'] = 'Not found inspection item set';
            return response()->json($data);
        }
        $check = $post->delete();
        if ($check) {
            $data['status'] = 'success';
            $data['message'] = 'Inspection item set deleted success!';
        } else {
            $data['message'] = 'Inspection item set deleted failed';
        }
        return response()->json($data);
    }
}

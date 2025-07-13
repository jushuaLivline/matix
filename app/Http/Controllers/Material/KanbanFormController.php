<?php

namespace App\Http\Controllers\Material;

use App\Http\Controllers\Controller;
use App\Models\KanbanMaster;
use App\Models\SupplyMaterialOrder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class KanbanFormController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $selectedManagementNos = array_map(
            fn($managementNo) => substr($managementNo, 0, 5), 
            $request->input('management_no', [])
        );
        $request->merge(['kanban_classification' => 1]);
        $kanbanMasters = [];

        if (!empty($selectedManagementNos)) {
            $kanbanMastersData = new KanbanMaster();
            $kanbanMasters = $kanbanMastersData->supplyMaterialKanban($request,$selectedManagementNos);
            $request->session()->put('KanbanMasters', $kanbanMasters);
        }

        $request->session()->forget('kanbanMasters');

        return view('pages.materials.supply_material_kanban_form', compact('kanbanMasters'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Start the transaction
        DB::beginTransaction();

        try {
            // Validate input data
            $validator = Validator::make($request->all(), [
                'creation_date.*' => 'required|date_format:Ymd',
                'proccess_code.*' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return redirect()->back()
                    ->withErrors($validator)
                    ->withInput()
                    ->with('error', '登録に必要ないくつかの情報が入力されていません');
            }

            // Retrieve session data
            $sessionData = $request->session()->get('KanbanMasters', []);
            if (empty($sessionData)) {
                return redirect()->back()
                    ->with('error-noData', '登録に必要ないくつかの情報が入力されていません');
            }

            $requestData = $request->all();
            $sessionData = $request->session()->all();
            $kanbanMasters = $sessionData['KanbanMasters'];

            // Combine the submitted data with the creation_date and process_code values
            foreach ($kanbanMasters as $index => $kanbanMaster) {
                $kanbanMaster->creation_date = $requestData['creation_date'][$index];
                $kanbanMaster->process_code = $requestData['process_code'][$index];
            }

            // Update the session data
            $request->session()->put('KanbanMasters', $kanbanMasters);

            foreach ($kanbanMasters as $kanbanMaster) {
                // Using only necessary fields for query
                $supplyMaterialOrder = new SupplyMaterialOrder();
                $creationDateFormatted =  Carbon::createFromFormat('Ymd', $kanbanMaster->creation_date)->format('Y-m-d');
                $kanbanMaster->create_date = $creationDateFormatted;
                $kanbanMaster->auth_user_id = Auth::user()->id;

                $supplyMaterialOrder->createSupplyMaterialOrder($kanbanMaster);
            }
            
            // Clear the session data
            Session::forget('KanbanMasters');

        } catch (\Exception $e) {
              // Rollback the transaction if something goes wrong
            DB::rollBack();
            // Return error response
            return redirect()->back()->with('error', $e->getMessage());
        }

        // Commit the transaction if everything is successful
        DB::commit();

        return redirect()->route('materials.kanban-form.index')->with('success', 'データは正常に登録されました');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // Start transaction
        DB::beginTransaction();

        try {
            $kanbanMaster = new KanbanMaster();
            $$kanbanMaster = $kanbanMaster->removeKabanMaster($id);
            
        } catch (\Exception $e) {
            // Rollback the transaction if there is an error
            DB::rollBack();
            return redirect()->back()->with('error', $e->getMessage());
        }

        // Commit the transaction if everything is successful
        DB::commit();

        return response()->json($kanbanMaster, 200);
    }

    public function ajaxCheckKanbanManagementNo(Request $request)
    {
        $barcode = $request->query('barcode'); // Get the barcode from the request

        if (!$barcode) {
            return response()->json(['error' => '管理No.が入力されていません。'], 400);
        }
        $request->merge(['kanban_classification' => 1]);
        $exists =  KanbanMaster::existsByBarcode($request, $barcode);

        return response()->json(['exists' => $exists]);
    }
}

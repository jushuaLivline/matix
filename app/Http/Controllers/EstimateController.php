<?php

namespace App\Http\Controllers;

use App\Http\Requests\Estimate\StoreRequest;
use App\Models\Attachment;
use App\Models\Customer;
use App\Models\Estimate;
use App\Models\EstimateReplyDetail;
use App\Services\TemporaryUploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstimateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        return redirect()->route('estimate.estimateSearch.index');
    }
    
    public function filtered(Request $request)
    {
        $estimates = [];
        $count = 0;
        if ($request->all()) {
            $query = Estimate::query()
                            ->when($request->estimate_request_start != "" && $request->estimate_request_end != "", function($query) use ($request) {
                                $estimateStartDate = Carbon::parse($request->estimate_request_start);
                                $estimateEndDate = Carbon::parse($request->estimate_request_end);
                                $query->whereBetween("estimate_d", [$estimateStartDate, $estimateEndDate]);
                            })
                            ->when($request->estimate_request_start != "" && $request->estimate_request_end == "", function($query) use ($request) {
                                $estimateStartDate = Carbon::parse($request->estimate_request_start);
                                $query->where("estimate_d", ">=", $estimateStartDate);
                            })

                            // for last reply
                            ->when($request->estimate_reply_start != "" && $request->estimate_reply_end != "", function($query) use ($request) {
                                $estimateStartDate = Carbon::parse($request->estimate_reply_start);
                                $estimateEndDate = Carbon::parse($request->estimate_reply_end);
                                $query->whereBetween("answer_due_d", [$estimateStartDate, $estimateEndDate]);
                            })
                            ->when($request->estimate_reply_start != "" && $request->estimate_reply_end == "", function($query) use ($request) {
                                $estimateStartDate = Carbon::parse($request->estimate_reply_start);
                                $query->where("answer_due_d", ">=", $estimateStartDate);
                            })
                            ->when($request->product_name, function($query) use ($request) {
                                $query->where("product_name", "LIKE", "%". $request->product_name);
                            })
                            ->when($request->product_code, function($query) use ($request) {
                                $query->where("product_code", "LIKE", "%". $request->product_code);
                            })
                            ->when($request->model, function($query) use ($request) {
                                $query->where("model_type", "LIKE", "%". $request->model . "%");
                            })
                            ->when($request->customer_code, function($query) use ($request) {
                                $customer = Customer::where("customer_code", $request->customer_code)->first();
                                if($customer){
                                    $query->where("customer_id", $customer->id);
                                }
                            })
                            ->when($request->unanswered, function($query) {
                                $query->doesntHave("replies");
                            })
                            ->when($request->answered, function($query) {
                                $query->has("replies");
                            })
                        
                            ->when($request->declined, function($query) {
                                $query->whereHas("replies", function($q){
                                    $q->where("decline_flag", 1);
                                });
                            })
                            ->withCount(['replies'])
                            ->with(['lastReply']);
                            
            $count = (clone $query)->count();

            $estimates = $query
                            ->paginate(20);
        }

        return view("pages.estimates.index", compact("estimates", "count"));
    }






    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        return view("pages.estimates.create");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreRequest $request, TemporaryUploadService $temporaryUploadService)
    {
        $this->validate($request, [
            'customer_code' => ['required', 'exists:customers,customer_code'],
            'customer_person' => ['required'],
            'estimate_d' => ['required'],
            'answer_due_d' => ['required'],
            'base_product_code' => ['required'],
            'product_code' => ['required'],
            'product_name' => ['required'],
            'model_type' => ['required'],
            'per_month_reference_amount' => ['required'],
            'sop_d' => ['required'],
            'message' => ['required'],
        ]);

        $estimate = new Estimate;
        $estimate->customer_id = Customer::where('customer_code', $request->customer_code)->first()->id;
        $estimate->customer_person = $request->customer_name;
        $estimate->estimate_d = Carbon::parse($request->estimate_d);
        $estimate->answer_due_d = Carbon::parse($request->answer_due_d);
        $estimate->base_product_code = $request->base_product_code;
        $estimate->product_code = $request->product_code;
        $estimate->product_name = $request->product_name;
        $estimate->model_type = $request->model_type;
        $estimate->per_month_reference_amount = $request->per_month_reference_amount;
        $estimate->sop_d = Carbon::parse($request->sop_d);
        $estimate->message = $request->message;
        $estimate->delete_flag = $request->delete_flag ?? 0;
        $estimate->save();


        $tempFiles = $temporaryUploadService->getTemporaryFiles([
            'form' => route("estimate.create"),
            'user_id' => $request->user()->id
        ]);

        if ($tempFiles) {
            $order = 1;
            foreach ($tempFiles as $file) {
                $attachment = new Attachment;
                $attachment->file = $file->file;
                $attachment->file_name = $file->file_name;
                $attachment->sort_no = $order;
                $attachment->extension = $file->extension();
                $estimate->attachments()->save($attachment);
                $order++;
                $file->delete();
            }
        }

        return redirect()->route("estimate.index");
    }


   
    public function show(Request $request, Estimate $estimate)
    {
        return true;
    }


    public function edit(Estimate $estimate)
    {
        return view("pages.estimates.edit", compact("estimate"));
    }


    public function update(Request $request, Estimate $estimate, TemporaryUploadService $temporaryUploadService)
    {
        $this->validate($request, [
            'customer_code' => ['required', 'exists:customers,customer_code'],
            'customer_person' => ['required'],
            'estimate_d' => ['required'],
            'answer_due_d' => ['required'],
            'base_product_code' => ['required'],
            'product_code' => ['required'],
            'product_name' => ['required'],
            'model_type' => ['required'],
            'per_month_reference_amount' => ['required'],
            'sop_d' => ['required'],
            'message' => ['required'],
        ]);
        
        $estimate->customer_id = DB::table(Customer::tableName())->where('customer_code', $request->customer_code)->first()->id;
        $estimate->customer_name = $request->customer_name;
        $estimate->estimate_d = Carbon::parse($request->estimate_d);
        $estimate->answer_due_d = Carbon::parse($request->answer_due_d);
        $estimate->base_product_code = $request->base_product_code;
        $estimate->product_code = $request->product_code;
        $estimate->product_name = $request->product_name;
        $estimate->model_type = $request->model_type;
        $estimate->per_month_reference_amount = $request->per_month_reference_amount;
        $estimate->sop_d = Carbon::parse($request->sop_d);
        $estimate->message = $request->message;
        $estimate->delete_flag = $request->delete_flag;
        $estimate->save();

        // Saving uploaded files

        $tempFiles = $temporaryUploadService->getTemporaryFiles([
            'form' => route("dashbord.estimate.create"),
            'user_id' => $request->user()->id
        ]);

        if ($tempFiles) {
            $order = 1;
            foreach ($tempFiles as $file) {
                $attachment = new Attachment;
                $attachment->file = $file->file;
                $attachment->file_name = $file->file_name;
                $attachment->sort_no = $order;
                $attachment->extension = $file->extension();
                $estimate->attachments()->save($attachment);
                $order++;
                $file->delete();
            }
        }

        return redirect()->route("estimate.edit", $estimate->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Estimate  $estimate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Estimate $estimate)
    {
        $estimate->delete();
        return back()->route("estimate.index");
    }
}

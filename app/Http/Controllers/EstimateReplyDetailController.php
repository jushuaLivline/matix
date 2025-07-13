<?php

namespace App\Http\Controllers;

use App\Http\Requests\Estimate\EstimateReplyDetailStoreRequest;
use App\Models\Attachment;
use App\Models\Employee;
use App\Models\Estimate;
use App\Models\EstimateReplyDetail;
use App\Models\EstimateReplyQuotation;
use App\Services\TemporaryUploadService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EstimateReplyDetailController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

   
    public function create(Request $request, Estimate $estimate, TemporaryUploadService $temporaryUploadService)
    {
        $sectionName = "営業課";
        $employees = DB::table("employees")
            ->select("employees.id as employee_id", "employees.employee_name as employee_name")
            ->join("departments",  "departments.code", "=", "employees.department_code")
            ->where("departments.section_name", "LIKE", "%". $sectionName ."%")
            ->get();

        $currentTempUploadedFiles = $temporaryUploadService->getTemporaryFiles([
            'form' => url()->current(),
            'user_id' => $request->user()->id
        ]);

        return view("pages.estimates.answer", [
            'estimate' => $estimate,
            'employees' => $employees,
            'currentTempUploadedFiles' => $currentTempUploadedFiles,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EstimateReplyDetailStoreRequest $request, Estimate $estimate, TemporaryUploadService $temporaryUploadService)
    {
        
        $estimateReplyDetail = new EstimateReplyDetail;
        $estimateReplyDetail->estimate_id = $estimate->id;
        $estimateReplyDetail->user_id = $request->person_in_charge;
        $estimateReplyDetail->reply_estimate_d = Carbon::parse($request->reply_estimate_d);
        $estimateReplyDetail->reply_message = $request->reply_message;
        $estimateReplyDetail->decline_flag = $request->decline_flag ?? 0;
        $estimateReplyDetail->delete_flag = $request->delete_flag ?? 0;
        $estimateReplyDetail->save();

        if($request->amount_per_month){
            
            if(is_array($request->amount_per_month)) {
                foreach($request->amount_per_month as $index => $amount_per_month) {
                    $estimateReplyQoutation = new EstimateReplyQuotation();
                    $estimateReplyQoutation->estimate_reply_detail_id = $estimateReplyDetail->id;
                    $estimateReplyQoutation->amount_per_month = $amount_per_month;
                    $estimateReplyQoutation->created_user = $request->user()->id;
                    $estimateReplyQoutation->save();

                    $order = 1;
                    foreach ($request->file('quotation_reploy_attachments') as $file) {
                        $path = $file->getRealPath();
                        $attachment = new Attachment();
                        $attachment->file = base64_encode(file_get_contents($path));
                        $attachment->file_name = $file->getClientOriginalName();
                        $attachment->sort_no = $order;
                        $attachment->extension = $file->extension();
                        $estimateReplyQoutation->attachments()->save($attachment);
                        $order++;
                    }
                }
            } else {
                $estimateReplyQoutation = new EstimateReplyQuotation();
                $estimateReplyQoutation->estimate_reply_detail_id = $estimateReplyDetail->id;
                $estimateReplyQoutation->amount_per_month = $request->amount_per_month;
                $estimateReplyQoutation->created_user = $request->user()->id;
                $estimateReplyQoutation->save();

                $tempFiles = $temporaryUploadService->getTemporaryFiles([
                    'form' => route("estimate.reply.create", $estimate->id),
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
                        $estimateReplyQoutation->attachments()->save($attachment);
                        $order++;
                        $file->delete();
                    }
                }
            }
        }
        
        return redirect()->route("estimate.show", $estimate);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\EstimateReplyDetail  $estimateReplyDetail
     * @return \Illuminate\Http\Response
     */
    public function show(EstimateReplyDetail $estimateReplyDetail)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\EstimateReplyDetail  $estimateReplyDetail
     * @return \Illuminate\Http\Response
     */
    public function edit(EstimateReplyDetail $estimateReplyDetail)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\EstimateReplyDetail  $estimateReplyDetail
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, EstimateReplyDetail $estimateReplyDetail)
    {
        $estimateReplyDetail->estimate_id = $request->estimate_id;
        $estimateReplyDetail->user_id = $request->user_id;
        $estimateReplyDetail->reply_estimate_d = Carbon::parse($request->reply_estimate_d);
        $estimateReplyDetail->reply_message = $request->reply_message;
        $estimateReplyDetail->decline_flag = $request->decline_flag ?? 0;
        $estimateReplyDetail->delete_flag = $request->delete_flag ?? 0;
        $estimateReplyDetail->save();
        
        return redirect()->route("estimate.show", $request->estimate_id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\EstimateReplyDetail  $estimateReplyDetail
     * @return \Illuminate\Http\Response
     */
    public function destroy(EstimateReplyDetail $estimateReplyDetail)
    {
        $estimateReplyDetail->delete();
        return back();
    }
}

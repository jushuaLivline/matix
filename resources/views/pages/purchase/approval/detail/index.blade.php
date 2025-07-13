@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/purchase/approval/detail/index.css')
    @vite('resources/css/sales/sale_plan_search.css')
@endpush

@section('title', '購買依頼（承認）詳細')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>購買依頼詳細</span></h1>
            </div>

            @if(session('success'))
                <div id="flash-message" style="background-color: #fff;">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="pagettlWrap">
                <h1><span>購買依頼詳細</span></h1>
            </div>
                
            <div class="box mb-3 mt-4">
                <div class="mb-4 d-flex">
                    <div class="mr-3">
                        <label class="form-label dotted indented">購買依頼No.</label>
                        <div class="d-flex">
                            <input type="text" name="requisition_number"
                                id="requisition_number"
                                class="text-left mr-2"
                                value="{{ $purchaseRecord->requisition_number }}" disabled>
                        </div>
                    </div>
                    <div class="mr-3">
                        <label class="form-label dotted indented">依頼日</label>
                        <div class="d-flex">
                            <input type="text" name="requested_date"
                                id="requested_date"
                                class="text-left mr-2"
                                value="{{ $purchaseRecord->requested_date ? date('Y/m/d', strtotime($purchaseRecord->requested_date)) : "" }}" disabled>
                        </div>
                    </div>
                    <div class="mr-3">
                        <label class="form-label dotted indented">依頼者</label>
                        <div class="d-flex">
                            <input type="text" name=""
                                id="" 
                                class="text-left mr-2"
                                value="{{ $purchaseRecord->employee?->employee_name ?? '' }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="mr-3">
                        <label class="form-label dotted indented">部門</label>
                        <div class="d-flex">
                            <input type="text" name="department_code"
                                id="department_code"
                                class="text-left mr-2 w-100px"
                                value="{{ $purchaseRecord->department?->code ?? '' }}" disabled>
                            <input type="text" readonly
                                name=""
                                id="" style="margin-right: 10px;"
                                value="{{ $purchaseRecord->department?->name ?? '' }}"
                                class="middle-name text-left" disabled>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <div class="mr-3">
                        <label class="form-label dotted indented">ライン</label>
                        <div class="d-flex">
                            <input type="text" name="line_code"
                                id="line_code"
                                class="text-left mr-2 w-100px"
                                value="{{ $purchaseRecord->line?->line_code ?? '' }}" disabled>
                            <input type="text" readonly
                                name=""
                                id=""
                                value="{{ $purchaseRecord->line?->line_name ?? '' }}"
                                class="middle-name text-left mr-2" disabled>
                        </div>
                    </div>
                </div>
                <div class="mb-4 d-flex">
                    <div class="mr-3">
                        <label class="form-label dotted indented">品番</label>
                        <div class="d-flex">
                            <input type="text" name="part_number"
                                id="part_number"
                                class="text-left mr-2 w-400px"
                                value="{{ $purchaseRecord->part_number }}" disabled>
                        </div>
                    </div>
                    
                    <div class="mr-3">
                        <label class="form-label dotted indented">品名</label>
                        <div class="d-flex">
                            <input type="text" name=""
                                id=""
                                class="text-left mr-2 w-400px"
                                value="{{ $purchaseRecord->product_name }}" disabled>
                        </div>
                    </div>

                    <div>
                        <label class="form-label dotted indented">規格</label>
                        <div class="d-flex">
                            <input type="text" name=""
                                    id="" style="margin-right: 10px; width: 400px"
                                    class="text-left"
                                    value="{{ $purchaseRecord->standard }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="mb-4 d-flex">
                    <div class="mr-3">
                        <label class="form-label dotted indented">購入理由</label>
                        <div class="d-flex">
                            <input type="text" name="reason"
                                id="reason"
                                class="text-left mr-2 w-500px"
                                value="{{ $purchaseRecord->reason }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="mb-4 d-flex">
                    <div class="mr-3">
                        <label class="form-label dotted indented">数量</label>
                        <div class="d-flex">
                            <input type="text" name="quantity"
                                id="quantity"
                                class="text-left mr-2 w-100px"
                                value="{{ $purchaseRecord->quantity }}" disabled>
                        </div>
                    </div>

                    <div class="mr-3">
                        <label class="form-label dotted indented">単位</label>
                        <div class="d-flex">
                            <input type="text" name=""
                                    id="" style="margin-right: 10px; width: 100px"
                                    class="text-left"
                                    value="{{ $purchaseRecord->unit?->name ?? '' }}" disabled>
                        </div>
                    </div>

                    <div class="mr-3">
                        <label class="form-label dotted indented">単価</label>
                        <div class="d-flex">
                            <input type="text" name=""
                                    id="" style="margin-right: 10px; width: 100px"
                                    class="text-left"
                                    value="{{ $purchaseRecord->unit_price }}" disabled>
                        </div>
                    </div>

                    <div class="mr-3">
                        <label class="form-label dotted indented">金額</label>
                        <div class="d-flex">
                            <input type="text" name=""
                                    id="" style="margin-right: 10px; width: 100px"
                                    class="text-left"
                                    value="{{ $purchaseRecord->amount_of_money }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label dotted indented">費目</label>
                    <div class="d-flex">
                        <input type="text" name=""
                                id=""
                                class="text-left mr-2 w-100px"
                                value="{{ $purchaseRecord->expense?->expense_item ?? '' }}" disabled>
                        <input type="text" readonly
                                name=""
                                id="" style="margin-right: 10px;"
                                value="{{ $purchaseRecord->expense?->item_name ?? '' }}"
                                class="middle-name text-left" disabled>
                    </div>
                </div>

                <div class="mb-4 d-flex">
                    <div class="mr-3">
                        <label class="form-label dotted indented">納期</label>
                        <div class="d-flex">
                            <input type="text" name="deadline"
                                id="deadline"
                                class="text-left mr-2"
                                value="{{ $purchaseRecord->deadline ? date('Y/m/d', strtotime($purchaseRecord->deadline)) : "" }}" disabled>
                        </div>
                    </div>
                </div>

                <div class="mb-4 mr-3">
                    <label class="form-label dotted indented">発注先</label>
                    <div class="d-flex">
                        <input type="text" name="supplier_code"
                                id="supplier_code"
                                class="text-left mr-2 w-100px"
                                value="{{ $purchaseRecord->supplier?->customer_code ?? '' }}" disabled>
                        <input type="text" readonly
                                name=""
                                id="" style="margin-right: 10px;"
                                value="{{ $purchaseRecord->supplier?->customer_name ?? '' }}"
                                class="middle-name text-left" disabled>
                    </div>
                </div>

                <div class="mb-2 d-flex">
                    <div class="mr-3">
                        <label class="form-label dotted indented">承認ルート</label>
                        <form id="remove_approval_form" action="{{ route('purchase.detail.purchaseRemoveApprovalUser') }}" method="POST">
                            @csrf
                            <div class="d-flex">
                                
                                <table class="table table-bordered text-center">
                                    <tbody>
                                        <tr height="100">
                                            @foreach ($purchaseRecord->approvals as $approval)
                                                                                                
                                                <td width="150" class="approval-row text-center">
                                                    @if($approval?->employee?->employee_code != Auth::user()->employee_code)
                                                        <div class="d-flex relative">
                                                            <input type="checkbox" name="approval_ids[]" id="" value="{{ $approval->id }}" style="width:15px !important; float: left;" 
                                                            class="approval_checkbox
                                                            @if (!empty($approval->approval_date))
                                                            d-none
                                                            @endif
                                                            ">

                                                        <input type="checkbox" name="approval_route_no[]" value="{{ $purchaseRecord->approval_route_number }}"  class="d-none">                                              
                                                        <input type="checkbox" name="approver_employee_code[]" value="{{ $approval?->employee?->employee_code }}"  class="d-none">
                                                        </div>
                                                    @endif 
                                                    @if ($approval->denial_date != '')
                                                        <p style="margin-top: 20px; color: red; font-weight: bold !important;" class="status_name">
                                                            否認
                                                        </p>

                                                        <p style="margin-top: 10px;">
                                                            {{ date('Y/m/d', strtotime($approval->denial_date)) }}
                                                        </p>
                                                    @elseif ($approval->approval_date != '')
                                                        <p style="margin-top: 20px; color: green; font-weight: bold !important;" class="status_name">
                                                            承認
                                                        </p>

                                                        <p style="margin-top: 10px;">
                                                            {{ date('Y/m/d', strtotime($approval->approval_date)) }}
                                                        </p>
                                                    @else
                                                        <p style="margin-top: 20px; color: red; font-weight: bold !important;" class="status_name">
                                                            未承認
                                                        </p>

                                                        <p style="margin-top: 10px;">
                                                            <br>
                                                        </p>
                                                    @endif

                                                    <p style="margin-top: 10px;">
                                                        {{ $approval?->employee?->employee_name }}
                                                    </p>
                                                </td>
                                            @endforeach
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" class="btn btn-blue btn-primary d-none" style="float: right;" id="delete_approval_button" data-confirm-message="{{ config("messages.confirmations.purchase_requisition_delete_approval_confirmation") }}">
                                チェックした承認を除外
                            </button>
                        </form>
                    </div>

                </div>
                <div class="mb-2 d-flex">
                    <form action="{{ route("purchase.detail.purchaseRequisitionDenied", $purchaseRecord->requisition_number) }}"  method="POST" id="deny-form">
                        @csrf
                        <input type="hidden" name="state_classification" value="9">
                        <input type="hidden" name="purchase_record_no" value="{{ $purchaseRecord->requisition_number }}">
                        <input type="hidden" name="approval_date" value="">
                        <input type="hidden" name="denial_date" value="{{ now()->format("Y-m-d") }}">
                        <input type="hidden" name="purchase" value="{{ now()->format("Y-m-d") }}">
                        <input type="hidden" name="notify_creator" value="{{ $purchaseRecord->creator }}">

                        <div class="mr-3">
                            <label class="form-label dotted indented">否認理由</label>
                            <div>
                                <textarea name="reason_for_denial" required cols="80" rows="4" @if($purchaseRecord->state_classification == "9") disabled @endif>{{ $purchaseRecord->reason_for_denial ?? ''}}</textarea>
                            </div>
                            <strong id="reason_for_denial_error" style="color:red;" data-confirm-message="{{ config("messages.validations.required") }}"></strong>
                            @if($purchaseRecord->state_classification !== "9")
                                <button type="button" id="denial-submit"
                                    class="btn btn-blue mt-2 btn-primary" style="float: right;"
                                    data-confirm-message="{{ config("messages.confirmations.purchase_requisition_denial_confirmation") }}">
                                    この依頼を否認
                                </button>
                            @endif
                        </div>
                    </form>
                </div>
                <form action="{{ route("purchase.detail.purchaseAddApprovalUser") }}" method="POST" class="with-js-validation  @if($last_approver?->approver_employee_code != Auth::user()->employee_code)d-none @endif" id="addNewApproverForm">
                    @csrf
                    <div class="mb-2 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">次承認依頼</label>
                            <div class="d-flex">
                                <input type="hidden" name="requisition_number" value="{{ $purchaseRecord->requisition_number }}">
                                <input type="hidden" name="purchase_record_no" value="{{ $purchaseRecord->requisition_number }}">
                                <input type="hidden" name="order_of_approval" value="{{ $last_approver?->order_of_approval + 1 }}">

                                <input type="text" id="employee_code" required 
                                    data-validate-exist-model="employee"
                                    data-validate-exist-column="employee_code"
                                    data-inputautosearch-model="employee"
                                    data-inputautosearch-column="employee_code"
                                    data-inputautosearch-return="employee_name"
                                    data-inputautosearch-reference="employee_name"
                                    name="approver_employee_code" style="width:100px; margin-right: 10px;" value="{{ $request['approver_employee_code'] ?? '' }}">
                                <input type="text" id="employee_name" name="employee_name" disabled value="{{ $request['employee_name'] ?? '' }}" style="margin-right: 10px;">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchEmployeeModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                                <button type="button" class="btn btn-blue ml-2 btn-primary" id="addNewApproverButton" style="float: right;" data-confirm-message="{{ config("messages.confirmations.purchase_requisition_create_new_approval_confirmation") }}">
                                    承認依頼をする
                                </button>
                            </div>
                            <div data-error-container="employee_code"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="btnListContainer">
                <div class="btnContainerMain justify-content-flex-end">
                    <div class="btnContainerMainRight">
                        <a href="{{ route("purchase.approval.list.index", $request) }}" type="button" class="btn btn-blue btn-primary">
                        一覧に戻る
                        </a>
                        
                        <button onclick="if(confirm('この申請を承認するため一覧のチェックをONにします、よろしいでしょうか？')) { $('#approve-form').submit() }" type="submit" class="btn btn-green btn-success">
                        一覧のチェックをオンにする
                        </button>
                    </div>
                </div>
                @php
                        $requestParams = request()->all();
                    if (empty($requestParams)) {
                        $requestParams = ['purpose' => 1];
                    }
                @endphp
                
                <form id="approve-form" action="{{ route("purchase.detail.purchaseRequisitionApprove", [$purchaseRecord->requisition_number, ... $requestParams]) }}" method="POST">
                    @csrf
                </form>
            </div>
    </div>

    <div id="approvalRouteModalStorage">
        @php
          $search_img = asset('images/icons/magnifying_glass.svg');
        @endphp
    </div>

    @include('partials.modals.masters._search', [
        'modalId' => 'searchEmployeeModal',
        'searchLabel' => '承認者',
        'resultValueElementId' => 'employee_code',
        'resultNameElementId' => 'employee_name',
        'model' => 'Employee'
    ])
@endsection

@push('scripts')
    @vite(['resources/js/purchase/approval/detail/index.js'])
@endpush
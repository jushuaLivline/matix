@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/sales/sale_plan_search.css')
    @vite('resources/css/purchase/approval/list/index.css')
@endpush

@section('title', '購買依頼承認')           

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>購買依頼承認</span></h1>
            </div>

            @if(session('success'))
                <div id="flash-message" style="background-color: #fff;">
                    {{ session('success') }}
                </div>
            @endif
            
            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>

            <form accept-charset="utf-8" class="mt-4 overlayedSubmitForm with-js-validation" data-disregard-empty="true" id="form_request">
                <input type="hidden" name="is_checked" value="{{ request()->get('is_checked')?? '' }}">
                <div class="box mb-3">
                    <div class="mb-4 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">目的</label>
                            <div class="d-flex">
                                @foreach($purposes as $index => $purpose)
                                    <p class="formPack">
                                        <label class="radioBasic">
                                            <input type="radio" name="purpose"
                                                value="{{ $index }}" @checked(Request::get("purpose") == $index) @checked($index == 1 && Request::get("purpose") == "")
                                            >
                                            <span>{{ $purpose }}</span>
                                        </label>
                                    </p>
                                    &nbsp;
                                    &nbsp;
                                @endforeach
                            </div>
                        </div>
                        
                    </div>
                    <div class="mb-4 d-flex">
                        <div class="mr-5">
                            <label class="form-label dotted indented">依頼日</label>
                            <div class="d-flex">
                                @include('partials._date_picker', [
                                    'inputName' => 'request_date_from',
                                    'attributes' => 'data-error-messsage-container=#request_error_message',
                                    'value' => request()->get('request_date_from')
                                ])
                                <span class="span-dash">
                                    ~
                                </span>
                                @include('partials._date_picker', ['inputName' => 'request_date_to', 'attributes' => 'data-error-messsage-container=#request_error_message', 'value' => request()->get('request_date_to')])
                            </div>
                            <div id="request_error_message"></div>
                        </div>
                        
                        <div class="mr-3">
                            <label class="form-label dotted indented">納期</label>
                            <div class="d-flex">
                                @include('partials._date_picker', [
                                    'inputName' => 'deadline_from',
                                    'attributes' => 'data-error-messsage-container=#deadline_error_message'
                                ])
                                <span class="span-dash">
                                    ~
                                </span>
                                @include('partials._date_picker', [
                                    'inputName' => 'deadline_to',
                                    'attributes' => 'data-error-messsage-container=#deadline_error_message
                                '])
                            </div>
                            <div id="deadline_error_message"></div>
                        </div>
                    </div>
                    <div class="mb-4 d-flex">
                        
                        <div class="mr-5">
                            <label class="form-label dotted indented">部門</label>
                            <div class="d-flex">
                                <input type="text" name="department_code_start" id="department_code_start"
                                    data-validate-exist-model="Department"
                                    data-validate-exist-column="code"
                                    data-error-messsage-container="#deparment_error_message"
                                    class="text-left acceptNumericOnly w-100px mr-2"
                                    maxlength="6"
                                    value="{{ request()->get('department_code_start') }}"
                                >
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchDepartmentStartModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                         alt="magnifying_glass.svg">
                                </button>
                                <span class="span-dash">
                                    ~
                                </span>
                                <input type="text" name="department_code_end" id="department_code_end"
                                    data-validate-exist-model="Department"
                                    data-validate-exist-column="code"
                                    data-error-messsage-container="#deparment_error_message"
                                    class="text-left acceptNumericOnly w-100px mr-2"
                                    maxlength="6"
                                    value="{{ request()->get('department_code_end') }}"
                                >
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchDepartmentEndModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                         alt="magnifying_glass.svg">
                                </button>
                            </div>
                            <div id="deparment_error_message"></div>
                        </div>
                        
                        <div class="mr-5">
                            <label class="form-label dotted indented">ライン</label>
                            <div class="d-flex">
                                <input type="text" name="line_code_start" id="line_code_start"
                                    data-validate-exist-model="line"
                                    data-validate-exist-column="line_code"
                                    data-error-messsage-container="#line_error_message"
                                    maxlength="3" 
                                    class="text-left acceptNumericOnly w-100px mr-2"
                                    value="{{ request()->get('line_code_start') }}"
                                >
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchLineStartModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                         alt="magnifying_glass.svg">
                                </button>
                                <span class="span-dash">
                                    ~
                                </span>
                                <input type="text" name="line_code_end" id="line_code_end"
                                    data-validate-exist-model="line"
                                    data-validate-exist-column="line_code"
                                    data-error-messsage-container="#line_error_message"
                                    maxlength="3" 
                                    class="text-left acceptNumericOnly w-100px mr-2"
                                    value="{{ request()->get('line_code_end') }}"
                                >
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchLineEndModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                         alt="magnifying_glass.svg">
                                </button>
                            </div>
                            <div id="line_error_message"></div>
                        </div>

                        <div class="mr-5">
                            <label class="form-label dotted indented">依頼者</label>
                            <div class="d-flex">
                                <input type="text" id="employee_code" name="employee_code"
                                    data-validate-exist-model="employee"
                                    data-validate-exist-column="employee_code"
                                    data-inputautosearch-model="employee"
                                    data-inputautosearch-column="employee_code"
                                    data-inputautosearch-return="employee_name"
                                    data-inputautosearch-reference="employee_name"
                                    class="mr-2 w-100px"
                                >
                                <input type="text" id="employee_name" name="employee_name"
                                    readonly value="{{ request()->get('employee_name') }}"
                                    class="mr-2"
                                >
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchEmployeeModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                            </div>
                            <div data-error-container="employee_code"></div>
                        </div>
                    </div>
                    <div class="mb-4 d-flex">
                        <div class="mr-5">
                            <label class="form-label dotted indented">発注先</label>
                            <div class="d-flex">
                                <input type="text" id="supplier_code" name="supplier_code"
                                    data-validate-exist-model="supplier"
                                    data-validate-exist-column="customer_code"
                                    data-inputautosearch-model="supplier"
                                    data-inputautosearch-column="customer_code"
                                    data-inputautosearch-return="supplier_name_abbreviation"
                                    data-inputautosearch-reference="supplier_name"
                                    maxlength="6"
                                    class="acceptNumericOnly mr-2 w-100px"
                                >
                                <input type="text" id="supplier_name" name="supplier_name"
                                    readonly value="{{ request()->get('supplier_name') }}"
                                    class="mr-2"
                                >
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchSupplierModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                         alt="magnifying_glass.svg">
                                </button>
                            </div>
                            <div data-error-container="supplier_code"></div>
                        </div>

                        <div class="mr-5">
                            <label class="form-label dotted indented">品番</label>
                            <div class="d-flex">
                                <input type="text" id="product_number" name="part_number"
                                    value="{{ request()->get('part_number') }}"
                                    class="mr-2"
                                >
                            </div>
                        </div>

                        <div class="mr-5">
                            <label class="form-label dotted indented">品名</label>
                            <div class="d-flex">
                                <input type="text" id="product_name" name="product_name"
                                    value="{{ request()->get('product_name') }}"
                                    class="mr-2"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="mb-4 d-flex">

                        <div class="mr-5">
                            <label class="form-label dotted indented">規格</label>
                            <div class="d-flex">
                                <input type="text" id="standard" name="standard"
                                    value="{{ request()->get('standard') }}"
                                    class="mr-2"
                                >
                            </div>
                        </div>

                        <div class="mr-5">
                            <label class="form-label dotted indented">購買依頼No.</label>
                            <div class="d-flex">
                                <input type="text" id="purchase_requisition_no" maxlength="10" name="purchase_requisition_no"
                                    value="{{ request()->get('purchase_requisition_no') }}"
                                    class="mr-2 acceptNumericOnly"
                                >
                            </div>
                        </div>
                    </div>
                    <a 
                        href="{{ route('purchase.approval.list.excel_export', array_merge(Request::all(), ['cache_key' => $cacheKey])) }}"
                        class="float-right btn btn-success"
                        onclick="{{ $datas->total() == 0 ? 'return false;' : '' }}"
                    >
                        検索結果をEXCEL出力
                    </a>
                    <div class="text-center">
                        <button type="reset" class="btn btn-primary mr-1 w-200px">検索条件をクリア</a>
                        <button type="submit" class="btn btn-primary w-200px">検索</button>
                    </div>
                </div>
            </form>

            <div class="pagettlWrap mt-2">
                <h1><span>検索結果</span></h1>
            </div>

            <div class="tableWrap bordertable">
                <form action="{{ route('purchase.approval.list.requisition_approval') }}" method="POST" id="approval-form">
                    @csrf
                    <input type="hidden" name="approval_type">
                    <div class="mb-2">
                        @if ($datas && $datas->total() > 0)
                            {{ $datas->total()  }}件中、{{ $datas->firstItem()  }}件～{{ $datas->lastItem()  }}件を表示してます
                        @endif
                        <table id="list-table" class="table table-bordered text-center table-striped-custom w-75">
                            <thead>
                                <tr>
                                    <th rowspan="2" width="25">
                                        <input type="checkbox" id="checkAll" style="margin: auto; height: 20px;">
                                    </th>
                                    <th>部門</th>
                                    <th>発注先</th>
                                    <th>数量</th>
                                    <th>単位</th>
                                    <th>依頼日</th>
                                    <th rowspan="2">購買依頼No.</th>
                                    <th rowspan="2">操作</th>
                                </tr>
                                <tr>
                                    <th>ライン</th>
                                    <th>品番・品名・規格</th>
                                    <th>単価</th>
                                    <th>金額</th>
                                    <th>納期</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($datas ?? [] as $data)
                                    @php
                                        $purchaseRequisitionIds = request()->get('is_checked');
                                    @endphp
                                    <tr data-state-classification="{{  $data->state_classification }}">
                                        <td rowspan="2">
                                            {{-- in_array($data->state_classification, ['1']) --}}
                                            <input type="checkbox" name="requisitionNumbers[]" class="checkboxes" value="{{ $data->requisition_number }}" style="margin: auto; height: 20px; "
                                            @if(Illuminate\Support\Str::contains($purchaseRequisitionIds, $data->requisition_number) ) 
                                            checked 
                                            @endif>
                                            {{  $purchaseRequisitionIds }}
                                        </td>
                                        <td class="tA-le">{{ $data->department->department_name ?? '' }}</td>
                                        <td class="tA-le">{{ $data->supplier->supplier_name_abbreviation ?? '' }}</td>
                                        <td class="tA-le">{{ $data->quantity ?? 0 }}</td>
                                        <td class="tA-le">{{ $data->unit->name ?? '' }}</td>
                                        <td class="tA-le">{{ date('Y/m/d', strtotime($data->requested_date)) }}</td>
                                        <td rowspan="2" class="tA-le">{{ $data->requisition_number }}</td>
                                        <td rowspan="2" class="tA-le" style="width:60px;">
                                            <a 
                                                href="{{ route("purchase.detail.showRequisitionApprovalDetails", [$data->requisition_number, ... Request::all()]) }}"
                                                class='btn btn-blue btn-primary'>
                                                編集
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="tA-le">{{ $data->line->line_name ?? '' }}</td>
                                        <td class="tA-le">{{ $data->part_number }}・{{ $data->pruduct_name }}・{{ $data->standard }}</td>
                                        <td class="tA-le">{{ $data->unit_price }}</td>
                                        <td class="tA-le">{{ $data->amount_of_money }}</td>
                                        <td class="tA-le">{{ date('Y/m/d', strtotime($data->deadline)) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            検索結果はありません
                                        </td>
                                    </tr>
                                @endforelse
                                
                            </tbody>
                        </table>
                        {{ $datas->appends(request()->query())->links() }}
                    </div>
                </form>
            </div>
            <div class="text-right mt-4">
                @php
                    $purpose = request()->get('purpose');
                @endphp
                @if($purpose == 1)

                    <button class="btn btn-green btn-success w-300" id="approve-button">チェックした依頼内容を承認</button>
                @elseif($purpose == 3)
                    <button class="btn btn-orange w-200px" id="unapprove-button">承認取消</button>
                @endif
            </div>
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '発注先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchDepartmentStartModal',
        'searchLabel' => '部門',
        'resultValueElementId' => 'department_code_start',
        'resultNameElementId' => 'department_name_start',
        'model' => 'Department'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchDepartmentEndModal',
        'searchLabel' => '部門',
        'resultValueElementId' => 'department_code_end',
        'resultNameElementId' => 'department_name_end',
        'model' => 'Department'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchLineStartModal',
        'searchLabel' => 'ライン',
        'resultValueElementId' => 'line_code_start',
        'resultNameElementId' => 'line_name_start',
        'model' => 'Line'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchLineEndModal',
        'searchLabel' => 'ライン',
        'resultValueElementId' => 'line_code_end',
        'resultNameElementId' => 'line_name_end',
        'model' => 'Line'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchEmployeeModal',
        'searchLabel' => '依頼者',
        'resultValueElementId' => 'employee_code',
        'resultNameElementId' => 'employee_name',
        'model' => 'Employee'
    ])
    
@endsection
@push('scripts')
    @vite(['resources/js/purchase/approval/list/index.js'])
@endpush
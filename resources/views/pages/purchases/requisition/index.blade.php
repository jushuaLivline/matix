@extends('layouts.app')

@push('styles')
    @vite([
        'resources/css/modals/index.css',
        'resources/css/search-modal.css',
        'resources/css/index.css',
        'resources/css/purchase/requisition/list.css',
    ])

@endpush

@section('title', '購買依頼一覧')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>購買依頼一覧</span></h1>
            </div>

            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>
            
            <form accept-charset="utf-8" id="form_request" class="overlayedSubmitForm with-js-validation" data-disregard-empty="true">
                <div class="box mb-3">
                    <div class="mb-2 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">依頼日</label>
                            <div class="d-flex">
                                @include('partials._date_picker', ['inputName' => 'request_date_from', 'attributes' => 'data-error-messsage-container=#request_error_message'])
                                <span style="font-size:24px; padding:5px 10px;">
                                    ~
                                </span>
                                @include('partials._date_picker', ['inputName' => 'request_date_to', 'attributes' => 'data-error-messsage-container=#request_error_message'])
                            </div>
                            <div id="request_error_message"></div>
                        </div>
                        <div class="mr-3">
                            <label class="form-label dotted indented">納期</label>
                            <div class="d-flex">
                                @include('partials._date_picker', ['inputName' => 'deadline_from', 'attributes' => 'data-error-messsage-container=#deadline_line_error_message'])
                                <span style="font-size:24px; padding:5px 10px;">
                                    ~
                                </span>
                                @include('partials._date_picker', ['inputName' => 'deadline_to', 'attributes' => 'data-error-messsage-container=#deadline_line_error_message'])
                            </div>
                            <div id="deadline_line_error_message"></div>
                        </div>

                        <div class="mr-3">
                            <label class="form-label dotted indented">部門</label>
                            <div class="d-flex">
                                <input type="text" id="department_code_start"
                                    class="acceptNumericOnly"
                                    maxlength="6"
                                    data-validate-exist-model="Department"
                                    data-validate-exist-column="code"
                                    data-inputautosearch-model="department"
                                    data-inputautosearch-column="code"
                                    data-inputautosearch-return="name_abbreviation"
                                    data-inputautosearch-reference="name"
                                    data-error-messsage-container="#deparment_error_message"
                                    name="department_code_start" style="width:100px; margin-right: 10px;" value="{{ request()->get('department_code_start') }}">
                                <button type="button" class="btnSubmitCustom js-modal-open search-btn text-white"
                                        data-target="searchDepartmentStartModal">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                    </svg>
                                </button>
                                <span style="font-size:24px; padding:5px 10px;">
                                    ~
                                </span>
                                <input type="text" id="department_code_end"
                                    class="acceptNumericOnly"
                                    maxlength="6"
                                    data-validate-exist-model="Department"
                                    data-validate-exist-column="code"
                                    data-inputautosearch-model="department"
                                    data-inputautosearch-column="code"
                                    data-inputautosearch-return="name_abbreviation"
                                    data-inputautosearch-reference="name"
                                    data-error-messsage-container="#deparment_error_message"
                                    name="department_code_end" style="width:100px; margin-right: 10px;" value="{{ request()->get('department_code_end') }}">

                                <button type="button" class="btnSubmitCustom js-modal-open search-btn text-white"
                                        data-target="searchDepartmentEndModal">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                    </svg>
                                </button>
                            </div>
                            <div id="deparment_error_message"></div>
                        </div>

                        <div class="mr-3">
                            <label class="form-label dotted indented">ライン</label>
                            <div class="d-flex">
                                <input type="text" id="line_code_start"
                                    class="acceptNumericOnly"
                                    maxlength="3"
                                    data-validate-exist-model="line"
                                    data-validate-exist-column="line_code"
                                    data-inputautosearch-model="line"
                                    data-inputautosearch-column="line_code"
                                    data-inputautosearch-return="line_name_abbreviation"
                                    data-inputautosearch-reference="line_name"
                                    data-error-messsage-container="#line_error_message"
                                name="line_code_start" style="width:100px; margin-right: 10px;" value="{{ request()->get('line_code_start') }}">
                                <button type="button" class="btnSubmitCustom js-modal-open search-btn text-white"
                                        data-target="searchLineStartModal">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                    </svg>
                                </button>
                                <span style="font-size:24px; padding:5px 10px;">
                                    ~
                                </span>
                                <input type="text" id="line_code_end"
                                    class="acceptNumericOnly"
                                    maxlength="3"
                                    data-validate-exist-model="line"
                                    data-validate-exist-column="line_code"
                                    data-inputautosearch-model="line"
                                    data-inputautosearch-column="line_code"
                                    data-inputautosearch-return="line_name_abbreviation"
                                    data-inputautosearch-reference="line_name"
                                    data-error-messsage-container="#line_error_message"
                                    name="line_code_end" style="width:100px; margin-right: 10px;" value="{{ request()->get('line_code_end') }}">
                                <button type="button" class="btnSubmitCustom js-modal-open search-btn text-white"
                                        data-target="searchLineEndModal">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                    </svg>
                                </button>
                            </div>
                            <div id="line_error_message"></div>
                        </div>
                    </div>

                    <div class="mb-2 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">依頼者</label>
                            <div class="d-flex">
                                <input type="text" id="employee_code"
                                    class="acceptNumericOnly"
                                    maxlength="10"
                                    data-validate-exist-model="employee"
                                    data-validate-exist-column="employee_code"
                                    data-inputautosearch-model="employee"
                                    data-inputautosearch-column="employee_code"
                                    data-inputautosearch-return="employee_name"
                                    data-inputautosearch-reference="employee_name"
                                    name="employee_code" style="width:110px; margin-right: 10px;" value="{{ request()->get('customer_code') }}">
                                <input type="text" id="employee_name" name="employee_name" readonly value="{{ request()->get('customer_name') }}" style="margin-right: 10px;">
                                <button type="button" class="btnSubmitCustom js-modal-open search-btn text-white"
                                        data-target="searchEmployeeModal">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                    </svg>
                                </button>
                            </div>
                            <div data-error-container="employee_code"></div>
                        </div>
                        <div class="mr-3">
                            <label class="form-label dotted indented">発注先</label>
                            <div class="d-flex">
                                <input type="text" id="supplier_code"
                                    class="acceptNumericOnly"
                                    maxlength="6"
                                    data-validate-exist-model="supplier"
                                    data-validate-exist-column="customer_code"
                                    data-inputautosearch-model="supplier"
                                    data-inputautosearch-column="customer_code"
                                    data-inputautosearch-return="customer_name"
                                    data-inputautosearch-reference="supplier_name"
                                    name="supplier_code" style="width:100px; margin-right: 10px;" value="{{ request()->get('supplier_code') }}">
                                <input type="text" id="supplier_name" name="supplier_name" readonly value="{{ request()->get('supplier_name') }}" style="margin-right: 10px;">
                                <button type="button" class="btnSubmitCustom js-modal-open search-btn text-white"
                                        data-target="searchSupplierModal">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                        <path
                                            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                    </svg>
                                </button>
                            </div>
                            <div data-error-container="supplier_code"></div>
                        </div>

                        <div class="mr-3">
                            <label class="form-label dotted indented">品番</label>
                            <div class="d-flex">
                                <input type="text" id="part_number" name="part_number" style="width:250px; margin-right: 10px;" value="{{ request()->get('part_number') }}">
                            </div>
                        </div>
                        <div class="mr-3">
                            <label class="form-label dotted indented">品名</label>
                            <div class="d-flex">
                                <input type="text" id="product_name" name="product_name" style="width:250px; margin-right: 10px;" value="{{ request()->get('product_name') }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-2 d-flex">

                        <div class="mr-3">
                            <label class="form-label dotted indented">規格</label>
                            <div class="d-flex">
                                <input type="text" id="standard" name="standard" style="width:250px; margin-right: 10px;" value="{{ request()->get('standard') }}">
                            </div>
                        </div>
                        <div class="mr-3">
                            <label class="form-label dotted indented mt-2">承認方法</label>
                            <div class="d-flex align-items-center">
                                @foreach($approvalMethods as $index => $approvalMethod)
                                <p class="formPack">
                                    <label class="radioBasic d-flex align-items-center">
                                        <input type="checkbox" name="approval_method_category[]" value="{{ $index }}" class="mr-2" {{ in_array($index, (array)request()->get('approval_method_category')) ? 'checked' : '' }}>
                                        <p class="mt-1 mr-3">{{ $approvalMethod }}</p>
                                    </label>
                                </p>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="mr-3">
                            <label class="form-label dotted indented mt-2">状態</label>
                            <div class="d-flex flex-wrap align-items-center">
                                @foreach($stateClasifications as $index => $stateClasification)
                                <p class="formPack">
                                    <label class="radioBasic d-flex align-items-center">
                                        <input type="checkbox" name="state_classification[]" value="{{ $index }}" class="mr-2" {{ in_array($index, (array)request()->get('state_classification')) ? 'checked' : '' }}>
                                        <p class="mt-1 mr-3">{{ $stateClasification }}</p>
                                    </label>
                                </p>
                                @endforeach
                            </div>
                        </div>                        
                    </div>
                    <div class="mb-2 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">購買依頼No.</label>
                            <div class="d-flex">
                                <input class="acceptNumericOnly" type="text" id="purchase_requisition_no" name="purchase_requisition_no" style="width:350px; margin-right: 10px;" value="{{ request()->get('purchase_requisition_no') }}">
                            </div>
                        </div>
                    </div>

                    <a href="{{ route("purchase.purchaseRequisitionSearch.export", Request::all()) }}" class="float-right btn btn-success">検索結果をEXCEL出力</a>
                    <div class="text-center">
                        <a class="btn btn-blue" style="min-width: 200px" id="resetForm">検索条件をクリア</a>
                        <button type="submit" class="btn btn-blue" style="min-width: 200px">検索</button>
                    </div>
                </div>
            </form>

            <div class="pagettlWrap mt-2">
                <h1><span>検索結果</span></h1>
            </div>
            
            <div class="tableWrap bordertable">
                <div class="table-container">
                    @if($datas && $datas->total() > 0 )
                        <div class="mb-2">{{ $datas->total() }}件中、{{ $datas->firstItem() }}件～{{ $datas->lastItem() }}件を表示してます</div>
                    @endif

                    <table class="table table-bordered text-center table-striped-custom">
                        <thead>
                        <tr>
                            <th class="col-state">状態</th>
                            <th class="col-approval">承認方法</th>
                            <th class="col-approver">次承認者</th>
                            <th class="col-department">部門</th>
                            <th class="col-line">ライン</th>
                            <th class="col-supplier">発注先</th>
                            <th class="col-product">品番・品名・規格</th>
                            <th class="col-quantity">数量</th>
                            <th class="col-price">単価</th>
                            <th class="col-unit">単位</th>
                            <th class="col-amount">金額</th>
                            <th class="col-date">依頼日</th>
                            <th class="col-deadline">納期</th>
                            <th class="col-number">購買依頼No.</th>
                            <th class="col-actions">操作</th>
                        </tr>
                        </thead>
                        <tbody>
                            @forelse ($datas as $data)
                            <tr>
                                <td>{{ $stateClasifications[$data->state_classification] ?? '' }}</td>
                                <td>{{ $approvalMethods[$data->approval_method_category] ?? '' }}</td>
                                <td>{{ $data->nextApprover?->employee_name ?? '（該当無し）' }}</td>
                                <td>{{ $data->department_code . '' . $data->department?->name }}</td>
                                <td>{{ $data->line?->line_name }}</td>
                                <td>{{ $data->supplier?->supplier_name_abbreviation }}</td>
                                <td class="text-start">{{ $data->part_number . '・' . $data->product_name . '・' . $data->standard }}</td>
                                <td class="text-end">{{ number_format($data->quantity) }}</td>
                                <td class="text-end">{{ number_format($data->unit_price) }}</td>

                                <!--td>{{ $data->unit_code }}</td-->
                                <td>{{ optional($data->unit)->name }}</td>

                                <td class="text-end">{{ number_format($data->amount_of_money) }}</td>
                                <td>{{ $data->requested_date?->format('Y-m-d') }}</td>
                                <td>{{ $data->deadline?->format('Y-m-d') }}</td>
                                <td>{{ $data->requisition_number }}</td>
                                <td>
                                    <a href="{{ route('purchase.requisition.edit', $data->requisition_number) }}" class="btn btn-primary btn-sm">編集</a>
                                    <a href="{{ route('purchase.requisition.create', ['id' => $data->id]) }}" class="btn btn-primary btn-sm">複写</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="15" class="text-center">検索結果はありません</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    @if ($datas instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        {{ $datas->appends(request()->except('page'))->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>

    @include('partials.modals.masters._search', [
        'modalId' => 'searchDepartmentStartModal',
        'searchLabel' => '部門',
        'resultValueElementId' => 'department_code_start',
        'resultNameElementId' => 'code',
        'model' => 'Department'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchDepartmentEndModal',
        'searchLabel' => '部門',
        'resultValueElementId' => 'department_code_end',
        'resultNameElementId' => 'code',
        'model' => 'Department'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchLineStartModal',
        'searchLabel' => 'ライン',
        'resultValueElementId' => 'line_code_start',
        'resultNameElementId' => 'line_code',
        'model' => 'Line'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchLineEndModal',
        'searchLabel' => 'ライン',
        'resultValueElementId' => 'line_code_end',
        'resultNameElementId' => 'line_code',
        'model' => 'Line'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchEmployeeModal',
        'searchLabel' => '依頼者',
        'resultValueElementId' => 'employee_code',
        'resultNameElementId' => 'employee_name',
        'model' => 'Employee'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '発注先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier'
    ])
    <!-- [Other modals remain the same] -->
@endsection

@if (!Session::has("PurchaseRequisitionEditPrevURL"))
    {{ session()->put('PurchaseRequisitionEditPrevURL', Request::getRequestUri()) }}
@endif

@push('scripts')
    @vite(['resources/js/purchase/requisitions/purchase-requisition.js'])
@endpush
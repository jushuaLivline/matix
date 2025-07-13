@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
@endpush

@section('title', '受入処理')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1>
                    <span>受入処理</span>
                </h1>
            </div>

            @if(session('success'))
                <div id="card" style="padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                    <div>
                        <p style="font-size: 18px; color: #0d9c38;">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div id="card" style="padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                    <div>
                        <p style="font-size: 18px; color: #d81414;">
                            {{ session('error') }}
                        </p>
                    </div>
                </div>
            @endif
            @if(session('delete'))
                <div id="card" style="padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                    <div>
                        <p style="font-size: 18px; color: #d81414;">
                            {{ session('delete') }}
                        </p>
                    </div>
                </div>
            @endif

            <div class="pagettlWrap">
                <h1><span>受入処理</span></h1>
            </div>
            
            <form id="acceptance-form" data-id="{{$requisition->id}}" 
                accept-charset="utf-8" 
                class="mt-4 overlayedSubmitForm with-js-validation">
                @csrf
                <div class="box mb-4 pb-5">
                    <div class="mb-4 d-flex-col space-y-4">
                        <div class="mb-4">
                            <label class="form-label dotted indented">注文書No.</label>
                            <div class="d-flex">
                                <input type="text" name="purchase_order_number" value="{{ $requisition->purchase_order_number }}" value="" readonly style="width: 19.3%;">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label dotted indented">購買依頼No.</label>
                            <div class="d-flex">
                                <input type="text" name="requisition_number" value="{{ $requisition->requisition_number }}" value="" readonly style="width: 19.3%;">
                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4 d-flex-col space-y-4">
                        <div class="mb-4">
                            <label class="form-label dotted indented">依頼者</label>
                            <div class="d-flex">
                                <input type="text" name="creator" id="employee_code"
                                    style="margin-right: 10px; width: 100px;"
                                    class="text-left employee acceptNumericOnly fetchQueryName"
                                    data-model="Employee"
                                    data-query="employee_code"
                                    data-query-get="employee_name"
                                    data-reference="employee_name"
                                    value="{{ $requisition?->employee?->employee_code ?? ''}}"
                                >
                                <input type="text" readonly
                                    id="employee_name"
                                    value="{{ $requisition?->employee?->employee_name ?? ''}}"
                                    class="middle-name text-left"
                                    style="margin-right: 10px; width: 290px;"
                                >
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchEmployeeModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label dotted indented">発注先</label>
                            <span class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" name="supplier_code"
                                    id="supplier_code" style="margin-right: 10px; width: 100px;"
                                    class="text-left Supplier acceptNumericOnly fetchQueryName"
                                    data-model="Customer"
                                    data-query="customer_code"
                                    data-query-get="supplier_name_abbreviation"
                                    data-reference="supplier_name"
                                    maxlength="6"
                                    value="{{ $requisition->supplier_code ?? ''}}"
                                >
                                <input type="text" readonly
                                    id="supplier_name"
                                    value="{{ $requisition?->supplier?->customer_name ?? ''}}"
                                    class="middle-name text-left" style="margin-right: 10px; width: 290px;"
                                >
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchSupplierModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4" style="display: grid;grid-template-columns: repeat(9, 1fr);gap: 0px;">
                        <div class="mr-1">
                            <label class="form-label dotted indented">発注日</label>
                            <div class="d-flex">
                                @include('partials._date_picker_estimates', [
                                    'inputName' => 'order_date', 
                                    'value' => optional($requisition->order_date)?->format('Ymd'),
                                    'required' => false,
                                    'input_width' => 8.5
                                ])
                            </div>
                            <div class="error-field"></div>
                        </div>
                        <div>
                            <label class="form-label dotted indented">納期</label>
                            <div class="d-flex">
                                @include('partials._date_picker_estimates', [
                                    'inputName' => 'deadline', 
                                    'required' => false,
                                    'value' => optional($requisition->deadline)?->format('Ymd'),
                                    'input_width' => 8.5
                                ])
                            </div>
                            <div class="error-field"></div>
                        </div>
                    </div>
                    <div class="mb-4 d-flex-col space-y-4">
                        <div class="mb-4">
                            <label class="form-label dotted indented">部門</label>
                            <div class="d-flex">
                                <input type="text" name="department_code"
                                    id="department_code"
                                    style="margin-right: 10px; width: 100px; ime-mode: disabled"
                                    class="text-left acceptNumericOnly fetchQueryName"
                                    data-model="Department"
                                    data-query="code"
                                    data-query-get="name_abbreviation"
                                    data-reference="department_name"
                                    maxlength="6"
                                    value="{{ $requisition->department_code  ?? ''}}"
                                >
                                <input type="text" readonly
                                    id="department_name"
                                    value="{{ $requisition?->department?->name  ?? ''}}"
                                    class="middle-name text-left" style="margin-right: 10px; width: 290px;"
                                >
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchDepartmentModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label dotted indented">ライン</label>
                            <div class="d-flex">
                                <input type="text" name="line_code"
                                    id="line_code"
                                    style="margin-right: 10px; width: 100px"
                                    class="text-left acceptNumericOnly fetchQueryname"
                                    data-model="Line"
                                    data-query="line_code"
                                    data-query-get="line_name"
                                    data-reference="line_name"
                                    maxlength="3"
                                    value="{{ $requisition->line_code  ?? ''}}"
                                >
                                <input type="text" readonly
                                    id="line_name"
                                    value="{{ $requisition?->line?->line_name  ?? ''}}"
                                    class="middle-name text-left" style="margin-right: 10px; width: 290px;"
                                >
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchLineModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="form-label dotted indented">機番</label>
                            <div class="d-flex">
                                <input type="text" name="machine_number" id="machine_code"
                                    style="margin-right: 10px; width: 100px"
                                    class="text-left acceptNumericOnly fetchQueryName"
                                    data-model="MachineNumber"
                                    data-query="machine_number"
                                    data-query-get="machine_number_name"
                                    data-reference="machine_name"
                                    value="{{ $requisition->machine_number  ?? ''}}">
                                <input type="text" readonly
                                    id="machine_name"
                                    value="{{ $requisition?->machine?->machine_number_name  ?? ''}}"
                                    class="middle-name text-left" style="margin-right: 10px; width: 290px;"
                                >
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchMachineModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                                <div class="error_msg"></div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                        <div>
                            <label class="form-label dotted indented">品番</label>
                            <span class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" id="part_number" 
                                    name="part_number"
                                    style="width: 100%;" 
                                    maxlength="100" name="part_number" 
                                    value="{{ $requisition->part_number  ?? ''}}"
                                >
                            </div>
                            <div class="error-field"></div>
                        </div>
                        <div>
                            <label class="form-label dotted indented">品名</label>
                            <div class="d-flex">
                                <input type="text" name="product_name" value="{{ $requisition->product_name  ?? ''}}" style="width: 100%;">
                            </div>
                        </div>
                        <div>
                            <label class="form-label dotted indented">規格</label>
                            <div class="d-flex">
                                <input type="text" name="standard" value="{{ $requisition->standard  ?? ''}}" style="width: 100%;">
                            </div>
                        </div>
                    </div>
                    <div class="mb-4" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                        <div>
                            <label class="form-label dotted indented">発注数</label>
                            <span class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="number" name="quantity"
                                    id="quantity" style="width: 100%;"
                                    class="text-left acceptNumericOnly"
                                    maxlength="9"
                                    data-accept-zero=true    
                                    value="{{ $requisition->quantity  ?? ''}}" style="width: 100%;"
                                >
                            </div>
                            <div class="error-field"></div>
                        </div>
    
                        <div>
                            <label class="form-label dotted indented">単位</label>
                            <div class="d-flex">
                                <select name="unit_code" style="width: 60%; height: 40px">
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->code  ?? ''}}" @if($requisition->unit_code == $unit->code) selected @endif>{{ $unit->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                        <div>
                            <label class="form-label dotted indented">単価</label>
                            <div class="d-flex">
                                <input type="text" name="unit_price"
                                    value="{{ $requisition->unit_price  ?? ''}}"
                                    style="width: 100%;"
                                    class="text-left acceptNumericOnly"
                                >
                            </div>
                        </div>
                        <div>
                            <label class="form-label dotted indented">金額</label>
                            <div class="d-flex">
                                <input type="text" name="amount_of_money" 
                                    value="{{ $requisition->amount_of_money  ?? ''}}" readonly
                                    style="width: 60%"
                                >
                            </div>
                        </div>
                    </div>
                    <div class="mb-4 d-flex-col space-y-4">
                        <div class="mb-4">
                            <label class="form-label dotted indented">費目</label>
                            <span class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" name="expense_items" 
                                    value="{{ $requisition->expense_items  ?? ''}}"
                                    id="item_code" style="margin-right: 10px; width: 100px;"
                                    class="text-left  Item acceptNumericOnly fetchQueryname"
                                    data-model="Item"
                                    data-query="expense_item"
                                    data-query-get="item_name"
                                    data-reference="item)name"
                                    maxlength="3"
                                >
                                <input type="text" readonly
                                    id="item_name"
                                    value="{{ $requisition?->expense?->item_name  ?? ''}}"
                                    class="middle-name text-left" style="margin-right: 10px; width: 290px;">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchItemModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="form-label dotted indented">課税区分</label>
                            <span class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <label class='mr-1'>
                                    <input type="radio" name="tax_classification" value="1" {{ $requisition->tax_classification == '1' ? 'checked' : '' }}> 課税
                                </label>
                                <label class='mr-1'>
                                    <input type="radio" name="tax_classification" value="2" {{ $requisition->tax_classification == '2' ? 'checked' : '' }}> 非課税
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label dotted indented">購入理由</label>
                        <div class="d-flex">
                            <input type="text" name="reason" value="{{ $requisition->reason  ?? ''}}" style="width: 50%;"  maxlength="100">
                        </div>
                    </div>
                    <div class="mb-4 d-flex-col space-y-4">
                        <div class="mb-4">
                            <label class="form-label dotted indented">使用先</label>
                            <div class="d-flex">
                                <input type="text" name="where_used_code" id="where_used_code"
                                    class="text-left Item acceptNumericOnly fetchQueryName"
                                    data-model="Customer"
                                    data-query="customer_code"
                                    data-query-get="supplier_name_abbreviation"
                                    data-reference="where_used_name"
                                    style="margin-right: 10px; width: 100px;"
                                    maxlength="6"
                                    value="{{ $requisition->where_used_code  ?? ''}}">
                                <input type="text" readonly
                                    id="where_used_name"
                                    value="{{ $requisition->customer  ?? ''}}"
                                    class="middle-name text-left" style="margin-right: 10px; width: 290px;"
                                >
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchCustomerModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label dotted indented">プロジェクトNo.</label>
                            <div class="d-flex">
                                <input type="text" name="project_number" id="project_code"
                                    data-validate-exist-model="Project"
                                    id="project_code" style="margin-right: 10px; width: 100px;"
                                    class="text-left Item fetchQueryName"
                                    data-model="Project"
                                    data-query="project_number"
                                    data-query-get="project_name"
                                    data-reference="project_name"
                                    value="{{ $requisition->project_number ?? '' }}"
                                    maxlength="8"
                                >
                                <input type="text" readonly
                                    id="project_name"
                                    value="{{ $requisition?->project?->project_name  ?? ''}}"
                                    class="middle-name text-left" style="margin-right: 10px; width: 290px;"
                                >
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchProjectModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label dotted indented">依頼時備考</label>
                        <div class="d-flex">
                            <input type="text" name="remarks" value="{{ $requisition->remarks  ?? ''}}" style="width: 50%;"  maxlength="100">
                        </div>
                        <div class="error_msg"></div>
                    </div>
                    <div class="text-right">
                        <button type="submit" id="form-submit"
                            class="buttonBasic bColor-green mt-1 btn btn-success"
                            style="height: 40px; padding: 8px; width: 200px;"
                        >発注情報更新</button>
                    </div>
                </div>
            </form>

            <div class="pagettlWrap">
                <h1><span>入荷・受入情報</span></h1>
            </div>

            <div class="tableWrap bordertable" style="clear: both;">
                <ul class="headerList">
                    {{-- <li>{{ $count }}件中、1件～{{ count($estimates) }}件を表示してます</li> --}}
                </ul>

                <form method="POST" action="{{ route('purchase.acceptance.store') }}" id="storeDataForm">
                    @csrf
                    @method('POST')
                    <table class="tableBasic list-table bordered">
                        <tbody>
                        <tr>
                            <th style="width: 220px;">入荷日</th>
                            <th style="width: 100px;">数量</th>
                            <th style="width: 100px;">再建不能</th>
                            <th style="width: 200px;">伝票No.</th>
                            <th style="width: 300px;">入荷時備考</th>
                            <th style="width: 100px;">受入</th>
                            <th style="width: 100px;">操作</th>
                        </tr>
                        <tbody>
                            @forelse($items as $item)
                                <tr data-id="{{ $item->id }}">
                                    <td class="tA-cn">
                                      
                                            <div class="d-flex ">
                                            <input type="text" minlength="8" maxlength="8" class="pickerJS w-50 " id="arrival_day_{{ $item->id }}" data-format="YYYY-MM-DD" data-value="" old="" value="{{$item->arrival_day->format('Y-m-d')}}" name="arrival_day[]" pattern="\d*" oninput="this.value = this.value.replace(/[^0-9]/g, '')" style="">
                                            <button type="button" class="btnSubmitCustom buttonPickerJS ml-2 " data-target="arrival_day_{{ $item->id }}" data-format="YYYY-MM-DD" style="">
                                                <img src="/images/icons/iconsvg_calendar_w.svg" alt="iconsvg_calendar_w.svg">
                                            </button>

                                        </div>
                                       
                                    </dl>
                                    
                                    </td>
                                    <td class="tA-ri">
                                        <input type="text" name="arrival_quantity[]" value="{{ $item->arrival_quantity ?? '' }}" >
                                    </td>
                                      <td class="tA-cn">
                                       <input type="checkbox" name="unable_to_resharpen_flag[]" value="1" {{ $item->unable_to_resharpen_flag == 1 ? 'checked' : '' }} >
                                      </td>
                                    <td class="tA-cn">
                                        <input type="text" name="slip_no[]" value="{{ $item->slip_no  ?? ''}}" >
                                    </td>
                                    <td class="tA-cn">
                                        <input type="text" name="remarks[]" value="{{ $item->remarks  ?? ''}}" >
                                    </td>
                                    <td class="tA-cn">
                                        <div style="display: flex; align-items: center;">
                                            <input type="checkbox" class="date_checkbox" id="dateCheckbox_{{ $item->id }}" style="margin-right: 10px;" @if($item->purchase_receipt_date) checked @endif>
                                            <input type="text" id="hidden_dateInput_{{ $item->id }}" value="{{ $item->purchase_receipt_date?->format('Y-m-d') }}" name="purchase_receipt_date_hidden[]" class="d-none" >
                                            <input type="text" id="dateInput_{{ $item->id }}" value="{{ $item->purchase_receipt_date?->format('Y-m-d') }}" name="purchase_receipt_date[]" disabled>
                                            <input type="text" id="dateInput_{{ $item->id }}" name="id[]" value="{{ $item->id }}" hidden>
                                        </div>
                                    </td>                                
                                    <td class="tA-cn">
                                        <button type="button" class="buttonBasic bColor-etc cursor-pointer" 
                                                    style="height: 40px; padding: 8px; width: 80px; background:#e26415; color: #fff; otuline:none;border:0;"
                                                    onclick="confirmDelete(this)">取消</button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="tA-cn">
                                        検索結果はありません
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </form>
            </div>

            <div class="float-right mt-2">
                <a href="{{ route("purchase.order.index",[
                    'order_date_end' => now()->endOfMonth()->format('Ymd'),
                    'status' => 'all',
                    'acceptance' => 'all'
                ]) }}" type="button" class="btn btn-blue btn-primary">
                一覧に戻る
                </a>
                <button type="button" id="storeDataButton" onclick="submitStoreForm()"
                        class="buttonBasic bColor-green mt-1 btn btn-success @if( count($items) == 0) btn-disabled @endif" style="height: 40px; padding: 8px; width: 200px;"
                        @if( count($items) == 0) disabled @endif>入荷・受入情報更新</button>
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
        'modalId' => 'searchDepartmentModal',
        'searchLabel' => '部門',
        'resultValueElementId' => 'department_code',
        'resultNameElementId' => 'department_name',
        'model' => 'Department'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchLineModal',
        'searchLabel' => 'ライン',
        'resultValueElementId' => 'line_code',
        'resultNameElementId' => 'line_name',
        'model' => 'Line'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchMachineModal',
        'searchLabel' => '機番',
        'resultValueElementId' => 'machine_code',
        'resultNameElementId' => 'machine_name',
        'model' => 'MachineNumber'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchItemModal',
        'searchLabel' => '費目',
        'resultValueElementId' => 'item_code',
        'resultNameElementId' => 'item_name',
        'model' => 'Item'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProjectModal',
        'searchLabel' => 'プロジェクト',
        'resultValueElementId' => 'project_code',
        'resultNameElementId' => 'project_name',
        'model' => 'Project'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchCustomerModal',
        'searchLabel' => '使用先',
        'resultValueElementId' => 'where_used_code',
        'resultNameElementId' => 'where_used_name',
        'model' => 'Customer'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchEmployeeModal',
        'searchLabel' => '依頼者',
        'resultValueElementId' => 'employee_code',
        'resultNameElementId' => 'employee_name',
        'model' => 'Employee'
    ])

    @push('scripts')
        @vite(['resources/js/purchase/acceptance/edit.js'])
    @endpush
@endsection
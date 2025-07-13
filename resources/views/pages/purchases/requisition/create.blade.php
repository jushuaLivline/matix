@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    <link rel="stylesheet" href="/plugins/sweetalert2/sweetalert2.css"> 
    <style>
        .calendar-plugin input {
            text-align: left;
            width: 6rem !important;
        }
        .btnExport {
            cursor: pointer;
        }
    </style>
    @vite('resources/css/sales/sale_plan_search.css')
@endpush

@section('title', '購買依頼入力')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>依頼内容入力</span></h1>
            </div>

            @if(session('success'))
                <div id="card" style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);margin-top: 20px;">
                    <div style="text-align: left;">
                        <p style="font-size: 18px; color: #0d9c38;">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            @endif

            <div class="pagettlWrap">
                <h1><span>依頼内容入力</span></h1>
            </div>
            <form action="{{ route('purchase.requisition.store') }}" method="POST" class="mt-4 overlayedSubmitForm with-js-validation"
                id="purchaseRequisitionInputForm"
                data-confirmation-message="購買依頼情報を登録します、よろしいでしょうか？"
                data-current-user="{{ Auth::user()->employee_code }}">

                @csrf

                <input type="hidden" name="requested_date" value="{{ now()->format('Ymd') }}">
                <input type="hidden" name="state_classification" value="0">

                <div class="box mb-4">
                    <!-- 部門・ラインセクション -->
                    <div class="mb-4 d-flex-col space-y-4">
                        <div class="mb-4">
                            <label class="form-label dotted indented">部門</label> <span
                                class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" name="department_code"
                                    id="department_code" style="margin-right: 10px; width: 100px; ime-mode: disabled"
                                    data-field-name="部門"
                                    data-validate-exist-model="Department"
                                    data-validate-exist-column="code"
                                    data-inputautosearch-model="Department"
                                    data-inputautosearch-column="code"
                                    data-inputautosearch-return="name"
                                    data-inputautosearch-reference="department_name"
                                    class="text-left acceptNumericOnly"
                                    minlength="6"
                                    maxlength="6"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    value="{{ $data?->department_code }}" 
                                    required>
                                <input type="text" readonly
                                    name="department_name"
                                    id="department_name" style="margin-right: 10px; width: 290px;"
                                    value="{{ $data?->department?->name }}"
                                    class="middle-name text-left">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchDepartmentModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                            </div>
                            @error('department_code')
                                <div class="text-danger" style="color:red !important">{{ $message }}</div>
                            @enderror
                            <div data-error-container="department_code"></div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label dotted indented">ライン</label> 
                            <div class="d-flex">
                                <input type="text" name="line_code"
                                        data-field-name="ライン"
                                       data-validate-exist-model="Line"
                                       data-validate-exist-column="line_code"
                                       data-inputautosearch-model="line"
                                       data-inputautosearch-column="line_code"
                                       data-inputautosearch-return="line_name"
                                       data-inputautosearch-reference="line_name"
                                       id="line_code" style="margin-right: 10px; width: 100px"
                                       class="text-left acceptNumericOnly"
                                       minlength="3"
                                       maxlength="3"
                                       onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                       value="{{ $data?->line_code }}" >
                                <input type="text" readonly
                                       name="line_name"
                                       id="line_name" style="margin-right: 10px; width: 290px;"
                                       value="{{ $data?->line?->line_name }}"
                                       class="middle-name text-left">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchLineModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                         alt="magnifying_glass.svg">
                                </button>
                            </div>
                            @error('line_code')
                                <div class="text-danger" style="color:red !important">{{ $message }}</div>
                            @enderror
                            <div data-error-container="line_code"></div>
                        </div>
                    </div>
                
                    <!-- 品番・品名・規格セクション -->
                    <div class="mb-4" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                        <div>
                            <label class="form-label dotted indented">品番</label> <span
                                class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" id="part_number" 
                                    data-field-name="品番"
                                    name="part_number" 
                                    style="width: 100%;" 
                                    maxlength="100"
                                    value="{{ $data?->part_number }}" required>
                            </div>
                            @error('part_number')
                                <div class="text-danger" style="color:red !important">{{ $message }}</div>
                            @enderror
                            <div data-error-container="part_number"></div>
                        </div>
                        
                        <div>
                            <label class="form-label dotted indented">品名</label>
                            <div class="d-flex">
                                <input type="text" id="product_name" 
                                        data-field-name="品名"
                                        name="product_name" 
                                        style="width: 100%;" 
                                        maxlength="100"
                                        value="{{ $data?->product_name }}">
                            </div>
                            @error('product_name')
                                <div class="text-danger" style="color:red !important">{{ $message }}</div>
                            @enderror
                            <div data-error-container="product_name"></div>
                        </div>
                
                        <div>
                            <label class="form-label dotted indented">規格</label>
                            <div class="d-flex">
                                <input type="text" id="standard" 
                                        data-field-name="規格"
                                        name="standard" 
                                        style="width: 100%;" 
                                        maxlength="100"
                                        value="{{ $data?->standard }}">
                            </div>
                            @error('standard')
                                <div class="text-danger" style="color:red !important">{{ $message }}</div>
                            @enderror
                            <div data-error-container="standard"></div>
                        </div>
                    </div>

                    <!-- 発注先セクション -->
                <div class="mb-4">
                    <label class="form-label dotted indented">発注先</label> <span
                        class="others-frame btn-orange badge">必須</span>
                    <div class="d-flex">
                        <input type="text" name="supplier_code"
                            id="supplier_code" style="margin-right: 10px; width: 190px;"
                            data-field-name="発注先"
                            data-validate-exist-model="supplier"
                            data-validate-exist-column="customer_code"
                            data-inputautosearch-model="supplier"
                            data-inputautosearch-column="customer_code"
                            data-inputautosearch-return="supplier_name_abbreviation"
                            data-inputautosearch-reference="supplier_name"
                            class="text-left searchOnInput Supplier acceptNumericOnly"
                            minlength="6"
                            maxlength="6"
                            onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                            value="{{ $data?->supplier_code }}" required>
                        <input type="text" readonly
                            name="supplier_name"
                            id="supplier_name" style="margin-right: 10px; width: 200px;"
                            value="{{ $data?->supplier?->supplier_name_abbreviation }}"
                            class="middle-name text-left">
                        <button type="button" class="btnSubmitCustom js-modal-open"
                                data-target="searchSupplierModal">
                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                alt="magnifying_glass.svg">
                        </button>
                    </div>
                    @error('supplier_code')
                    <div class="text-danger" style="color:red !important">{{ $message }}</div>
                    @enderror
                    <div data-error-container="supplier_code"></div>
                </div>
                <!-- 数量・単位・単価・金額セクション -->
                <div class="mb-4" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px;">
                    <div>
                        <label class="form-label dotted indented">数量</label> <span
                            class="others-frame btn-orange badge">必須</span>
                        <div class="d-flex">
                            <input type="number" name="quantity"
                                data-field-name="数量"
                                id="quantity" style="width: 100%;"
                                class="text-left acceptNumericOnly"
                                maxlength="9"
                                data-accept-zero=true    
                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                value="{{ $data?->quantity }}" required>
                        </div>
                        @error('quantity')
                            <div class="text-danger" style="color:red !important">{{ $message }}</div>
                        @enderror
                        <div data-error-container="quantity"></div>
                    </div>

                    <div>
                        <label class="form-label dotted indented">単位</label>
                        <div class="d-flex">
                            <select name="unit_code" id="unit_code" style="width: 60%; height: 40px"
                            data-field-name="単位">
                                @foreach ($codes as $code)
                                    @if (isset($data?->unit_code) && $code->code == $data?->unit_code)
                                    <option value="{{ $code->code }}" selected>{{ $code->name }}</option>
                                    @else
                                    <option value="{{ $code->code }}">{{ $code->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        @error('unit_code')
                            <div class="text-danger" style="color:red !important">{{ $message }}</div>
                        @enderror
                        <div data-error-container="unit_code"></div>
                    </div>
                </div>
                <div class="mb-4 d-flex">
                    <div class="mr-4">
                        <label class="form-label dotted indented">単価</label> <span
                            class="others-frame btn-orange badge">必須</span>
                        <div class="d-flex">
                            <input type="text" name="unit_price"
                                id="unit_price" style="width: 100%;"
                                data-field-name="単価"
                                class="text-left acceptNumericOnly"
                                minlength="1"
                                data-accept-zero="true"
                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                value="{{ $data?->unit_price }}" required>
                        </div>
                        @error('unit_price')
                            <div class="text-danger" style="color:red !important">{{ $message }}</div>
                        @enderror
                        <div data-error-container="unit_price"></div>
                    </div>

                    <div>
                        <label class="form-label dotted indented">金額</label>
                        <div class="d-flex">
                            <input type="text" readonly
                                data-field-name="金額"
                                name="amount_of_money"
                                id="amount_of_money" style="width: 100%; margin-right: 10px;"
                                value="{{ $data?->amount_of_money }}"
                                class="middle-name text-left">
                        </div>
                        @error('amount_of_money')
                            <div class="text-danger" style="color:red !important">{{ $message }}</div>
                        @enderror
                        <div data-error-container="amount_of_money"></div>
                    </div>
                </div>
                <!-- 購入理由セクション -->
                <div class="mb-4">
                    <label class="form-label dotted indented">購入理由</label>
                    <div class="d-flex">
                        <input type="text" id="reason" 
                                data-field-name="購入理由"
                                name="reason" 
                                style="width: 50%;" 
                                maxlength="100"
                                value="{{ $data?->reason }}">
                    </div>
                    @error('reason')
                        <div class="text-danger" style="color:red !important">{{ $message }}</div>
                    @enderror
                    <div data-error-container="reason"></div>
                </div>
                <!-- 費目・納期・見積書セクション -->
                <div class="mb-4" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                    <div>
                        <label class="form-label dotted indented">費目</label> <span
                            class="others-frame btn-orange badge">必須</span>
                        <div class="d-flex">
                            <input type="text" name="expense_items"
                                data-field-name="費目"
                                data-validate-exist-model="item"
                                data-validate-exist-column="expense_item"
                                data-inputautosearch-model="item"
                                data-inputautosearch-column="expense_item"
                                data-inputautosearch-return="item_name"
                                data-inputautosearch-reference="expense_item_name"
                                id="expense_item_code" style="margin-right: 10px; width: 100px;"
                                class="text-left searchOnInput Item acceptNumericOnly"
                                minlength="3"
                                maxlength="3"
                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                value="{{ $data?->expense_items }}" required>
                            <input type="text" readonly
                                name="expense_item_name"
                                id="expense_item_name" style="margin-right: 10px; width: 290px;"
                                value="{{ $data?->expense?->item_name }}"
                                class="middle-name text-left">
                            <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchItemModal">
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                    alt="magnifying_glass.svg">
                            </button>
                        </div>
                        @error('expense_items')
                            <div class="text-danger" style="color:red !important">{{ $message }}</div>
                        @enderror
                        <div data-error-container="expense_items"></div>
                    </div>
                </div>

                <div class="mb-4 d-flex" style="width: 30%">
                    <div>
                        <label class="form-label dotted indented">納期</label>
                        <div class="d-flex">
                            @include('partials._date_picker', ['inputName' => 'deadline', "value" => $data?->deadline?->format("Ymd"),
                            'disabledPreviousDates' => true,'attributes' => 'data-error-messsage-container=#request_error_message data-field-name=納期'])
                        </div>
                        @error('deadline')
                            <div class="text-danger" style="color:red !important">{{ $message }}</div>
                        @enderror
                        <div id="request_error_message"></div>
                    </div>
                    <div>
                        <label class="form-label dotted indented mt-2">見積書</label>
                        <div class="d-flex">
                            @foreach($quotationExistenceFlags as $index => $quotationExistenceFlag)
                                <p class="formPack radioSale" style="margin-right: 20px;">
                                    <label class="radioBasic">
                                        <input type="radio" 
                                                name="quotation_existence_flag" 
                                                value="{{ $index }}" 
                                                {{ isset($data) ? ($data->quotation_existence_flag == $index ? 'checked' : '') : 
                                                    ((request()->quotation_existence_flag ?? 0) == $index ? 'checked' : '') }}>
                                        <span>{{ $quotationExistenceFlag }}</span>
                                    </label>
                                </p>
                            @endforeach
                        </div>
                        @error('quotation_existence_flag')
                            <div class="text-danger" style="color:red !important">{{ $message }}</div>
                        @enderror
                        <div data-error-container="quotation_existence_flag"></div>
                    </div>
                </div>

                <!-- 備考セクション -->
                <div class="mb-4">
                    <label class="form-label dotted indented">備考</label>
                    <div class="d-flex">
                        <input type="text" id="remarks" 
                                data-field-name="備考"
                                name="remarks" 
                                style="width: 50%;" 
                                maxlength="100"
                                value="{{ $data?->remarks }}">
                    </div>
                    @error('remarks')
                        <div class="text-danger" style="color:red !important">{{ $message }}</div>
                    @enderror
                    <div data-error-container="remarks"></div>
                </div>

                <!-- 承認方法・承認ルートセクション -->
                <div class="mb-4" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                    <div>
                        <label class="form-label dotted indented">承認方法</label>
                        <div class="d-flex">
                            @foreach($approvalMethods as $index => $approvalMethod)
                                <p class="formPack radioSale" style="margin-right: 20px;">
                                    <label class="radioBasic">
                                        <input type="radio" 
                                            data-field-name="承認方法"
                                            name="approval_method_category" 
                                            value="{{ $index }}" 
                                            {{ isset($data) ? ($data->approval_method_category == $index ? 'checked' : '') : 
                                                ((request()->approval_method_category ?? 1) == $index ? 'checked' : '') }}>
                                        <span>{{ $approvalMethod }}</span>
                                    </label>
                                </p>
                            @endforeach
                        </div>
                        @error('approval_method_category')
                            <div class="text-danger" style="color:red !important">{{ $message }}</div>
                        @enderror
                        <div data-error-container="approval_method_category"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <div id="approval-form-container">
                        <label class="form-label dotted indented">承認ルート</label>
                        <div class="d-flex">
                            <select name="approval_route_number" id="approval_route_number" 
                                    data-field-name="承認ルート"
                                    style="width: 250px; margin-right: 10px;"
                                    data-approval-route_number="{{ $data?->approval_route_number }}"
                                    ></select>
                            <button type="button" class="btn btn-blue js-modal-open"
                                    data-target="approvalModal" style="padding-top: 10px;" id="open_approval_modal">
                                承認ルート設定
                            </button>
                        </div>
                        <input type="hidden" name="next_approver" id="next_approver" value="">
                        @error('approval_route_number')
                            <div class="text-danger" style="color:red !important">{{ $message }}</div>
                        @enderror
                        <div data-error-container="approval_route_number"></div>
                    </div>
                </div>

                <div class="btnListContainer">
                    <div class="btnContainerMain justify-content-flex-end">
                        <div class="btnContainerMainRight">
                            <a href="{{route('purchase.requisition.index',[
                                'request_date_from' => now()->startOfMonth()->format('Ymd'),
                                'request_date_to' => now()->endOfMonth()->format('Ymd'),
                            ])}}" class="btn btn-blue">
                                メニューに戻る
                            </a>
                            @if (!$duplicate_flag)
                                <a href="{{route('purchase.requisition.copy_previous_input')}}" id="btn-populate-input-from-session" class="btn btn-blue">
                                    前回入力から複写
                                </a>
                            @endif
                            <button type="button" 
                                    class="btn btn-blue btn-primary"
                                    data-clear-inputs
                                    data-clear-form-target="#purchaseRequisitionInputForm"
                                    data-confirmation-message="「購買依頼（申請）情報をクリアします、よろしいでしょうか？」">
                                クリア
                            </button>
                            <button type="submit" class="btn btn-green btn-success registrationButton">
                                この内容で登録する
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div id="approvalRouteModalStorage">
    </div>
    @include('partials.modals.approval_modal')
    @include('partials.modals.create_approval_modal')
    @include('partials.modals.update_approval_modal')
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
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '発注先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchItemModal',
        'searchLabel' => '費目',
        'resultValueElementId' => 'expense_item_code',
        'resultNameElementId' => 'expense_item_name',
        'model' => 'Item'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchEmployeeModal',
        'searchLabel' => '承認者',
        'resultValueElementId' => 'employee_code',
        'resultNameElementId' => 'employee_name',
        'model' => 'Employee'
    ])
@endsection

@push('scripts')
        <script src="/plugins/sweetalert2/sweetalert2.min.js"></script>
       
        <script>
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            var count = 0;

            @if(Request::get("clone"))
                $.ajax({
                    url: "/api/purchase/purchase-requisition-input/" + {{ Request::get("clone") }} ,
                    type: 'GET',
                    headers: {
                    'X-CSRF-TOKEN': token
                    },
                    success: function(response) {
                        populateInputFields(response);
                    },
                    error: function(xhr, status, error) {
                    }
                });

                function populateInputFields(response) {
                    var responseData = response;

                    for (var key in responseData) {
                        if (responseData.hasOwnProperty(key)) {
                            var value = responseData[key];
                            $(`.input[name="${key}"`).prop('checked', false);
                            $('input[value="' + value + '"]').prop('checked', true);

                            if(key == "approval_method_category"){
                                if(value == 2){
                                    $("#approval-form-container").hide()
                                }else{
                                    $("#approval-form-container").show()
                                }
                            }
                            var inputField = document.getElementById(key);
                            if (inputField) {
                                inputField.value = value;
                            }
                        }
                    }
                }
            @endif
        </script>

        @vite('resources/js/purchase/requisition/create.js')
@endpush
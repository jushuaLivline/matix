@extends('layouts.app')

@push('styles')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/index.css')
    <style>
        .calendar-plugin input {
            text-align: left;
            width: 6rem !important;
        }
        .btnExport {
            cursor: pointer;
        }
    </style>
@endpush

@php
    $purchase_category = request()->get('purchase_category');
    $categoryName = $purchase_category == 1 ? '生産品' : '購入品';
    $title = $categoryName . ' 購入実績入力';
    $previous_data = session('previous_data');
@endphp

@section('title', $title)

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>{{$title}}</span></h1>
            </div>

            @if(session('success'))
                <div id="card" style="background-color: #f0f0f0; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);margin-top: 20px;">
                    <div style="text-align: left;">
                        <p style="font-size: 18px; color: #0d9c38; margin-bottom: 10px;">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            @endif

            <div class="pagettlWrap">
                <h1><span>{{ $title }}</span></h1>
            </div>
            <form id="form_request" class="overlayedSubmitForm with-js-validation" action="{{ route('purchase.history.store') }}" method="POST" accept-charset="utf-8">
                @csrf
                @method('POST')
                <input type="hidden" name="purchase_category" value="{{request()->get('purchase_category')}}">
                <input type="hidden" name="creator" value="{{auth()->user()->employee_code}}">
                <div class="box">
                    <div class="mb-4 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">伝票区分</label> <span class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <p class="formPack radioSale">
                                    <label class="radioBasic">
                                        <input type="radio" name="voucher_class" value="1" {{($previous_data?->voucher_class ?? 1) == 1 ? 'checked' : ''}}>
                                        <span>購入</span>
                                    </label>
                                </p>
                                <p class="formPack radioSale">
                                    <label class="radioBasic">
                                        <input type="radio" name="voucher_class" value="6" {{($previous_data?->voucher_class ?? 1) == 6 ? 'checked' : ''}}>
                                        <span>修正・返品</span>
                                    </label>
                                </p>
                                <p class="formPack radioSale">
                                    <label class="radioBasic">
                                        <input type="radio" name="voucher_class" value="9" {{($previous_data?->voucher_class ?? 1) == 9 ? 'checked' : ''}}>
                                        <span>値引</span>
                                    </label>
                                </p>
                            </div>
                            @error('voucher_class')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Display if it's 生産品 --}}
                    @if($purchase_category == 1)
                        <div class="mb-4">
                            <label class="form-label dotted indented">伝票種類</label> <span
                                class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <p class="formPack radioSale">
                                    <label class="radioBasic">
                                        <input type="radio" name="slip_type" value="1"
                                            {{ ($previous_data?->slip_type ?? 1) == 1 ? 'checked' : '' }}>
                                        <span>納入伝票</span>
                                    </label>
                                </p>
                                <p class="formPack radioSale">
                                    <label class="radioBasic">
                                        <input type="radio" name="slip_type" value="2"
                                            {{ ($previous_data?->slip_type ?? 1) == 2 ? 'checked' : '' }}>
                                        <span>外注加工伝票</span>
                                    </label>
                                </p>
                                <p class="formPack radioSale">
                                    <label class="radioBasic">
                                        <input type="radio" name="slip_type" value="3"
                                            {{ ($previous_data?->slip_type ?? 1) == 3 ? 'checked' : '' }}>
                                        <span>購入材伝票</span>
                                    </label>
                                </p>
                            </div>
                        </div>
                    @endif
            
                    <div class="mb-4">
                        <div class="" style="width: 30%">
                            <label class="form-label dotted indented">入荷日</label>
                            <span class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                @include('partials._date_picker', [
                                    'inputName' => 'arrival_date', 
                                    'value' => $previous_data?->arrival_date ?? '',
                                    'required' => true, 
                                    'attributes' => 'data-error-messsage-container=#err_msg_arr_date'
                                ])
                            </div>
                            @error('arrival_date')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <div id="err_msg_arr_date"></div>
                        </div>
                    </div>
            
                    <div class="mb-4 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">仕入先</label> <span class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" name="supplier_code"
                                       id="supplier_code"
                                       data-validate-exist-model="supplier"
                                       data-validate-exist-column="customer_code"
                                       data-inputautosearch-model="supplier"
                                       data-inputautosearch-column="customer_code"
                                       data-inputautosearch-return="supplier_name_abbreviation"
                                       data-inputautosearch-reference="supplier_name"
                                       class="text-left searchOnInput Supplier w-100c mr-10c"
                                       minlength="6"
                                       maxlength="6"
                                       onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                       value= "{{ $previous_data?->supplier_code ?? ''}}"
                                       required
                                    >
                                <input type="text" readonly
                                       name="supplier_name"
                                       id="supplier_name"
                                       value= "{{ $previous_data?->supplier_name ?? ''}}"
                                       class="middle-name text-left w-290c mr-10c">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchSupplierModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                         alt="magnifying_glass.svg">
                                </button>
                            </div>
                            @error('supplier_code')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            @error('supplier_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <div data-error-container="supplier_code"></div>
                        </div>
                    </div>
            
                    <div class="mb-4">
                        <div class="mr-3">
                            <label class="form-label dotted indented">機番</label>
                            <div class="d-flex">
                                <input type="text" name="machine_number" id="machine_number"
                                    data-validate-exist-model="MachineNumber" 
                                    data-validate-exist-column="machine_number"
                                    data-inputautosearch-model="MachineNumber" 
                                    data-inputautosearch-column="machine_number"
                                    data-inputautosearch-return="machine_number_name" 
                                    data-inputautosearch-reference="machine_number_name"
                                    class="text-left w-100c mr-10c" 
                                    maxlength="5"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    value= "{{ $previous_data?->machine_number ?? ''}}"
                                >
                                <input type="text" name="machine_number2" id="machine_number2" class="text-left mr-10c"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    maxlength="1"
                                    style="width: 35px;"
                                    value= "{{ $previous_data?->machine_branch_number ?? ''}}"
                                >
                                <input type="text" readonly name="machine_number_name" id="machine_number_name"
                                    value= "{{ $previous_data?->machine_number_name ?? ''}}"
                                    class="middle-name text-left w-290c mr-10c">
                                <button type="button" class="btnSubmitCustomMachineNumber js-modal-open"
                                    data-target="searchMachineNumberModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                            </div>
                            @error('machine_number')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
            
                    <div class="mb-4 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">部門</label>
                            <div class="d-flex">
                                <input type="text" name="department_code"
                                       id="department_code"
                                       data-validate-exist-model="Department"
                                       data-validate-exist-column="code"
                                       data-inputautosearch-model="Department"
                                       data-inputautosearch-column="code"
                                       data-inputautosearch-return="name"
                                       data-inputautosearch-reference="department_name"
                                       class="text-left w-100c mr-10c"
                                       minlength="6"
                                       maxlength="6"
                                       onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                       value= "{{ $previous_data?->department_code ?? ''}}"
                                >
                                <input type="text" readonly
                                       name="department_name"
                                       id="department_name"
                                       value= "{{ $previous_data?->department_name ?? ''}}"
                                       class="middle-name text-left w-290c mr-10c">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchDepartmentModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                         alt="magnifying_glass.svg">
                                </button>
                            </div>
                            @error('department_code')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
            
                    <div class="mb-4">
                        <label class="form-label dotted indented">ライン</label>
                        <div class="d-flex">
                            <input type="text" name="line_code"
                                    data-validate-exist-model="Line"
                                    data-validate-exist-column="line_code"
                                    data-inputautosearch-model="line"
                                    data-inputautosearch-column="line_code"
                                    data-inputautosearch-return="line_name"
                                    data-inputautosearch-reference="line_name"
                                    id="line_code"
                                    class="text-left w-150c mr-10c"
                                    minlength="3"
                                    maxlength="3"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    value= "{{ $previous_data?->line_code ?? ''}}"
                            >
                            <input type="text" readonly
                                    name="line_name"
                                    id="line_name"
                                    value= "{{ $previous_data?->line_name ?? ''}}"
                                    class="middle-name text-left w-290c mr-10c">
                            <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchLineModal">
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                            </button>
                        </div>
                        @error('line_code')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
            
                    <div class="mb-4 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">費目</label> <span class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" name="item_code" id="item_code" class="text-left mr-10c"
                                    data-validate-exist-model="Item" 
                                    data-validate-exist-column="expense_item"
                                    data-inputautosearch-model="Item" 
                                    data-inputautosearch-column="expense_item"
                                    data-inputautosearch-return="item_name" 
                                    data-inputautosearch-reference="item_name"
                                    class="text-left" 
                                    maxlength="3"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    value= "{{ $previous_data?->item_code ?? ''}}"
                                    style="width: 80px" required>
                                <input type="text" readonly name="item_name" id="item_name"
                                    value= "{{ $previous_data?->item_name ?? ''}}"
                                    class="middle-name text-left mr-10c">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchItemModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                            </div>
                            @error('item_code')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <div data-error-container="item_code"></div>
                        </div>
                    </div>
            
                    <div class="mb-4 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">品番</label> <span class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" id="part_number" 
                                    name="part_number" 
                                    style="width: 100%;" 
                                    maxlength="100"
                                    value= "{{ $previous_data?->part_number ?? ''}}"
                                    required>
                            </div>
                            @error('part_number')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <div data-error-container="part_number"></div>
                        </div>
                        <div class="mr-3">
                            <label class="form-label dotted indented">品名</label> <span class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" name="product_name"
                                    id="product_name"
                                    class="text-left w-190c"
                                    value= "{{ $previous_data?->product_name ?? ''}}"
                                    required>
                            </div>
                            @error('product_name')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <div data-error-container="product_name"></div>
                        </div>
                        <div class="mr-3">
                            <label class="form-label dotted indented">規格</label>
                            <div class="d-flex">
                                <input type="text" name="standard"
                                    id="standard"
                                    class="text-left w-190c"
                                    value= "{{ $previous_data?->standard ?? ''}}"
                                >
                            </div>
                            @error('standard')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
            
                    <div class="mb-4 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">使用先</label>
                            <div class="d-flex">
                                <input type="text" name="where_to_use_code" id="where_to_use_code"
                                    data-validate-exist-model="Customer" 
                                    data-validate-exist-column="customer_code"
                                    data-inputautosearch-model="Customer" 
                                    data-inputautosearch-column="customer_code"
                                    data-inputautosearch-return="customer_name" 
                                    data-inputautosearch-reference="where_to_use_name"
                                    class="text-left mr-10c w-100c" 
                                    style="margin-right: 15px;"
                                    maxlength="6"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    value="{{ $previous_data?->where_to_use_code ?? ''}}">
                                <input type="text" readonly name="where_to_use_name" id="where_to_use_name"
                                    value="{{ $previous_data?->where_to_use_name ?? ''}}"
                                    class="middle-name text-left mr-10c"
                                    style="width: 300px;">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                    data-target="searchCustomerModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                </button>
                            </div>
                            @error('where_to_use_code')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
            
                    <div class="mb-4 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">数量</label> <span class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="number" 
                                    name="quantity"
                                    id="quantity"
                                    class="text-left w-100c mr-10c"
                                    min="1"
                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                    value="{{ $previous_data?->quantity ?? ''}}"
                                    required>
                            </div>
                            @error('quantity')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                            <div data-error-container="quantity"></div>
                        </div>
            
                        <div class="mr-3">
                           <label class="form-label dotted indented">単位</label>
                           <div class="d-flex">
                               <select class="" name="unit_code" id="unit_code" style="width: 100%; height: 40px">
                                   @foreach ($codes as $code)
                                       @if ($code->code == $previous_data?->unit_code)
                                           <option value="{{ $code->code }}" selected>{{ $code->name }}</option>
                                       @else
                                           <option value="{{ $code->code }}">{{ $code->name }}</option>
                                       @endif
                                   @endforeach
                               </select>
                           </div>
                           @error('unit_code')
                               <div class="text-danger">{{ $message }}</div>
                           @enderror
                       </div>
                   </div>
            
                   <div class="mb-4 d-flex">
                    <div class="mr-3">
                        <label class="form-label dotted indented">単価</label> <span class="others-frame btn-orange badge">必須</span>
                        <div class="d-flex">
                            <input type="text" 
                                name="unit_price" 
                                id="unit_price" 
                                class="text-right" 
                                style="width: 120px;"
                                value="{{ $previous_data?->unit_price ?? '' }}"
                                onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                required>
                        </div>
                        @error('unit_price')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        <div data-error-container="unit_price"></div>
                    </div>
                
                    <div class="mr-3">
                        <label class="form-label dotted indented">金額</label>
                        <div class="d-flex">
                            <input type="text" 
                                readonly 
                                name="amount_of_money" 
                                id="amount_of_money"
                                value="{{ $previous_data?->amount_of_money ?? '' }}"
                                class="middle-name text-right w-150c mr-10c">
                        </div>
                        @error('amount_of_money')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            
                   <div class="mb-4 d-flex">
                       <div class="mr-3">
                           <label class="form-label dotted indented">課税区分</label> <span class="others-frame btn-orange badge">必須</span>
                           <div class="d-flex">
                               <p class="formPack radioSale">
                                   <label class="radioBasic">
                                       <input type="radio" name="tax_classification" value="1" {{ ($previous_data?->tax_classification ?? 1) == 1 ? 'checked' : '' }} required>
                                       <span>課税</span>
                                   </label>
                               </p>
                               <p class="formPack radioSale">
                                   <label class="radioBasic">
                                       <input type="radio" name="tax_classification" value="2" {{ ($previous_data?->tax_classification ?? 1) == 2 ? 'checked' : '' }}>
                                       <span>非課税</span>
                                   </label>
                               </p>
                           </div>
                           @error('tax_classification')
                               <div class="text-danger">{{ $message }}</div>
                           @enderror
                       </div>
                   </div>
            
                   <div class="mb-4 d-flex">
                       <div class="mr-3">
                           <label class="form-label dotted indented">伝票No.</label>
                           <div class="d-flex">
                               <input type="text" name="slip_code"
                                      id="slip_code"
                                      class="text-left mr-10c"
                                      value="{{ $previous_data?->slip_code }}">
                           </div>
                           @error('slip_code')
                               <div class="text-danger">{{ $message }}</div>
                           @enderror
                       </div>
                   </div>
            
                   <div class="mb-4">
                       <label class="form-label dotted indented">プロジェクトNo.</label>
                       <div class="d-flex">
                           <input type="text" name="project_code" id="project_code" class="text-left mr-10c"
                               data-validate-exist-model="Project" 
                               data-validate-exist-column="project_number"
                               data-inputautosearch-model="Project" 
                               data-inputautosearch-column="project_number"
                               data-inputautosearch-return="project_name" 
                               data-inputautosearch-reference="project_name"
                               class="text-left" 
                               maxlength="8"
                               style="width: 120px;" value="{{ $previous_data?->project_code }}">
                           <input type="text" readonly name="project_name" id="project_name"
                               value="{{ $previous_data?->project_name }}"
                               class="middle-name text-left mr-10c">
                           <button type="button" class="btnSubmitCustom js-modal-open"
                               data-target="searchProjectModal">
                               <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                   alt="magnifying_glass.svg">
                           </button>
                       </div>
                       @error('project_code')
                           <div class="text-danger">{{ $message }}</div>
                       @enderror
                   </div>
            
                   <div class="mb-2 d-flex">
                       <div class="mr-3">
                           <label class="form-label dotted indented">備考</label>
                           <div class="d-flex">
                               <textarea rows="5" cols="100" type="text" name="remarks" id="remarks" value="" class=""
                                   placeholder="">{{$previous_data?->remarks}}</textarea>
                           </div>
                           @error('remarks')
                               <div class="text-danger">{{ $message }}</div>
                           @enderror
                       </div>
                   </div>
            
                   <div class="error_msg"></div>
               </div>
            
               <div class="btnListContainer">
                   <div class="btnContainerMain justify-content-flex-end">
                       <div class="btnContainerMainRight">
                           <a type="button" class="btn btn-blue" href="{{ route('purchase.history.index',[
                                'arrival_date_start' => now()->startOfMonth()->format('Ymd'),
                                'arrival_date_end' => now()->endOfMonth()->format('Ymd'),
                                'voucher_class' => 0,
                                'category' => 2,
                           ]) }}">
                               一覧に戻る
                           </a>
                           @if($is_previous_input)
                               <a type="button" class="btn btn-blue" href="{{ route('purchase.history.copy_previous_input', ['purchase_category' => request()->get('purchase_category')]) }}">
                                前回入力から複写
                               </a>
                            @endif
                           <a type="button" id="clear_form" class="btn btn-blue">
                               クリア
                           </a>
                           <button type="submit" class="btn btn-green" id="submit_button" onclick="return confirm('購入実績を登録します、よろしいでしょうか？');">
                               この内容で登録する
                           </button>
                       </div>
                   </div>
               </div>
            </form>
        </div>
    </div>

    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '仕入先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier'
    ])
    @include('partials.modals.machine_numbers._search', [
        'modalId' => 'searchMachineNumberModal',
        'searchLabel' => '機番',
        'resultValueElementId' => 'machine_number',
        'resultNameElementId' => 'machine_number_name',
        'resultBranchNumberEle' => 'machine_number2',
        'model' => 'MachineNumber',
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
        'modalId' => 'searchLineModal',
        'searchLabel' => 'ライン',
        'resultValueElementId' => 'line_code',
        'resultNameElementId' => 'line_name',
        'model' => 'Line'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchItemModal',
        'searchLabel' => '仕入先',
        'resultValueElementId' => 'item_code',
        'resultNameElementId' => 'item_name',
        'model' => 'Item'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductNumberModal',
        'searchLabel' => '品番',
        'resultValueElementId' => 'product_number_number',
        'resultNameElementId' => 'product_number_name',
        'model' => 'ProductNumber',
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProjectModal',
        'searchLabel' => 'プロジェクトNo.',
        'resultValueElementId' => 'project_code',
        'resultNameElementId' => 'project_name',
        'model' => 'Project',
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchCustomerModal',
        'searchLabel' => '使用先.',
        'resultValueElementId' => 'where_to_use_code',
        'resultNameElementId' => 'where_to_use_name',
        'model' => 'Customer',
    ])

@php
$configs = [
    'Supplier' => 'supplier_name',
    'MachineNumber' => 'machine_number_name',
    'Department' => 'department_name',
    'Line' => 'line_name',
    'Item' => 'item_name',
    'ProductNumber' => 'product_number_name',
    'Project' => 'project_name',
    'Customer' => 'where_to_use_name'
];

foreach ($configs as $key => $reference) {
    $dataConfigs[$key] = [
        'model' => $key,
        'reference' => $reference
    ];
}
@endphp

<x-search-on-input :dataConfigs="$dataConfigs" />
@endsection
@push('scripts')
    @vite('resources/js/purchase/history/create.js')
@endpush
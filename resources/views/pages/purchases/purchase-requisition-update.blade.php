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

@section('title', '発注処理詳細')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>発注処理詳細</span></h1>
            </div>

            <div class="pagettlWrap">
                <h1><span>発注処理詳細</span></h1>
            </div>
            <form action="{{ route('purchase.order.processing.update', $purchaseRequisition) }}" id="form" method="POST" class="overlayedSubmitForm">
                @csrf @method("PUT")
                <div class="box mb-3">
                    <div class="mb-3 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">購買依頼No.</label>
                            <div class="d-flex">
                                <input type="text" id="requisition_number" name="requisition_number" value="{{ $purchaseRequisition->requisition_number }}" style="width:400px; margin-right: 10px;">
                            </div>
                        </div>
                        <div class="mr-3">
                            <label class="form-label dotted indented">依頼日</label>
                            <div class="d-flex">
                                @include('partials._date_picker', ['inputName' => 'requested_date', 'value' => $purchaseRequisition->requested_date?->format("Ymd")])
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">部門</label> <span
                                class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" name="department_code" value="{{ $purchaseRequisition->department_code }}"
                                       id="department_code" style="margin-right: 10px;"
                                       class="text-left searchOnInput Department" required>
                                <input type="text" readonly
                                       name="department_name" value="{{ $purchaseRequisition->department?->name }}"
                                       id="department_name" style="margin-right: 10px;"
                                       class="middle-name text-left">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchDepartmentModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                         alt="magnifying_glass.svg">
                                </button>
                            </div>
                        </div>
                        <div class="mr-3">
                            <label class="form-label dotted indented">ライン</label> <span
                                class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" name="line_code" value="{{ $purchaseRequisition->line_code }}"
                                       id="line_code" style="margin-right: 10px;"
                                       class="text-left searchOnInput Line" required>
                                <input type="text" readonly
                                       name="line_name" value="{{ $purchaseRequisition->line?->line_name }}"
                                       id="line_name" style="margin-right: 10px;"
                                       class="middle-name text-left">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchLineModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                         alt="magnifying_glass.svg">
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 d-flex">
                        
                        <div class="mr-3">
                            <label class="form-label dotted indented">品番</label> <span
                                class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" id="part_no" name="part_number" style="width:400px; margin-right: 10px;" value="{{ $purchaseRequisition->part_number }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 d-flex">
                        
                        <div class="mr-3">
                            <label class="form-label dotted indented">品名</label>
                            <div class="d-flex">
                                <input type="text" id="product_name" name="product_name" style="width:400px; margin-right: 10px;" value="{{ $purchaseRequisition->product_name }}">
                            </div>
                        </div>
                        <div class="mr-3">
                            <label class="form-label dotted indented">規格</label>
                            <div class="d-flex">
                                <input type="text" id="specification" name="standard" style="width:400px; margin-right: 10px;" value="{{ $purchaseRequisition->standard }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 d-flex">
                        
                        <div class="mr-3">
                            <label class="form-label dotted indented">発注先</label> <span
                                class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" name="supplier_code" value="{{ $purchaseRequisition->supplier_code }}"
                                       id="supplier_code" style="margin-right: 10px;"
                                       class="text-left searchOnInput Supplier" required>
                                <input type="text" readonly
                                       name="supplier_name" value="{{ $purchaseRequisition->supplier?->customer_name }}"
                                       id="supplier_name" style="margin-right: 10px;"
                                       class="middle-name text-left">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchSupplierModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                         alt="magnifying_glass.svg">
                                </button>
                            </div>
                        </div>
                        <div class="mr-3">
                            <label class="form-label dotted indented">数量</label> <span
                                class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" name="quantity"
                                       id="quantity" style="margin-right: 10px; width: 100px;"
                                       class="text-left"
                                       value="{{ $purchaseRequisition->quantity }}" required>
                            </div>
                        </div>
                        <div class="mr-3">
                            <label class="form-label dotted indented">単位</label>
                            <div class="d-flex">
                                <select class="" name="unit_code" id="unit_code" style="width: 100%; height: 40px">
                                    @foreach ($codes as $code)
                                        <option value="{{ $code->code }}" @selected($code->code == $purchaseRequisition->unit_code)>{{ $code->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 d-flex">
                        
                        <div class="mr-3">
                            <label class="form-label dotted indented">単価</label>
                            <div class="d-flex">
                                <input type="text" name="unit_price"
                                       id="unit_price" style="margin-right: 10px; width: 200px;"
                                       class="text-left"
                                       value="{{ $purchaseRequisition->unit_price }}">
                            </div>
                        </div>
                        <div class="mr-3">
                            <label class="form-label dotted indented">金額</label>
                            <div class="d-flex">
                                <input type="text" readonly
                                       name="amount_of_money" value="{{ $purchaseRequisition->amount_of_money }}"
                                       id="amount_of_money" style="margin-right: 10px;"
                                       class="middle-name text-left">
                                <button type="button" class="btnSubmitCustom" style="padding-top: 10px;" id="calculate_amount">
                                金額計算
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 d-flex">
                        
                        <div class="mr-3">
                            <label class="form-label dotted indented">購入理由</label>
                            <div class="d-flex">
                                <input type="text" id="reason" name="reason" value={{ $purchaseRequisition->reason }} style="width:400px; margin-right: 10px;" >
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 d-flex">
                        
                        <div class="mr-3">
                            <label class="form-label dotted indented">費目</label> <span
                                class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" name="expense_item_code" value="{{ $purchaseRequisition->expense_items }}"
                                       id="expense_item_code" style="margin-right: 10px;"
                                       class="text-left searchOnInput Item" required>
                                <input type="text" readonly
                                       name="expense_item_name" value="{{ $purchaseRequisition->item?->item_name }}"
                                       id="expense_item_name" style="margin-right: 10px;"
                                       class="middle-name text-left">
                                <button type="button" class="btnSubmitCustom js-modal-open"
                                        data-target="searchItemModal">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                         alt="magnifying_glass.svg">
                                </button>
                            </div>
                        </div>
                        <div class="mr-3">
                            <label class="form-label dotted indented">納期</label>
                            <div class="d-flex">
                                @include('partials._date_picker', ['inputName' => 'deadline', 'value' => $purchaseRequisition->deadline?->format("Ymd")])
                            </div>
                        </div>
                        <div class="mr-3">
                            <label class="form-label dotted indented">見積書</label>
                            <div class="d-flex">
                                <p class="formPack radioSale">
                                    <label class="radioBasic">
                                        <input type="radio" name="quotation_existence_flag" value="1" {{ ($purchaseRequisition->quotation_existence_flag ?? 1) == 1 ? 'checked' : '' }}>
                                        <span>無し</span>
                                    </label>
                                </p>
                                <p class="formPack radioSale">
                                    <label class="radioBasic">
                                        <input type="radio" name="quotation_existence_flag" value="2" {{ ($purchaseRequisition->quotation_existence_flag ?? 1) == 2 ? 'checked' : '' }}>
                                        <span>有り</span>
                                    </label>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">備考</label>
                            <div class="d-flex">
                                <input type="text" id="remarks" name="remarks" style="width:400px; margin-right: 10px;" value="{{ $purchaseRequisition->remarks }}">
                            </div>
                        </div>
                        <div class="mr-3">
                            <label class="form-label dotted indented">補助費目</label>
                            <div class="d-flex">
                                <p class="formPack radioSale">
                                    <label class="radioBasic">
                                        <input type="radio" name="subsidy_items" value="0" {{ ($purchaseRequisition->subsidy_items ?? 0) == 0 ? 'checked' : '' }}>
                                        <span>無し</span>
                                    </label>
                                </p>
                                <p class="formPack radioSale">
                                    <label class="radioBasic">
                                        <input type="radio" name="subsidy_items" value="1" {{ $purchaseRequisition->subsidy_items == 1 ? 'checked' : '' }}>
                                        <span>有り</span>
                                    </label>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4 d-flex">
                        <div class="mr-3">
                            <label class="form-label dotted indented">承認方法</label>
                            <div class="d-flex">
                                <p class="formPack radioSale">
                                    <label class="radioBasic">
                                        <input type="radio" name="approval_method_category" value="1" {{ ($purchaseRequisition->approval_method_category ?? 1) == 1 ? 'checked' : '' }}>
                                        <span>システム</span>
                                    </label>
                                </p>
                                <p class="formPack radioSale">
                                    <label class="radioBasic">
                                        <input type="radio" name="approval_method_category" value="2" {{ ($purchaseRequisition->approval_method_category ?? 1) == 2 ? 'checked' : '' }}>
                                        <span>依頼書</span>
                                    </label>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @if(session('success'))
                    <div id="card" style="background-color: #f0f0f0; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);margin-top: 20px;">
                        <div style="text-align: center;">
                            <p style="font-size: 18px; color: #0d9c38; margin-bottom: 10px;">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                @endif

                <div class="btnListContainer">
                    <div class="btnContainerMain justify-content-flex-end">
                        <div class="btnContainerMainRight">
                            <button type="submit" class="btn btn-green px-5">
                                更新
                            </button>
                            <button type="button" onclick="deleteItem()" class="btn btn-orange px-5">
                                削除
                            </button>
                            <button type="reset" class="btn btn-blue btn-reset px-5">
                                クリア
                            </button>
                            <a href="{{ route("purchase.order.processing") }}" type="button" class="btn btn-blue  px-5">
                                戻る
                            </a>
                        </div>
                    </div>
                </div>
            </form>
            <form action="{{ route("purchase.order.processing.delete", $purchaseRequisition) }}" method="POST" id="deleteForm">@method("DELETE") @csrf</form>
    </div>

    @php
        $dataConfigs['Item'] = [
            'model' => 'Item',
            'reference' => 'expense_item_name'
        ];
        $dataConfigs['Supplier'] = [
            'model' => 'Supplier',
            'reference' => 'supplier_name'
        ];
        $dataConfigs['Department'] = [
            'model' => 'Department',
            'reference' => 'department_name'
        ];
        $dataConfigs['Line'] = [
            'model' => 'Line',
            'reference' => 'line_name'
        ];
    @endphp

    <x-search-on-input :dataConfigs="$dataConfigs" />

    <div id="approvalRouteModalStorage">
        <?php
            $search_img = asset('images/icons/magnifying_glass.svg');
        ?>
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
        'searchLabel' => '費目選択',
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

        $("#calculate_amount").on('click', function () {
            let amount = $("#price").val() * $("#quantity").val();
            $("#amount_of_money").val(amount);
        });

        function deleteItem(){
            var confirmMessage = confirm("本当にクリアしますか？");
            if(confirmMessage){
                $("#deleteForm").submit();
            }
        }

    </script>
@endpush
@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/materials/supply_fraction_instruction.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '端数指示入力')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                端数指示入力
            </div>
            
            <div id="warningInputs" style="background-color: #fff; margin-top:20px; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); color: red; display: none">
                <div style="text-align: left;">登録に必要ないくつかの情報が入力されていません！</div>
            </div>
            <div id="successInputs" style="background-color: #fff; margin-top:20px; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); display:none; color:#0d9c38;">
                <div style="text-align: left;">データは正常に登録されました</div>
            </div>
            <div id="successUpdate" style="background-color: #fff; margin-top:20px; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); display:none; color:#0d9c38;">
                <div style="text-align: left;">データは正常に更新されました</div>
            </div>
            
            <div class="section">
                <h1 class="form-label bar indented">端数指示入力</h1>
                <div class="box mb-3">
                    <div class="mb-2">
                        <form class="overlayedSubmitForm with-js-validation">
                            <div class="mr-3">
                                <label class="form-label dotted indented">材料メーカー</label> <span
                                    class="btn-orange badge">必須</span>
                                    <div class="d-flex">
                                        <input type="text" id="supplier_code" 
                                                    data-field-name="外注先"
                                                    data-error-messsage-container="#supplier_code_error"
                                                    data-validate-exist-model="supplier"
                                                    data-validate-exist-column="customer_code"
                                                    data-inputautosearch-model="supplier"
                                                    data-inputautosearch-column="customer_code"
                                                    data-inputautosearch-return="supplier_name_abbreviation"
                                                    data-inputautosearch-reference="supplier_name"
                                                    name="supplier_code" style="width:100px; margin-right: 10px;" value="{{ $supplier->customer_code ?? '' }}">
                                        <input type="text" id="supplier_name" name="supplier_name" readonly value="{{ $supplier->customer_name ?? '' }}" style="margin-right: 10px;">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchSupplierModal"
                                                data-query-field="">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                        </button>
                                    </div>
                                    <div id="supplier_code_error"></div>
                            </div>
                        </form>
                        <div class="mt-2 material-fraction-table">
                            <table class="table table-bordered table-striped text-center">
                                <thead>
                                <tr>
                                    <th>材料品番</th>
                                    <th>材料品名</th>
                                    <th>指示日</th>
                                    <th width="5%">便</th>
                                    <th width="6%">数量</th>
                                    <th width="12%">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($supplyOrders as $data)
                                        <tr data-instruction-id="{{ $data['id'] }}" data-editable-field>
                                            <td>
                                                <div class="center">
                                                    <input type="text" id="product_code_{{ $data['id'] }}" name="product_code" class="mr-1" value="{{ $data['material_number'] }}" data-old-value="{{ $data['material_number'] }}" disabled>
                                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                                            data-target="searchProductModal_{{ $data['id'] }}"
                                                            disabled
                                                            data-modal-button>
                                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                                alt="magnifying_glass.svg">
                                                    </button>
                                                </div>
                                                <div id="product_code_error" class="error_message text-left"></div>
                                            </td>
                                            <td>
                                                <input type="text" readonly
                                                    id="product_name_{{ $data['id'] }}"
                                                    name="product_name"
                                                    value="{{ $data['product_name'] }}"
                                                    class="middle-name"
                                                    style="width: 170px"
                                                    data-old-value="{{ $data['product_name'] }}">
                                            </td>
                                            <td width="18%">
                                                <div style="display: flex; justify-content: center;">
                                                    <input type="text" name="instruction_date" style="text-align: center" 
                                                        id="instruction_date_{{ $data['id'] }}"
                                                        data-format="YYYYMMDD"
                                                        minlength="8"
                                                        maxlength="8"
                                                        pattern="\d*" 
                                                        value="{{ $data['instruction_date'] }}"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                        disabled
                                                        data-old-value="{{ $data['instruction_date'] }}">
                                                    <button type="button" class="btnSubmitCustom buttonPickerJS ml-1" 
                                                            data-target="instruction_date_{{ $data['id'] }}"
                                                            data-format="YYYYMMDD"
                                                            disabled
                                                            data-modal-button>
                                                        <img src="{{ asset('images/icons/iconsvg_calendar_w.svg') }}" alt="iconsvg_calendar_w.svg">
                                                    </button>
                                                </div>
                                                <div id="instruction_date_error" class="error_message text-left"></div>
                                            </td>
                                            <td>
                                                <input type="text" class="numberCharacter acceptNumericOnly" 
                                                data-accept-zero="true"
                                                maxlength="2"
                                                name="instruction_no" 
                                                disabled 
                                                value="{{ $data['instruction_no'] }}"
                                                data-old-value="{{ $data['instruction_no'] }}"
                                                >
                                                <div id="instruction_no_error" class="error_message text-left"></div>
                                            </td>
                                            <td>
                                                <input type="text" class="numberCharacter acceptNumericOnly" 
                                                data-accept-zero="true"
                                                name="instruction_kanban_quantity" disabled value="{{ $data['instruction_kanban_quantity'] }}"
                                                data-old-value="{{ $data['instruction_kanban_quantity'] }}"
                                                >
                                                <div id="instruction_kanban_quantity_error" class="error_message text-left"></div>
                                            </td>
                                            <td>
                                                <div class="center" id="EditDelete">
                                                    <button class="btn btn-block btn-blue mr-1" id="edit"
                                                    data-edit-button>編集</button>
                                                    <button class="btn btn-block btn-orange" style="margin-left: 2px" id="delete" data-delete-button>削除</button>
                                                </div>
                                                
                                                <div class="center" id="UdpateUndo" style="display: none;">
                                                    <button class="btn btn-block btn-green mr-1" id="update"
                                                    data-update-button>更新</button>
                                                    <button  class="btn btn-block btn-gray" style="margin-left: 1px" id="undo" data-cancel-button>取消</button>
                                                </div>
                                            </td>
                                        </tr>
                                        @include('partials.modals.masters._search', [
                                            'modalId' => 'searchProductModal_'. $data['id'],
                                            'searchLabel' => '材料',
                                            'resultValueElementId' => 'product_code_'. $data['id'],
                                            'resultNameElementId' => 'product_name_'. $data['id'],
                                            'model' => 'ProductMaterial'
                                        ])
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>
                                            <div class="center">
                                                <input type="text" id="product_code" value="" class="mr-1">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                        data-target="searchProductNumberModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                            alt="magnifying_glass.svg">
                                                </button>
                                            </div>
                                            <div id="product_code_error" class="error_message text-left"></div>
                                        </td>
                                        <td>
                                            <input type="text" readonly
                                                id="product_name"
                                                value=""
                                                class="middle-name"
                                                style="width: 170px">
                                        </td>
                                        <td width="18%">
                                            <div style="display: flex; justify-content: center;">
                                                <input type="text" style="text-align: center" 
                                                    id="date_instruction" 
                                                    data-format="YYYYMMDD"
                                                    minlength="8"
                                                    maxlength="8"
                                                    pattern="\d*" 
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                <button type="button" class="btnSubmitCustom buttonPickerJS ml-1 buttonPickerJSDate" 
                                                        data-target="date_instruction"
                                                        data-format="YYYYMMDD">
                                                    <img src="{{ asset('images/icons/iconsvg_calendar_w.svg') }}" alt="iconsvg_calendar_w.svg">
                                                </button>
                                            </div>
                                            <div id="instruction_date_error" class="error_message"></div>
                                        </td>
                                        <td>
                                            <input type="text" class="numberCharacters acceptNumericOnly text-right"
                                                data-accept-zero="true" 
                                                id="number_instruction" value="" maxlength="2">
                                                <div id="instruction_no_error" class="error_message"></div>
                                        </td>
                                        <td>
                                            <input type="text" class="numberCharacters acceptNumericOnly text-right" 
                                            data-accept-zero="true" id="instruction_kanban_quantity" value="">
                                            <div id="instruction_kanban_quantity_error" class="error_message"></div>
                                        </td>
                                        <td>
                                            <div class="center">
                                                <button  class="btn btn-block btn-green mr-1" data-insert-button style="width:100px;">追加</button>
                                                <button   class="btn btn-block btn-gray" style="margin-left: 1px; width:100px;"
                                                data-clear-button>クリア</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div style="text-align:right">
                <a href="{{ route('material.fractionCreate.index') }}" class="btn btn-blue" style="width: 250px"> 一覧に戻る </a>
                <button  class="btn btn-green btn-disabled" style="width: 15rem;"
                disabled
                data-bulk-save-button> この内容で登録する </button>
            </div>
        </div>
    </div>

    <div id="supplyInstructionData" data-info='@json(session("sessionSupplyInstructionData", []))'></div>
    
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductNumberModal',
        'searchLabel' => '材料',
        'resultValueElementId' => 'product_code',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductMaterial'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '材料メーカー',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier',
        'query'=> "searchProductNumberModal",
        'reference' => "supplier_code"
    ])

@endsection

@push('scripts')
    @vite('resources/js/material/fraction/create.js');
@endpush
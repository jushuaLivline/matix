@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/materials/supply_fraction_instruction.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '支給材端数指示入力')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
              外注加工支給品指示入力

            </div>
            
            <div id="warningInputs" style="background-color: #fff; margin-top:20px; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); color: red; display: none">
                <div style="text-align: left;">登録に必要ないくつかの情報が入力されていません！</div>
            </div>
            <div id="successInputs" style="background-color: #fff; margin-top:20px; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); display:none; color:#0d9c38;">
                <div style="text-align: left;">データは正常に登録されました</div>
            </div>
            <div id="successUpdate" style="background-color: #fff; margin-top:20px; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); display:none; color:#0d9c38;">
                <div style="text-align: left;">更新が完了いたしました</div>
            </div>
            
            <div class="section">
                <h1 class="form-label bar indented">外注加工支給品指示入力</h1>
                <div class="box mb-3">
                    <div class="mb-2">
                        <form class="overlayedSubmitForm with-js-validation">
                            <div class="mr-3">
                                <label class="form-label dotted indented">支給先</label> <span
                                    class="btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <input type="text"
                                        id="supplier_code"
                                        name="supplier_code"
                                        data-field-name="支給先"
                                        data-validate-exist-model="supplier"
                                        data-validate-exist-column="customer_code"
                                        data-inputautosearch-model="supplier"
                                        data-inputautosearch-column="customer_code"
                                        data-inputautosearch-return="supplier_name_abbreviation"
                                        data-inputautosearch-reference="supplier_name"
                                        minlength="6"
                                        maxlength="6"
                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                        value="{{ $supplier->customer_code ?? '' }}"
                                        class="w-10 acceptNumericOnly">
                                    <input type="text" readonly
                                        id="supplier_name"
                                        name="supplier_name"
                                        value="{{ $supplier->supplier_name_abbreviation ?? '' }}"
                                        class="middle-name ml-half"
                                        style="width: 200px">
                                    <button type="button" class="btnSubmitCustom js-modal-open ml-half"
                                        data-target="searchSupplierModal"
                                        data-query="searchProductNumberModal"
                                        data-reference="customer_code">
                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                                    </button>
                                </div>
                                <div id="supplier_code_error" class="error_message text-left"></div>
                            </div>
                        </form>
                        <div class="mt-2">
                            <table class="table table-bordered table-striped text-center">
                                <thead>
                                <tr>
                                    <th>製品品番</th>
                                    <th>品名</th>
                                    <th>支給日</th>
                                    <th width="5%">便</th>
                                    <th width="6%">数量</th>
                                    <th width="6%">有償/無償</th>
                                    <th width="12%">操作</th>

                                </tr>
                                </thead>
                                <tbody class="align-top">
                                    @foreach($supplyReplenishment as $data)
                                        <tr data-instruction-id="{{ $data['id'] }}" data-editable-field>
                                            <td>
                                                <div class="center">
                                                    <input type="text" 
                                                        id="product_code_{{ $data['id'] }}" 
                                                        daata-validate-exist-model="ProductNumber"
                                                        data-validate-exist-column="part_number"
                                                        data-inputautosearch-model="ProductNumber"
                                                        data-inputautosearch-column="part_number"
                                                        data-inputautosearch-return="product_name"
                                                        data-inputautosearch-reference="product_name"
                                                        name="product_code" class="mr-1" 
                                                        value="{{ $data['product_code'] }}" 
                                                        data-old-value="{{ $data['product_code'] }}" disabled>
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
                                            <td>
                                                <div style="display: flex; justify-content:left;">
                                                    <input type="text" name="supply_date" style="text-align:left" 
                                                        id="supply_date_{{ $data['id'] }}"
                                                        data-format="YYYYMMDD"
                                                        minlength="8"
                                                        maxlength="8"
                                                        pattern="\d*" 
                                                        value="{{ $data['supply_date'] }}"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                        disabled
                                                        data-old-value="{{ $data['supply_date'] }}">
                                                    <button type="button" class="btnSubmitCustom buttonPickerJS ml-1" 
                                                            data-target="supply_date_{{ $data['id'] }}"
                                                            data-format="YYYYMMDD"
                                                            disabled
                                                            data-modal-button>
                                                        <img src="{{ asset('images/icons/iconsvg_calendar_w.svg') }}" alt="iconsvg_calendar_w.svg">
                                                    </button>
                                                </div>
                                                <div id="supply_date_error" class="error_message text-left"></div>
                                            </td>
                                            <td>
                                                <input type="text" class="numberCharacter acceptNumericOnly" 
                                                data-accept-zero="true"
                                                maxlength="2"
                                                name="supply_flight_no" 
                                                disabled 
                                                value="{{ $data['supply_flight_no'] }}"
                                                data-old-value="{{ $data['supply_flight_no'] }}"
                                                >
                                                <div id="supply_flight_no_error" class="error_message text-left"></div>
                                            </td>
                                            <td>
                                                <input type="text" class="numberCharacter acceptNumericOnly" 
                                                data-accept-zero="true"
                                                name="supply_quantity" disabled value="{{ $data['supply_quantity'] }}"
                                                data-old-value="{{ $data['supply_quantity'] }}"
                                                >
                                                <div id="supply_quantity_error" class="error_message text-left"></div>
                                            </td>
                                            <td>
                                                <select name="payment_classification" 
                                                  id="payment_classification"
                                                  disabled
                                                  width="w-full"
                                                  data-old-value="{{ $data['payment_classification'] }}">
                                                  <option value="1" {{ $data['payment_classification'] == 1 ? 'selected' : '' }}>無償</option>
                                                  <option value="2" {{ $data['payment_classification'] == 2 ? 'selected' : '' }}>有償</option>
                                                </select>
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
                                    <tfoot class="align-top">
                                    <tr>
                                        <td>
                                            <div>
                                                <div style="display: flex; justify-content:left;">
                                                <input type="text" 
                                                        data-validate-exist-model="ProductNumber"
                                                        data-validate-exist-column="part_number"
                                                        data-inputautosearch-model="ProductNumber"
                                                        data-inputautosearch-column="part_number"
                                                        data-inputautosearch-return="product_name"
                                                        data-inputautosearch-reference="product_name"
                                                        id="product_code" 
                                                        value="" class="mr-1">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                        data-target="searchProductNumberModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                            alt="magnifying_glass.svg">
                                                </button>
                                                </div>
                                                <div id="product_code_error" class="error_message text-left"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="text" readonly
                                                id="product_name"
                                                value=""
                                                class="middle-name"
                                                style="width: 170px">
                                        </td>
                                        <td width="18%">
                                            <div style="display: flex; justify-content:left;">
                                                <input type="text" style="text-align:left" 
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
                                            <div id="supply_date_error" class="error_message text-left"></div>
                                        </td>
                                        <td>
                                            <input type="text" class="numberCharacters acceptNumericOnly text-right"
                                                data-accept-zero="true" 
                                                id="supply_flight_no" value="" maxlength="2">
                                                <div id="supply_flight_no_error" class="error_message text-left"></div>
                                        </td>
                                        <td>
                                            <input type="text" class="numberCharacters acceptNumericOnly text-right" 
                                            data-accept-zero="true" id="supply_quantity" value="">
                                            <div id="supply_quantity_error" class="error_message text-left"></div>
                                        </td>
                                        <td>
                                            <select name="payment_classification" 
                                            id="payment_classification"
                                            width="w-full">
                                              <option value="1">無償</option>
                                              <option value="2">有償</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="center">
                                                <button  class="btn btn-block btn-green mr-1" data-insert-button style="width:100px;">追加</button>
                                                <button   class="btn btn-block btn-gray" style="margin-left: 1px; width:100px;"
                                                data-clear-button>クリア</button>
                                            </div>
                                        </td>
                                    </tr>
                                    <tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div style="text-align:right">
                <button  class="btn btn-green btn-disabled" style="width: 15rem;"
                disabled
                data-bulk-save-button> この内容で登録する </button>
            </div>
        </div>
    </div>

    <div id="SupplyReplenishmentData" data-info='@json(session("sessionSupplyReplenishmentData", []))'></div>
    
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductNumberModal',
        'searchLabel' => '材料',
        'resultValueElementId' => 'product_code',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductMaterial'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '支給先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier',
        'query'=> "searchProductNumberModal",
        'reference' => "supplier_code"
    ])

@endsection
@push('scripts')
    @vite('resources/js/outsource/supply/replenishment/create.js');
@endpush
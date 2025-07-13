@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '臨時かんばん入力')
@php
$dataConfigs = [];
@endphp
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                臨時かんばん入力
            </div>

            <div id="card" style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);margin-top: 20px;display:none;">
                <div style="text-align: left;">
                    <p style="font-size: 18px; color: #0d9c38;"></p>
                </div>
            </div>
      
            <div class="text-red" id="warningInputs" style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);margin-top: 20px;display:none;">登録に必要ないくつかの情報が入力されていません！</div>
            <div class="text-red" id="warningManagementNo" style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);margin-top: 20px;display:none;">管理No.が存在しません</div>
            <div id="successInputs" style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);margin-top: 20px; display:none; color:#0d9c38;">データは正常に登録されました</div>
            <div class="text-red d-none" id="error-message"style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);margin-top: 20px;"></div>
            <div id="successUpdate" style="background-color: #fff; margin-top:20px; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); display:none; color:#0d9c38;">
                <div style="text-align: left;">データは正常に更新されました</div>
            </div>
           
            <div class="section">
                <h1 class="form-label bar indented">臨時かんばん入力</h1>
                <div class="box mb-3 with-js-validation">
                    <div class="mb-4 d-flex-inline flex-direction-column align-items-start mr-1">
                        <label class="form-label dotted indented">指示日</label>
                        <div class="center">
                            <input type="text" id="date_instruction"
                                data-format="YYYYMMDD"
                                minlength="8"
                                maxlength="8"
                                pattern="\d*" 
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                value="{{ $sessionKanbanDataInstructionDate }}">
                            <button type="button" class="btnSubmitCustom buttonPickerJS ml-1" 
                                    data-target="date_instruction"
                                    data-format="YYYYMMDD">
                                <img src="{{ asset('images/icons/iconsvg_calendar_w.svg') }}" alt="iconsvg_calendar_w.svg">
                            </button>
                        </div>
                        <div id="instruction_date_error" class="error_message text-left"></div>
                    </div>
                    <div class="mb-2 d-flex-inline flex-direction-column align-items-start">
                        <label class="form-label dotted indented">仕入先</label>
                        <div class="d-flex">
                            <input type="text" id="supplier_code" name="supplier_code" 
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
                                value="{{ $supplier->customer_code ?? '' }}" class="searchOnInput Supplier" style="margin-left: 10px; width: 120px">
                            <input type="text" readonly id="supplier_name" name="supplier_name" value="{{ $supplier->customer_name ?? '' }}" class="middle-name ml-half" style="width: 250px">
                            <button type="button" class="btnSubmitCustom js-modal-open ml-half"
                                            data-target="searchSupplierModal"
                                            data-query="searchSupplierModal"
                                            data-reference="customer_code">
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                        alt="magnifying_glass.svg">
                            </button>
                        </div>
                    </div>

                    <div class="mb-2">
                        <div>
                            <form id="oustoruceTempKanbanForm">
                            <table id="outsourced-processings" class="table table-bordered table-striped text-center">
                                <thead>
                                <tr>
                                    <th width="5%">管理No.</th>
                                    <th width="18%">製品品番</th>
                                    <th width="22%">品名</th>
                                    <th>背番号</th>
                                    <th>便</th>
                                    <th>収容数</th>
                                    <th>枚数</th>
                                    <th width="7%">数量</th>
                                    <th>操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @foreach($sessionKanbanData as $data)
                                        <tr data-temp-kanban-id="{{ $data['id'] }}" cached>
                                            <td>
                                                <input type="text"
                                                       name="management_no"
                                                       value="{{ $data['management_no'] }}"
                                                       class="numberCharacter text-left"
                                                       pattern="[0-9]*"
                                                       inputmode="numeric"
                                                       oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                       maxlength="5"
                                                       disabled>
                                                       <div id="management_no_error" class="error_message text-left"></div>
                                            </td>
                                            <td class="text-center">
                                                <div class="center">
                                                    <input type="text" id="product_code_{{ $data['id'] }}" name="product_code" 
                                                            value="{{ $data['product_code'] }}" 
                                                            class="searchOnInput ProductMaterial{{ $data['id'] }}"
                                                            disabled>
                                                    <button type="button" class="btnSubmitCustom js-modal-open ml-2"
                                                            data-target="searchProductModal_{{ $data['id'] }}"
                                                            disabled>
                                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                                alt="magnifying_glass.svg">
                                                    </button>
                                                </div>
                                                <div id="product_code_error" class="error_message text-left"></div>
                                            </td>
                                            <td>
                                                <input type="text"
                                                    id="product_name_{{ $data['id'] }}"
                                                    name="product_name"
                                                    value="{{ $data['product_name'] }}"
                                                    class="middle-name text-left"
                                                    readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="textCharacter" name="uniform_number" value="{{ $data['uniform_number'] ?? '' }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="numberCharacter" name="instruction_number" value="{{ $data['instruction_number'] }}" disabled>
                                                <div id="instruction_number_error" class="error_message text-left"></div>
                                            </td>
                                            <td>
                                                <input type="text" class="numberCharacter calculate_subtotal" name="number_of_accomodated" value="{{ $data['number_of_accomodated'] ?? '' }}" readonly>
                                            </td>
                                            <td>
                                                <input type="text" class="numberCharacter calculate_subtotal" name="instruction_kanban_quantity" value="{{ $data['instruction_kanban_quantity'] }}" disabled>
                                                <div id="instruction_kanban_quantity_error" class="error_message text-left"></div>
                                            </td>
                                            <td class="tA-cn">
                                                <input type="text" class="numberCharacter subTotal" value="{{ $data['arrival_quantity'] }}" readonly style="text-align: center !important;">
                                            </td>
                                            <td style="width: 16%;">
                                                <div class="center" id="EditDelete">
                                                    <button type="button" onclick="enableInputs(this)" class="btn btn-block btn-blue mr-2" id="edit">編集</button>
                                                    <button type="button" onclick="confirmDelete(this)" class="btn btn-block btn-orange" style="margin-left: 2px" id="delete">削除</button>
                                                </div>
                                                
                                                <div class="center" id="UdpateUndo" style="display: none;">
                                                    <button type="button" onclick="updateData(this)" class="btn btn-block btn-green" id="update">更新</button>
                                                    <button type="button" onclick="cancelEdit(this)" class="btn btn-block btn-gray" style="margin-left: 1px" id="undo">取消</button>
                                                </div>
                                            </td>
                                        </tr>
                                        @include('partials.modals.masters._search', [
                                            'modalId' => 'searchProductModal_'. $data['id'],
                                            'searchLabel' => '品番',
                                            'resultValueElementId' => 'product_code_'. $data['id'],
                                            'resultNameElementId' => 'product_name_'. $data['id'],
                                            'model' => 'ProductNumber'
                                        ])
                                        @php
                                            $dataConfigs['ProductMaterial' . $data['id']] = [
                                                'model' => 'ProductMaterial',
                                                'reference' => 'product_name_' . $data['id']
                                            ];
                                        @endphp
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr data-temp-kanban-id="default">
                                        <td style="width: 14%;">
                                            <input type="text"
                                                name="management_no"
                                                id="management_no"
                                                maxlength="5"
                                                class="acceptNumericOnly">
                                            <div id="management_no_error" class="error_message text-left"></div>
                                        </td>
                                        <td style="width: 14%;">
                                            <div class="center">
                                                <input type="text" name="product_code" id="product_code" value="" class="searchOnInput ProductMaterial">
                                                <button type="button" class="btnSubmitCustom js-modal-open ml-2"
                                                        data-target="searchProductNumberModal"
                                                        data-query="searchProductNumberModal"
                                                        data-reference="product_name">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                            alt="magnifying_glass.svg">
                                                </button>
                                            </div>
                                            <div id="product_code_error" class="error_message text-left"></div>
                                        </td>
                                        <td style="width: 9%;">
                                            <input type="text"
                                                id="product_name"
                                                value=""
                                                class="middle-name text-left"
                                                style="width: 100%"
                                                readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="textCharacter" name="uniform_number" id="uniform_number" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="numberCharacter" id="number_instruction" maxlength="2"
                                                onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                            <div id="instruction_number_error" class="error_message text-left"></div>

                                        </td>
                                        <td>
                                            <input type="text" class="numberCharacter calculate_subtotal" name="number_of_accomodated" id="number_of_accomodated" readonly>
                                        </td>
                                        <td>
                                            <input type="text" class="numberCharacter calculate_subtotal" id="instruction_kanban_quantity"
                                                onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                            <div id="instruction_kanban_quantity_error" class="error_message text-left"></div>
                                        </td>
                                        <td>
                                            <input type="text" class="numberCharacter"  id="subTotal" disabled style="text-align: center !important;">
                                        </td>
                                        <td style="width: 16%;">
                                            <div class="center">
                                                
                                                <button type="button" onclick="storeData(this)" class="btn btn-block btn-green text-sm">追加</button>
                                                <button type="button" onclick="clearData(this)" class="btn btn-block btn-gray ml-2 text-sm">クリア</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="d-flex justify-content-end mt-3 mb-3">
                
                <div>
                    @php
                        $class= (count($sessionKanbanData) > 0) ? "" :"btn-disabled";
                        $attr =  (count($sessionKanbanData) > 0) ? "" :"disabled";    
                
                    @endphp
                        <a href="{{ route('outsource.order.index') }}" class="btn btn-primary">一覧に戻る</a>
                        <button  onclick="bulkSavingData(this)"  class="btn btn-success btn-bulk-saving {{$class}}"  {{  $attr }}>この内容で登録する</button>
                </div>
            </div>
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductNumberModal',
        'searchLabel' => '製品品番',
        'resultValueElementId' => 'product_code',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchSupplierModal',
        'searchLabel' => '仕入先',
        'resultValueElementId' => 'supplier_code',
        'resultNameElementId' => 'supplier_name',
        'model' => 'Supplier',
        'query'=> "searchProductModal",
        'reference' => "supplier_code"
    ])

@php
$dataConfigs['Supplier'] = [
    'model' => 'Supplier',
    'reference' => 'supplier_name'
];
$dataConfigs['ProductMaterial'] = [
    'model' => 'ProductNumber',
    'reference' => 'product_name'
];
@endphp

<x-search-on-input :dataConfigs="$dataConfigs" />
@endsection
@push('scripts')
    <script>
        //Declared PHP Variable
        const sessionKanbanData = {!! json_encode(session('sessionKanbanData', [])) !!};
    </script>
    @vite('resources/js/outsource/kanban/temporary/create.js')
@endpush
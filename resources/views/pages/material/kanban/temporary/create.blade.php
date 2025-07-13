@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
@endpush

@section('title', '臨時かんばん入力')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                臨時かんばん入力
            </div>

            @if(session('success'))
                <div id="card" style="background-color: #fff; margin-top:20px; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                    <div style="text-align: left;">
                        <p style="font-size: 18px; color: #0d9c38; margin-bottom: 10px;">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            @endif

            <div class="section">
                <h1 class="form-label bar indented">臨時かんばん入力</h1>
                <div class="box mb-3">
                    <div class="mb-2">
                        <div>
                            <table class="table table-bordered table-striped text-center with-js-validation">
                                <thead>
                                    <tr>
                                        <th>管理No.</th>
                                        <th width="15%">材料メーカー名</th>
                                        <th>材料品番</th>
                                        <th>材料品名</th>
                                        <th>背番号</th>
                                        <th>指示日</th>
                                        <th width="5%">便</th>
                                        <th width="5%">収容数</th>
                                        <th width="5%">枚数</th>
                                        <th width="5%">数量</th>
                                        <th width="12%">操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($sessionSupplyTempData as $index => $data)
                                        <tr data-supply-material-order-id="{{ $data['id'] }}">
                                            <td>
                                                <input type="text" name="management_no"
                                                    id="management_no_{{ $data['id'] }}"
                                                    maxlength="5"
                                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                    value="{{ $data['management_no'] }}"
                                                    data-original-value="{{ $data['management_no'] }}"
                                                    class="numberCharacter management_no" disabled>

                                                <div id="management_no_error" class="error_message"></div>
                                            </td>

                                            <td>
                                                <div class="center">
                                                    <input type="text" name="material_manufacturer_code"
                                                        id="material_manufacturer_code__{{ $data['id'] }}"
                                                        value="{{ $data['material_manufacturer_code'] }}"
                                                        data-original-value="{{ $data['material_manufacturer_code'] }}"
                                                        class="middle-name material_manufacturer_code" disabled>
                                                </div>
                                            </td>

                                            <td>
                                                <div class="center">
                                                    <input type="text" id="product_code_{{ $data['id'] }}"
                                                        name="product_code" 
                                                        data-validate-exist-model="ProductNumber"
                                                        data-validate-exist-column="part_number"
                                                        data-inputautosearch-model="ProductNumber"
                                                        data-inputautosearch-column="part_number"
                                                        data-inputautosearch-return="product_name"
                                                        data-inputautosearch-reference="product_name_{{ $data['id'] }}"
                                                        onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                        value="{{ $data['material_number'] }}"
                                                        data-original-value="{{ $data['material_number'] }}"
                                                        class="mr-2" disabled>

                                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                                        data-target="searchProductModal_{{ $data['id'] }}">
                                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                            alt="magnifying_glass.svg">
                                                    </button>
                                                </div>
                                                <div id="product_code_error" class="error_message"></div>
                                            </td>

                                            <td>
                                                <input type="text" readonly id="product_name_{{ $data['id'] }}"
                                                    data-original-value="{{ $data['product_name'] }}"
                                                    value="{{ $data['product_name'] }}" name="product_name_{{ $data['id'] }}"
                                                    class="middle-name" style="width: 170px">
                                            </td>

                                            <td>
                                                <input type="text" id="uniform_number_{{ $data['id'] }}"
                                                    name="uniform_number" style="width: 170px"
                                                    data-original-value="{{ $data['uniform_number'] }}"
                                                    value="{{ $data['uniform_number'] }}" readonly>
                                            </td>

                                            <td>
                                                <div style="display: flex; justify-content: center;">
                                                    <input type="text" name="instruction_date" style="text-align: center"
                                                        id="instruction_date_{{ $data['id'] }}" data-format="YYYYMMDD"
                                                        data-original-value="{{ $data['instruction_date'] }}"
                                                        minlength="8" maxlength="8" pattern="\d*"
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                        value="{{ $data['instruction_date'] }}" disabled>
                                                    <button type="button" class="btnSubmitCustom buttonPickerJS ml-1"
                                                        data-target="instruction_date_{{ $data['id'] }}"
                                                        data-format="YYYYMMDD">
                                                        <img src="{{ asset('images/icons/iconsvg_calendar_w.svg') }}"
                                                            alt="iconsvg_calendar_w.svg">
                                                    </button>
                                                </div>
                                                <div id="instruction_date_error" class="error_message"></div>
                                            </td>

                                            <td>
                                                <input type="text" class="numberCharacter acceptNumericOnly" 
                                                    onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                    maxlength="2"
                                                    data-original-value="{{ $data['instruction_no'] }}"
                                                    name="instruction_no" id="instruction_no_{{ $data['id'] }}"
                                                    value="{{ $data['instruction_no'] }}" disabled>

                                                    <div id="instruction_no_error" class="error_message"></div>
                                            </td>

                                            <td>
                                                <input type="number" class="numberCharacter acceptNumericOnly"
                                                    data-original-value="{{ $data['number_of_accomodated'] }}"
                                                    id="number_of_accomodated_{{ $data['id'] }}"
                                                    name="number_of_accomodated"
                                                    value="{{ $data['number_of_accomodated'] }}" disabled>

                                                    <div id="number_of_accomodated_error" class="error_message"></div>
                                            </td>

                                            <td>
                                                <input type="number" min="1" class="numberCharacter acceptNumericOnly"
                                                    data-original-value="{{ $data['instruction_kanban_quantity'] }}"
                                                    id="instruction_kanban_quantity_{{ $data['id'] }}"
                                                    name="instruction_kanban_quantity"
                                                    value="{{ $data['instruction_kanban_quantity'] }}" disabled>
                                                    <div id="instruction_kanban_quantity_error" class="error_message"></div>
                                            </td>

                                            <td>
                                                <input type="number" min="1" class="numberCharacter"
                                                    id="subTotal_{{ $data['id'] }}" name="subTotal_{{ $data['id'] }}"
                                                    disabled>
                                            </td>

                                            <td>
                                                <div class="center" id="EditDelete">
                                                    <button onclick="enableInputs(this)" class="btn btn-block btn-blue"
                                                        id="edit">編集</button>
                                                    <button onclick="confirmDelete(this)" class="btn btn-block btn-orange"
                                                        style="margin-left: 2px" id="delete">削除</button>
                                                </div>

                                                <div class="center" id="UdpateUndo" style="display: none;">
                                                    <button onclick="updateData(this)"
                                                        class="btn btn-block btn-green mr-1" id="update">更新</button>
                                                    <button onclick="cancelEdit(this)"
                                                        data-row-index="{{ $data['id'] }}"
                                                        class="btn btn-block btn-gray" style="margin-left: 1px"
                                                        id="undo">取消</button>
                                                </div>
                                            </td>
                                        </tr>
                                        @include('partials.modals.masters._search', [
                                            'modalId' => 'searchProductModal_' . $data['id'],
                                            'searchLabel' => '品番',
                                            'resultValueElementId' => 'product_code_' . $data['id'],
                                            'resultNameElementId' => 'product_name_' . $data['id'],
                                            'model' => 'ProductNumber',
                                        ])
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td>
                                            <input type="text" id="management_no" class="numberCharacter management_no"
                                                maxlength="5"
                                                onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                                <div id="management_no_error" class="error_message"></div>
                                        </td>

                                        <td>
                                            <div class="center">
                                                <input type="text" name="material_manufacturer_code"
                                                    id="material_manufacturer_code" class="middle-name material_manufacturer_code" disabled>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="center">
                                                <input type="text" id="product_code" value="" class="mr-1" class="text-left">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                    data-target="searchProductModal" data-query="searchProductNumberModal"
                                                    data-reference="product_code">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </div>
                                            <div id="product_code_error" class="error_message"></div>
                                        </td>

                                        <td>
                                            <input type="text" readonly id="product_name" name="product_name" value=""
                                                class="middle-name" style="width: 170px">
                                        </td>
                                        
                                        <td>
                                            <input type="text" id="uniform_number" name="uniform_number" style="width: 170px" readonly>
                                        </td>

                                        <td>
                                            <div style="display: flex; justify-content: center;">
                                                <input type="text" style="text-align: center" id="date_instruction"
                                                    data-format="YYYYMMDD" minlength="8" maxlength="8" pattern="\d*"
                                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                <button type="button" class="btnSubmitCustom buttonPickerJS ml-1"
                                                    data-target="date_instruction" data-format="YYYYMMDD">
                                                    <img src="{{ asset('images/icons/iconsvg_calendar_w.svg') }}"
                                                        alt="iconsvg_calendar_w.svg">
                                                </button>
                                            </div>
                                            <div id="instruction_date_error" class="error_message"></div>
                                        </td>

                                        <td>
                                            <input type="text" onkeypress="return event.charCode >= 48 && event.charCode <= 57"
                                                maxlength="2" 
                                                class="numberCharacter acceptNumericOnly" 
                                                id="number_instruction">
                                                <div id="instruction_no_error" class="error_message"></div>
                                        </td>

                                        <td>
                                            <input type="number" class="numberCharacter acceptNumericOnly" id="number_of_accomodated_new"
                                                min="1">
                                                <div id="number_of_accomodated_error" class="error_message"></div>
                                        </td>

                                        <td>
                                            <input type="number" class="numberCharacter acceptNumericOnly"
                                                id="instruction_kanban_quantity_new" value="" min="1">
                                                <div id="instruction_kanban_quantity_error" class="error_message"></div>
                                        </td>

                                        <td>
                                            <input type="number" class="numberCharacter acceptNumericOnly" id="subTotal_new" disabled>
                                        </td>

                                        <td>
                                            <div class="center">
                                                <button onclick="storeData(this)"
                                                    class="btn btn-block btn-green mr-1">追加</button>
                                                <button onclick="clearData(this)" class="btn btn-block btn-gray"
                                                    style="margin-left: 1px">クリア</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            @if (session('success'))
                <div id="card"
                    style="background-color: #f0f0f0; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
                    <div style="text-align: center;">
                        <p style="font-size: 18px; color: #0d9c38; margin-bottom: 10px;">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            @endif
            <div class="space-between">
                <div>
                    <p class="text-red" id="warningInputs" style="display:none;">登録に必要ないくつかの情報が入力されていません！</p>
                    <p id="successInputs" style="display:none; color:#0d9c38;">「データは正常に登録されました」</p>
                </div>
                <div>
                    @php
                        $class = $sessionSupplyTempData ? '' : 'btn-disabled';
                        $attributes = $sessionSupplyTempData? '' : 'disabled';
                    @endphp
                    <a href="{{ route('material.order.index') }}" class="btn btn-blue"
                        style="width: 250px"> 一覧に戻る </a>
                    <button onclick="bulkSavingData(this)" class="btn btn-green btn-bulk-saving {{  $class }}" {{ $attributes }} style="width: 15rem"> この内容で登録する
                    </button>
                </div>
            </div>
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductModal',
        'searchLabel' => '材料品番',
        'resultValueElementId' => 'product_code',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber',
        'query' => 'searchProductNumberModal',
        'reference' => 'product_code',
    ])
@endsection
@push('scripts')
<script>
    window.sessionSupplyTempData = @json($sessionSupplyTempData);
</script>
@vite(['resources/js/material/kanban/temporary/create.js'])
@endpush
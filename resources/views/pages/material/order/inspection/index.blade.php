@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
    {{-- @vite('resources/css/materials/receipt_and_inspection.css') --}}
@endpush

@section('title', '検収入力')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                検収入力
            </div>

            <div id="warningInputs" style="background-color: #fff; margin-top:20px; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); color: red; display: none">
                <div style="text-align: left;">登録に必要ないくつかの情報が入力されていません</div>
            </div>
            <div id="successInputs" style="background-color: #fff; margin-top:20px; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); display:none; color:#0d9c38;">
                <div style="text-align: left;">支給材検収情報の登録が完了しました</div>
            </div>
            <div id="successUpdate" style="background-color: #fff; margin-top:20px; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); display:none; color:#0d9c38;">
                <div style="text-align: left;">更新が完了しました</div>
            </div>

            <div class="section">
                <h1 class="form-label bar indented">検収入力</h1>
                <div class="box mb-3">
                    <div class="mb-2 material-order-inspection-table">
                        <div>
                        <form  method="POST" class="with-js-validation">
                            <table class="table table-bordered table-striped text-center" style="max-width: 1500px">
                                <thead>
                                <tr>
                                    <th>納入日</th>
                                    <th width="5%">便No.</th>
                                    <th>納入番号</th>
                                    <th>材料品番</th>
                                    <th width="210px;">品名</th>
                                    <th width="80px;">納入数</th>
                                    <th width="180px">操作</th>
                                </tr>
                                </thead>
                                <tbody>
                                    @forelse ($recentSupplyArrivals as $index => $recentSupplyMaterialArrival)
                                        @php
                                            $counter = ($index == 0) ? 1 : $index ;
                                        @endphp

                                        <tr data-inspection-input-id="{{ $recentSupplyMaterialArrival['id'] }}">
                                            <td>
                                                <div style="display: flex; justify-content: center;">                                                   
                                                    <input type="text" name="arrival_day" style="text-align: center" 
                                                        id="instruction_date_{{ $recentSupplyMaterialArrival['id'] }}" 
                                                        data-format="YYYYMMDD"
                                                        minlength="8"
                                                        maxlength="8"
                                                        pattern="\d*" 
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                        old="{{ $recentSupplyMaterialArrival['arrival_day'] }}"
                                                        value="{{ $recentSupplyMaterialArrival['arrival_day'] }}"
                                                        class="edit_field"
                                                        disabled>
                                                    <button type="button" class="btnSubmitCustom buttonPickerJS ml-1" 
                                                            data-target="instruction_date_{{ $recentSupplyMaterialArrival['id'] }}"
                                                            data-format="YYYYMMDD" disabled>
                                                        <img src="{{ asset('images/icons/iconsvg_calendar_w.svg') }}" alt="iconsvg_calendar_w.svg">
                                                    </button>
                                                </div>
                                                <div id="arrival_day_error" class="error_message text-left"></div>
                                            </td>
                                            <td>
                                                <input type="text" id="flight_no_{{ $recentSupplyMaterialArrival['id'] }}" disabled old="{{ $recentSupplyMaterialArrival['flight_no'] }}" value="{{ $recentSupplyMaterialArrival['flight_no'] }}" class="numberCharacter acceptNumericOnly edit_field" data-accept-zero="true" maxlength="2" name="flight_no">
                                                <div id="flight_no_error" class="error_message text-left"></div>
                                            </td>
                                            <td>
                                                <input type="text" id="delivery_no_{{ $recentSupplyMaterialArrival['id'] }}" disabled class="textCharacter acceptNumericOnly edit_field" name="delivery_no" old="{{ $recentSupplyMaterialArrival['delivery_no'] }}" value="{{ $recentSupplyMaterialArrival['delivery_no'] }}">
                                                <div id="delivery_no_error" class="error_message text-left"></div>
                                            </td>
                                            <td>
                                                <div style="display: flex; justify-content: center;">
                                                    <input type="text" id="product_code_{{ $recentSupplyMaterialArrival['id'] }}"
                                                            name="material_no"
                                                            ata-validate-exist-model="ProductNumber"
                                                            data-validate-exist-column="part_number"
                                                            data-inputautosearch-model="ProductNumber"
                                                            data-inputautosearch-column="part_number"
                                                            data-inputautosearch-return="product_name"
                                                            data-inputautosearch-reference="product_name"
                                                            old="{{ $recentSupplyMaterialArrival['material_no'] }}"
                                                            value="{{ $recentSupplyMaterialArrival['material_no'] }}"
                                                            class="edit-material-number mr-1 edit_field"
                                                            material="{{ $recentSupplyMaterialArrival['id'] }}"
                                                            disabled>
                                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                                            data-target="searchProductModal_{{ $recentSupplyMaterialArrival['id'] }}" disabled>
                                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                                alt="magnifying_glass.svg">
                                                    </button>
                                                </div>
                                                <div id="material_no_error" class="error_message text-left"></div>
                                            </td>
                                            <td>
                                                <input type="text" disabled class="textCharacter text-center" 
                                                        id="product_name_{{ $recentSupplyMaterialArrival['id'] }}" 
                                                        name="product_name"
                                                        old="{{ $recentSupplyMaterialArrival['product_name'] }}"
                                                        value="{{ $recentSupplyMaterialArrival['product_name'] }}">
                                            </td>
                                            <td>
                                                <input id="arrival_quantity_{{ $recentSupplyMaterialArrival['id'] }}" type="text" disabled class="numberCharacter edit_field acceptNumericOnly" name="arrival_quantity" old="{{ $recentSupplyMaterialArrival['arrival_quantity'] }}" value="{{ $recentSupplyMaterialArrival['arrival_quantity'] }}"
                                                data-accept-zero="true">
                                                <div id="arrival_quantity_error" class="error_message text-left"></div>
                                            </td>
                                            <td>
                                                <div class="center EditDelete" id="EditDelete{{ $recentSupplyMaterialArrival['id'] }}">
                                                    <button type="button" class="btn btn-block btn-blue mr-1 edit" material="{{ $recentSupplyMaterialArrival['id'] }}">編集</button>
                                                    <button type="button"  data-delete-button class="btn btn-block btn-orange" style="margin-left: 2px" id="delete">削除</button>
                                                </div>
                                                <div class="center updateUndo" id="UdpateUndo{{ $recentSupplyMaterialArrival['id'] }}" style="display: none;">
                                                    <button type="button"  data-update-button class="btn btn-block btn-green mr-1" id="update">更新</button>
                                                    <button type="button"  data-cancel-edit-button class="btn btn-block btn-gray" style="margin-left: 1px" material="{{ $recentSupplyMaterialArrival['id'] }}">取消</button>
                                                </div>
                                            </td>
                                        </tr>
                                        @include('partials.modals.masters._search', [
                                            'modalId' => 'searchProductModal_'. $recentSupplyMaterialArrival['id'],
                                            'searchLabel' => '材料',
                                            'resultValueElementId' => 'product_code_'. $recentSupplyMaterialArrival['id'],
                                            'resultNameElementId' => 'product_name_'. $recentSupplyMaterialArrival['id'],
                                            'model' => 'ProductMaterial'
                                        ])
                                    @empty
                                        {{-- <tr>
                                            <td colspan="7" class="text-center">検索結果はありません</td>
                                        </tr> --}}
                                    @endforelse
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td>
                                            <div style="display: flex; justify-content: center;">
                                                <input type="text" name="arrival_day" style="text-align: center" 
                                                        id="arrival_day" 
                                                        data-format="YYYYMMDD"
                                                        minlength="8"
                                                        maxlength="8"
                                                        pattern="\d*" 
                                                        oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                                    <button type="button" class="btnSubmitCustom buttonPickerJS ml-1 buttonPickerJSDate" 
                                                            data-target="arrival_day"
                                                            data-format="YYYYMMDD">
                                                        <img src="{{ asset('images/icons/iconsvg_calendar_w.svg') }}" alt="iconsvg_calendar_w.svg">
                                                    </button>
                                            </div>
                                            <div id="arrival_day_error" class="error_message text-left"></div>
                                        </td>
                                        <td>
                                            <input type="text" class="numberCharacter acceptNumericOnly" data-accept-zero="true" id="flight_no" maxlength="2" value="">
                                            <div id="flight_no_error" class="error_message text-left"></div>
                                        </td>
                                        <td>
                                            <input type="text" class="textCharacter acceptNumericOnly" id="delivery_no">
                                            <div id="delivery_no_error" class="error_message text-left"></div>
                                        </td>
                                        <td>
                                            <div style="display: flex; justify-content: center;">
                                                <input type="text" id="product_code" name="material_no" class="mr-1" disabled>
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                        data-target="searchProductModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                            alt="magnifying_glass.svg">
                                                </button>
                                            </div>
                                            <div id="material_no_error" class="error_message text-left"></div>
                                        </td>
                                        <td>
                                            <input type="text" readonly
                                            id="product_name"
                                            name="product_name"
                                            value=""
                                            class="middle-name"
                                            style="width: 170px">
                                        </td>
                                        <td>
                                            <input type="text" class="numberCharacter acceptNumericOnly" id="arrival_quantity"
                                            data-accept-zero="true">
                                            <div id="arrival_quantity_error" class="error_message text-left"></div>
                                        </td>
                                        <td>
                                            <div class="center">
                                                <button type="button"
                                                data-insert-button
                                                class="btn btn-block btn-green mr-1" style="margin-left: 1px">追加</button>
                                                <button type="button" data-clear-button class="btn btn-block btn-gray" style="margin-left: 1px">クリア</button>
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
            <div style="text-align:right">
                <a href="{{ route('material.received.materials.index') }}" class="btn btn-blue" style="width: 250px"> メニューに戻る </a>
                <button data-bulk-save-button  class="btn btn-green btn-disabled" style="width: 15rem" disabled> この内容で登録する </button>
            </div>
        </div>
    </div>
    <div id="supplyInspectionData" data-info='@json(session("sessionReceiptAndInspectionData", []))'></div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductModal',
        'searchLabel' => '材料',
        'resultValueElementId' => 'product_code',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber'
    ])

@push('scripts')
@vite(['resources/js/material/order/inspection/index.js'])
@endpush

@endsection

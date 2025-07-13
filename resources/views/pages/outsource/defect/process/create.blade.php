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
            加工不良実績入力
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
                <h1 class="form-label bar indented">加工不良実績入力</h1>
                <div class="box mb-3">
                    <div class="mb-2">
                        <form class="overlayedSubmitForm with-js-validation"
                              data-disabled-overlay="true"
                              action="{{ route('outsource.defect.process.store') }}" >
                            <div class="mb-2 d-flex">
                            <div class="mr-5">
                                <label class="form-label dotted indented">廃却日</label> 
                                  <span id="others-frame" class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    @include('partials._date_picker', [
                                        'inputName' => 'disposal_date',
                                        'value' => isset($firstData['return_date']) ? $firstData['return_date'] : now()->format('Ymd'),
                                        'inputClass' => 'w-120c',
                                        'attributes' => 'data-error-messsage-container=#disposal_date_message'
                                    ])
                                </div>
                                <div id ="disposal_date_error" class="error_message text-left"></div>
                            </div>
                            <div class="mr-3">
                              <label class="form-label dotted indented">工程</label> <span
                                  class="others-frame btn-orange badge">必須</span>
                              <div class="d-flex">
                                <div class="formPack mr-10c">
                                    <input type="text" name="process_code"
                                        data-error-messsage-container="#process_code_message"
                                        data-validate-exist-model="Process" 
                                        data-validate-exist-column="process_code"
                                        data-inputautosearch-model="Process" 
                                        data-inputautosearch-column="process_code"
                                        data-inputautosearch-return="abbreviation_process_name" 
                                        data-inputautosearch-reference="process_name"
                                        data-custom-required-error-message="材料メーカーを入力してください"
                                        maxlength="4"
                                        id="process_code" class="searchOnInput Process w-100c"
                                        value="{{ $supplier->process_code ?? '' }}">
                                </div>
                                <div class="formPack fixedWidth box-middle-name mr-4  w-200c">
                                    <input type="text" readonly
                                        name="process_name"
                                        id="process_name"
                                        value="{{ $supplier->process_name ?? '' }}"
                                        class="middle-name text-left">
                                </div>
                                <div class="formPack fixedWidth w-20c">
                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchProcessModal"
                                            data-query-field="">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                            alt="magnifying_glass.svg">
                                    </button>
                                </div>
                            </div>
                            <div id ="process_code_error" class="error_message text-left"></div>
                          </div>
                        </div>
                        
                        <div class="mt-4">
                            <table class="table table-bordered table-striped text-center">
                                <thead>
                                <tr>
                                    <th width="20%">製品品番</th>
                                    <th>品名</th>
                                    <th width="10%">数量</th>
                                    <th width="10%">単価</th>
                                    <th width="10%">金額</th>
                                    <th width="10%">伝票No</th>
                                    <th width="15%">操作</th>
                                </tr>
                                </thead>
                                <tbody class="align-top">
                                    @foreach ($sessionDefectProcessData as $item)
                                        <tr data-id="{{ $item['id'] }}" id="row-{{ $item['id'] }}">
                                            <td>
                                                <div class="center">
                                                    <input type="text" id="product_code_{{ $item['id'] }}" 
                                                    name="product_code"
                                                    data-error-messsage-container="#product_code-error"
                                                    data-validate-exist-model="ProductNumber"
                                                    data-validate-exist-column="part_number"
                                                    data-inputautosearch-model="ProductNumber"
                                                    data-inputautosearch-column="part_number"
                                                    data-inputautosearch-return="product_name"
                                                    data-inputautosearch-reference="product_name_{{ $item['id'] }}"
                                                    value="{{ $item['product_code'] }}" 
                                                    data-old-value="{{ $item['product_code'] }}" 
                                                    class="text-center" disabled>
                                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                                            data-target="searchProductNumberModal_{{ $item['id'] }}"
                                                            style="margin-left: 2px;" disabled
                                                            data-modal-button>
                                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                            alt="magnifying_glass.svg">
                                                    </button>
                                                </div>
                                                <div id ="product_code_error" class="error_message text-left"></div>
                                            </td>
                                            <td>
                                                <input type="text" readonly
                                                id="product_name_{{ $item['id'] }}"
                                                name="product_name_{{ $item['id'] }}"
                                                value="{{ $item['product_name'] }}"
                                                data-old-value="{{ $item['product_name'] }}">
                                            </td>
                                            <td>
                                                <input type="text"
                                                    style="width: 100%"
                                                    class="text-center acceptNumericOnly"
                                                    value="{{ $item['quantity'] }}"
                                                    data-old-value="{{ $item['quantity'] }}"
                                                    id="quantity_{{ $item['id'] }}"
                                                    name="quantity"
                                                    maxLength="6"
                                                    disabled>
                                                    <div id ="quantity_error" class="error_message text-left"></div>
                                            </td>
                                            <td>
                                                <input type="text" 
                                                  id="processing_unit_price_{{ $item['id'] }}" 
                                                  name="processing_unit_price"
                                                  value="{{ $item['processing_unit_price'] ?? 0}}" 
                                                  data-old-value="{{ $item['processing_unit_price'] }}" 
                                                  readonly>
                                            </td>
                                            <td>
                                                <input type="text" 
                                                  id="subTotal_{{ $item['id'] }}" 
                                                  name="subTotal"
                                                  value="{{ $item['subTotal'] ?? 0 }}" 
                                                  data-old-value="{{ $item['subTotal'] }}" 
                                                  readonly>
                                            </td>
                                            <td>
                                                <input type="text" 
                                                    id="slip_no_{{ $item['id'] }}" 
                                                    name="slip_no"
                                                    value="{{ $item['slip_no'] }}" 
                                                    data-old-value="{{ $item['slip_no'] }}" 
                                                    class=" text-center" disabled>
                                                    <div id ="slip_no_error" class="error_message text-left"></div>
                                            </td>
                                            <td>
                                            <div class="center" id="EditDelete">
                                                    <button type="button" class="btn btn-block btn-blue mr-1" id="edit"
                                                    data-edit-button>編集</button>
                                                    <button type="button" class="btn btn-block btn-orange" style="margin-left: 2px" id="delete" data-delete-button>削除</button>
                                                </div>
                                                
                                                <div class="center" id="UdpateUndo" style="display: none;">
                                                    <button type="button" class="btn btn-block btn-green mr-1" id="update"
                                                    data-update-button>更新</button>
                                                    <button type="button"  class="btn btn-block btn-gray" style="margin-left: 1px" id="undo" data-cancel-button>取消</button>
                                                </div>
                                            </td>
                                            
                                        </tr>
                                        @include('partials.modals.masters._search', [
                                            'modalId' => 'searchProductNumberModal_'. $item['id'],
                                            'searchLabel' => '製品品番',
                                            'resultValueElementId' => 'product_code_'. $item['id'],
                                            'resultNameElementId' => 'product_name_'. $item['id'],
                                            'model' => 'ProductNumber'
                                        ])
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <td>
                                            <div class="center">
                                                <input type="text" name="product_code" id="product_code" 
                                                  data-validate-exist-model="ProductNumber"
                                                  data-validate-exist-column="part_number"
                                                  data-inputautosearch-model="ProductNumber"
                                                  data-inputautosearch-column="part_number"
                                                  data-inputautosearch-return="product_name"
                                                  data-inputautosearch-reference="product_name"
                                                value="" 
                                                class="text-center">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                        data-target="searchProductNumberModal"
                                                        data-query="sampleModal"
                                                        data-reference="material_code"
                                                        style="margin-left: 2px;">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </div>
                                            <div id ="product_code_error" class="error_message text-left"></div>
                                        </td>
                                        <td>
                                            <input type="text" readonly
                                            id="product_name"
                                            name="product_name">
                                        </td>
                                        <td>
                                            <input type="text"
                                                style="width: 100%"
                                                class="acceptNumericOnly text-center"
                                                name="quantity"
                                                maxLength="6"
                                                id="quantity">
                                                <div id ="quantity_error" class="error_message text-left"></div>
                                        </td>
                                        <td>
                                            <input type="text" id="processing_unit_price"  name="processing_unit_price"
                                            class="text-center acceptNumericOnly" readonly>
                                        </td>
                                        <td>
                                            <input type="text" id="subTotal" name="subTotal" class="text-center acceptNumericOnly" readonly>
                                        </td>
                                        <td>
                                            <input type="text" value="" id="slip_no" name="slip_no" class="acceptNumericOnly text-center">
                                            <div id ="slip_no_error" class="error_message text-left"></div>
                                        </td>
                                        <td>
                                            <div class="center">
                                                <!-- <button onclick="if(confirm('内示情報を削除します、よろしいでしょうか？')){}"
                                                        class="btn btn-block btn-green" type="submit">追加
                                                </button>
                                                <button onclick="clearData(this)" type="button"
                                                        class="btn btn-block btn-gray" style="margin-left: 6px">クリア
                                                </button> -->

                                                <button  type="button" class="btn btn-block btn-green mr-1" data-insert-button>追加</button>
                                                <button   type="button" class="btn btn-block btn-gray" style="margin-left: 1px;"
                                                data-clear-button>クリア</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
            <div style="text-align:right">
                
                <a href="{{ route('outsource.defect.process.index') }}" class="btn btn-blue" style="width: 250px"> 一覧に戻る </a>
               
                <button  class="btn btn-green btn-disabled" style="width: 15rem;"
                disabled
                data-bulk-save-button> この内容で登録する </button>
            </div>
        </div>
    </div>

    <div id="sessionDefectProcessData" data-info='@json(session("sessionDefectProcessData", []))'></div>
    
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProcessModal',
        'searchLabel' => '工程',
        'resultValueElementId' => 'process_code',
        'resultNameElementId' => 'process_name',
        'model' => 'Process'
    ])
    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductNumberModal',
        'searchLabel' => '製品品番',
        'resultValueElementId' => 'product_code',
        'resultNameElementId' => 'product_name',
        'model' => 'ProductNumber'
    ])

@endsection

@push('scripts')
    @vite('resources/js/outsource/defect/process/create.js');
@endpush

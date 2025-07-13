@push('styles')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/master/modal/ups_modal.css')
@endpush
<div id="sequenceSettingModal" class="modal js-modal modal__bg modalSs">
    <div class="modal__content sequence-modal">
        <button id="closeSequenceSettingModal" type="button" class="modalCloseBtn js-modal-close">x</button>
        <div class="modalInner inner-modal with-js-validation-modal">


            <div class="pagettlWrap">
                <h1>
                    <span>{{ $modalLabel }}</span>
                </h1>
            </div>
            <input type="hidden" name="part_number" id="part_number" value="{{ $partNumber }}">
            <input type="hidden" name="product_id" id="product_id" value="{{ $productId }}">
            <input type="hidden" name="product_name" id="product_name" value="{{ $productName }}">
            <div class="section">
                {{-- <h1 class="form-label bar indented">グループ設定</h1> --}}
                <div class="box mb-1 content-box">
                    <div class="row-content">
                        <!-- 品番 -->
                        <div class="flex-row">
                            <label for="part_number" class="label_for">品番</label>
                            <span id="part_number" class="span_for">{{ $partNumber }}</span>
                        </div>
                        <!-- 品名 -->
                        <div class="flex-row">
                            <label for="product_name" class="label_for">品名</label>
                            <span id="product_name" class="span_for">{{ $productName }}</span>
                        </div>
                    </div>
                </div>
                <div class="box mb-1 content-box">
                    <div class="mb-2" id="table-process">
                        <table class="table-modal align-middle">
                            <thead class="table-head">
                                <tr>
                                    <th class="modal-th" style="width: 150px; max-width: 150px; min-width: 150px;">工程順序</th>
                                    <th class="modal-th" style="width: 150px; max-width: 150px; min-width: 150px;">工程コード</th>
                                    <th class="modal-th">単価</th>
                                    <th class="modal-th">工程名</th>
                                    <th class="modal-th">工程内容</th>
                                    <th class="modal-th">荷姿</th>
                                    <th class="modal-th" style="width: 100px; max-width: 100px; min-width: 100px;">内外区分</th>
                                    <th class="modal-th" style="width: 250px; max-width: 250px; min-width: 250px;">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr class="modal-tr" id="inputs_process">
                                    <td class="modal-td text-left">
                                        {{-- process_order --}}
                                    </td>
                                    <td class="modal-td text-left" style="padding-left: 7px; padding-right: 7px;">
                                        {{-- process_code 
                                        <input type="text" id="process_code" name="process_code" style="width: 125px;">
                                        <button type="button" class="magnify-btn js-modal-open"
                                                    data-target="searchProcessModal"
                                                    data-query-field="">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                        </button>
                                        --}}


                                        <div class="d-flex">
                                            <p class="formPack mr-2">
                                                <input type="text" id="process_code" name="process_code" 
                                                    data-validate-exist-model="Process" 
                                                    data-validate-exist-column="process_code"
                                                    data-inputautosearch-model="Process"
                                                    data-inputautosearch-column="process_code"
                                                    data-inputautosearch-return="process_name"
                                                    data-inputautosearch-reference="process_name" maxlength="4"
                                                    value=""
                                                    class="w-100 input-required"
                                                    data-name = "Process code" 
                                                    data-modal-autosearch>
                                            </p>
                                            <div class="formPack ">
                                                <button type="button" class="btnSubmitCustom js-modal-open"
                                                    data-target="searchProcessModal">
                                                    <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                        alt="magnifying_glass.svg">
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    {{-- process_unit_price.processing_unit_price  effective_date >= today --}}
                                    <td class="modal-td text-right p-right-5" style="padding: 5px;"></td>
                                    {{-- process_name --}}
                                    <td class="modal-td text-center" id="process_name" style="padding: 5px;">
                                    </td>
                                    <td class="modal-td" style="width:150px">
                                        {{-- process_details --}}
                                        <input type="text" id="process_details" 
                                            class="input-required"
                                            data-name = "Process details" 
                                            name="process_details" maxlength="4" style="width:95% !important; margin:5px;">
                                    </td>
                                    <td class="modal-td" style="width:150px">
                                        {{-- packing --}}
                                        <input type="text" id="packing" 
                                            name="packing" 
                                            class="input-required"
                                            data-name = "Packaging" 
                                            maxlength="20" style="width:95% !important; margin:5px;">
                                    </td>
                                    <td class="modal-td text-center">
                                        {{-- inside_outside --}}
                                    </td>
                                    <td class="modal-td text-left">
                                        <div class="div-button" style="justify-content: start; padding-left: 7px;">
                                            <button type='button' class="btn save-btn-process">追加</button>
                                            <button type='button' class="btn clear-btn-process">クリア</button>
                                        </div>
                                    </td>
                                </tr>

                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="justify-between">
                {{-- Use form to use success message --}}
                <form action="{{ route('master.processSequence.destroy', $partNumber) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="part_number" id="part_number" value="{{ $partNumber }}">
                    <input type="hidden" name="product_id" id="product_id" value="{{ $productId }}">
                    <button type="submit" class="btn btn-orange btn-md btn-disabled" 
                        disabled
                        onclick="return confirm('工程順序設定を削除します、よろしいでしょうか？');">
                        削除
                    </button>
                </form>
                <form action="{{ route('master.processSequence.store') }}" method="POST"
                    id="saveProcessOrderForm">
                    @csrf
                    <input type="hidden" name="part_number" id="part_number" value="{{ $partNumber }}">
                    <input type="hidden" name="product_id" id="product_id" value="{{ $productId }}">
                    <button type="button" class="btn-done-process btn btn-success btn-md btn-disabled" disabled>登録する</button>
                </form>
            </div>

        </div>
    </div>
</div>
<div class="process-search-modal"></div>
<div class="process-setting-modal"></div>
<div id="processSequenceModal"></div>

@include('partials.modals.masters._search', [
    'modalId' => 'searchProcessModal',
    'searchLabel' => '工程コード',
    'resultValueElementId' => 'process_code',
    'resultNameElementId' => 'process_name',
    'model' => 'Process'
    ])
@include('pages.master.part.modal.process_setting_modal', [
    'partNumber' => $partNumber,
    'productName' => $productName,
])



@push('scripts')
    @vite(['resources/js/master/part/modal/process-sequence-settings.js'])
@endpush
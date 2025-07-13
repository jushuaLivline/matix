@push('styles')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/master/modal/ups_modal.css')
@endpush
<div id="processSettingModal" class="modal js-modal modal__bg modalSs processSettingModal">
    <div class="modal__content">
        <button type="button" id="settingClose" class="modalCloseBtn js-modal-close-setting">x</button>
        <div class="modalInner inner-modal">
            <div class="pagettlWrap">
                <h1>
                    <span>工程単価設定</span>
                </h1>
            </div>
            <input type="hidden" name="part_number" id="part_number" value="{{ $partNumber }}">
            <input type="hidden" name="process_code" id="process_code" value="">
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
                        <div class="flex-row">
                            <label for="process_name" class="label_for">工程</label>
                            <span id="process_name" class="span_for"></span>
                        </div>
                    </div>
                </div>
                <div class="box mb-1 content-box">
                    <div class="mb-2" id="table-setting">
                        <table class="table-modal">
                            <thead class="table-head">
                                <tr>
                                    <th class="modal-th">適用日</th>
                                    <th class="modal-th">売単価</th>
                                    <th class="modal-th" style="width: 90px;">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                            <tfoot>
                                <tr class="modal-tr" id="inputs_setting">
                                    <td class="modal-td" style="width:150px">
                                        <div class="div-calendar-setting">
                                        @include('partials._date_picker', [
                                                'inputName' => 'effective_date_setting', 
                                                'dateFormat' => 'YYYYMMDD', 
                                                'inputClass' => 'input-required w-100c', 
                                                'value' => Request::get("effective_date")
                                                ])
                                        </div>
                                    </td>
                                    <td class="modal-td" style="width:150px">
                                        <input
                                            type="text"
                                            pattern="\d*"
                                            oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                            id="processing_unit_price"
                                            name="processing_unit_price"
                                            style="width:95% !important; margin:5px;"
                                            class="input-required acceptNumericOnly"
                                            maxlength="10"
                                        >
                                    </td>
                                    <td class="modal-td" class="text-center">
                                        <div class="div-button">
                                            <button class="btn save-btn-setting">追加</button>
                                            <button class="btn clear-btn-setting">クリア</button>
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
                <form action="{{ route('master.processUnitPrice.destroy', $partNumber) }}" method="POST" 
                id="deleteProcessUnitPriceForm">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="part_number" id="part_number" value="{{ $partNumber }}">
                    <input type="hidden" name="process_code" id="process_code" value="">
                    <input type="hidden" name="product_id" id="product_id" value="{{ $productId }}">
                    <button type="submit" class="btn btn-orange btn-md btn-delete-setting btn-disabled" disabled>
                        削除
                    </button>
                </form>
                <form action="{{ route('master.processUnitPrice.store') }}" method="POST"
                    id="saveProcessUnitPriceForm">
                    @csrf
                    <input type="hidden" name="part_number" id="part_number" value="{{ $partNumber }}">
                    <input type="hidden" name="process_code" id="process_code" value="">
                    <input type="hidden" name="product_id" id="product_id" value="{{ $productId }}">
                    <button type="button" class="btn-done-setting btn btn-success btn-md btn-disabled" disabled >登録する</button>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    @vite(['resources/js/master/part/modal/process-unit-pirce-settings.js'])
@endpush
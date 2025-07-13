@push('styles')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/master/modal/ups_modal.css')
@endpush
<div id="unitPriceSettingModal" class="modal js-modal modal__bg modalSs">
    <div class="modal__content content-modal" style="width: 80% !important">
        <button id="btnModalClose" type="button" class="modalCloseBtn js-modal-close">x</button>
        <div class="modalInner inner-modal">
            <div class="pagettlWrap">
                <h1>
                    <span>{{ $modalLabel }}</span>
                </h1>
            </div>
            <input type="hidden" name="part_number" id="part_number" value="{{ $partNumber }}">
            <input type="hidden" name="product_id" id="product_id" value="{{ $productId }}">
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
                {{-- 
                <div class="box mb-1 content-box">
                    <div class="mb-2" id="table-container-static">
                        <table class="table-modal">
                            <thead class="table-head">
                                <tr>
                                    <th class="modal-th">適用日</th>
                                    <th class="modal-th">売単価</th>
                                    <th class="modal-th">買単価</th>
                                    <th class="modal-th">加工単価</th>
                                    <th class="modal-th">材料・構成部品単価</th>
                                    <th class="modal-th">工程単価</th>
                                    <th class="modal-th">外注加工単価</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="modal-tr">
                                    <td class="modal-td text-center" style="width:150px; padding: 20px">
                                        <span>
                                            {{ isset($productPrices['effective_date']) ? date('Y/m/d', strtotime($productPrices['effective_date'])) : '' }}
                                        </span>
                                    </td>
                                    <td class="modal-td text-right p-right-5" style="width:150px">
                                        <span>
                                            {{ $productPrices['sell_price'] ?? '' }}
                                        </span>
                                    </td>
                                    <td class="modal-td text-right p-right-5" style="width:150px">
                                        <span>
                                            {{ $productPrices['unit_price'] ?? '' }}
                                        </span>
                                    </td>
                                    <td class="text-right p-right-5 modal-td">{{ $productPrices != null && $productPrices['sell_price'] != null ? $productPrices['sell_price'] - $productPrices['unit_price'] : '' }}</td>
                                    <td class="text-right p-right-5 modal-td">{{ $productPrices['unit_price'] ?? '' }}</td>
                                    <td class="text-right p-right-5 modal-td">{{ $insideProcess }}</td>
                                    <td class="text-right p-right-5 modal-td">{{ $outsideProcess }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                --}}
                
                <div class="box mb-1 content-box">
                    <div class="mb-2" id="table-container">
                        <table class="table-modal">
                            <thead class="table-head">
                                <tr>
                                    <th class="modal-th" style="width: 160px;">適用日</th>
                                    <th class="modal-th" style="width: 80px; max-width: 80px;">売単価</th>
                                    <th class="modal-th" style="width: 80px; max-width: 80px;">買単価</th>
                                    <th class="modal-th">加工単価</th>
                                    <th class="modal-th">材料・構成部品単価</th>
                                    <th class="modal-th">工程単価</th>
                                    <th class="modal-th">外注加工単価</th>
                                    <th class="modal-th" style="width: 130px;">操作</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                            <tfoot>
                                <tr class="modal-tr" id="inputs">
                                    <td class="modal-td" style="width:150px">
                                        <div class="div-calendar">
                                            @include('partials._date_picker', [
                                                'inputName' => 'effective_date', 
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
                                            id="sell_price"
                                            name="sell_price"
                                            style="width:92% !important; margin:5px;"
                                            class="input-required acceptNumericOnly"
                                            maxlength="10"
                                        >
                                    </td>
                                    <td class="modal-td" style="width:150px">
                                        <input
                                                type="text"
                                                pattern="\d*"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                                id="unit_price"
                                                name="unit_price"
                                                style="width:92% !important; margin:5px;"
                                                class="input-required acceptNumericOnly"
                                                maxlength="10"
                                            >
                                    </td>
                                    <td class="text-right p-right-5 modal-td"></td>
                                    <td class="text-right p-right-5 modal-td"></td>
                                    <td class="text-right p-right-5 modal-td"></td>
                                    <td class="text-right p-right-5 modal-td"></td>
                                    <td class="text-center modal-td">
                                        <div class="div-button">
                                       <button class="btn save-btn">追加</button>
                                            <button class="btn clear-btn">クリア</button>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                        <!-- <p>※工程単価、外注加工単価には仕掛品の加工単価は含まれていません</p> -->
                    </div>
                </div>
            </div>
            <div class="justify-between">
                <form action="{{ route('master.partNumberUnitPrice.destroy', $partNumber ?? 0) }}" method="POST" 
                id="deletePartNumberUnitPriceForm">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="part_number" id="part_number" value="{{ $partNumber }}">
                    <input type="hidden" name="product_id" id="product_id" value="{{ $productId }}">
                    <button type="submit" class="btn-del-save btn-del btn btn-orange btn-disabled" disabled>
                        削除
                    </button>
                </form>
                <form action="{{ route('master.partNumberUnitPrice.store') }}" method="POST"
                    id="savePartNumberUnitPriceForm">
                    @csrf
                    <input type="hidden" name="part_number" id="part_number" value="{{ $partNumber }}">
                    <input type="hidden" name="product_id" id="product_id" value="{{ $productId }}">
                    <button type="button" class="btn-del-save btn-done btn btn-succes btn-disabled" disabled>登録する</button>
                </form>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    @vite(['resources/js/master/part/modal/part_number_unit-price-settings.js'])
@endpush
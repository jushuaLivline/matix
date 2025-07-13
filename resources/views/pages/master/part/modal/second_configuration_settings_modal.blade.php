@push('styles')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/master/modal/ups_modal.css')
@endpush

<div id="{{ $modalId }}" class="modal js-modal-second modal__bg modalSs" style="min-width: 60%;;">
    <div class="modal__content process-modal"  style="min-width: 60%; width: 900px;">
        <button type="button" class="modalCloseBtn second-conf-close">x</button>
        <div class="modalInner inner-modal" id="modalContent">
            <div class="pagettlWrap">
                <h1>
                    <span id="modalTitle">{{ $modalLabel }}</span>
                </h1>
            </div>
            {{-- {{ $configurations }} --}}
            <form action="#" accept-charset="utf-8" id="configuration-form-id">
                @csrf
                <input type="hidden" id="config_id" name="config_id" value="{{ $config->id ?? null }}">
                <div class="box content-box">
                    {{-- parent_part_number --}}
                    <div class="row-field">
                        <div class="label-row conf-label-row">
                            <span class="input-label">
                                親品番
                            </span>
                        </div>
                        <div class="input-row">
                            <input type="text" readonly class="row-input-mid" id="parent_part_number" name="parent_part_number" value="{{ old('part_number', $product->part_number ?? '') }}">
                        </div>
                        <div class="error_msg"></div>
                    </div>
    
                    {{-- product_name --}}
                    <div class="row-field">
                        <div class="label-row conf-label-row">
                            <span class="input-label">
                                親品名
                            </span>
                        </div>
                        <div class="input-row">
                            <input type="text" readonly class="row-input-long" id="product_name_selected" name="product_name_selected" value="{{ old('product_name', $product->product_name ?? '') }}">
                        </div>
                        <div class="error_msg"></div>
                    </div>
    
                    {{-- child_part_number --}}
                    <div class="row-field">
                        <div class="label-row conf-label-row">
                            <span class="input-label">
                                品番
                            </span>
                        </div>
                        <div class="input-row flex-gap-8">
                            <input hidden type="text" id="orig_child_part_number" name="orig_child_part_number" value="">
                            <input type="text" id="child_part_number" name="child_part_number" value="" class="" style="max-width: 150px">
                            <input type="text" readonly
                                        name="child_product_name"
                                        id="child_product_name"
                                        disabled
                                        value=""
                                        class="middle-name"
                                        style="width: 210px">
                            <button type="button" class="btnSubmitCustomSecond btnSubmitCustom js-modal-open"
                                        data-target="searchPartNumberModal">
                                <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                    alt="magnifying_glass.svg">
                            </button>
                        </div>
                        <div class="error_msg"></div>
                    </div>
    
                    {{-- number_used --}}
                    <div class="row-field">
                        <div class="label-row conf-label-row">
                            <span class="input-label">
                                使用個数
                            </span>
                        </div>
                        <div class="input-row">
                            <input type="number" class="" id="number_used" name="number_used" value="" min="0">
                        </div>
                        <div class="error_msg"></div>
                    </div>
    
                    {{-- material_classification --}}
                    <div class="row-field last-row-field">
                        <div class="label-row conf-label-row">
                            <span class="input-label">
                                製品区分
                            </span>
                        </div>
                        <div class="input-row flex-radio"> 
                            <label class="container-radio">材料
                                <input type="radio" name="material_classification" value="1" id="material_classification1" checked>
                                <span class="checkmark"></span>
                            </label>
                            <label class="container-radio">構成部品
                                <input type="radio" name="material_classification" value="2" id="material_classification2">
                                <span class="checkmark"></span>
                            </label>
                        </div>
                        <div class="error_msg"></div>
                    </div>

                    {{-- delete_flag --}}
                    @if (!empty($config->id))
                    <tr>
                        <input  style="max-width: 20px; " type="checkbox" name="delete_flag" value="1">&nbsp;
                    </tr>
                    @endif
    
                    <div class="buttonRow" style="display: flex; justify-content: space-between; margin-top: 2em">
                        <div>
                            <button type="button" id="back_" class="buttonCreate button-product">
                                戻る
                            </button>
                        </div>
                        <div style="display: flex; justify-content: flex-end;">
                            <div>
                                <button type="button"
                                   class="button-product js-btn-reset-reload-second" style="color: #ffffff;
                                        background-color: var(--btn-basic);
                                        border: #0077c7 solid 1px; margin-right: 10px; width: 180px">クリア
                                </button>
                            </div>
                            <div>
                                <button type="button" id="delte_child_config" class="button-product btn-delete" style="width: 180px; margin-right: 10px;">
                                    削除
                                </button>
                            </div>
                            <div>
                                <button type="button" id="{{ !empty($conf->id) ? 'btn_editChildConfiguration' : 'btn_saveConfiguration'  }}" class="button-product btn-save" style="width: 180px">
                                    登録
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>
</div>
@include('partials.modals.masters.products._search', [
    'modalId' => 'searchPartNumberModal',
    'searchLabel' => '品番',
    'resultValueElementId' => 'child_part_number',
    'resultNameElementId' => 'child_product_name',
    'model' => 'ProductNumber',
    'hint' => 'prod_cat_zero'
])

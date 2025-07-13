@push('styles')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/master/modal/ups_modal.css')
    <style>
        /* Your custom styles here */
        .card-body {
            text-indent: 2em;
        }

        .button-container {
            display: flex;
            align-items: center;
            justify-content: center;
            /* height: 100vh; */
        }

        .btnAction {
            margin: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }

        .disabled {
            background-color: rgb(159, 168, 168);
            cursor: default;
            border-color: rgb(159, 168, 168);
        }

        .panel {
            padding: 0 18px;
            background-color: white;
            /* max-height: 0; */
            /* overflow: hidden; */
            transition: max-height 0.2s ease-out;
        }

        .highlight {
            background-color: yellow; /* You can change the highlighting style */
        }
        .no-outline {
            /* border: none; */
            /* outline: none !important; */
            cursor: pointer;
            /* background-color: #ffffff; */
        }
        .product_info {
            margin-bottom: 20px;
        }
        .swal2-container , #unitPriceSettingModal{
            z-index: 9999; /* Set a high value to make it appear on top */
        }

    </style>
@endpush

<div id="{{ $modalId }}" class="modal js-modal-first modal__bg modalSs" style="min-width: 60%; z-index: 3000; ">
    <div class="modal__content"  style="min-width: 60%; width: 910px;">
        <button type="button" class="modalCloseBtn js-modal-close-first">x</button>
        <div class="modalInner inner-modal">
            <div class="pagettlWrap">
                <h1>
                    <span>{{ $modalLabel }}</span>
                </h1>
            </div>

            <div class="box mb-1 content-box">
                <div class="product_info">
                    <label type="text" readonly data-id="{{ $product->id }}" class="no-outline parentLabel">{{ $product->part_number . ' ' . $product->product_name . '（製品）' }}</label>
                    <div class="panel childLabels">
                        @foreach($configurations as $conf)
                            <div class="item-configuration">
                                - <label type="text" class="childLabel no-outline"
                                    data-id="{{ $conf->id }}"
                                    data-numberUsed="{{ $conf->number_used }}"
                                    data-materialClassification="{{ $conf->material_classification }}"
                                    data-childPartNumber="{{ $conf->child_part_number  }}"
                                    data-productName="{{ $conf->product_name }}">
                                        {{ $conf->child_part_number . ' ' . $conf->product_name  }}{{$conf->material_classification == 1 ? '（材料）' : '（構成部品）' }} ' X '  {{ intval($conf->number_used) }}
                                    </label>
                            </div>
                            {{-- <br> --}}
                        @endforeach
                    </div>
                </div>
                <div class="button-container">
                    <button id="btnNew" type="button"
                           
                            class="btnAction button-product button-modal btn-new  js-modal-open"
                            data-target="secondConfigurationModal"
                            id="show_second_conf_settings_modal">
                            材料・構成部品を追加
                    </button>
                    <button type="button"
                            id="btn-edit"
                            class="btnAction button-product button-modal disabled btn-edit js-modal-open"
                            data-target="secondConfigurationModal">
                            構成編集</button>

                    <button type="button" class="btnAction button-product button-modal js-modal-open" id="editInNewTab" data-id="{{$product->id}}">品番設定</button>
                    <button type="button" class="btnAction button-product button-modal js-modal-open" id="unitPriceSetting" data-target="unitPriceSettingModal">品番単価設定</button>
                    <button type="button" class="btnAction button-product button-modal js-modal-open" id="sequenceSetting" data-target="sequenceSettingModal">工程順序設定</button>
                    <button type="button" class="btnAction button-product button-modal js-modal-open js-modal-close">閉じる</button>
                </div>
            </div>
        </div>
    </div>
</div>

@include('pages.master.part.modal.second_configuration_settings_modal', [
    'modalId' => 'secondConfigurationModal',
    'modalLabel' => '構成マスタメンテ：材料・構成部品を追加',
])

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@push('scripts')
    @vite(['resources/js/master/products/modals/configuration-settings.js'])
@endpush

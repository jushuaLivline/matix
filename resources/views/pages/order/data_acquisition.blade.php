@extends('layouts.app')

@push('styles')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/order/style.css')
@endpush

@section('title', '内示データ取込')

@push('scripts')
    <script>
        $('#submit-form').on('click', function() {
            $('#uploading-form').submit();
        });
        // $('#uploading-form').on('submit', function(e) {
        //     e.preventDefault();
            
        // });
        $('input:radio[name="delivery-option"]').change(function(){
            if ($(this).is(':checked') && $(this).val() == 0) {
                $("#others-frame").css("display", "flex");
            } else {
                $("#others-frame").css("display", "none");
            }
        });

        $('.file-input-trigger').on('click', function() {
            $("#file").click();
        });

        function ChangeText(oFileInput, sTargetID) {
            document.getElementById(sTargetID).value = oFileInput.value.split('\\').pop();
        }
    </script>
@endpush

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                内示データ取込
            </div>

            <div class="section">
                <form class="overlayedSubmitForm" action="{{ route('order.process.data.acquisition') }}" method="post" id="uploading-form" enctype="multipart/form-data">
                    @csrf
                    <h1 class="form-label bar indented">取込条件設定</h1>
                    <div class="box mb-3">
                        <div class="mb-3">
                            <label class="form-label dotted indented">データ種類</label>
                            @php
                                $selected = request('delivery-option');
                            @endphp
                            <div>
                                <label class="radioBasic mr-2">
                                    <input type="radio" name="delivery-option" value="1" {{ $selected === null || $selected == '1' ? 'checked' : '' }}/>
                                    <span>アイシン精機</span>
                                </label>
                                <label class="radioBasic mr-2">
                                    <input type="radio" name="delivery-option" value="2" {{ $selected == '2' ? 'checked' : '' }}/>
                                    <span>アイシンAI </span>
                                </label>
                                <label class="radioBasic mr-2">
                                    <input type="radio" name="delivery-option" value="3" {{ $selected == '3' ? 'checked' : '' }}/>
                                    <span>アイシンAW</span>
                                </label>
                                <label class="radioBasic mr-2">
                                    <input type="radio" name="delivery-option" value="0" {{ $selected == '0' ? 'checked' : '' }}/>
                                    <span>その他</span>
                                </label>
                            </div>
                        </div>
                        <div class="mb-3" style="display:none !important;" id="others-frame">
                            <div style='margin-right:3rem;'>
                                <label class="form-label dotted indented">納入先</label>
                                <div class="d-flex">
                                    <input type="text" id="customer_code" class='mr-2' name="customer_code" value="" style="width:100px">
                                    <input type="text" id="customer_name" class="mr-2" name="customer_name" disabled>
                                    <button type="button" class="btnSubmitCustom js-modal-open search-btn text-white"
                                            data-target="searchCustomerModal">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path
                                                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                        </svg>
                                    </button>
                                    <!-- <button class="search-btn text-white" type="button">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path
                                                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                        </svg>
                                    </button> -->
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label dotted indented">取込ファイル</label>
                            <div>
                                <!-- <input type="text" value=""> -->
                                <input id="file-textbox" required type="text" class="file-input-trigger">
                                
                                <button type="button" class="btn text-white btn-disabled file-input-trigger">
                                    参照...
                                </button>
                                <input id="file" type="file" style='visibility: hidden;' name="file" onchange="ChangeText(this, 'file-textbox');"/>
                            </div>
                            @if($errors->any())
                                <h4 class="text-red">{{$errors->first()}}</h4>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
            <div class="float-right ml-2">
                <a href="javascript:void(0)" class="btn btn-primary btn-wide" id="submit-form">データ取込結果を確認</a>
            </div>
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchCustomerModal',
        'searchLabel' => '納入先',
        'resultValueElementId' => 'customer_code',
        'resultNameElementId' => 'customer_name',
        'model' => 'Customer'
    ])
@endsection

@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/estimates/data_list.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/order/style.css')
@endpush

@section('title', '指示部品内示入力')	
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                指示部品内示入力
            </div>

            <div class="section">
                <form id="specifiedPartForm" accept-charset="utf-8" class="overlayedSubmitForm" data-disregard-empty="true">
                    <h1 class="form-label bar indented">検索</h1>
                    <div class="box mb-3">
                        <a href="{{ route('order.data.acquisition') }}" class="float-right">ファイルでの取込はこちら</a>
                        <div class="mb-3 mr-3">
                            <label class="form-label dotted indented">年月</label> <span
                                class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" value="{{ Request::get("month_year") ?? $month->format('Ym') }}" name="year_month" style="width:100px">
                            </div>
                            <div class="error_msg"></div>
                        </div>
                        <div class="mb-3" style="display:flex">
                            <div class="mr-3">
                                <label class="form-label dotted indented">納入先 </label> <span
                                    class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <p class="formPack fixedWidth fpfw25p mr-1">
                                        <input type="text" name="customer_code"  value="{{ Request::get('customer_code') }}" id="customer_code" class="mr-25">
                                    </p>
                                    <p class="formPack fixedWidth fpfw50p box-middle-name mr-1">
                                        <input type="text" readonly
                                            name="customer_name"
                                            id="customer_name"
                                            value="{{ Request::get('customer_name') }}"
                                            class="middle-name mr-25">
                                    </p>
                                    <p class="formPack fixedWidth fpfw25p">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchCustomerModal"
                                            data-query="searchProductNumberModal"
                                            data-reference="customer_code"
                                        >
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                        </button>
                                    </p>
                                </div>
                                <div class="error_msg"></div>
                            </div>
                            <div style="flex:1">
                                <label class="form-label dotted indented">受入</label>
                                <div>
                                    <input type="text" value="{{ Request::get("acceptance") }}" name="acceptance" style="width:50px">
                                </div>
                            </div>
                        </div>

                        <div class="mr-4">
                            <label class="form-label dotted indented">製品品番 </label> <span
                                class="others-frame btn-orange badge">必須</span>
                            <div class="d-flex">
                                <p class="formPack fixedWidth fpfw25p mr-1">
                                    <input type="text" 
                                        value="{{ Request::get("product_code") }}" 
                                        name="product_code" 
                                        id="product_code" class="mr-25"
                                        >
                                </p>
                                <p class="formPack fixedWidth fpfw25p mr-1">
                                    <input type="text" readonly value="{{ Request::get("product_name") }}" name="product_name" id="product_name" class="mr-25">
                                </p>
                                <p class="formPack fixedWidth fpfw25p mr-1">
                                    <button class="btnSubmitCustom js-modal-open" 
                                        data-target="searchProductNumberModal">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                    </button>
                                </p>
                            </div>
                            <div class="error_msg"></div>
                        </div>
                        <div class="text-center">
                            <a href="{{ route("order.specified.part") }}"
                                class="btn btn-primary btn-wide">検索条件をクリア</a>
                            <button type="submit" class="btn btn-primary btn-wide">
                                検索
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="section">
                <h1 class="form-label bar indented">検索結果</h1>
                @if($unofficialNotice)
                <div class="box">
                    <div class="mb-1">
                        {{ $month->format('Y') }}年{{ $month->format("m") }}月の内示情報を表示してます
                        
                        <a class="btn btn-blue text-white" href="{{ route('order.specified.part', ['year_month' => $month->subMonth(1)->format("Ym"), 'customer_code' => Request::get("customer_code"), 'customer_name' => Request::get('customer_name'), 'product_name' => Request::get("product_name"), 'product_code' => Request::get("product_code"), 'acceptance' => Request::get("acceptance") ]) }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-left" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M11.354 1.646a.5.5 0 0 1 0 .708L5.707 8l5.647 5.646a.5.5 0 0 1-.708.708l-6-6a.5.5 0 0 1 0-.708l6-6a.5.5 0 0 1 .708 0z"/>
                            </svg>
                            前月
                        </a>
                        <a class="btn btn-blue text-white" href="{{ route('order.specified.part', ['year_month' => $month->addMonth(2)->format("Ym"), 'customer_code' => Request::get("customer_code"), 'customer_name' => Request::get('customer_name'), 'product_name' => Request::get("product_name"), 'product_code' => Request::get("product_code"), 'acceptance' => Request::get("acceptance") ]) }}">
                            翌月
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-chevron-right" viewBox="0 0 16 16">
                                <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                            </svg>
                        </a>
                    </div>
                    <form action="{{ route('order.specified.part.post') }}" id="days_form" method="POST">
                        @csrf
                        <input type="hidden" name="product_code" value="{{ Request::get("product_code") }}">
                        <input type="hidden" name="customer_code" value="{{ Request::get("customer_code") }}">
                        <input type="hidden" name="year_month" value="{{ Request::get("year_month") }}">
                        <input type="hidden" name="acceptance" value="{{ Request::get("acceptance") }}">
                        @foreach($dates as $dateList)
                            <table @if(count($dateList) == 1) style="width: 10%; !important" @endif class="table table-bordered table-striped">
                                <thead>
                                    @foreach($dateList as $date)
                                        <th>{{ $date->format('d') }}日</th>
                                    @endforeach
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($dateList as $date)
                                            @php
                                                $day = 'day_' . (int) $date->format('d');
                                                if($unofficialNotice){
                                                    $value = $unofficialNotice->{$day} ?? 0;    
                                                }else{
                                                    $value = 0;
                                                }    
                                            @endphp
                                            <td>
                                                <input type="text" name="{{ $day }}" class="full-width daily-input" value="{{ $value }}">
                                            </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        @endforeach
                    </form>
                </div>
                @else
                <div class="box text-center">
                    <h4>検索結果はありません</h4>
                </div>
                @endif
            </div>
            <div class="float-right">
                <button onclick="if(confirm('「内示情報を削除します、よろしいでしょうか？」')){ $('.daily-input').val(0) }" class="btn px-3 btn-orange">削　除</button>
                <button type="submit" form="days_form" class="btn btn-green"> この内容で登録する </button>
            </div>
        </div>
    </div>


@include('partials.modals.masters._search', [
    'modalId' => 'searchCustomerModal',
    'searchLabel' => '納入先',
    'resultValueElementId' => 'customer_code',
    'resultNameElementId' => 'customer_name',
    'model' => 'Customer',
    'query'=> "searchProductNumberModal",
    'reference' => "customer_code"
])

@include('partials.modals.masters._search', [
    'modalId' => 'searchProductNumberModal',
    'searchLabel' => '製品品番',
    'resultValueElementId' => 'product_code',
    'resultNameElementId' => 'product_name',
    'model' => 'Product'
])


@endsection

@push('scripts')
    <script>
        $('#specifiedPartForm').validate({
            rules: {
                year_month: {
                    required: true
                },
                customer_code: {
                    required: true
                },
                product_code: {
                    required: true
                },
            },
            messages: {
                year_month: {
                    required: '入力してください'
                },
                customer_code: {
                    required: '入力してください'
                },
                product_code: {
                    required: '入力してください'
                },
            },
            errorElement : 'div',
            errorPlacement: function(error, element) {
                if($(element).closest('div')){
                    $(element).closest('div').siblings('div').html(error);
                }else{
                    $(element).closest('p').closest('div').siblings('div').html(error);
                }
            },
            invalidHandler: function(event, validator) {
                setInterval(() => {
                    $('.submit-overlay').css('display', "none");
                }, 0);
            }
        })
    </script>
@endpush
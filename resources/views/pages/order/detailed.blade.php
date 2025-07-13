@extends('layouts.app')

@push('styles')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/index.css')
    @vite('resources/css/order/style.css')
@endpush

@section('title', '確定受注入力')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                確定受注入力
            </div>
            
            <div class="section">
                <form id="orderDetaildForm" class="overlayedSubmitForm" data-disregard-empty="true">
                <h1 class="form-label bar indented">検索</h1>
                <div class="box mb-3">
                    <div class="mb-3">
                        <div class="d-flex">
                            <div class='mr-4'>
                                <label class="form-label dotted indented">受注日</label> <span class="btn-orange badge">必須</span>
                                <div class="d-flex">
                                    @include('partials._date_picker', ['inputName' => 'order_date', 'value' => Request::get('order_date')])
                                </div>
                                <div class="error_msg"></div>
                                {{-- @if ($errors->has('order_date'))
                                    <div class="error_msg">入力してください</div>
                                @endif --}}
                            </div>
                            <div class="mr-4">
                                <label class="form-label dotted indented">便No.</label>
                                <div>
                                    <input type="text" value="{{ Request::get('flight_no') }}" style="width:100px" name="flight_no">
                                </div>
                            </div>
                            <div class="mr-4">
                                <label class="form-label dotted indented">納入先</label>
                                <div class="d-flex">
                                    <input type="text" id="customer_code" name="customer_code" value="{{ Request::get('customer_code') }}" style="width:100px" class="mr-2">
                                    <input type="text" id="customer_name" name="customer_name" value="{{ Request::get('customer_name') }}" readonly class="mr-2">
                                    <p class="formPack fixedWidth fpfw25p">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchCustomerModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                        </button>
                                    </p>
                                    <!-- <button class="search-btn text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path
                                                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                        </svg>
                                    </button> -->
                                </div>
                            </div>
                            <div class="mr-4">
                                <label class="form-label dotted indented">工場</label>
                                <div>
                                    <input type="text" value="{{ Request::get('order_plant') }}" style="width:100px" name="order_plant">
                                </div>
                            </div>
                            <div class="mr-4">
                                <label class="form-label dotted indented">受入</label> 
                                <div>
                                    <input type="text" value="{{ Request::get('acceptance') }}" style="width:100px" name="acceptance">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 d-flex">
                        <div class="mr-4">
                            <label class="form-label dotted indented">区分</label> <span class="btn-orange badge">必須</span>
                            <div>
                                <label class='radioBasic mr-1'>
                                    <input type="radio" name="class_type" class="" value="1" {{ (Request::get('class_type') ?? 1) == 1 ? 'checked' : '' }}> 
                                    <span>
                                        かんばん
                                    </span>
                                </label>
                                <label class='radioBasic mr-1'>
                                    <input type="radio" name="class_type" class="" value="2" {{ (Request::get('class_type') ?? 1) == 2 ? 'checked' : '' }}> 
                                    <span>
                                        指示
                                    </span>
                                </label>
                            </div>
                            
                        </div>
                        <div>
                            <label class="form-label dotted indented">製品品番</label>
                            <div class="d-flex">
                                <div class='mr-4 d-flex'>
                                    <input type="text" id="part_number_first" name="part_number_first" value="{{ Request::get('part_number_first') }}" class="mr-2" name="order_min_product_number">
                                    <input type="text" id="product_name_first" name="product_name_first" value="{{ Request::get('product_name_first') }}" disabled  class="mr-2">
                                    <p class="formPack fixedWidth fpfw25p">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchPartNumberModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                    alt="magnifying_glass.svg">
                                        </button>
                                    </p>
                                    <!-- <button class="search-btn text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path
                                                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                        </svg>
                                    </button> -->

                                    <span style="font-size:24px; padding:0px 10px;">
                                        ~
                                    </span>

                                    <input type="text"  value="{{ Request::get('part_number_second') }}" class="mr-2" id="part_number_second" name="part_number_second" >
                                    <input type="text" value="{{ Request::get('product_name_second') }}" id="product_name_second" name="product_name_second" disabled  class="mr-2">
                                    <button type="button" class="btnSubmitCustom js-modal-open search-btn text-white" data-target="searchPartNumberModalSecond">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path
                                                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                        </svg>
                                    </button>
                                    <!-- <button class="search-btn text-white">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                                            <path
                                                d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z" />
                                        </svg>
                                    </button> -->


                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-center sc mt-4">
                        <a class="btn btn-primary btn-wide" href="{{ route('order.detailed') }}"> 検索条件をクリア</a>
                        <button class="btn btn-primary btn-wide" type="submit">検索</button>
                    </div>
                </div>
                </form>
            </div>

            <div class="section">
                <h1 class="form-label bar indented">検索結果</h1>
                <div class="box">
                    <div class="mb-3">
                        @if ($orders)
                            {{ $orders->total() }}件中、{{ $orders->firstItem()  }}件～{{ $orders->lastItem()  }}件を表示してます
                        @endif
                        <table class="table width-auto table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="f-bold">製品品番</th>
                                    <th class="f-bold">品名</th>
                                    <th>背番号</th>
                                    <th>収容数</th>
                                    <th>{{ (request()->class_type ?? 1) == 1 ? '枚数' : '個数'}}</th>
                                    <th>納入番号</th>
                                    <th colspan="3">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                <tr>
                                    <td>{{ $order->edited_part_number ?? '' }}</td>
                                    <td>{{ $order->product->product_name ?? '' }}</td>
                                    <td>{{ $order->confirm_order_no ?? '' }}</td>
                                    <td>{{ $order->uniform_number ?? '' }}</td>
                                    <td><input type="text" class="w-md" value="{{ $order->number_of_accommodated }}" style="width: 3rem !important;"></td>
                                    <td>{{ $order->delivery_no ?? ''}}</td>
                                    <td>
                                        <button onclick="if(confirm('「確定受注データを削除します、よろしいでしょうか？」')){}" class="btn btn-block btn-orange">削除</button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center">検索結果はありません</td>
                                </tr>
                                @endforelse
                                
                            </tbody>
                        </table>
                    </div>
                    <!-- <div class="mb-3">
                        n件中、n件～n件を表示してます
                        <table class="table width-auto table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th class="f-bold">製品品番</th>
                                    <th class="f-bold">品名</th>
                                    <th>背番号</th>
                                    <th>収容数</th>
                                    <th>個数</th>
                                    <th>納入番号</th>
                                    <th colspan="3">操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>ZZZZZZZZ</td>
                                    <td>XXXXXXXX</td>
                                    <td>ZZZZ</td>
                                    <td>99</td>
                                    <td><input type="text" class="w-md" value="99"></td>
                                    <td>ZZZZ</td>
                                    <td>
                                        <button onclick="if(confirm('「内示情報を削除します、よろしいでしょうか？」')){}" class="btn btn-block btn-orange">削除</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>ZZZZZZZZ</td>
                                    <td>XXXXXXXX</td>
                                    <td>ZZZZ</td>
                                    <td>99</td>
                                    <td><input type="text" class="w-md" value="99"></td>
                                    <td>ZZZZ</td>
                                    <td>
                                        <button onclick="if(confirm('「内示情報を削除します、よろしいでしょうか？」')){}" class="btn btn-block btn-orange">削除</button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>ZZZZZZZZ</td>
                                    <td>XXXXXXXX</td>
                                    <td>ZZZZ</td>
                                    <td>99</td>
                                    <td><input type="text" class="w-md" value="99"></td>
                                    <td>ZZZZ</td>
                                    <td>
                                        <button onclick="if(confirm('「内示情報を削除します、よろしいでしょうか？」')){}" class="btn btn-block btn-orange">削除</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div> -->
                </div>
                {{-- @include('partials._pagination') --}}
            </div>
           
        </div>
    </div>

    @include('partials.modals.masters._search', [
        'modalId' => 'searchCustomerModal',
        'searchLabel' => '納入先',
        'resultValueElementId' => 'customer_code',
        'resultNameElementId' => 'customer_name',
        'model' => 'NotSupplier'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchPartNumberModal',
        'searchLabel' => '品番',
        'resultValueElementId' => 'part_number_first',
        'resultNameElementId' => 'product_name_first',
        'model' => 'ProductNumber'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchPartNumberModalSecond',
        'searchLabel' => '品番',
        'resultValueElementId' => 'part_number_second',
        'resultNameElementId' => 'product_name_second',
        'model' => 'ProductNumber'
    ])
@endsection

@push('scripts')
    <script>
        $('#orderDetaildForm').validate({
            rules: {
                order_date: {
                    required: true
                },
            },
            messages: {
                order_date: {
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
@extends('layouts.app')

@push('styles')
    @vite('resources/css/order/style.css')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
    @vite('resources/css/order/confirmed/create.css')
@endpush

@section('title', '確定受注入力')	
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                確定受注入力
            </div>            
            <form method="GET" id="firm-order-form" class="overlayedSubmitForm with-js-validation">
                <div class="section">
                    <h1 class="form-label bar indented">検索</h1>
                    <div class="box mb-1">
                        <div class="mb-4 d-flex">
                            <div>
                                <label class="form-label dotted indented">受注日</label> <span id="others-frame" class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex mr-20c" >
                                    @php
                                        $date =  $data['created_at'] ?? now()->format('Ymd');
                                    @endphp
                                    @include('partials._date_picker', [
                                        'inputName' => 'created_at', 
                                        'attributes' => 'data-error-messsage-container=#date_error_message data-field-name=受注日', 
                                        'inputClass' => 'text-left w-100c', 
                                        'value' => $date, 
                                        'required' => true])

                                    </div>
                                    <div id="date_error_message" style="width: 100%;"></div>
                            </div>

                            <div class="mr-3">
                                <label class="form-label dotted indented">便No.</label> <span id="others-frame" class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <input  type="text" name="delivery_no" id="delivery_no"
                                         data-field-name="便No"
                                        data-error-messsage-container="#delivery_no_error"
                                        value="{{ $data['delivery_no'] ?? ''}}"
                                        required>
                                </div>
                                <div id="delivery_no_error"></div>
                            </div>
    
                            <div class="mr-4">
                                <label class="form-label dotted indented">納入先</label> <span class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                        @php
                                            $customer_code  =  request()->get('customer_code') ?? '';
                                            $customer_name  =  ($customer_code) ? request()->get('customer_name')  : '';
                                        @endphp
                                        <input type="text" id="customer_code" 
                                                    data-field-name="納入先"
                                                    data-error-messsage-container="#supplier_code_error"
                                                    data-validate-exist-model="customer"
                                                    data-validate-exist-column="customer_code"
                                                    data-inputautosearch-model="customer"
                                                    data-inputautosearch-column="customer_code"
                                                    data-inputautosearch-return="customer_name"
                                                    data-inputautosearch-reference="customer_name"
                                                    name="customer_code" style="width:100px; margin-right: 10px;" 
                                                    value="{{ $customer_code }}"
                                                    required>
                                        <input type="text" id="customer_name" name="customer_name" readonly 
                                                value="{{ $customer_name  }}" style="margin-right: 10px;">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchCustomerModal"
                                                data-query-field="">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                        </button>
                                    </div>
                                    <div id="supplier_code_error"></div>
                            </div>

                            <div class="ml-3 mr-4">
                                <label class="form-label dotted indented">工場</label> 
                                <div class="d-flex">
                                    <input  type="text" name="plant" id="plant"
                                        maxlength="2"
                                        value="{{ $data['plant'] ?? ''}}" >
                                </div>
                                <div class="error_msg"></div>
                            </div>

                            <div class="ml-3">
                                <label class="form-label dotted indented">受入</label> <span id="others-frame" class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex">
                                    <input  type="text" name="acceptance" id="acceptance"
                                        data-field-name="受入"
                                        data-error-messsage-container="#acceptance_error"
                                        maxlength="2"
                                        value="{{ $data['acceptance'] ?? ''}}"
                                        required>
                                </div>
                                <div id="acceptance_error"></div>
                            </div>
                        </div>
    
                        <div class="mb-2 d-flex">
                            <div class="mr-4">
                                <label class="form-label dotted indented">区分</label> <span
                                    class="others-frame btn-orange badge">必須</span>
                                <div class="d-flex radio-div">
                                    <input type="radio" value="1" name="classification" id="radio-kanban"
                                        {{ isset($data['classification']) ? ($data['classification'] == "1" ? 'checked' : '') : 'checked' }}
                                        required
                                    >
                                    <label for="radio-kanban">かんばん</label>
                                    <input type="radio" value="2" name="classification" id="radio-instructions"
                                        {{ isset($data['classification']) && $data['classification'] == "2" ? 'checked' : '' }}
                                    >
                                    <label for="radio-instructions">指示</label>
                                </div>
                                <div class="error_msg"></div>
                            </div>

                            <div class="mr-2">
                                <label class="form-label dotted indented">製品品番</label>
                                <div class="d-flex">
                                    @php
                                        $product_code  =  $data['product_code'] ?? '';
                                        $product_name  =  ($product_code) ? request()->get('product_name')  : '';
                                    @endphp
                                    <input type="text" name="product_code" id="product_code" 
                                        data-field-name="製品品番"
                                        data-error-messsage-container="#product_code_error"
                                        data-validate-exist-model="ProductNumber"
                                        data-validate-exist-column="part_number"
                                        data-inputautosearch-model="ProductNumber"
                                        data-inputautosearch-column="part_number"
                                        data-inputautosearch-return="product_name"
                                        data-inputautosearch-reference="product_name"
                                        value="{{ $product_code }}"
                                        class="w-130c mr-2">

                                    <input type="text" readonly 
                                        value="{{ $product_name  }}"
                                        class="middle-name mr-2" name="product_name" id="product_name">

                                    <button type="button" class="btnSubmitCustom js-modal-open"
                                            data-target="searchProductModal">
                                        <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                alt="magnifying_glass.svg">
                                    </button>
                                </div>
                                <div id="product_code_error"></div>
                            </div>
                        </div>

                        <div class="text-center button-field">
                            <button type="reset"
                                    class="btn btn-primary btn-wide">
                                    検索条件をクリア
                            </button>
                            <button type="submit" class="btn btn-primary btn-wide">検索</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="section">
                <h1 class="form-label bar indented">検索結果</h1>
                <div class="tableWrap bordertable" style="clear: both;">
                    @if($result && $result->total() > 0)
                        <ul class="headerList">
                            {{ $result->total() }}件中、{{ $result->firstItem() }}件～{{ $result->lastItem() }} 件を表示してます
                        </ul>
                    @endif
                    <table class="tableBasic list-table" id="list-table" style="width:70%;">
                        <thead>
                            <tr>
                                <th>製品品番</th>
                                <th>品名</th>
                                <th>背番号</th>
                                <th>収容数</th>
                                <th>個数</th>
                                <th>納入番号</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($result as $order )
                                <tr data-tr-id="{{$order->id}}">
                                    <td class="tA-cn">{{ $order->part_number }}</td>
                                    <td class="tA-cn">{{ optional($order->product)->product_name }}</td>
                                    <td class="tA-cn">{{ $order->uniform_number }}</td>
                                    <td class="tA-cn">
                                        <input type="text"
                                            class="acceptNumericOnly"
                                            name="number-of-accommodated"
                                            value="{{ $order->number_of_accommodated}}">
                                    </td>
                                    <td class="tA-cn kanban-number">
                                        @if($order->classification == 2)
                                            <input type="text"
                                                class="acceptNumericOnly"
                                                name="kanban-number"
                                                value="{{ $order->kanban_number}}">
                                        @else
                                            {{ $order->kanban_number}}
                                        @endif
                                    </td>
                                    <td class="tA-le">{{ $order->ai_delivery_number }}</td>
                                    <td class="tA-cn">
                                        <button type="button" class="btn delete-btn">
                                            削除
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center">
                                        検索結果はありません
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="button-group mt-4">
                    {{ $result->appends(request()->all())->links() }}
                    <button class="btn btn-success" id="register" {{$result->total() == 0 ? 'disabled' : ''}}>この内容で登録する</button>
                </div>
                
            </div>

        </div>
    </div>

    @include('partials.modals.masters._search', [
        'modalId' => 'searchCustomerModal',
        'searchLabel' => '納入先',
        'resultValueElementId' => 'delivery_destination_code',
        'resultNameElementId' => 'delivery_destination_name',
        'model' => 'Customer'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductModal',
        'searchLabel' => '製品品番',
        'resultValueElementId' => 'part_number_from',
        'resultNameElementId' => 'product_name_from',
        'model' => 'ProductNumber'
    ])

    @include('partials.modals.masters._search', [
        'modalId' => 'searchProductToModal',
        'searchLabel' => '製品品番',
        'resultValueElementId' => 'part_number_to',
        'resultNameElementId' => 'product_name_to',
        'model' => 'ProductNumber'
    ])
@endsection

@push('scripts')
    @vite('resources/js/order/confirmed/create.js')
@endpush
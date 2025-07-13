@extends('layouts.app') 

@push('styles')
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
@endpush


@section('title', '見積検索・一覧') 
@section('content') 
<div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>見積一覧</span></h1>
            </div>
            <div class="pagettlWrap">
                <h1><span>検索</span></h1>
            </div>
            <form accept-charset="utf-8"
                class="overlayedSubmitForm with-js-validation" 
                data-disregard-empty="true"
                id="estimateSearchForm"
                >
                <div class="tableWrap borderLesstable inputFormArea section">
                    <!-- 得意先 -->
                    <dl class="formsetBox">
                        <dt>得意先</dt>
                        <dd>
                            <div class="mb-2">
                                <div class="d-flex">
                                    @php
                                        $customer_code  =  request()->get('customer_code') ?? '';
                                        $customer_name  =  ($customer_code) ? request()->get('customer_name')  : '';
                                    @endphp
                                    <input type="text" id="customer_code"
                                                data-field-name="得意先"
                                                data-error-messsage-container="#supplier_code_error"
                                                data-validate-exist-model="customer"
                                                data-validate-exist-column="customer_code"
                                                data-inputautosearch-model="customer"
                                                data-inputautosearch-column="customer_code"
                                                data-inputautosearch-return="customer_name"
                                                data-inputautosearch-reference="customer_name"
                                                name="customer_code" style="width:100px; margin-right: 10px;" 
                                                value="{{ $customer_code }}">
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
                        </dd>
                    </dl>
                    <!-- 見積依頼日 -->
                    <dl class="formsetBox">
                        <dt>見積依頼日</dt>
                        <dd>
                            <div class="mb-2">
                            <div class="d-flex">
                                @include('partials._date_picker', [
                                        'inputName' => 'estimate_request_date_start', 
                                        'attributes' => 'data-error-messsage-container=#request_error_message data-field-name=見積依頼日', 
                                        'value' => request()->get('estimate_request_date_start'),
                                        'inputClass' => 'w-100c',
                                        ])
                                <span style="font-size:24px; padding:5px 10px;">
                                    ~
                                </span>
                                @include('partials._date_picker', [
                                        'inputName' => 'estimate_request_date_end', 
                                        'attributes' => 'data-error-messsage-container=#request_error_message data-field-name=見積依頼日', 
                                        'value' => request()->get('estimate_request_date_end'),
                                        'inputClass' => 'w-100c',
                                        ])
                            </div>
                            <div id="request_error_message"></div>
                            </div>
                        </dd>
                    </dl>


                    <!-- 見積回答日 -->
                    <dl class="formsetBox">
                        <dt>見積回答日</dt>
                        <dd>
                            <div class="mb-2">
                            <div class="d-flex">
                                @include('partials._date_picker', [
                                        'inputName' => 'reply_due_date_start', 
                                        'attributes' => 'data-error-messsage-container=#reply_due_date_start_error data-field-name=見積回答日', 
                                        'value' => request()->get('reply_due_date_start'),
                                        'inputClass' => 'w-100c',
                                        ])
                                <span style="font-size:24px; padding:5px 10px;">
                                    ~
                                </span>
                                @include('partials._date_picker', [
                                        'inputName' => 'reply_due_date_end', 
                                       'attributes' => 'data-error-messsage-container=#reply_due_date_start_error data-field-name=見積回答日', 
                                        'value' => request()->get('reply_due_date_end'),
                                        'inputClass' => 'w-100c ',
                                        ])
                            </div>
                            <div id="reply_due_date_start_error"></div>
                            </div>
                        </dd>
                    </dl>


                    <!-- 品番 -->
                    <dl class="formsetBox">
                        <dt>品番</dt>
                        <dd>
                            <div class="mb-2">
                                <p class="formPack fixedWidth">
                                    <input type="text" name="product_code"
                                        class="w-100c"
                                        value="{{ Request::get("product_code") }}" class="">
                                </p>
                                <div class="error_msg"></div>
                            </div>
                        </dd>
                    </dl>

                    <!-- 品名 -->
                    <dl class="formsetBox">
                        <dt>品名</dt>
                        <dd>
                            <div class="mb-2">
                                <p class="formPack fixedWidth">
                                        <input type="text" name="part_name"
                                            class="w-100c"
                                            value="{{ Request::get("part_name") }}" class="">
                                    </p>
                                    <div class="error_msg"></div>
                            </div>
                        </dd>
                    </dl>


                      <!-- 型式 -->
                      <dl class="formsetBox">
                        <dt>型式</dt>
                        <dd>
                            <div class="mb-2">
                            <p class="formPack fixedWidth">
                                <input type="text" name="model_type" value="{{ Request::get("model_type") }}"
                                    class="">
                            </p>
                            <div class="error_msg"></div>
                            </div>
                        </dd>
                    </dl>

                      <!-- 見積回答種別 -->
                      <dl class="formsetBox">
                        <dt>見積回答種別</dt>
                        <dd>
                            <div class="mb-2">
                            <p class="formPack mr-2">
                                <label class="align-content-center d-flex">
                                    <input type="checkbox" name="unanswered"
                                        @checked(Request::get("unanswered")) value="1">
                                    <span  class="mt-15c">未回答</span>
                                </label>
                            </p>
                            <p class="formPack mr-2">
                                <label class="align-content-center d-flex">
                                    <input type="checkbox" name="answered"
                                        @checked(Request::get("answered")) value="1">
                                    <span  class="mt-15c">回答済</span>
                                </label>
                            </p>
                            <p class="formPack mr-2">
                                <label class="align-content-center d-flex">
                                    <input type="checkbox" name="declined"
                                        @checked(Request::get("declined")) value="1">
                                    <span class="mt-15c">見積辞退</span>
                                </label>
                            </p>
                            <div class="error_msg"></div>
                            </div>
                        </dd>
                    </dl>



                   
                    <ul class="buttonlistWrap">
                        <li>
                            <a href="{{ route("estimate.index") }}" class="buttonBasic bColor-ok">検索条件をクリア</a>
                        </li>
                        <li>
                            <button  type="submit" class="btn btn-primary buttonBasic">検索</button>
                        </li>
                    </ul>
                </div>
            </form>
            <div class="pagettlWrap">
                <h1><span>検索結果</span></h1>
            </div>
            <div class="tableWrap bordertable" style="clear: both;">
                <ul class="headerList">
                    <li>
                        @if(count($estimateSearchRecord) > 0)
                            {{ $estimateSearchRecord->total() }}件中、{{ $estimateSearchRecord->firstItem() }}件～{{ $estimateSearchRecord->lastItem() }}
                        件を表示しています @endif
                    </li>
                    <li>
                        <a href="{{ route('estimate.create') }}" class="buttBasic btn btn-primary bColor-ok"> 見積依頼を新規登録 </a>
                    </li>
                </ul>
                 <table class="table table-bordered table-striped align-middle text-center">  
                 
                    <thead>
                        <tr>
                            <th>得意先名</th>
                            <th>品番</th>
                            <th>型式</th>
                            <th>基準数/月</th>
                            <th>SOP</th>
                            <th>見積依頼日</th>
                            <th>回答期日</th>
                            <th>最終回答種別</th>
                            <th>最終回答日</th>
                            <th>操作</th>

                        </tr>
                    </thead>
                        <tbody>
                            @forelse($estimateSearchRecord as $estimate) 
                                <tr data-id={{  $estimate->id }}>
                                    <td class="tA-le">{{ $estimate->customer_contact_person }}</td>
                                    <td class="tA-cn">{{ $estimate->product_code }}</td>
                                    <td class="tA-cn">{{ $estimate->model_type }}</td>
                                    <td class="tA-ri">{{ number_format($estimate->monthly_standard_amount) }}</td>
                                    <td class="tA-le">{{ $estimate->sop ? \Carbon\Carbon::parse($estimate->sop)->format('Y/m/d') : '' }}</td>
                                    <td class="tA-le">{{ $estimate->sop ? \Carbon\Carbon::parse($estimate->estimate_request_date)->format('Y/m/d') : '' }}</td>
                                    <td class="tA-le">{{ $estimate->sop ? \Carbon\Carbon::parse($estimate->reply_due_date)->format('Y/m/d') : '' }}</td>
                                    <td class="tA-cn">
                                        @if($estimate->lastReply)
                                        @if($estimate->lastReply?->decline_flag) 見積辞退 @else 回答済 @endif @else 未回答 @endif
                                    </td>
                                    <td class="tA-le">
                                        {{ optional($estimate->lastReply?->created_at)->format("Y/m/d")  ?? ''}}
                                    </td>
                                    <td class="tA-cn">
                                        <a href="{{ route("estimate.estimateDetail.show", $estimate) }}"> 履歴詳細を見る({{ $estimate->replies_count }})
                                        </a>
                                    </td>
                                </tr> 
                        @empty 
                        <tr>
                            <td class="text-center" colspan="10">検索結果はありません</td>
                        </tr> 
                        @endforelse
                    </tbody>
                </table>
                @if ($estimateSearchRecord)
                {{ $estimateSearchRecord->links() }} @endif
            </div>
        </div>
    </div>
    @include('partials.modals.masters._search', [
        'modalId' => 'searchCustomerModal',
        'searchLabel' => '得意先',
        'resultValueElementId' => 'customer_code',
        'resultNameElementId' => 'customer_name',
        'model' => 'Customer'
    ])
@endsection

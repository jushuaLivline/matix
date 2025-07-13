@extends('layouts.app')
@include("partials._dropzone")
@push('styles')
    <style>
        .btnSubmitCustom {
            display: inline-block;
        }
    </style>
@endpush

@section('title', '見積依頼新規登録')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>見積依頼新規登録</span></h1>
            </div>

            <form action="{{ route("estimate.store") }}" accept-charset="utf-8" method="POST" class="overlayedSubmitForm" id="createReqFrm" enctype="multipart/form-data">
                @csrf
                <div class="tableWrap borderLesstable inputFormArea">
                    <table class="tableBasic">
                        <tbody>
                        <!-- 得意先 -->
                        <td>
                            <dl class="formsetBox">
                                <dt class="requiredForm">得意先</dt>
                                <dd style="width: 115%">
                                    <p class="formPack fixedWidth fpfw25p mr-1">
                                        <input type="text" name="customer_code"  value="{{ Request::get('customer_code') }}" id="customer_code"  placeholder="123456">
                                    </p>
                                    <p class="formPack fixedWidth fpfw50p box-middle-name mr-1">
                                        <input type="text" readonly
                                            id="customer_name"
                                            value="{{ Request::get('customer_name') }}"
                                            name="customer_name" class="middle-name">
                                    </p>
                                    <p class="formPack fixedWidth fpfw25p">
                                        <button type="button" class="btnSubmitCustom js-modal-open"
                                                data-target="searchCustomerModal">
                                            <img src="{{ asset('images/icons/magnifying_glass.svg') }}"
                                                 alt="magnifying_glass.svg">
                                        </button>
                                    </p>
                                    <div class="error_msg"></div>
                                </dd>
                            </dl>
                        </td>
                        <!-- 得意先担当者名 -->
                        <td>
                            <dl class="formsetBox">
                                <dt class="requiredForm">得意先担当者名</dt>
                                <dd>
                                    <p class="formPack fixedWidth fpfw100p">
                                        <input type="text" name="customer_person" placeholder="">
                                    </p>
                                    <div class="error_msg"></div>
                                </dd>
                            </dl>
                        </td>
                        <!-- 見積依頼日 -->
                        <td>
                            <dl class="formsetBox">
                                <dt class="requiredForm">見積依頼日</dt>
                                <dd>
                                    @include('partials._date_picker_estimates', ['inputName' => 'estimate_d'])
                                    <div class="error_msg"></div>
                                </dd>
                            </dl>
                        </td>
                        <!-- 回答期日 -->
                        <td>
                            <dl class="formsetBox">
                                <dt class="requiredForm">回答期日</dt>
                                <dd>
                                    @include('partials._date_picker_estimates', ['inputName' => 'answer_due_d'])
                                    <div class="error_msg"></div>
                                </dd>
                            </dl>
                        </td>
                        <!-- 品番 -->
                        <td>
                            <dl class="formsetBox">
                                <dt class="requiredForm">品番</dt>
                                <dd>
                                    <p class="formPack fixedWidth fpfw100p">
                                        <input type="text" name="base_product_code" placeholder="">
                                    </p>
                                    <div class="error_msg"></div>
                                </dd>
                            </dl>
                        </td>
                        <!-- 品名 -->
                        <td>
                            <dl class="formsetBox">
                                <dt class="requiredForm">品名</dt>
                                <dd>
                                    <p class="formPack fixedWidth fpfw100p">
                                        <input type="text" name="product_name" placeholder="">
                                    </p>
                                    <div class="error_msg"></div>
                                </dd>
                            </dl>
                        </td>
                        <!-- 型式 -->
                        <td>
                            <dl class="formsetBox">
                                <dt class="requiredForm">型式</dt>
                                <dd>
                                    <p class="formPack fixedWidth fpfw100p">
                                        <input type="text" name="model_type" placeholder="">
                                    </p>
                                    <div class="error_msg"></div>
                                </dd>
                            </dl>
                        </td>
                        <!-- 基準数/月 -->
                        <td>
                            <dl class="formsetBox">
                                <dt class="requiredForm">基準数/月</dt>
                                <dd>
                                    <p class="formPack fixedWidth fpfw100p">
                                        <input type="text" name="per_month_reference_amount" placeholder="">
                                    </p>
                                    <div class="error_msg"></div>
                                </dd>
                            </dl>
                        </td>
                        <!-- SOP -->
                        <td>
                            <dl class="formsetBox">
                                <dt class="requiredForm">SOP</dt>
                                <dd class="mt-2">
                                    <div style="display:flex;">
                                        @include('partials._date_picker', ['inputName' => 'sop_d'])
                                    </div>
                                    <div class="error_msg"></div>
                                </dd>
                            </dl>
                        </td>
                        <!-- ベース品番 -->
                        <td>
                            <dl class="formsetBox">
                                <dt>ベース品番</dt>
                                <dd>
                                    <p class="formPack fixedWidth fpfw100p">
                                        <input type="text" name="product_code">
                                    </p>
                                    <div class="error_msg"></div>
                                </dd>
                            </dl>
                        </td>
                        <!-- 得意先依頼内容 -->
                        <td width="100%">
                            <dl class="formsetBox">
                                <dt class="requiredForm">得意先依頼内容</dt>
                                <dd>
                                    <p class="formPack fixedWidth fpfw100p" style="position:relative">
                                        <span style="position:absolute; bottom:10px; right:10px"><span id="limit-indicator">0</span>/1000</span>
                                        <textarea rows="10" cols="1500"
                                                  name="message"
                                                  id="message-with-limiter"
                                                  class=""></textarea>
                                    </p>
                                    <div class="error_msg"></div>
                                </dd>
                            </dl>
                        </td>
                        <!-- 添付ファイル -->
                        <td width="100%">
                            <dl class="formsetBox">
                                <dt>添付ファイル（アップロード可能なファイル形式：{{ collect(config('filesystems.attachment.accepted_extension'))->implode(", ") }}　ファイルサイズは{{ config("filesystems.attachment.uploading_max_size") }}MBまで）
                                </dt>
                                <dd>
                                    <div class="dropzone"></div>
                                    {{-- <p class="formPack fixedWidth fpfw100p containerDrag d-inline-block" id="box">
                                        <input type="file" name="attachments[]" id="upload-button" class="d-none" multiple
                                               accept=""/>
                                        <label class="dropFiles" for="upload-button">
                                            ここにファイルをドラッグ＆ドロップしてください
                                        </label>
                                        <div id="error" style="color:red"></div>
                                        <div id="image-display"></div>
                                    </p> --}}
                                </dd>
                            </dl>
                        </td>
                        </tbody>
                    </table>
                </div>
                <div class="mt-2">
                    <button type="submit" class="btn btn-success  float-right mt-3 btn-wide">この内容で登録する</button>
                </div>
            </form>
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

@push('scripts')
    @vite(['resources/js/estimates/data-form.js'])
@endpush


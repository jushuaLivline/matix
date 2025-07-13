@extends('layouts.app')
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    @vite('resources/css/index.css')
    @vite('resources/css/modals/index.css')
    @vite('resources/css/search-modal.css')
    <style>
        .dz-size{
            display: none !important;
        }

        .dropzone .dz-preview .dz-image {
            height: 70px;
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

            @if(session('success'))
                <div id="card" style="background-color: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);margin-top: 20px;">
                    <div style="text-align: left;">
                        <p style="font-size: 18px; color: #0d9c38;">
                            {{ session('success') }}
                        </p>
                    </div>
                </div>
            @endif

            <form action="{{ route("estimate.requestCreate.store") }}" method="POST" class="overlayedSubmitForm with-js-validation" id="createReqFrm" accept-charset="utf-8"
            data-confirmation-message="見積依頼を登録します、よろしいでしょうか？">
                @csrf
                <input type="hidden" name="creator" value="{{ request()->user()->employee_code }}">
                <input type="hidden" name="created_at" value="{{ now()->format('Y-m-d H:i:s')}}">
                <div class="tableWrap borderLesstable inputFormArea">
                    <table class="tableBasic">
                        <tbody>
                        <!-- 得意先 -->
                        <td>
                            <dl class="formsetBox">
                                <dt class="requiredForm">得意先</dt>
                                <dd style="width: 115%">
                                    
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

                                </dd>
                            </dl>
                        </td>
                        <!-- 得意先担当者名 -->
                        <td>
                            <dl class="formsetBox">
                                <dt class="requiredForm">得意先担当者名</dt>
                                <dd>
                                    <p class="formPack fixedWidth fpfw100p">
                                        <input type="text" name="customer_contact_person" placeholder=""
                                            data-field-name="得意先担当者名"
                                            data-error-messsage-container="#customer_contact_person_error"
                                            required
                                            maxlength="20">
                                            
                                    </p>
                                    <div id="customer_contact_person_error"></div>
                                </dd>
                            </dl>
                        </td>
                        <!-- 見積依頼日 -->
                        <td>
                            <dl class="formsetBox">
                                <dt class="requiredForm">見積依頼日</dt>
                                <dd>
                                    @php
                                        $estimate_request_date  =  request()->get('estimate_request_date') ?? '';
                                    @endphp
                                    <div class="d-flex">
                                        @include('partials._date_picker', [
                                            'inputName' => 'estimate_request_date', 
                                            'attributes' => 'data-error-messsage-container=#estimate_request_date_error_message data-field-name=見積依頼日', 
                                            'inputClass' => 'text-left w-100c', 
                                            'value' => $estimate_request_date, 
                                            'required' => true
                                        ])
                                    </div>
                                    <div id="estimate_request_date_error_message"></div>
                                </dd>
                            </dl>
                        </td>
                        <!-- 回答期日 -->
                        <td>
                            <dl class="formsetBox">
                                <dt class="requiredForm">回答期日</dt>
                                <dd>
                                    @php
                                        $reply_due_date  =  request()->get('reply_due_date') ?? '';
                                    @endphp
                                    <div class="d-flex">
                                        @include('partials._date_picker', [
                                            'inputName' => 'reply_due_date', 
                                            'attributes' => 'data-error-messsage-container=#reply_due_date_error data-field-name=回答期日', 
                                            'inputClass' => 'text-left w-100c', 
                                            'value' => $reply_due_date, 
                                            'required' => true
                                        ])
                                    </div>
                                    <div id="reply_due_date_error"></div>
                                </dd>
                            </dl>
                        </td>
                        <!-- 品番 -->
                        <td>
                            <dl class="formsetBox">
                                <dt class="requiredForm">品番</dt>
                                <dd>
                                    <p class="formPack fixedWidth fpfw100p">
                                        <input type="text" name="product_code" placeholder=""
                                            data-field-name="品番"
                                            data-error-messsage-container="#product_code_cust_error"
                                            required
                                            maxLength="20">
                                    </p>
                                    <div id="product_code_cust_error"></div>
                                </dd>
                            </dl>
                        </td>
                        <!-- 品名 -->
                        <td>
                            <dl class="formsetBox">
                                <dt class="requiredForm">品名</dt>
                                <dd>
                                    <p class="formPack fixedWidth fpfw100p">
                                        <input type="text" name="part_name" placeholder=""
                                             data-field-name="品名"
                                             maxLength="30"
                                            data-error-messsage-container="#part_name_cust_error"
                                            required>
                                    </p>
                                    <div id="part_name_cust_error"></div>
                                </dd>
                            </dl>
                        </td>
                        <!-- 型式 -->
                        <td>
                            <dl class="formsetBox">
                                <dt class="requiredForm">型式</dt>
                                <dd>
                                    <p class="formPack fixedWidth fpfw100p">
                                        <input type="text" name="model_type" placeholder=""
                                            data-field-name="型式"
                                            data-error-messsage-container="#model_type_cust_error"
                                            maxLength="30"
                                            required>
                                    </p>
                                    <div id="model_type_cust_error"></div>
                                </dd>
                            </dl>
                        </td>
                        <!-- 基準数/月 -->
                        <td>
                            <dl class="formsetBox">
                                <dt class="requiredForm">基準数/月</dt>
                                <dd>
                                    <p class="formPack fixedWidth fpfw100p">
                                        <input type="text" name="monthly_standard_amount" placeholder="" 
                                            data-field-name="基準数/月"
                                            data-error-messsage-container="#monthly_standard_amount_error"
                                            required
                                            onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                                    </p>
                                    <div id="monthly_standard_amount_error"></div>
                                </dd>
                            </dl>
                        </td>
                        <!-- SOP -->
                        <td>
                            <dl class="formsetBox">
                                <dt class="requiredForm">SOP</dt>
                                <dd class="mt-2">
                                    @php
                                        $sop  =  request()->get('sop') ?? '';
                                    @endphp
                                    <div class="d-flex">
                                        @include('partials._date_picker', [
                                            'inputName' => 'sop', 
                                            'attributes' => 'data-error-messsage-container=#sop_error data-field-name=回答期日', 
                                            'inputClass' => 'text-left w-100c', 
                                            'value' => $sop, 
                                            'required' => true
                                        ])
                                    </div>
                                    <div id="sop_error"></div>
                                </dd>
                            </dl>
                        </td>
                        <!-- ベース品番 -->
                        <td>
                            <dl class="formsetBox">
                                <dt>ベース品番</dt>
                                <dd>
                                    <p class="formPack fixedWidth fpfw100p">
                                        <input type="text" name="base_product_code" maxLength="30">
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
                                                name="request_content"
                                                id="message-with-limiter"
                                                data-field-name="得意先依頼内容"
                                                data-error-messsage-container="#request_content_error"
                                                required
                                                maxlength="1000"
                                                class=""></textarea>
                                    </p>
                                    <div id="request_content_error"></div>
                                </dd>
                            </dl>
                        </td>
                        <!-- 添付ファイル -->
                        <td width="100%">
                            <input type="hidden" name="attachment_file" id="attachment_file" data-file-name="">
                            <dl class="formsetBox">
                                <dt>添付ファイル（アップロード可能なファイル形式：{{ collect(config('filesystems.attachment.accepted_extension'))->implode(", ") }}　ファイルサイズは{{ config("filesystems.attachment.uploading_max_size") }}MBまで）
                                </dt>
                                <dd>
                                    <div class="dropzone"></div>
                                    <div class="error_msg mt-1"></div>
                                </dd>
                            </dl>
                        </td>
                        </tbody>
                    </table>
                </div>
                <div class="mt-2">
                    <button type="submit" class="btn btn-success  float-right mt-3 btn-wide submit-button">この内容で登録する</button>
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
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    @vite(['resources/js/estimate/request/create/index.js'])
    <script>
        window.initializeDropzone = function(target = null, maxFile = null) {
            Dropzone.autoDiscover = false;

            const targetElem = target || 'div.dropzone';
            const submitBtn = document.querySelector(".submit-button");

            const dropzoneElement = document.querySelector(targetElem);
            if (!dropzoneElement) {
                console.warn(`Dropzone element not found: ${targetElem}`);
                return;
            }

            const errorContainer = dropzoneElement.nextElementSibling;

            const myDropzone = new Dropzone(dropzoneElement, {
                url: "/estimate/request/store_file",
                addRemoveLinks: true,
                createImageThumbnails: false,
                dictDefaultMessage: "クリックまたはドロップしてファイルをアップロードしてください。",
                dictRemoveFile: "<span style='color:white; background-color:red; padding:3px 5px; border-radius: 50%; cursor:pointer'>X</span>",
                maxFilesize: 10, // MB
                maxFiles: maxFile ?? null,
                paramName: "file",
                dictFileTooBig: "10MB 以内のファイルをアップロードしてください",
                dictInvalidFileType: "無効なファイル形式です。許可されている形式: xlsx,xls,docx,doc,pptx,ppt,pdf,jpg,gif,png",
                acceptedFiles: "application/vnd.ms-excel,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet,application/vnd.openxmlformats-officedocument.wordprocessingml.document,application/msword,application/vnd.openxmlformats-officedocument.presentationml.presentation,application/vnd.ms-powerpoint,application/pdf,image/gif,image/jpeg,image/png",
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                },
                init: function () {
                    const dz = this;

                    if (maxFile) {
                        dz.on("addedfile", function (file) {
                            if (this.files.length > maxFile) {
                                this.removeFile(this.files[0]);
                            }
                        });
                    }

                    setTimeout(() => {
                        document.querySelectorAll(".dz-button").forEach(btn => {
                            btn.textContent = "クリックまたはドロップしてファイルをアップロードしてください。";
                        });
                    }, 100);
                },
                success: function (file, response) {
                    // Attach uploaded file name to hidden input
                    const input = document.querySelector("#attachment_file");
                    if (input) {
                        input.value = response.name;
                        input.setAttribute("data-file-name", file.name);
                    }

                    if (errorContainer) errorContainer.textContent = ""; // clear previous errors
                    if (submitBtn) {
                        toggleSubmitButton(submitBtn, false);
                    }
                },
                error: function (file, message) {
                    if (typeof message === 'object' && message.message) {
                        message = message.message;
                    }

                    if (errorContainer) errorContainer.textContent = message;
                    this.removeFile(file);

                    if (submitBtn) {
                        toggleSubmitButton(submitBtn, true);
                    }
                },
                removedfile: function (file) {
                    const input = document.querySelector("#attachment_file");
                    if (input && input.getAttribute("data-file-name") === file.name) {
                        input.value = "";
                        input.removeAttribute("data-file-name");
                    }

                    if (file.previewElement) file.previewElement.remove();
                    if (submitBtn && this.files.length === 0) {
                        toggleSubmitButton(submitBtn, false);
                    }
                }
            });
        };

        function toggleSubmitButton(button, isDisabled) {
            button.disabled = isDisabled;
            button.classList.toggle("btn-disabled", isDisabled);
        }
        initializeDropzone('.dropzone', 1);
    </script>
@endpush
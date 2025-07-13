@extends('layouts.app')
@push('styles')
    @vite('resources/css/common.css') 
    @vite('resources/css/vendor/dropzone/dropzone.min.css')
@endpush

@section('title', '見積回答作成')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>見積回答作成</span></h1>
            </div>

            <form action="{{ route('estimate.estimateResponse.update', $estimateReply->id) }}" accept-charset="utf-8" 
                method="POST" 
                class="with-js-validation"
                id="responseQuotationForm"
                data-confirmation-message="見積回答を更新します、よろしいでしょうか？">
                @csrf
                @method('PUT')
                <input type="hidden" name="estimate_id" value="{{ $estimate->id }}">
                <input type="hidden" name="creator" value="{{ request()->user()->employee_code }}">
                <input type="hidden" name="updated_user" value="{{ request()->user()->id }}">
                <input type="hidden" name="updated_at" value="{{ now()->format('Y-m-d H:i:s')}}">

                <!-- Hidden input to store file names -->
                <div id="hiddenInputs">
                    @foreach($estimateReply?->estimate?->attachments as $attachment)
                        @if($attachment?->attachment_file)
                            
                        @endif
                    @endforeach
                </div>

                <div class="tableWrap borderLesstable inputFormArea">
                    <table class="tableBasic">
                        <tbody>
                            <!-- 得意先 -->
                            <td>
                                <dl class="formsetBox">
                                    <dt>得意先</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw25p">
                                            <input type="text" readonly name=""
                                                value="{{ $estimate->customer_code }}">
                                        </p>
                                        <p class="formPack fixedWidth fpfw75p">
                                            <input type="text" readonly value="{{ $estimate?->customer?->customer_name }}" class="middle-name">
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                            <!-- 得意先担当者名 -->
                            <td>
                                <dl class="formsetBox">
                                    <dt>得意先担当者名</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw100p">
                                            <input type="text" readonly id="" name=""
                                                value="{{ $estimate->customer_contact_person }}">
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                            <!-- 見積依頼日 -->
                            <td>
                                <dl class="formsetBox">
                                    <dt>見積依頼日</dt>
                                    <dd>
                                        <p class="formPack calendar-plugin">
                                            <input type="text" readonly id="" name=""
                                                value="{{ optional($estimate->estimate_request_date)->format('Y/m/d') }}">
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                            <!-- 回答期日 -->
                            <td>
                                <dl class="formsetBox">
                                    <dt>回答期日</dt>
                                    <dd>
                                        <p class="formPack calendar-plugin">
                                            <input type="text" readonly id="" name=""
                                                value="{{ $estimate->reply_due_date->format('Y/m/d') }}">
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                            <!-- 品番 -->
                            <td width="10%">
                                <dl class="formsetBox">
                                    <dt>品番</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw100p">
                                            <input type="text" readonly name=""
                                                value="{{ $estimate->product_code }}" placeholder="123456-1">
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                            <!-- 品名 -->
                            <td>
                                <dl class="formsetBox">
                                    <dt>品名</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw100p">
                                            <input type="text" readonly name=""
                                                value="{{ $estimate->part_name }}" placeholder="">
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                            <!-- 型式 -->
                            <td width="10%">
                                <dl class="formsetBox">
                                    <dt>型式</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw100p">
                                            <input type="text" readonly name=""
                                                value="{{ $estimate->model_type }}" placeholder="ABC-1">
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                            <!-- 基準数/月 -->
                            <td width="10%">
                                <dl class="formsetBox">
                                    <dt>基準数/月</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw100p">
                                            <input type="text" readonly name=""
                                                value="{{ $estimate->monthly_standard_amount }}" placeholder="10000">
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                            <!-- SOP -->
                            <td>
                                <dl class="formsetBox">
                                    <dt>SOP</dt>
                                    <dd>
                                        <p class="formPack calendar-plugin">
                                            <input type="text" readonly id="" name=""
                                                value="{{ $estimate->sop->format('Y/m/d') }}">
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                            <!-- 型式 -->
                            <td width="10%">
                                <dl class="formsetBox">
                                    <dt>ベース品番</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw100p">
                                            <input type="text" readonly name=""
                                                value="{{ $estimate->model_type }}" placeholder="">
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                            <!-- 得意先依頼内容 -->
                            <td width="100% ">
                                <dl class="formsetBox">
                                    <dt>得意先依頼内容</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw100p">
                                            <textarea readonly rows="10" cols="1500" type="text" class=""
                                                placeholder="">{{ $estimate?->request_content }}</textarea>
                                        </p>
                                        <div class="error_msg"></div>
                                    </dd>
                                </dl>
                            </td>
                            <!-- 見積依頼時の添付ファイル -->
                            <td width="100% ">
                                <dl class="formsetBox mb-2">
                                    <dt>見積依頼時の添付ファイル</dt>
                                    <dd>
                                        <div class="formPack fixedWidth fpfw100p fileLoad mt-1 ml-3">
                                            <a href="{{ route('estimate.estimateResponseDownload', $estimate->attachment_file) }}">{{ $estimate->attachment_file }}</a>
                                        </div>
                                    </dd>
                                </dl>
                            </td>
                            <!-- 見積辞退 -->
                            <td width="100%">
                                <dl class="formsetBox">
                                    <dt>見積辞退</dt>
                                    <dd>
                                        <p class="formPack">
                                            <label class="checkBasic">
                                                <input type="checkbox" name="decline_flag" value="1"
                                                    class=""
                                                    @if($estimateReply->decline_flag) checked @endif>
                                                <span>見積を辞退する</span>
                                            </label>
                                        </p>
                                    </dd>
                                </dl>
                            </td>
                            <!-- 見積回答日 -->
                            <td >
                                <dl class="formsetBox" >
                                    <dt class="requiredForm">見積回答日</dt>
                                    <dd>
                                        @php
                                            $reply_date = optional($estimateReply->estimate_reply_date)->format('Ymd') ?? '';
                                        @endphp
                                        <div style="display:flex"> 
                                            @include('partials._date_picker', [
                                            'inputName' => 'estimate_reply_date', 
                                            'attributes' => 'data-error-messsage-container=#date_error_message data-field-name=見積回答日', 
                                            'value' => request()->get('estimate_reply_date'),
                                            'inputClass' => 'w-100c',
                                            'required' => true,
                                            'value' => $reply_date
                                            ])
                                        </div>
                                        <div id="date_error_message"></div>
                                    </dd>
                                </dl>
                            </td>
                            <td width="100%">
                                <dl class="formsetBox">
                                    <dt class="requiredForm">社内担当者名</dt>
                                    <dd>
                                        <p class="formPack fixedWidth" style="width: 150px;">
                                            <select name="employee_code" required class="classic"
                                                data-field-name=社内担当者名
                                                data-error-messsage-container=#internal_error_message>
                                                <!-- <option value=""></option> -->
                                                @foreach($employees as $employee)
                                                    <option 
                                                        @if($estimateReply->employee_code == $employee->employee_code ) selected @endif
                                                        value="{{ $employee->employee_code }}">
                                                        {{ $employee->employee_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </p>
                                        <div id="internal_error_message"></div>
                                    </dd>
                                </dl>
                            </td>

                            <!-- 担当者回答内容 -->
                            <td width="100%">
                                <dl class="formsetBox">
                                    <dt class="requiredForm">担当者回答内容</dt>
                                    <dd>
                                        <p class="formPack fixedWidth fpfw100p" style="position:relative">
                                            <span style="position:absolute; bottom:10px; right:10px"><span id="limit-indicator">0</span>/1000</span>
                                            <textarea rows="10" cols="1500"
                                                name="reply_content"
                                                id="message-with-limiter"
                                                value="{{ old("reply_content") }}"
                                                required
                                                class=""
                                                maxLength="1000"
                                                data-field-name=担当者回答内容
                                                data-error-messsage-container=#reply_error_message>{{ $estimateReply->reply_content }}</textarea>
                                        </p>
                                        <div id="reply_error_message"></div>
                                    </dd>
                                </dl>
                            </td>

                        </tbody>
                    </table>
                    <div class="per-month-inputs-container">
                        @foreach($estimateReply?->estimate?->attachments as $index => $attachment)
                            @if($attachment?->attachment_file)
                            <div class="tleAddElement">
                                <!-- <button class="remove-btn" type="button">X</button> -->
                                <input type="hidden" name="estimate_reply_detail_ids[]" value="{{ $attachment->id }}">
                                <input type="hidden" name="existing_files[]" value="{{  $attachment?->attachment_file }}">
                                <table class="tableBasic">
                                    <tbody>
                                        
                                        <td width="100%">
                                            <dl class="formsetBox">
                                                <dt class="requiredForm">基準数/月</dt>
                                                <dd>
                                                    <p class="formPack fixedWidth fpfw25p">
                                                  <input type="text"
                                                        name="monthly_standard_amount[{{$index}}]"
                                                    class="acceptNumericOnly monthlyAmountInput"
                                                        data-field-name=基準数/月
                                                        data-error-messsage-container="#monthly_error_message_{{$index}}"
                                                        value="{{ $attachment->monthly_standard_amount }}"
                                                        required></p>
                                                    <div id="monthly_error_message_{{ $index }}" class="monthlyAmountErrorContainer error_msg"></div>
                                                </dd>
                                            </dl>
                                        </td>

                                        <td width="99%">
                                            <dl class="formsetBox">
                                                <dt>添付ファイル（アップロード可能なファイル形式：{{ collect(config('filesystems.attachment.accepted_extension'))->implode(', ') }}　ファイルサイズは{{ config('filesystems.attachment.uploading_max_size') }}MBまで）
                                                </dt>
                                                <dd>
                                                    <div class="dropzone dropzoneCustom d-flex justify-content-start align-content-center"></div>
                                                    <div class="error_msg mt-1" style="font-weight: normal"></div>

                                                </dd>
                                            </dl>
                                        </td>
                                    </tbody>
                                </table>
                            </div>
                        @endif
                        @endforeach
                    </div>


                    <div class="d-flex justify-content-end">
                        <button type="button" class="btn btn-primary buttonBasic" id="createElement" data-update>見積を追加する</button>
                    </div>
                </div>
                <div>
                    <button type="submit" class="btn btn-success float-right mt-3 submit-button w-200c">更新</button>
                </div>
            </form>

        </div>
    </div>
 
@endsection
@push('scripts')


    @vite('resources/js/vendor/dropzone/dropzone.min.js')

    <script>
        // Map existing attachments to JS array with name, size (0), and path.
        var existingFiles = @json($estimateReply?->estimate?->attachments->map(fn($file) => [
            'name' => $file->attachment_file,
            'size' => 0,
            'path' => asset('storage/public/estimate/' . $file->attachment_file)
        ]));
    </script>
    @vite('resources/js/estimate/response/create/dropzone.js')
    @vite('resources/js/estimate/response/create/index.js')


 
@endpush
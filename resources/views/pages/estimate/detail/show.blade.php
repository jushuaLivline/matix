@extends('layouts.app')

@push('styles')
@vite('resources/css/index.css')
@vite('resources/css/common.css')
@endpush

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="accordion">
                <h1><span>見積詳細</span></h1>
            </div>
            @if(session('success'))
                <div class="tableWrap borderLesstable message">
                    <div class="success">
                        {{ session('success') }}
                    </div>
                </div>
            @endif
            @if(session('error'))
                <div class="tableWrap borderLesstable message">
                    <div class="error">
                        {{ session('error') }}
                    </div>
                </div>
             @endif
            <div class="pagettlWrap">
                <h1><span>見積依頼履歴</span></h1>
            </div>

            <div class="tableWrap">
                <table class="tableBasic list-table table-bordered table-striped align-middle">
                <!-- <table class="table table-bordered table-striped align-middle text-center">   -->
                    <tbody>
                    <tr>
                        <th  class="tA-cn">得意先</th>
                        <th  class="tA-cn">品番</th>
                        <th  class="tA-cn">品名</th>
                        <th  class="tA-cn">型式</th>
                        <th  class="tA-cn">基準数/月</th>
                        <th  class="tA-cn">SOP</th>
                        <th  class="tA-cn">社内担当者</th>
                        <th  class="tA-cn">得意先担当者名</th>
                        <th  class="tA-cn">見積依頼日</th>
                        <th  class="tA-cn">回答期日</th>
                        <th  class="tA-cn">最終回答種別</th>
                        <th  class="tA-cn">最終回答日</th>
                        <th  class="tA-cn">操作</th>
                    </tr>
                    @forelse($estimate?->replies as $reply)
                        <tr @if($estimate?->lastReply?->id == $reply->id) class="active" @endif data-estimate-reply-id="{{ $reply->id }}" >
                            <td class="tA-le">{{ $estimate?->customer?->customer_name  ?? ''}}</td>
                            <td class="tA-le">{{ $reply?->estimate?->product_code  ?? ''}}</td>
                            <td class="tA-le">{{ $reply?->estimate?->part_name  ?? ''}}</td>
                            <td class="tA-le">{{ $reply?->estimate?->model_type  ?? ''}}</td>
                            <td class="tA-le">{{ $reply?->estimate?->monthly_standard_amount ?? '' }}</td>
                            <td class="tA-le">{{ optional($reply?->estimate?->sop)->format("Y/m/d") ?? ''}}</td>
                            <td class="tA-le">{{ $reply?->employee?->employee_name }}</td>
                            <td class="tA-le">{{ $reply?->estimate?->customer_contact_person ?? '' }}</td>
                            <td class="tA-le">{{  optional($reply?->estimate?->estimate_request_date)->format("Y/m/d") ?? ''}}</td>
                            <td class="tA-le">{{ optional($reply?->estimate?->reply_due_date)->format("Y/m/d") ?? '' }}</td>
                            <td class="tA-le">
                                @if($reply?->decline_flag)
                                    @if($reply?->decline_flag)
                                    見積辞退
                                    @else
                                        回答済
                                    @endif
                                @else
                                    未回答
                                @endif
                            </td>
                            <td class="tA-le">
                                {{ optional($reply?->estimate_reply_date)->format("Y/m/d")  ?? ''}} 
                            </td>   
                            <td class="tA-cn">  
                                <a href="{{ route("estimate.estimateResponse.edit", [$reply?->estimate->id, 'reply_id' => $reply->id]) }}">
                                    詳細を確認する
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="13">検索結果はありません</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagettlNomalWrap">
                <h1><span>見積依頼詳細</span></h1>
            </div>
            <div class="tableWrap p-30" style="clear: both;">
                <dl>
                    <dt class="font11 mb-2" style="font-weight: bold;">得意先担当者名</dt>
                    <dd>
                        <p class="font11 ml-2">{{ $estimate?->customer_contact_person  ?? ''}}</p>
                    </dd>
                </dl>
                <dl class="margin-top">
                    <dt class="font11 mb-2" style="font-weight: bold;">得意先依頼内容</dt>
                    <dd>
                        <p class="font11 ml-2">{{ $estimate?->request_content  ?? ''}}</p>
                    </dd>
                </dl>
                <dl class="margin-top">
                    <dt class="font11 mb-2" style="font-weight: bold;">添付ファイル</dt>
                    <dd class="font11 nameFile">
                        @if ($estimate->attachment_file)
                        <ul class="ml-3">
                            <li><a href="{{ route('estimate.estimateResponseDownload', $estimate->attachment_file) }}">{{ $estimate->attachment_file }}</a></li>
                        </ul>
                        @endif
                    </dd>
                </dl>
            </div>
            @if($estimate?->lastReply)
                <div class="pagettlNomalWrap">
                    <h1><span>見積回答詳細</span></h1>
                </div>
                <div class="tableWrap p-30" style="clear: both;">
                    <dl class="">
                        <dt class="font11 mb-2" style="font-weight: bold;">見積回答日</dt>
                        <dd>
                            <p class="font11 ml-2">{{  optional($estimate?->lastReply?->created_at)->format("Y/m/d") ?? '' }}</p>
                        </dd>
                    </dl>

                    <dl class="margin-top">
                        <dt class="font11 mb-2" style="font-weight: bold;">回答種別</dt>
                        <dd>
                            <p class="font11 ml-2">
                                @if($estimate?->lastReply?->decline_flag)
                                    @if($estimate?->lastReply?->decline_flag)
                                        見積辞退
                                    @else
                                        回答済
                                    @endif
                                @else
                                    未回答
                                @endif
                            </p>
                        </dd>
                    </dl>
                    <dl class="margin-top">
                        <dt class="font11 mb-2" style="font-weight: bold;">月産台数</dt>
                        <dd>
                            <p class="font11 ml-2">{{  $estimate?->monthly_standard_amount ?? '' }}</p>
                        </dd>
                    </dl>
                    <dl class="margin-top">
                        <dt class="font11 mb-2" style="font-weight: bold;">社内担当者名</dt>
                        <dd>
                            <p class="font11 ml-2">{{  $estimate?->lastReply?->employee->employee_name?? '' }}</p>
                        </dd>
                    </dl>
                    <dl class="margin-top">
                        <dt class="font11 mb-2" style="font-weight: bold;">担当者回答</dt>
                        <dd>
                            <p class="font11 ml-2">{{  $estimate?->lastReply?->reply_content ?? '' }}</p>
                        </dd>
                    </dl>
                    <dl class="margin-top">
                        <dt class="font11 mb-2" style="font-weight: bold;">添付ファイル</dt>
                        <dd>
                            <ul style="list-style: disc;" class="ml-4">
                                @foreach($estimate?->attachments as $attachment)
                                    @if($attachment->attachment_file)
                                        <li class="formPack fixedWidth fpfw100p fileLoad mt-2 ml-2">
                                            <a href="{{ route('estimate.estimateResponseDownload', $attachment->attachment_file) }}">{{ $attachment->attachment_file }}</a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </dd>
                    </dl>
                   

                    @foreach($estimate?->lastReply?->replyQuations as $replyQuation)
                        <dl class="margin-top">
                            <dt class="font11 mb-2" style="font-weight: bold;">見積: {{ $loop->iteration }}</dt>
                            <dt class="font11 mb-2" style="font-weight: bold;">月産台数 {{ $replyQuation->amount_per_month }}</dt>
                            <dd class="font11 nameFile">
                                @foreach($replyQuation->attachments as $attachment)
                                    <a href="{{ route("attachment.download", $attachment) }}">{{ $attachment->file_name }}</a>
                                @endforeach
                            </dd>
                        </dl>
                        <br>
                    @endforeach

                </div>
            @endif
        
            <div class="mt-4 flex-center">
                <a href="{{ route("estimate.estimateResponseCreate", $estimate) }}" 
                    class="btnSubmitBlue text-white">見積回答を新規登録</a>
            </div>
        </div>
    </div>
@endsection

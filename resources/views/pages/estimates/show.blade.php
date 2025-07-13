@extends('layouts.app')

@push('styles')
@vite('resources/css/index.css')
    {{-- @vite('resources/css/estimates/description_form.css') --}}
    {{-- <style>
        .btnSubmitCustom {
            display: inline-block;
        }
    </style> --}}
@endpush

@section('title', '見積詳細')

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
                <table class="tableBasic list-table">
                    <tbody>
                    <tr>
                        <th  class="tA-le">得意先名</th>
                        <th  class="tA-le">品番</th>
                        <th  class="tA-le">型式</th>
                        <th  class="tA-le">基準数/月</th>
                        <th  class="tA-le">SOP</th>
                        <th  class="tA-le">社内担当</th>
                        <th  class="tA-le">得意先担当</th>
                        <th  class="tA-le">見積依頼日</th>
                        <th  class="tA-le">回答期日</th>
                        <th  class="tA-le">最終回答種別</th>
                        <th  class="tA-le">最終回答日</th>
                        <th  class="tA-le">操作</th>
                    </tr>
                    @forelse($replies as $reply)
                        <tr @if($estimateReply->id == $reply->id) class="active" @endif>
                            <td class="tA-le">{{ $reply->estimate?->customer_contact_person }}</td>
                            <td class="tA-le">{{ $reply->estimate?->product_code }}</td>
                            <td class="tA-le">{{ $reply->estimate?->model_type }}</td>
                            <td class="tA-le">{{ $reply->estimate?->monthly_standard_amount }}</td>
                            <td class="tA-le">{{ $reply->estimate?->sop->format("Y/m/d") }}</td>
                            <td class="tA-le">{{ $reply->employee?->employee_name }}</td>
                            <td class="tA-le">{{ $reply->estimate?->customer_contact_person }}</td>
                            <td class="tA-le">{{ $reply->estimate?->estimate_request_date->format("Y/m/d") }}</td>
                            <td class="tA-le">{{ $reply->estimate?->reply_due_date->format("Y/m/d") }}</td>
                            <td class="tA-le">
                                @if($reply->lastReply)
                                    @if($reply->lastReply?->decline_flag)
                                        見積辞退
                                    @else
                                        回答済
                                    @endif
                                @else
                                    未回答
                                @endif
                            </td>
                            <td class="tA-le">
                                {{ optional($reply->estimate_reply_date)->format("Y/m/d")  ?? ''}} 
                            </td>   
                            <td class="tA-cn">  
                                <a href="{{ route("estimate.show", [$reply->estimate->id, 'reply' => $reply->id]) }}">
                                    詳細を確認する
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center" colspan="12">検索結果はありません</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagettlNomalWrap">
                <h1><span>見積依頼詳細</span></h1>
            </div>
            <div class="tableWrap bordertable" style="clear: both;">
                <dl>
                    <dt class="font11" style="font-weight: bold;">得意先担当者名</dt>
                    <dd>
                        <p class="font11">{{ $estimate->customer_person }}</p>
                    </dd>
                </dl>
                <dl class="margin-top">
                    <dt class="font11" style="font-weight: bold;">得意先依頼内容</dt>
                    <dd>
                        {{ $estimate->message }}
                    </dd>
                </dl>
                <dl class="margin-top">
                    <dt class="font11" style="font-weight: bold;">添付ファイル</dt>
                    <dd class="font11 nameFile">
                        @foreach($estimate->attachments as $attachment)
                            <a href="{{ route("attachment.download", $attachment) }}">{{ $attachment->file_name }}</a>
                        @endforeach
                    </dd>
                </dl>
            </div>
            @if($estimateReply)
                <div class="pagettlNomalWrap">
                    <h1><span>見積回答詳細</span></h1>
                </div>
                <div class="tableWrap bordertable" style="clear: both;">
                    <div class="sectionBox1">
                        <p><span>見積回答日</span><span>{{  $estimateReply->reply_estimate_d?->format("m/d/Y") }}</span>
                        </p>
                        <p>
                            <span>回答種別</span>
                            @if($reply->lastReply)
                                @if($reply->lastReply?->decline_flag)
                                    <span>見積辞退</span>
                                @else
                                    <span>回答済</span>
                                @endif
                            @else
                                <span>未回答</span>
                            @endif
                        </p>
                        {{-- <p><span>月産台数</span><span>7500</span> --}}
                        </p>
                    </div>
                    <dl class="margin-top">
                        <dt class="font11" style="font-weight: bold;">社内担当者名</dt>
                        <dd>
                            <p class="font11">{{ $estimateReply->employee?->employee_name }}</p>
                        </dd>
                    </dl>
                    <dl class="margin-top">
                        <dt class="font11" style="font-weight: bold;">担当者回答</dt>
                        <dd class="font11">
                            {{ $estimateReply->reply_message }}
                        </dd>
                    </dl>
                    {{-- <dl class="margin-top">
                        <dt class="font11" style="font-weight: bold;">添付ファイル</dt>
                        <dd class="font11 nameFile">
                            @foreach($estimateReply->attachments as $attachment)
                                <a href="{{ route("attachment.download", $attachment) }}">{{ $attachment->file_name }}</a>
                            @endforeach
                        </dd>
                    </dl> --}}
                    @foreach($estimateReply->replyQuations as $replyQuation)
                        <dl class="margin-top">
                            <dt class="font11" style="font-weight: bold;">見積: {{ $loop->iteration }}</dt>
                            <dt class="font11" style="font-weight: bold;">月産台数 {{ $replyQuation->amount_per_month }}</dt>
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
        
            <div class="buttonlistWrap flex-center">
                <a href="{{ route("estimate.estimateResponseCreate", $estimate) }}" class="btnSubmitBlue text-white">見積回答を新規登録</a>
            </div>
        </div>
    </div>
@endsection

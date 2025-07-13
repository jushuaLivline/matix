@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    <style>
        .textBold {
            font-weight: bold;
        }

        .frameArea {
            width: auto;
            height: 200px;
            background-color: var(--bright-gray);
            border: 1px solid var(--argent);
            margin: 20px;
        }
    </style>
@endpush

@section('title', '売上締め処理')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                売上締め処理
            </div>

            <div class="section">
                <h1 class="form-label bar indented">売上締め処理</h1>
                <div class="box">
                    <div class="mb-2">
                        <div class="d-flex">
                            <div class="textBold">YYYY年MM月</div>
                            <div style="margin-left: 20px">の売上締め処理を行います、よろしいでしょうか？</div>
                        </div>
                    </div>
                    <div class="mb-2">
                        対象は以下のデータです。
                    </div>
                    <div class="mb-2">
                        <p class="textBold"><span style="font-size: 26px">■</span>出荷検収管理</p>
                        <p>・出荷実績入力</p>
                    </div>
                    <div class="mb-2">
                        <p class="textBold"><span style="font-size: 26px">■</span>販売管理</p>
                        <p>・売上実績入力</p>
                        <p>・返品実績入力</p>
                    </div>
                    <div class="mb-2">
                        <p class="textBold"><span style="font-size: 26px">■</span>月次処理</p>
                        <p>・AI買取明細データ取込</p>
                    </div>
                    <div class="mb-2 textBold">
                        締め処理後は、データの追加、編集、削除ができなくなります。入力漏れ、入力ミス等がないかご確認の上、実行してください。
                    </div>
                    <div class="frameArea">
                    </div>
                </div>
            </div>
            <div class="text-right">
                <div>
                    <a href="#" class="btn btn-success btn-wide"> 実行 </a>
                </div>
            </div>
        </div>
    </div>
@endsection

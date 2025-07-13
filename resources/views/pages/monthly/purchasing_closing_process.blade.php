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

@section('title', '購買締め処理')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                購買締め処理
            </div>

            <div class="section">
                <h1 class="form-label bar indented">購買締め処理</h1>
                <div class="box">
                    <div class="mb-2">
                        <div class="d-flex">
                            <div class="textBold">YYYY年MM月</div>
                            <div style="margin-left: 20px">の購買締め処理を行います、よろしいでしょうか？</div>
                        </div>
                    </div>
                    <div class="mb-2">
                        対象は以下のデータです。
                    </div>
                    <div class="mb-2">
                        <p class="textBold"><span style="font-size: 26px">■</span>支給材管理</p>
                        <p>・検収入力</p>
                        <p>・検収取消</p>
                        <p>・返品実績入力</p>
                    </div>
                    <div class="mb-2">
                        <p class="textBold"><span style="font-size: 26px">■</span>外注管理</p>
                        <p>・検収入力</p>
                        <p>・検収取消</p>
                        <p>・材料不良実績入力</p>
                        <p>・加工不良実績入力</p>
                    </div>
                    <div class="mb-2">
                        <p class="textBold"><span style="font-size: 26px">■</span>購買処理</p>
                        <p>・生産品 購入実績入力</p>
                        <p>・購買品 購入実績入力</p>
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

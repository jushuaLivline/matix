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

@section('title', '経理締め処理')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                経理締め処理
            </div>

            <div class="section">
                <h1 class="form-label bar indented">経理締め処理</h1>
                <div class="box">
                    <div class="mb-3">
                        <div class="d-flex">
                            <div class="textBold">YYYY年MM月度</div>
                            <div style="margin-left: 20px">の締め処理を行います。</div>
                        </div>
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

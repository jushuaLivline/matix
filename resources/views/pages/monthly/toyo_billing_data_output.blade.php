@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
@endpush

@section('title', '訂正東陽請求データ出力')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                訂正東陽請求データ出力
            </div>

            <div class="section">
                <h1 class="form-label bar indented">訂正東陽請求データ出力</h1>
                <div class="box">
                    <div class="mb-2 d-flex" style="flex-direction: column">
                        <div class="mt-2">
                            <label class="form-label dotted indented">年月指定</label>
                            <span class="btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" value="YYYYMM">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <a onclick="if(confirm('「発注明細書を出力します、よろしいでしょうか？」')){}" href="#" class="float-right btn btn-success btn-wide">取リ込み</a>
            </div>
        </div>
    </div>
@endsection

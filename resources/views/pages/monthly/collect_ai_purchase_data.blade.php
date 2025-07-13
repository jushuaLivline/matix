@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
@endpush

@section('title', 'AI買取明細データ取込')
@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                AI買取明細データ取込
            </div>

            <div class="section">
                <h1 class="form-label bar indented">買取明細データ取込</h1>
                <div class="box mb-3">
                    <div class="mb-2 d-flex" style="flex-direction: column">
                        <div class="mr-10">
                            <label class="form-label dotted indented">取込対象</label>
                            <span class="btn-orange badge">必須</span>
                            <div class="mt-2">
                                <div class="form-label indented w-10">アイシン精機</div>
                                <input type="radio" name="issue-classification-option" value="1" checked class="issue-option-radio"/>買取支給明細
                            </div>
                            <div class="mt-2">
                                <div class="form-label indented w-10">AI-A</div>
                                <input type="radio" name="issue-classification-option" value="1" class="issue-option-radio"/>買取支給明細
                            </div>
                            <div class="mt-2">
                                <div class="form-label indented w-10">アイシンAW</div>
                                <input type="radio" name="issue-classification-option" value="1" class="issue-option-radio"/>買取明細
                                <input type="radio" name="issue-classification-option" value="1" class="issue-option-radio ml-3"/> 支給明細
                            </div>
                            <div class="mt-2">
                                <div class="form-label indented w-10">アイシン高岡</div>
                                <input type="radio" name="issue-classification-option" value="1" class="issue-option-radio"/>買取明細
                                <input type="radio" name="issue-classification-option" value="1" class="issue-option-radio ml-3"/>支給明細
                            </div>
                            <div class="mt-2">
                                <div class="form-label indented w-10">デンソー</div>
                                <input type="radio" name="issue-classification-option" value="1" class="issue-option-radio"/>買取支給明細
                            </div>
                            <div class="mt-2">
                                <div class="form-label indented w-10">KHI</div>
                                <input type="radio" name="issue-classification-option" value="1" class="issue-option-radio"/>買取明細
                            </div>
                            <div class="mt-2">
                                <div class="form-label indented w-10">稲垣工業</div>
                                <input type="radio" name="issue-classification-option" value="1" class="issue-option-radio"/>買取明細
                            </div>
                            <div class="mt-2">
                                <div class="form-label indented w-10">アサヒ精機</div>
                                <input type="radio" name="issue-classification-option" value="1" class="issue-option-radio"/>買取明細
                                <input type="radio" name="issue-classification-option" value="1" class="issue-option-radio ml-3"/>支給明細
                            </div>
                        </div>
                        <div class="mt-2">
                            <label class="form-label dotted indented">取込ファイル</label>
                            <span class="btn-orange badge">必須</span>
                            <div class="d-flex">
                                <input type="text" style="width:30%">
                                <button class="btn btn-secondary ml-2">参照...</button>
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

@extends('layouts.app')

@push('styles')
    @vite('resources/css/index.css')
    <style>
        input[type="file"] {
            display: none;
        }

        .frameFile {
            width: 35%;
            height: 37px;
            border: 2px solid var(--argent);
            margin-right: 10px;
            border-radius: 6px;
            line-height: 37px;
            padding-left: 20px;
            font-size: 18px;
        }

        .btnUpload {
            padding: 0 1.5rem;
            background-color: var(--gray-dark);
            border: none;
            border-radius: 6px;
            font-size: 20px;
        }
    </style>
@endpush

@push('scripts')
    <script>
        $(document).ready(function () {
            $("#openDick").click(function () {
                $("#readFile").trigger("click");
            });

            $('#readFile').change(function (e) {
                let fileName = e.target.files[0].name;
                $('.frameFile').text(fileName);
            });
        });
    </script>
@endpush

@section('title', '東陽請求データ取込')

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="pageHeaderBox rounded">
                東陽請求データ取込
            </div>

            <div class="section">
                <h1 class="form-label bar indented"> 東陽請求データ取込</h1>
                <div class="box mb-3">
                    <div class="mb-2">
                        <div class="mr-3">
                            <label class="form-label dotted indented">取込ファイル</label> <span
                                class="btn-orange badge">必須</span>
                            <div class="d-flex customFile">
                                <input type="file" id="readFile" value="99999" class="mr-half">
                                <div class="frameFile"></div>
                                <button class="btn btn-secondary" id="openDick">参照...</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-right">
                <div>
                    <a href="#" class="btn btn-success btn-wide"> 取り込み </a>
                </div>
            </div>
        </div>
    </div>
@endsection

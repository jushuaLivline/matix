@extends('layouts.app')

@push('styles')
    @vite('resources/css/notifies/complete.css')
@endpush

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="tableWrap borderLesstable">
                <div class="tableIntitle">IDリマインダ</div>
                <table class="tableBasic">
                    <tbody>
                    <tr>
                        <td colspan="2" width="100%">
                            <dl class="formsetBox">
                                <dt>登録のされているメールアドレスにメールが送信されました</dt>
                            </dl>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="{{ route('auth.login') }}">
                                <button class="btnCustomSubmit">
                                    戻る
                                </button>
                            </a>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

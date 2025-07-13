@extends('layouts.app')

@push('styles')
    @vite('resources/css/app.css')
@endpush

@section('content')
    <div class="auth content">
        <div class="contentInner">
            <div class="tableWrap borderLesstable">
                <div class="tableIntitle">パスワードを忘れた</div>
                <form action="{{ route('auth.authRemind.store') }}" method="post" id="forgotPassword" autocomplete="off">
                @csrf
                    <table class="tableBasic">
                        <tbody>
                        <tr>
                            <td colspan="2" width="100%">
                                <label id="" class="error" for="">{!! \Session::get('error') !!}</label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" width="100%">
                                <dl class="formsetBox">
                                    <dt><span class="labelTitle">登録されているメールアドレスを入力してください</span></dt>
                                    <dd>
                                        <p class="formPack">
                                            <input type="email" name="email" value="" class="">
                                        </p>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" width="100%">
                                <dl class="formsetBox">
                                    <dt><span class="labelTitle">ユーザーIDを入力してください</span></dt>
                                    <dd>
                                        <p class="formPack">
                                            <input type="text" name="name" value="" class="">
                                        </p>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                        <tr class="reminder">
                            <td>
                                <button type="submit" class="btnCustomSubmit">メール送信</button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @vite('resources/js/auth/remind/password/index.js')
@endpush

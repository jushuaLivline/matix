@extends('layouts.app')

@push('styles')
    @vite('resources/css/auth/password_setting.css')
@endpush

@section('content')
    <div class="content">
        <div class="contentInner">
            <div class="tableWrap borderLesstable">
                <div class="tableIntitle">パスワード設定</div>
                <form action="{{ route('auth.resetPassword.update', request()->get('id')) }}" method="post" id="passwordSetting" autocomplete="off">
                    @csrf
                    @method('PUT')
                    <table class="tableBasic">
                        <tbody>
                        <tr>
                            <td colspan="2" width="100%">
                                <dl class="formsetBox">
                                    <dt><span class="labelTitle">ユーザーID</span></dt>
                                    <dd>
                                        <p class="formPack">
                                            {{ $user->employee_code }}
                                        </p>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" width="100%">
                                <dl class="formsetBox">
                                    <dt><span class="labelTitle">設定したいパスワード(半角英数字4-10文字以内)</span></dt>
                                    <dd>
                                        <p class="formPack">
                                            <input type="password" name="password" id="password" value="" class="" >
                                        </p>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" width="100%">
                                <dl class="formsetBox">
                                    <dt><span class="labelTitle">パスワード再入力</span></dt>
                                    <dd>
                                        <p class="formPack">
                                            <input type="password" name="reEnterPassword" value="" class="">
                                        </p>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <button type="submit" class="btnCustomSubmit">パスワード設定</button>
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
    @vite('resources/js/auth/reset/password/index.js')
@endpush

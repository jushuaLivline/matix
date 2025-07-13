@extends('layouts.app')

@push('styles')
    @vite('resources/css/app.css')
@endpush

@section('content')
    <div class="auth content">
        <div class="contentInner">
            <div class="tableWrap borderLesstable">
                <div class="tableIntitle">ユーザーIDを忘れた</div>
                <form action="{{ route('auth.authRemindProcessId') }}" method="post" id="forgotUserId" autocomplete="off">
                @csrf
                    <table class="tableBasic">
                        <tbody>
                        <tr>
                            <td colspan="2" width="100%">
                                <label id="name-error" class="error" for="name">{!! \Session::get('error') !!}</label>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" width="100%">
                                <dl class="formsetBox">
                                    <dt><span class="labelTitle">登録されているメールアドレスを入力してください</span></dt>
                                    <dd>
                                        <p class="formPack">
                                            <input type="email" name="email" value="{{ old('email') }}" class="" style="width: 100%">
                                        </p>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" width="100%">
                                <dl class="formsetBox">
                                    <dt><span class="labelTitle">パスワードを入力してください</span></dt>
                                    <dd>
                                        <div class="password-container w-full">
                                            <input type="password" name="password" id="password"
                                                value = "{{ isset($data) ? $data->password : Request::get('password') }}"
                                                class="password-input w-full"
                                            >
                                            <span class="toggle-password" id="toggle-password">
                                                <i class="fas fa-eye-slash"></i>
                                            </span>
                                        </div>
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
    @vite('resources/js/auth/remind/id/index.js')
@endpush

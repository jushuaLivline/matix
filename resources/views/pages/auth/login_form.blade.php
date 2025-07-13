@extends('layouts.app')

@push('styles')
    @vite('resources/css/app.css')
@endpush

@section('content')
    <div class="auth content">
        <div class="contentInner">
        <div class="tableWrap borderLesstable">
                <div class="tableIntitle">ログイン</div>
                <form action="{{ route('auth.login.process') }}" method="post" id="formLogin" autocomplete="off" class="overlayedSubmitForm">
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
                                    <dt><span class="labelTitle">ユーザーID</span></dt>
                                    <dd>
                                        <p class="formPack">
                                            <input type="text" name="name" value="{{ old('name') }}">
                                        </p>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" width="100%">
                                <dl class="formsetBox">
                                    <dt><span class="labelTitle">パスワード</span></dt>
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
                        <tr>
                            <td colspan="2" width="100%">
                                <dl class="formsetBox">
                                    <dd>
                                        <p class="formPack">
                                            <label class="customLabelCheck">
                                                <input type="checkbox" name="rememberId" value="1"
                                                       class="chk-middle-name" {{ (request()->cookie('saved_user_id') ?? '') != '' ? 'checked' : '' }}>
                                                <span></span> ユーザーIDを記憶する
                                            </label>
                                        </p>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                        <tr class="loginActions">
                            <td width="50%">
                                <div class="item-column-down">
                                    <a href="{{ route('auth.authRemindGetId') }}">ユーザーIDを忘れた</a>
                                    <a href="{{ route('auth.authRemind.index') }}">パスワードを忘れた</a>
                                </div>
                            </td>
                            <td width="50%" class="items-under">
                                <button type="submit" class="btnCustomSubmit">ログイン</button>
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
    @vite('resources/js/auth/login_form.js')
@endpush

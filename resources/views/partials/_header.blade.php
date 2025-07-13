<div class="header">
    <div class="headerInner">
        <p class="siteTtl">
            <a href="{{ route('dashboard.index') }}">
                <img src="{{ asset('images/matix_logo.png') }}" alt="">
            </a>
        </p>
        <p class="desText">
            <i>MATIX ONE SYSTEM</i>
        </p>
        @auth
        <ul class="loginBox">
            <li class="loginName">{{ request()->user()->department->name ?? '' }}：{{ request()->user()->employee_name }}</li>
            <li class="loginBtn"><a class="with-overlay" href="{{ route('auth.logout') }}" >ログアウト</a></li>
        </ul>
        @endauth
    </div>
</div>

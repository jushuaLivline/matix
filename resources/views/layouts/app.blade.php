<!DOCTYPE html>
<html lang="ja">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    {{-- <meta name="viewport" content="width=device-width, initial-scale=1.0">--}}
    <meta http-equiv="content-style-type" content="text/css" />
    <meta http-equiv="content-script-type" content="text/javascript" />
    <meta name="robots" content="noindex,nofollow">
    <meta name="viewport" content="width=1240,user-scalable=no">
    <meta name="description" content="" />
    <meta name="keywords" content="" />
	<link rel="icon" type="image/png" href="/images/favicon.png">

    @yield("head")

    {{--CSRF Token--}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- <title>@yield('title', config('app.name', 'livline')) | {{ config('app.name', 'livline') }}</title> --}}
	<title>@yield('title', 'Matix')</title>

    <link rel="stylesheet" href="{{ asset('css/lib/plugin-pickadateJs/default.css') }}">
    <link rel="stylesheet" href="{{ asset('css/lib/plugin-pickadateJs/default.date.css') }}">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

	
    @vite(['resources/sass/custom.scss'])
    {{-- @vite(['resources/css/app.css']) --}}
    @stack('style-libraries')
    @stack('styles')
    <style>
		.menu-drawer .btn {
			border-radius: 0em;
		}
		.menu-drawer .btn:before, .menu-drawer .btn:after {
			background-size: 24px 24px;
		}
		.menu-drawer .menu-content {
			border-right: var(--medium-electric-blue) solid 4px;
			border-top: #bfd0e1 solid 1px;
			border-bottom: #bfd0e1 solid 1px;
		}
		.naviBox .navimenuttl {
			border-top: #ffffff solid 1px;
			padding: 0.25em 0.25em 0.25em 1.25em;
			background-color: var(--header-color);
			color: #ffffff;
			font-weight: normal;
		}
		.naviBox .navimenuttl:hover {
			cursor: pointer;
		}
		.naviBox ul>.navimenuTop {
			padding: 0.25em 0 0 1em;
		}
		.naviBox ul>li {
			padding: 0.25em 0 0 2em;
		}
		.naviBox ul>li>a:hover {
			background-color: var(--medium-electric-blue);
		}
		.naviBox ul:last-child {
			border-bottom: var(--header-color) solid 1px;
		}
		.menu-drawer .menu-content {
			width: 250px;
		}
			
		/**accordion menu**/
		.naviBox .navimenuttl{
			position: relative;
			transition: .3s;
		}
		.naviBox .navimenuttl::before {
			content: "";
			position: absolute;
			top: 50%;
			right: 14px;
			transform: translateY(-50%);
			width: 12px;
			height: 2px;
			background: #ffffff;
		}
		.naviBox .navimenuttl::after {
			content: "";
			position: absolute;
			top: 50%;
			right: 19px;
			transform: translateY(-50%);
			transition: all .3s;
			width: 2px;
			height: 12px;
			background: #ffffff;
		}
		.naviBox .navimenuttl.open {
			background-color: var(--medium-electric-blue);
		}
		.naviBox .navimenuttl.open::after {
			top: 25%;
			transform: rotate(90deg);
			opacity: 0;
		}
	</style>
    <!-- Google Tag Manager -->
    <script>
        var dateweek = '';
        var baseId = '21';
        var target = 'YYYYMMDD';

        (function (w, d, s, l, i) {
            w[l] = w[l] || [];
            w[l].push({
                'gtm.start': new Date().getTime(),
                event: 'gtm.js'
            });
            var f = d.getElementsByTagName(s)[0],
                j = d.createElement(s),
                dl = l != 'dataLayer' ? '&l=' + l : '';
            j.async = true;
            j.src =
                'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
            f.parentNode.insertBefore(j, f);
        })(window, document, 'script', 'dataLayer', 'GTM-MMNKG2Q');
    </script>
</head>

<body>
	<div class="submit-overlay">
		<img src="/images/loading.svg" width="8%" alt="">
	</div>
<!-- Google Tag Manager (noscript) -->
<noscript>
    <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MMNKG2Q" height="0" width="0"
            style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->
<div class="wrapper" id="app">
    @include("partials._header")
    @auth
    @include("partials._menu_drawer")
    @endauth
    <div class="container">
        <div class="containerInner clearfix">
            @yield("content")
        </div>
    </div>
    @include("partials._footer")
</div>
<script type="text/javascript" src="{{ asset('js/lib/plugin-pickadateJs/jquery.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/lib/plugin-pickadateJs/picker.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/lib/plugin-pickadateJs/picker.date.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/lib/plugin-pickadateJs/legacy.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/lib/plugin-pickadateJs/ja_JP.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/lib/plugin-pickadateJs/jquery.floatThead.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/lib/plugin-pickadateJs/attendance_userdayresults.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/lib/jquery-validation/index.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/lib/lodash.min.js') }}"></script>

{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script> --}}

<script>
	$(function () {
		$('.navimenuttl + ul').css("display", "none");
		$('.navimenuttl').on('click', function () {
			$(this).toggleClass('open', 800);
			$(".navimenuttl").not(this).removeClass("open");
			$(this).next().slideToggle();
			$('.navimenuttl').not($(this)).next('.navimenuttl + ul').slideUp();
		})
	});

	// get the validation messages from the config
	window.validationMessages = @json(config('messages.validations')) || {};
</script>


@vite(['resources/js/common/index.js'])
@stack('scripts')
</body>

</html>

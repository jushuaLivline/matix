@php
$title = null;
if(isset($metadata)) {
	$title = $metadata->getMetaSlug('meta-title');
}
if (empty($title)) {
	$title = strtoupper(config('app.name', 'Home page'));
}
@endphp
<title>{{ $title }}</title>
@if(isset($metadata))
<meta name="google-site-verification" content="{{ $metadata->getMetaSlug('google-analytics') }}" />
<meta name="keywords" content="{{ $metadata->getMetaSlug('meta-keyword') }}"/>
<meta name="description" content="{{ $metadata->getMetaSlug('meta-description') }}"/>
<meta name="author" content="{{ $metadata->getMetaSlug('title') }}"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
<meta name="csrf-token" content="{{ csrf_token() }}">

{{-- <link rel="canonical" href="{{ URL::current() }}" /> --}}
<meta itemprop="name" content="{{ $metadata->getMetaSlug('title') }}">
<meta itemprop="description" content="{{ $metadata->getMetaSlug('description') }}">
<meta property="og:locale" content="{{ $metadata->getMetaSlug('locale') }}" />
<meta property="og:type" content="article" />
<meta property="og:title" content="{{ $metadata->getMetaSlug('title') }}" />
<meta property="og:image" content="{{ $metadata->getMetaSlug('image') }}" />
<meta property="og:description" content="{{ $metadata->getMetaSlug('description') }}" />
{{-- <meta property="og:site_name" content="{{ $_SERVER['REQUEST_URI'] }}" /> --}}
{{-- <meta property="og:url" content="{{ URL::current() }}" /> --}}
<meta name="twitter:card" content="{{ $metadata->getMetaSlug('title') }}">
<meta name="twitter:site" content="{{ $metadata->getMetaSlug('twitter') }} ">
<meta name="twitter:title" content="{{ $metadata->getMetaSlug('title') }}">
<meta name="twitter:description" content="{{ $metadata->getMetaSlug('description') }}">
<meta name="twitter:creator" content="{{ $metadata->getMetaSlug('twitter')}} ">
@endif

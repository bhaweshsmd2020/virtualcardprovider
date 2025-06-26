<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no,  maximum-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<meta http-equiv="X-UA-Compatible" content="IE=edge">

<meta name="app-name" content="{{ config('app.name') }}" />
<title inertia>{{ SeoMeta::get('title') ?? SeoMeta::get('site_name') }} - {{ config('app.name') }}</title>
<meta name="description" itemprop="description" inertia content="{{ SeoMeta::get('description') }}" />
<meta name="keywords" inertia content="{{ SeoMeta::get('keywords') }}" />
<meta property="og:description" inertia content="{{ SeoMeta::get('description') }}" />
<meta property="og:title" inertia content="{{ SeoMeta::get('site_name') }}" />
<meta property="og:url" inertia content="{{ request()->fullUrl() }}" />

<meta property="og:site_name" inertia content="{{ SeoMeta::get('site_name') }}" />
<meta property="og:image" inertia content="{{ SeoMeta::get('preview') }}" />
<meta property="og:image:url" inertia content="{{ SeoMeta::get('preview') }}" />

<meta name="twitter:card" inertia content="{{ SeoMeta::get('description') }}" />
<meta name="twitter:title" inertia content="{{ SeoMeta::get('site_name') }}" />
<meta name="app-translations" content="{{ getTranslationFile() }}" />

<meta name="google-site-verification" content="XnV3kDwD7lz2wjaLdde-IVRxmXwo904AkjtqUSFQi5E" />
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<!-- For Resposive Device -->
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<!-- For Window Tab Color -->
<!-- Chrome, Firefox OS and Opera -->
<meta name="theme-color" content="#1A4137">
<!-- Windows Phone -->
<meta name="msapplication-navbutton-color" content="#1A4137">
<!-- iOS Safari -->
<meta name="apple-mobile-web-app-status-bar-style" content="#1A4137">
<!-- Place favicon.ico in the root directory -->
<link rel="icon" href="{{ asset(get_option('primary_data', true)['favicon'] ?? '/favicon.ico') }}" type="image/png">

<!-- Template CSS -->
<link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/custom-animation.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style.css?v=7') }}">
<link rel="stylesheet" href="{{ asset('assets/css/responsive.css?v=7') }}">


<!-- Extra CSS  -->
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom.css?v=2') }}" media="all">
<link rel="stylesheet" type="text/css" href="{{ asset('assets/css/custom-toastr.css') }}" media="all">




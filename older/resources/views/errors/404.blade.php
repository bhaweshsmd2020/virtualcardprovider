
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no,  maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <meta name="app-name" content="{{ config('app.name') }}" />
    <title>404 Error - {{ config('app.name') }}</title>
    @include('layouts.default.head')

</head>

<body>
<div class="main-page-wrapper">    
        <div class="error-page text-center d-flex align-items-center justify-content-center flex-column light-bg position-relative ">
            <h1 class="font-magnita">{{ __('404') }}</h1>
            <h2 class="fw-bold">{{ __('Page Not Found') }}</h2>
            <p class="text-lg mb-45">{{ __('The page You are looking for does not exist.') }}</p>
            <div><a href="/" class="btn-four">{{ __('Go Back') }}</a></div>
            <img src="{{ asset('assets/images/assets/ils_05.svg') }}" alt="" class="lazy-img shapes shape_01">
            <img src="{{ asset('assets/images/assets/ils_06.svg') }}" alt="" class="lazy-img shapes shape_02">
        </div>      
    </div> <!-- /.main-page-wrapper -->
    @include('layouts.default.scripts')
</body>

</html>

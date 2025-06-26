<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no,  maximum-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <meta name="app-name" content="{{ config('app.name') }}" />
    <title>@yield('title')</title>
    @include('layouts.default.head')

</head>

<body>
<div class="main-page-wrapper">    
        <div class="error-page text-center d-flex align-items-center justify-content-center flex-column light-bg position-relative">
            <h2 class="font-magnita">@yield('code')</h2>
            <h3 class="fw-bold mb-100">@yield('message')</h3>
        </div>      
    </div> <!-- /.main-page-wrapper -->
    @include('layouts.default.scripts')
</body>

</html>

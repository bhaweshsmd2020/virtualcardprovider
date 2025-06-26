<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    @include('layouts.default.head')
    @vite(['resources/js/app.js'])
    @inertiaHead
    @cookieconsentscripts
</head>

<body>
    @routes()
    @inertia
    @if (env('COOKIE_CONSENT',false) && !getCookieConsent())
    @cookieconsentview
    @endif

    @include('layouts.default.scripts')
</body>

</html>
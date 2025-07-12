<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- ✅ Load reCAPTCHA Enterprise before app.js -->
    <script src="https://www.google.com/recaptcha/enterprise.js?render=6LflO4ArAAAAALLrKRKI4YeH1DN45CmDfdPbL6qx"></script>

    <!-- ✅ Vite assets -->
    @vite('resources/css/app.css')
    @vite('resources/js/app.js')

    <!-- ✅ Inertia head support -->
    @inertiaHead
</head>

<body>
    @routes
    @inertia
</body>

</html>

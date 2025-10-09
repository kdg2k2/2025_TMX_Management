<!DOCTYPE html>
<html lang="vi" dir="ltr" data-nav-layout="vertical" data-theme-mode="light" data-header-styles="transparent"
    data-width="fullwidth" data-menu-styles="transparent" data-page-style="flat" data-vertical-style="default">

<head>
    <base href="{{ asset('') }}">
    <meta content="{{ csrf_token() }}" name="csrf-token">
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="icon" type="image/*" href="\assets\images\brand-logos\favicon.ico">
    <title>{{ config('app.name') }}</title>

    <link id="style" href="assets/libs/bootstrap/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/styles.css" rel="stylesheet" />
    <link href="assets/css/icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="assets/libs/nostfly-main/nostfly.css">
    @yield('css')
</head>

<body class="{{ $bodyClass ?? '' }}">
    @include('admin.partials.loader')

    @yield('body')

    <script src="assets/libs/choices.js/public/assets/scripts/choices.min.js"></script>
    <script src="assets/js/main.js"></script>
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/loading-animation/loading.js"></script>
    <script src="assets/libs/nostfly-main/nostfly.js"></script>
    <script src="assets/js/nostfly/nostfly.js"></script>
    <script src="assets/js/auth/csrf_token.js"></script>
    <script src="assets/js/http-request/fetch.js"></script>
    <script>
        @if (session('success'))
            var success = @json(session('success'));
            alertSuccess(success);
        @endif

        @if (session('err'))
            var fail = @json(session('err'));
            alertDanger(fail);
        @endif

        @if ($errors && $errors->any())
            @foreach ($errors->all() as $error)
                alertDanger(@json($error));
            @endforeach
        @endif
    </script>
    @yield('js')
</body>

</html>

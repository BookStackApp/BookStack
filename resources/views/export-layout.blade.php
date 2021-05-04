<!doctype html>
<html lang="{{ config('app.lang') }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@yield('title')</title>

    @include('partials.export-styles', ['format' => $format])
    @include('partials.export-custom-head')
</head>
<body>
<div class="page-content">
    @yield('content')
</div>
</body>
</html>
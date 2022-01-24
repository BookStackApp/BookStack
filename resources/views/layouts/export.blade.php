<!doctype html>
<html lang="{{ config('app.lang') }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@yield('title')</title>

    @include('common.export-styles', ['format' => $format, 'engine' => $engine ?? ''])
    @include('common.export-custom-head')
</head>
<body>
<div class="page-content">
    @yield('content')
</div>
</body>
</html>
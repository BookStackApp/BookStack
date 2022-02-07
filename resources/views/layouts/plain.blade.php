<!DOCTYPE html>
<html lang="{{ config('app.lang') }}"
      dir="{{ config('app.rtl') ? 'rtl' : 'ltr' }}">
<head>
    <title>{{ isset($pageTitle) ? $pageTitle . ' | ' : '' }}{{ setting('app-name') }}</title>

    <!-- Meta -->
    <meta name="viewport" content="width=device-width">
    <meta charset="utf-8">

    <!-- Styles and Fonts -->
    <link rel="stylesheet" href="{{ versioned_asset('dist/styles.css') }}">
    <link rel="stylesheet" media="print" href="{{ versioned_asset('dist/print-styles.css') }}">

    <!-- Custom Styles & Head Content -->
    @include('common.custom-styles')
    @include('common.custom-head')
</head>
<body>
    @yield('content')
</body>
</html>

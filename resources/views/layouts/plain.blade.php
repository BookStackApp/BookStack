<!DOCTYPE html>
<html lang="{{ isset($locale) ? $locale->htmlLang() : config('app.default_locale') }}"
      dir="{{ isset($locale) ? $locale->htmlDirection() : 'auto' }}"
      class="@yield('document-class')">
<head>
    <title>{{ isset($pageTitle) ? $pageTitle . ' | ' : '' }}{{ setting('app-name') }}</title>

    <!-- Meta -->
    <meta name="viewport" content="width=device-width">
    <meta charset="utf-8">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ versioned_asset('dist/styles.css') }}">

    <!-- Custom Styles & Head Content -->
    @include('layouts.parts.custom-styles')
    @include('layouts.parts.custom-head')
</head>
<body>
    @yield('content')
</body>
</html>

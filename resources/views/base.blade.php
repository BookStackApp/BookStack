<!DOCTYPE html>
<html class="@yield('body-class')">
<head>
    <title>{{ isset($pageTitle) ? $pageTitle . ' | ' : '' }}{{ setting('app-name') }}</title>

    <!-- Meta -->
    <meta name="viewport" content="width=device-width">
    <meta name="token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ baseUrl('/') }}">
    <meta charset="utf-8">

    <!-- Styles and Fonts -->
    <link rel="stylesheet" href="{{ versioned_asset('dist/styles.css') }}">
    <link rel="stylesheet" media="print" href="{{ versioned_asset('dist/print-styles.css') }}">

    @yield('head')

    <!-- Custom Styles & Head Content -->
    @include('partials.custom-styles')
    @include('partials.custom-head')

    @stack('head')

    <!-- Translations for JS -->
    @stack('translations')

</head>
<body class="@yield('body-class')">

    @include('partials.notifications')
    @include('common.header')

    <section id="content" class="block">
        @yield('content')
    </section>

    <div back-to-top class="primary-background">
        <div class="inner">
            @icon('chevron-up') <span>{{ trans('common.back_to_top') }}</span>
        </div>
    </div>

    @yield('bottom')
    <script src="{{ versioned_asset('dist/app.js') }}"></script>
    @yield('scripts')

</body>
</html>

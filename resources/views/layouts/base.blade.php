<!DOCTYPE html>
<html lang="{{ config('app.lang') }}"
      dir="{{ config('app.rtl') ? 'rtl' : 'ltr' }}"
      class="{{ setting()->getForCurrentUser('dark-mode-enabled') ? 'dark-mode ' : '' }}">
<head>
    <title>{{ isset($pageTitle) ? $pageTitle . ' | ' : '' }}{{ setting('app-name') }}</title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <meta name="theme-color" content="{{ setting('app-color') }}"/>

    <!-- Social Cards Meta -->
    <meta property="og:title" content="{{ isset($pageTitle) ? $pageTitle . ' | ' : '' }}{{ setting('app-name') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @stack('social-meta')

    <!-- Styles and Fonts -->
    <link rel="stylesheet" href="{{ versioned_asset('dist/styles.css') }}">
    <link rel="stylesheet" media="print" href="{{ versioned_asset('dist/print-styles.css') }}">

    <!-- Icons -->
    <link rel="icon" type="image/png" sizes="256x256" href="{{ setting('app-icon') ?: url('/icon.png') }}">
    <link rel="icon" type="image/png" sizes="180x180" href="{{ setting('app-icon-180') ?: url('/icon-180.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ setting('app-icon-180') ?: url('/icon-180.png') }}">
    <link rel="icon" type="image/png" sizes="128x128" href="{{ setting('app-icon-128') ?: url('/icon-128.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ setting('app-icon-64') ?: url('/icon-64.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ setting('app-icon-32') ?: url('/icon-32.png') }}">

    @yield('head')

    <!-- Custom Styles & Head Content -->
    @include('common.custom-styles')
    @include('common.custom-head')

    @stack('head')

    <!-- Translations for JS -->
    @stack('translations')
</head>
<body
    @if(setting()->getForCurrentUser('ui-shortcuts-enabled', false))
        component="shortcuts"
        option:shortcuts:key-map="{{ \BookStack\Settings\UserShortcutMap::fromUserPreferences()->toJson() }}"
    @endif
      class="@stack('body-class')">

    @include('layouts.parts.base-body-start')
    @include('common.skip-to-content')
    @include('common.notifications')
    @include('common.header')

    <div id="content" components="@yield('content-components')" class="block">
        @yield('content')
    </div>

    @include('common.footer')

    <div component="back-to-top" class="back-to-top print-hidden">
        <div class="inner">
            @icon('chevron-up') <span>{{ trans('common.back_to_top') }}</span>
        </div>
    </div>

    @yield('bottom')
    <script src="{{ versioned_asset('dist/app.js') }}" nonce="{{ $cspNonce }}"></script>
    @yield('scripts')

    @include('layouts.parts.base-body-end')
</body>
</html>

<!DOCTYPE html>
<html lang="{{ isset($locale) ? $locale->htmlLang() : config('app.default_locale') }}"
      dir="{{ isset($locale) ? $locale->htmlDirection() : 'auto' }}"
      class="{{ setting()->getForCurrentUser('dark-mode-enabled') ? 'dark-mode ' : '' }}">
<head>
    <title>{{ isset($pageTitle) ? $pageTitle . ' | ' : '' }}{{ setting('app-name') }}</title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <meta name="token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ url('/') }}">
    <meta name="theme-color" content="{{(setting()->getForCurrentUser('dark-mode-enabled') ? setting('app-color-dark') : setting('app-color'))}}"/>

    <!-- Social Cards Meta -->
    <meta property="og:title" content="{{ isset($pageTitle) ? $pageTitle . ' | ' : '' }}{{ setting('app-name') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @stack('social-meta')

    <!-- Styles -->
    <link rel="stylesheet" href="{{ versioned_asset('dist/styles.css') }}">

    <!-- Icons -->
    <link rel="icon" type="image/png" sizes="256x256" href="{{ setting('app-icon') ?: url('/icon.png') }}">
    <link rel="icon" type="image/png" sizes="180x180" href="{{ setting('app-icon-180') ?: url('/icon-180.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ setting('app-icon-180') ?: url('/icon-180.png') }}">
    <link rel="icon" type="image/png" sizes="128x128" href="{{ setting('app-icon-128') ?: url('/icon-128.png') }}">
    <link rel="icon" type="image/png" sizes="64x64" href="{{ setting('app-icon-64') ?: url('/icon-64.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ setting('app-icon-32') ?: url('/icon-32.png') }}">

    <!-- PWA -->
    <link rel="manifest" href="{{ url('/manifest.json') }}">
    <meta name="mobile-web-app-capable" content="yes">

    <style>
    .toggle-view-btn {
      display: inline-flex;
      margin-right: 20px;
    }

    .toggle-view-btn .min-ico {
      display: none;
    }

    .toggle-view-btn.on .min-ico  {
      display: block;
    }

    .toggle-view-btn.on .max-ico  {
      display: none;
    }

    .tri-layout-container.max .tri-layout-sides{
      display: none!important;
    }

    .tri-layout-container.max .tri-layout-middle {
      grid-column-start: a;
      grid-column-end: c;
    }

    .tri-layout-container.max .tri-layout-middle #main-content {
      max-width: 100%!important;
    }

    .tri-layout-container.max .tri-layout-middle .page-content {
      max-width: 100%;
    }
    </style>

    <!-- OpenSearch -->
    <link rel="search" type="application/opensearchdescription+xml" title="{{ setting('app-name') }}" href="{{ url('/opensearch.xml') }}">

    <!-- Custom Styles & Head Content -->
    @include('layouts.parts.custom-styles')
    @include('layouts.parts.custom-head')

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
    @include('layouts.parts.skip-to-content')
    @include('layouts.parts.notifications')
    @include('layouts.parts.header')

    <div id="content" components="@yield('content-components')" class="block">
        @yield('content')
    </div>
    <script src="{{ versioned_asset('dist/extras.js') }}" type="module" nonce="{{ $cspNonce }}"></script>
    @include('layouts.parts.footer')

    <div component="back-to-top" class="back-to-top print-hidden">
        <div class="inner">
            @icon('chevron-up') <span>{{ trans('common.back_to_top') }}</span>
        </div>
    </div>

    @if($cspNonce ?? false)
        <script src="{{ versioned_asset('dist/app.js') }}" type="module" nonce="{{ $cspNonce }}"></script>
    @endif
    
    <!-- Anti-Copy Protection Script -->
    <script nonce="{{ $cspNonce ?? '' }}">
    (function() {
        'use strict';
        
        // Disable right-click context menu
        document.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
        
        // Disable keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Disable Ctrl+C, Ctrl+A, Ctrl+V, Ctrl+X, Ctrl+S, Ctrl+P
            if (e.ctrlKey && [67, 65, 86, 88, 83, 80].includes(e.keyCode)) {
                e.preventDefault();
                return false;
            }
            
            // Disable F12 (Developer Tools)
            if (e.keyCode === 123) {
                e.preventDefault();
                return false;
            }
            
            // Disable Ctrl+Shift+I (Developer Tools)
            if (e.ctrlKey && e.shiftKey && e.keyCode === 73) {
                e.preventDefault();
                return false;
            }
            
            // Disable Ctrl+Shift+J (Console)
            if (e.ctrlKey && e.shiftKey && e.keyCode === 74) {
                e.preventDefault();
                return false;
            }
            
            // Disable Ctrl+U (View Source)
            if (e.ctrlKey && e.keyCode === 85) {
                e.preventDefault();
                return false;
            }
        });
        
        // Disable text selection events
        document.addEventListener('selectstart', function(e) {
            e.preventDefault();
            return false;
        });
        
        // Disable drag start
        document.addEventListener('dragstart', function(e) {
            e.preventDefault();
            return false;
        });
        
        // Disable print screen
        document.addEventListener('keyup', function(e) {
            if (e.keyCode === 44) { // Print Screen
                e.preventDefault();
                return false;
            }
        });
        
        // Clear clipboard periodically (if supported)
        if (navigator.clipboard && navigator.clipboard.writeText) {
            setInterval(function() {
                navigator.clipboard.writeText('').catch(function() {
                    // Ignore errors if clipboard access is denied
                });
            }, 2000);
        }
        
        // Basic developer tools detection
        let devtools = {open: false};
        setInterval(function() {
            if (window.outerHeight - window.innerHeight > 200 || 
                window.outerWidth - window.innerWidth > 200) {
                if (!devtools.open) {
                    devtools.open = true;
                    // You can customize this message or action
                    console.warn('Developer tools detected!');
                }
            } else {
                devtools.open = false;
            }
        }, 1000);
        
        // Disable image right-click and drag
        document.addEventListener('DOMContentLoaded', function() {
            const images = document.querySelectorAll('img');
            images.forEach(function(img) {
                img.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    return false;
                });
                img.addEventListener('dragstart', function(e) {
                    e.preventDefault();
                    return false;
                });
            });
        });
        
    })();
    </script>
    
    @stack('body-end')

    @include('layouts.parts.base-body-end')
</body>
</html>

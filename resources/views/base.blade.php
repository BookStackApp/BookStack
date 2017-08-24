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
    <link rel="stylesheet" href="{{ versioned_asset('css/styles.css') }}">
    <link rel="stylesheet" media="print" href="{{ versioned_asset('css/print-styles.css') }}">
    <link rel="stylesheet" href="{{ baseUrl('/libs/material-design-iconic-font/css/material-design-iconic-font.min.css') }}">

    <!-- Scripts -->
    <script src="{{ baseUrl('/libs/jquery/jquery.min.js?version=2.1.4') }}"></script>
    <script src="{{ baseUrl('/libs/jquery/jquery-ui.min.js?version=1.11.4') }}"></script>
    <script src="{{ baseUrl('/translations') }}"></script>

    @yield('head')

    @include('partials/custom-styles')

    @if(setting('app-custom-head') && \Route::currentRouteName() !== 'settings')
        <!-- Custom user content -->
        {!! setting('app-custom-head') !!}
        <!-- End custom user content -->
    @endif
</head>
<body class="@yield('body-class')" ng-app="bookStack">

    @include('partials/notifications')

    <header id="header">
        <div class="container">
            <div class="row">
                <div class="col-sm-4" ng-non-bindable>
                    <a href="{{ baseUrl('/') }}" class="logo">
                        @if(setting('app-logo', '') !== 'none')
                            <img class="logo-image" src="{{ setting('app-logo', '') === '' ? baseUrl('/logo.png') : baseUrl(setting('app-logo', '')) }}" alt="Logo">
                        @endif
                        @if (setting('app-name-header'))
                            <span class="logo-text">{{ setting('app-name') }}</span>
                        @endif
                    </a>
                </div>
                <div class="col-sm-8">
                    <div class="float right">
                        <div class="header-search">
                            <form action="{{ baseUrl('/search') }}" method="GET" class="search-box">
                                <button id="header-search-box-button" type="submit"><i class="zmdi zmdi-search"></i> </button>
                                <input id="header-search-box-input" type="text" name="term" tabindex="2" placeholder="{{ trans('common.search') }}" value="{{ isset($searchTerm) ? $searchTerm : '' }}">
                            </form>
                        </div>
                        <div class="links text-center">
                            <a href="{{ baseUrl('/books') }}"><i class="zmdi zmdi-book"></i>{{ trans('entities.books') }}</a>
                            @if(signedInUser() && userCan('settings-manage'))
                                <a href="{{ baseUrl('/settings') }}"><i class="zmdi zmdi-settings"></i>{{ trans('settings.settings') }}</a>
                            @endif
                            @if(!signedInUser())
                                <a href="{{ baseUrl('/login') }}"><i class="zmdi zmdi-sign-in"></i>{{ trans('auth.log_in') }}</a>
                            @endif
                        </div>
                        @if(signedInUser())
                            @include('partials._header-dropdown', ['currentUser' => user()])
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="content" class="block">
        @yield('content')
    </section>

    <div back-to-top>
        <div class="inner">
            <i class="zmdi zmdi-chevron-up"></i> <span>{{ trans('common.back_to_top') }}</span>
        </div>
    </div>
@yield('bottom')
<script src="{{ versioned_asset('js/common.js') }}"></script>
@yield('scripts')
</body>
</html>

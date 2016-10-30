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

    @yield('head')

    @include('partials/custom-styles')

    <!-- Custom user content -->
    @if(setting('app-custom-head'))
        {!! setting('app-custom-head') !!}
    @endif
</head>
<body class="@yield('body-class')" ng-app="bookStack">

    @include('partials/notifications')

    <header id="header">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-sm-4" ng-non-bindable>
                    <a href="{{ baseUrl('/') }}" class="logo">
                        @if(setting('app-logo', '') !== 'none')
                            <img class="logo-image" src="{{ setting('app-logo', '') === '' ? baseUrl('/logo.png') : baseUrl(setting('app-logo', '')) }}" alt="Logo">
                        @endif
                        <span class="logo-text">{{ setting('app-name') }}</span>
                    </a>
                </div>
                <div class="col-lg-4 col-sm-3 text-center">
                    <form action="{{ baseUrl('/search/all') }}" method="GET" class="search-box">
                        <input id="header-search-box-input" type="text" name="term" tabindex="2" value="{{ isset($searchTerm) ? $searchTerm : '' }}">
                        <button id="header-search-box-button" type="submit" class="text-button"><i class="zmdi zmdi-search"></i></button>
                    </form>
                </div>
                <div class="col-lg-4 col-sm-5">
                    <div class="float right">
                        <div class="links text-center">
                            <a href="{{ baseUrl('/books') }}"><i class="zmdi zmdi-book"></i>Books</a>
                            @if(isset($currentUser) && userCan('settings-manage'))
                                <a href="{{ baseUrl('/settings') }}"><i class="zmdi zmdi-settings"></i>Settings</a>
                            @endif
                            @if(!isset($signedIn) || !$signedIn)
                                <a href="{{ baseUrl('/login') }}"><i class="zmdi zmdi-sign-in"></i>Sign In</a>
                            @endif
                        </div>
                        @if(isset($signedIn) && $signedIn)
                            <div class="dropdown-container" dropdown>
                                <span class="user-name" dropdown-toggle>
                                    <img class="avatar" src="{{$currentUser->getAvatar(30)}}" alt="{{ $currentUser->name }}">
                                    <span class="name" ng-non-bindable>{{ $currentUser->getShortName(9) }}</span> <i class="zmdi zmdi-caret-down"></i>
                                </span>
                                <ul>
                                    <li>
                                        <a href="{{ baseUrl("/user/{$currentUser->id}") }}" class="text-primary"><i class="zmdi zmdi-account zmdi-hc-fw zmdi-hc-lg"></i>View Profile</a>
                                    </li>
                                    <li>
                                        <a href="{{ baseUrl("/settings/users/{$currentUser->id}") }}" class="text-primary"><i class="zmdi zmdi-edit zmdi-hc-fw zmdi-hc-lg"></i>Edit Profile</a>
                                    </li>
                                    <li>
                                        <a href="{{ baseUrl('/logout') }}" class="text-neg"><i class="zmdi zmdi-run zmdi-hc-fw zmdi-hc-lg"></i>Logout</a>
                                    </li>
                                </ul>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="content" class="block">
        @yield('content')
    </section>

    <div id="back-to-top">
        <div class="inner">
            <i class="zmdi zmdi-chevron-up"></i> <span>Back to top</span>
        </div>
    </div>
@yield('bottom')
<script src="{{ versioned_asset('js/common.js') }}"></script>
@yield('scripts')
</body>
</html>

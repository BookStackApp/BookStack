<!DOCTYPE html>
<html>
<head>
    <title>{{ isset($pageTitle) ? $pageTitle . ' | ' : '' }}{{ Setting::get('app-name', 'BookStack') }}</title>

    <!-- Meta -->
    <meta name="viewport" content="width=device-width">
    <meta name="token" content="{{ csrf_token() }}">
    <meta charset="utf-8">

    <!-- Styles and Fonts -->
    <link rel="stylesheet" href="{{ versioned_asset('css/styles.css') }}">
    <link rel="stylesheet" media="print" href="{{ versioned_asset('css/print-styles.css') }}">
    <link rel="stylesheet" href="/libs/material-design-iconic-font/css/material-design-iconic-font.min.css">

    <!-- Scripts -->
    <script src="/libs/jquery/jquery.min.js?version=2.1.4"></script>

    @yield('head')
</head>
<body class="@yield('body-class')" ng-app="bookStack">

    @include('partials/notifications')

    <header id="header">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-sm-4" ng-non-bindable>
                    <a href="/" class="logo">
                        @if(Setting::get('app-logo', '') !== 'none')
                            <img class="logo-image" src="{{ Setting::get('app-logo', '') === '' ? '/logo.png' : Setting::get('app-logo', '') }}" alt="Logo">
                        @endif
                        <span class="logo-text">{{ Setting::get('app-name', 'BookStack') }}</span>
                    </a>
                </div>
                <div class="col-lg-4 col-sm-3 text-center">
                    <form action="/search/all" method="GET" class="search-box">
                        <input id="header-search-box-input" type="text" name="term" tabindex="2" value="{{ isset($searchTerm) ? $searchTerm : '' }}">
                        <button id="header-search-box-button" type="submit" class="text-button"><i class="zmdi zmdi-search"></i></button>
                    </form>
                </div>
                <div class="col-lg-4 col-sm-5">
                    <div class="float right">
                        <div class="links text-center">
                            <a href="/books"><i class="zmdi zmdi-book"></i>Books</a>
                            @if(isset($currentUser) && $currentUser->can('settings-update'))
                                <a href="/settings"><i class="zmdi zmdi-settings"></i>Settings</a>
                            @endif
                            @if(!isset($signedIn) || !$signedIn)
                                <a href="/login"><i class="zmdi zmdi-sign-in"></i>Sign In</a>
                            @endif
                        </div>
                        @if(isset($signedIn) && $signedIn)
                            <div class="dropdown-container" dropdown>
                                <span class="user-name" dropdown-toggle>
                                    <img class="avatar" src="{{$currentUser->getAvatar(30)}}" alt="{{ $currentUser->name }}">
                                    <span class="name" ng-non-bindable>{{ $currentUser->name }}</span> <i class="zmdi zmdi-caret-down"></i>
                                </span>
                                <ul>
                                    <li>
                                        <a href="/user/{{$currentUser->id}}" class="text-primary"><i class="zmdi zmdi-account zmdi-hc-fw zmdi-hc-lg"></i>View Profile</a>
                                    </li>
                                    <li>
                                        <a href="/settings/users/{{$currentUser->id}}" class="text-primary"><i class="zmdi zmdi-edit zmdi-hc-fw zmdi-hc-lg"></i>Edit Profile</a>
                                    </li>
                                    <li>
                                        <a href="/logout" class="text-neg"><i class="zmdi zmdi-run zmdi-hc-fw zmdi-hc-lg"></i>Logout</a>
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

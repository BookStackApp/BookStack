<!DOCTYPE html>
<html>
<head>
    <title>{{ setting('app-name') }}</title>

    <!-- Meta -->
    <meta name="viewport" content="width=device-width">
    <meta name="token" content="{{ csrf_token() }}">
    <meta name="base-url" content="{{ baseUrl('/') }}">
    <meta charset="utf-8">

    <!-- Styles and Fonts -->
    <link rel="stylesheet" href="{{ versioned_asset('css/styles.css') }}">
    <link rel="stylesheet" media="print" href="{{ versioned_asset('css/print-styles.css') }}">
    <link rel="stylesheet" href="{{ baseUrl("/libs/material-design-iconic-font/css/material-design-iconic-font.min.css") }}">

    <!-- Scripts -->
    <script src="{{ baseUrl("/libs/jquery/jquery.min.js?version=2.1.4") }}"></script>
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
            <div class="col-md-6">

                <a href="{{ baseUrl('/') }}" class="logo">
                    @if(setting('app-logo', '') !== 'none')
                        <img class="logo-image" src="{{ setting('app-logo', '') === '' ? baseUrl('/logo.png') : baseUrl(setting('app-logo', '')) }}" alt="Logo">
                    @endif
                    @if (setting('app-name-header'))
                        <span class="logo-text">{{ setting('app-name') }}</span>
                    @endif
                </a>
            </div>
            <div class="col-md-6">
                <div class="float right">
                    <div class="links text-center">
                        @yield('header-buttons')
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

<section class="container">
    @yield('content')
</section>

<script src="{{ versioned_asset('js/common.js') }}"></script>
</body>
</html>

<!DOCTYPE html>
<html>
<head>
    <title>BookStack</title>

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
    @include('partials/custom-styles')
</head>
<body class="@yield('body-class')" ng-app="bookStack">

@include('partials/notifications')

<header id="header">
    <div class="container">
        <div class="row">
            <div class="col-md-6">

                <a href="/" class="logo">
                    <img class="logo-image" src="/logo.png" alt="Logo">
                    <span class="logo-text">{{ Setting::get('app-name', 'BookStack') }}</span>
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
                                    <span class="name" ng-non-bindable>{{ $currentUser->name }}</span> <i class="zmdi zmdi-caret-down"></i>
                                </span>
                            <ul>
                                <li>
                                    <a href="/users/{{$currentUser->id}}" class="text-primary"><i class="zmdi zmdi-edit zmdi-hc-lg"></i>Edit Profile</a>
                                </li>
                                <li>
                                    <a href="/logout" class="text-neg"><i class="zmdi zmdi-run zmdi-hc-lg"></i>Logout</a>
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

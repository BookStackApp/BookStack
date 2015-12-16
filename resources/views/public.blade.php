<!DOCTYPE html>
<html>
<head>
    <title>BookStack</title>

    <!-- Meta -->
    <meta name="viewport" content="width=device-width">
    <meta charset="utf-8">

    <!-- Styles and Fonts -->
    <link rel="stylesheet" href="{{ versioned_asset('css/styles.css') }}">
    <link rel="stylesheet" media="print" href="{{ versioned_asset('css/print-styles.css') }}">
    <link href='//fonts.googleapis.com/css?family=Roboto:400,400italic,500,500italic,700,700italic,300italic,100,300' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/libs/material-design-iconic-font/css/material-design-iconic-font.min.css">

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>

</head>
<body class="@yield('body-class')" id="app">

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
                        <img class="avatar" src="{{$currentUser->getAvatar(30)}}" alt="{{ $currentUser->name }}">
                        <div class="dropdown-container" data-dropdown>
                                <span class="user-name" data-dropdown-toggle>
                                    {{ $currentUser->name }} <i class="zmdi zmdi-caret-down"></i>
                                </span>
                            <ul>
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

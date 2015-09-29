<!DOCTYPE html>
<html>
<head>
    <title>BookStack</title>
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/css/app.css">
    <link href='//fonts.googleapis.com/css?family=Roboto:400,400italic,500,500italic,700,700italic,300italic,100,300' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/bower/material-design-iconic-font/dist/css/material-design-iconic-font.min.css">

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="/js/common.js"></script>

</head>
<body class="@yield('body-class')">

@if(Session::has('success'))
    <div class="notification anim pos">
        <i class="zmdi zmdi-mood"></i> <span>{{ Session::get('success') }}</span>
    </div>
@endif

@if(Session::has('error'))
    <div class="notification anim neg stopped">
        <i class="zmdi zmdi-alert-circle"></i> <span>{{ Session::get('error') }}</span>
    </div>
@endif

<header id="header">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <a href="/" class="logo">{{ Setting::get('app-name', 'BookStack') }}</a>
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

</body>
</html>

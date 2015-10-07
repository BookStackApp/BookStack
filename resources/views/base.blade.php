<!DOCTYPE html>
<html>
<head>
    <title>BookStack</title>

    <!-- Meta-->
    <meta name="viewport" content="width=device-width">
    <meta name="token" content="{{ csrf_token() }}">

    <!-- Styles and Fonts -->
    <link rel="stylesheet" href="/css/app.css">
    <link href='//fonts.googleapis.com/css?family=Roboto:400,400italic,500,500italic,700,700italic,300italic,100,300' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/bower/material-design-iconic-font/dist/css/material-design-iconic-font.min.css">

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="/bower/jquery-sortable/source/js/jquery-sortable.js"></script>
    <script src="/bower/dropzone/dist/min/dropzone.min.js"></script>
    <script src="/bower/vue/dist/vue.min.js"></script>
    <script src="/bower/vue-resource/dist/vue-resource.min.js"></script>

    @yield('head')
</head>
<body class="@yield('body-class')" id="app">

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
                <div class="col-md-4 col-sm-3">
                    <a href="/" class="logo">
                        @if(Setting::get('app-logo', '') !== 'none')
                            <img class="logo-image" src="{{ Setting::get('app-logo', '') === '' ? '/logo.png' : Setting::get('app-logo', '') }}" alt="Logo">
                        @endif
                        <span class="logo-text">{{ Setting::get('app-name', 'BookStack') }}</span>
                    </a>
                </div>
                <div class="col-md-4 col-sm-3 text-center">
                    <form action="/search/all" method="GET" class="search-box">
                        <input type="text" name="term" tabindex="2" value="{{ isset($searchTerm) ? $searchTerm : '' }}">
                        <button class="text-button"><i class="zmdi zmdi-search"></i></button>
                    </form>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="float right">
                        <div class="links text-center">
                            <a href="/books"><i class="zmdi zmdi-book"></i>Books</a>
                            @if($currentUser->can('settings-update'))
                                <a href="/settings"><i class="zmdi zmdi-settings"></i>Settings</a>
                            @endif
                            @if(!$signedIn)
                                <a href="/login"><i class="zmdi zmdi-sign-in"></i>Sign In</a>
                            @endif
                        </div>
                        @if($signedIn)
                            <img class="avatar" src="{{$currentUser->getAvatar(30)}}" alt="{{ $currentUser->name }}">
                            <div class="dropdown-container" data-dropdown>
                                <span class="user-name" data-dropdown-toggle>
                                    {{ $currentUser->name }} <i class="zmdi zmdi-caret-down"></i>
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

    <section id="content" class="block">
        @yield('content')
    </section>

@yield('bottom')
<script src="/js/common.js"></script>
</body>
</html>

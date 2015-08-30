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
    <script src="/bower/bootstrap/dist/js/bootstrap.js"></script>
    <script src="/bower/jquery-sortable/source/js/jquery-sortable.js"></script>
    <script src="/bower/dropzone/dist/min/dropzone.min.js"></script>
    <script src="/bower/vue/dist/vue.min.js"></script>
    <script>
        $.fn.smoothScrollTo = function() {
            if(this.length === 0) return;
            $('body').animate({
                scrollTop: this.offset().top - 60 // Adjust to change final scroll position top margin
            }, 800); // Adjust to change animations speed (ms)
            return this;
        };
        $.expr[":"].contains = $.expr.createPseudo(function(arg) {
            return function( elem ) {
                return $(elem).text().toUpperCase().indexOf(arg.toUpperCase()) >= 0;
            };
        });
    </script>

    @yield('head')
</head>
<body>

    @if(Session::has('success'))
        <div class="notification anim pos">
            <i class="zmdi zmdi-mood"></i> <span>{{ Session::get('success') }}</span>
        </div>
    @endif

    @if(Session::has('error'))
        <div class="notification anim neg">
            <i class="zmdi zmdi-alert-circle"></i> <span>{{ Session::get('error') }}</span>
        </div>
    @endif

    <header id="header">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <a href="/" class="logo">{{ Setting::get('app-name', 'BookStack') }}</a>
                </div>
                <div class="col-md-3">
                    <div class="search-box text-center" style="display: none">
                        <form action="/pages/search/all" id="search-form" method="GET" style="display: none;">
                            <input type="text" placeholder="Search all pages..." name="term" id="search-input">
                        </form>
                    </div>
                    <img class="avatar" src="{{Auth::user()->getAvatar(30)}}" alt="{{ Auth::user()->name }}">
                <span class="user-name">
                    {{ Auth::user()->name }}
                </span>
                </div>
                <div class="col-md-5">
                    <div class="float right links">
                        <a href="/search"><i class="zmdi zmdi-search"></i></a>
                        <a href="/books"><i class="zmdi zmdi-book"></i>Books</a>
                        <a href="/users"><i class="zmdi zmdi-accounts"></i>Users</a>
                        <a href="/logout"><i class="zmdi zmdi-run zmdi-hc-flip-horizontal"></i>Logout</a>
                    </div>
                </div>
            </div>
        </div>
        {{--@if(isset($book) && isset($current) && !isset($books))--}}
            {{--<div class="book-tree">--}}
                {{--@include('pages/sidebar-tree-list', ['book' => $book])--}}
            {{--</div>--}}
        {{--@endif--}}
        @yield('sidebar')
    </header>

    <section id="content">
        @yield('content')
    </section>

@yield('bottom')
    <script>
        $('.notification').click(function() {
            $(this).fadeOut(100);
        });
    </script>
</body>
</html>

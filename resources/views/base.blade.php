<!DOCTYPE html>
<html>
<head>
    <title>BookStack</title>
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/css/app.css">
    <link href='//fonts.googleapis.com/css?family=Roboto:400,400italic,500,500italic,700,700italic,300italic,100,300' rel='stylesheet' type='text/css'>
    {{--<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">--}}
    <link rel="stylesheet" href="/bower/material-design-iconic-font/dist/css/material-design-iconic-font.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="/bower/bootstrap/dist/js/bootstrap.js"></script>
    <script src="/bower/jquery-sortable/source/js/jquery-sortable.js"></script>
    <script src="https://fb.me/react-0.13.3.js"></script>
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

    <section id="sidebar">
        <div class="sidebar-bg"><div class="overlay"></div></div>
        <header>
            <div class="padded row clearfix">
                <div class="col-md-12 logo-container">
                    {{--<div ><img class="logo float left" src="/bookstack.svg" alt="BookStack"></div>--}}
                    <div class="logo">BookStack</div>
                    <div><i class="zmdi zmdi-account"></i> {{ \Illuminate\Support\Facades\Auth::user()->name }}</div>
                </div>
            </div>
        </header>
        <div class="search-box">
            <form action="/pages/search/all" id="search-form" method="GET">
                <input type="text" placeholder="Search all pages..." name="term" id="search-input">
            </form>
        </div>
        <ul class="menu">
            <li><a href="/books"><i class="zmdi zmdi-book"></i>Books</a></li>
            <li><a href="/users"><i class="zmdi zmdi-accounts"></i>Users</a></li>
            <li><a href="/logout"><i class="zmdi zmdi-run zmdi-hc-flip-horizontal"></i>Logout</a></li>
        </ul>
        @if(isset($book) && !isset($books))
            <div class="book-tree">
                @include('pages/sidebar-tree-list', ['book' => $book])
            </div>
        @endif
        @yield('sidebar')
    </section>

    <section id="content">
        @yield('content')
    </section>

@yield('bottom')

    <script src="/js/all.js"></script>
</body>
</html>

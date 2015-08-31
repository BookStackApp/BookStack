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
<body class="@yield('body-class')">

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
                <div class="col-md-3">
                    <a href="/" class="logo">{{ Setting::get('app-name', 'BookStack') }}</a>
                </div>
                <div class="col-md-9">
                    <div class="float right">
                        <div class="links text-center">
                            <a href="/search"><i class="zmdi zmdi-search"></i></a>
                            <a href="/books"><i class="zmdi zmdi-book"></i>Books</a>
                            @if($currentUser->can('settings-update'))
                                <a href="/settings"><i class="zmdi zmdi-settings"></i>Settings</a>
                            @endif
                        </div>
                        <img class="avatar" src="{{$currentUser->getAvatar(30)}}" alt="{{ $currentUser->name }}">
                        <div class="dropdown-container" data-dropdown>
                            <span class="user-name" data-dropdown-toggle>
                                {{ $currentUser->name }} <i class="zmdi zmdi-caret-down"></i>
                            </span>
                            <ul class="dropdown">
                                <li>
                                    <a href="/users/{{$currentUser->id}}" class="text-primary"><i class="zmdi zmdi-edit zmdi-hc-lg"></i>Edit Profile</a>
                                </li>
                                <li>
                                    <a href="/logout" class="text-neg"><i class="zmdi zmdi-run zmdi-hc-lg"></i>Logout</a>
                                </li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="content">
        @yield('content')
    </section>

@yield('bottom')
    <script>
        $(function() {

            $('.notification').click(function() {
                $(this).fadeOut(100);
            });

            // Dropdown toggles
            $('[data-dropdown-toggle]').click(function() {
                var toggleButton = $(this);
                var container = toggleButton.closest('[data-dropdown]');
                var dropdown = container.find('.dropdown');
                dropdown.show().addClass('anim menuIn');

                container.mouseleave(function() {
                   dropdown.hide();
                    dropdown.removeClass('anim menuIn');
                });
            });

        });
    </script>
</body>
</html>

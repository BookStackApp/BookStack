<!DOCTYPE html>
<html>
<head>
    <title>Oxbow</title>
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/css/app.css">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,400italic,500,500italic,700,700italic,300italic,100,300' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script src="/bower/bootstrap/dist/js/bootstrap.js"></script>
    <script>
        $.fn.smoothScrollTo = function() {
            $('body').animate({
                scrollTop: this.offset().top - 60 // Adjust to change final scroll position top margin
            }, 800); // Adjust to change animations speed (ms)
            return this;
        };
    </script>
    @yield('head')
</head>
<body>

    <header>
        <div class="container">
            <div class="padded-vertical clearfix">
                <div class="logo float left">Oxbow</div>
                <ul class="menu float right">
                    <li><a href="/books"><i class="fa fa-book"></i>Books</a></li>
                </ul>
            </div>
        </div>
    </header>

    <section class="container">
        @yield('content')
    </section>

</body>
</html>

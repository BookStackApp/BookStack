<!DOCTYPE html>
<html>
<head>
    <title>Oxbow</title>
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/css/app.css">
    <link href='http://fonts.googleapis.com/css?family=Roboto:400,400italic,500,500italic,700,700italic,300italic,100,300' rel='stylesheet' type='text/css'>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
</head>
<body>

    <header class="container">
        <div class="padded-vertical clearfix">
            <div class="logo float left">Oxbow</div>
            <ul class="menu float right">
                <li><a href="/books">Books</a></li>
            </ul>
        </div>
        <hr>
    </header>

    <section class="container">
        @yield('content')
    </section>

</body>
</html>

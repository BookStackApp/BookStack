<!DOCTYPE html>
<html>
<head>
    <title>BookStack</title>
    <meta name="viewport" content="width=device-width">
    <link rel="stylesheet" href="/css/app.css">
    <link href='//fonts.googleapis.com/css?family=Roboto:400,400italic,500,500italic,700,700italic,300italic,100,300' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="/bower/material-design-iconic-font/dist/css/material-design-iconic-font.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>

</head>
<body class="@yield('body-class')">

<section id="sidebar">
    @yield('sidebar')
</section>

<section class="container">
    @yield('content')
</section>

</body>
</html>

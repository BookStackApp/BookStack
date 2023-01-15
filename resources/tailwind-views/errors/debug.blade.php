<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>Error: {{ $error }}</title>

    <style>
        html, body {
            background-color: #F2F2F2;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", "Oxygen", "Ubuntu", "Roboto", "Cantarell", "Fira Sans", "Droid Sans", "Helvetica Neue", sans-serif;
        }

        html {
            padding: 0;
        }

        body {
            margin: 0;
            border-top: 6px solid #206ea7;
        }

        h1 {
            margin-top: 0;
        }

        h2 {
            color: #666;
            font-size: 1rem;
            margin-bottom: 0;
        }

        .container {
            max-width: 800px;
            margin: 1rem auto;
        }

        .panel {
            background-color: #FFF;
            border-radius: 3px;
            box-shadow: 0 1px 6px -1px rgba(0, 0, 0, 0.1);
            padding: 1rem 2rem;
            margin: 2rem 1rem;
        }

        .panel-title {
            font-weight: bold;
            font-size: 1rem;
            color: #FFF;
            margin-top: 0;
            margin-bottom: 0;
            background-color: #206ea7;
            padding: 0.25rem .5rem;
            display: inline-block;
            border-radius: 3px;
        }

        pre {
            overflow-x: scroll;
            background-color: #EEE;
            border: 1px solid #DDD;
            padding: .25rem;
            border-radius: 3px;
        }

        a {
            color: #206ea7;
            text-decoration: none;
        }

        a:hover, a:focus {
            text-decoration: underline;
            color: #105282;
        }

        ul {
            margin-left: 0;
            padding-left: 1rem;
        }

        li {
            margin-bottom: .4rem;
        }

        .notice {
            margin-top: 2rem;
            padding: 0 2rem;
            font-weight: bold;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">

        <p class="notice">
            WARNING: Application is in debug mode. This mode has the potential to leak confidential
            information and therefore should not be used in production or publicly
            accessible environments.
        </p>

        <div class="panel">
            <h4 class="panel-title">Error</h4>
            <h2>{{ $errorClass }}</h2>
            <h1>{{ $error }}</h1>
        </div>

        <div class="panel">
            <h4 class="panel-title">Help Resources</h4>
            <ul>
                <li>
                    <a href="https://www.bookstackapp.com/docs/admin/debugging/" target="_blank">Review BookStack debugging documentation &raquo;</a>
                </li>
                <li>
                    <a href="https://github.com/BookStackApp/BookStack/releases" target="_blank">Ensure your instance is up-to-date &raquo;</a>
                </li>
                <li>
                    <a href="https://github.com/BookStackApp/BookStack/issues?q=is%3Aissue+{{ urlencode($error) }}" target="_blank">Search for the issue on GitHub &raquo;</a>
                </li>
                <li>
                    <a href="https://discord.gg/ztkBqR2" target="_blank">Ask for help via Discord &raquo;</a>
                </li>
                <li>
                    <a href="https://duckduckgo.com/?q={{urlencode("BookStack {$error}")}}" target="_blank">Search the error message &raquo;</a>
                </li>
            </ul>
        </div>

        <div class="panel">
            <h4 class="panel-title">Environment</h4>
            <ul>
                @foreach($environment as $label => $text)
                <li><strong>{{ $label }}:</strong> {{ $text }}</li>
                @endforeach
            </ul>
        </div>

        <div class="panel">
            <h4 class="panel-title">Stack Trace</h4>
            <pre>{{ $trace }}</pre>
        </div>

    </div>
</body>
</html>
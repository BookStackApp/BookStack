<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>{{ setting('app-name') }} API Docs</title>
    <style>
        body {
            max-width: 800px;
            margin: 0 auto;
            padding-bottom: 2rem;
            font-family: sans-serif;
        }
        h2 {
            margin-top: 2rem;
        }
        table.table {
            border-collapse: collapse;
        }
        table.table th, table.table td {
            border: 1px solid #ccc;
            padding: 0.2em 0.4em;
        }
    </style>
</head>
<body>

<h1>{{ setting('app-name') }} API Documentation</h1>

<h2>Contents</h2>
<ul>
@foreach($gettingStartedSections as $id => $label)
    <li><a href="#{{ $id }}">{{ $label }}</a></li>
@endforeach
<li style="list-style: none">---</li>
@foreach($docs as $model => $endpoints)
    <li><a href="#section-{{ str_replace(' ', '-', $model) }}">{{ $model }}</a></li>
@endforeach
</ul>

<section id="section-getting-started" component="code-highlighter" class="card content-wrap auto-height">
    @include('api-docs.parts.getting-started')
</section>

@foreach($docs as $model => $endpoints)
    <section id="section-{{ str_replace(' ', '-', $model) }}" class="card content-wrap auto-height">
        <h2>{{ $model }}</h2>

        @if($endpoints[0]['model_description'])
            <p>{{ $endpoints[0]['model_description'] }}</p>
        @endif

        @foreach($endpoints as $endpoint)
            <div>
                <h3 id="{{ $endpoint['name'] }}">{{ $endpoint['name'] }}</h3>
                <p>{{ $endpoint['method'] }} {{ url($endpoint['uri']) }}</p>
            </div>
            @include('api-docs.parts.endpoint-details')
        @endforeach
    </section>
@endforeach

</body>
</html>
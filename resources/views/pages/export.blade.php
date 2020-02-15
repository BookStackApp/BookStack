<!doctype html>
<html lang="{{ config('app.lang') }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $page->name }}</title>

    @include('partials.export-styles', ['format' => $format])

    @if($format === 'pdf')
        <style>
            body {
                font-size: 14px;
                line-height: 1.2;
            }

            h1, h2, h3, h4, h5, h6 {
                line-height: 1.2;
            }

            table {
                max-width: 800px !important;
                font-size: 0.8em;
                width: 100% !important;
            }

            table td {
                width: auto !important;
            }
        </style>
    @endif

    @include('partials.custom-head')
</head>
<body>

<div id="page-show">
    <div class="page-content">

        @include('pages.page-display')

        <hr>

        <div class="text-muted text-small">
            @include('partials.entity-export-meta', ['entity' => $page])
        </div>

    </div>
</div>

</body>
</html>

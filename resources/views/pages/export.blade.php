<!doctype html>
<html lang="{{ config('app.lang') }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $page->name }}</title>

    <style>
        @if (!app()->environment('testing'))
        {!! file_get_contents(public_path('/dist/export-styles.css')) !!}
        @endif
    </style>
    @yield('head')
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

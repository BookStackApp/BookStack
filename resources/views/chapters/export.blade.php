<!doctype html>
<html lang="{{ config('app.lang') }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $chapter->name }}</title>

    @include('partials.export-styles', ['format' => $format])

    <style>
        .page-break {
            page-break-after: always;
        }
        ul.contents ul li {
            list-style: circle;
        }
        @media screen {
            .page-break {
                border-top: 1px solid #DDD;
            }
        }
    </style>
    @include('partials.export-custom-head')
</head>
<body>

<div class="page-content">

    <h1 style="font-size: 4.8em">{{$chapter->name}}</h1>

    <p>{{ $chapter->description }}</p>

    @if(count($pages) > 0)
        <ul class="contents">
            @foreach($pages as $page)
                <li><a href="#page-{{$page->id}}">{{ $page->name }}</a></li>
            @endforeach
        </ul>
    @endif

    @foreach($pages as $page)
        <div class="page-break"></div>
        <h1 id="page-{{$page->id}}">{{ $page->name }}</h1>
        {!! $page->html !!}
    @endforeach

</div>

</body>
</html>

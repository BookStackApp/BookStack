<!doctype html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $book->name }}</title>

    <style>
        @if (!app()->environment('testing'))
        {!! file_get_contents(public_path('/css/export-styles.css')) !!}
        @endif
        .page-break {
            page-break-after: always;
        }
        .chapter-hint {
            color: #888;
            margin-top: 32px;
        }
        .chapter-hint + h1 {
            margin-top: 0;
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
    @yield('head')
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="page-content">

                <h1 style="font-size: 4.8em">{{$book->name}}</h1>

                <p>{{ $book->description }}</p>

                @if(count($bookChildren) > 0)
                <ul class="contents">
                    @foreach($bookChildren as $bookChild)
                        <li><a href="#{{$bookChild->getType()}}-{{$bookChild->id}}">{{ $bookChild->name }}</a></li>
                        @if($bookChild->isA('chapter') && count($bookChild->pages) > 0)
                            <ul>
                                @foreach($bookChild->pages as $page)
                                    <li><a href="#page-{{$page->id}}">{{ $page->name }}</a></li>
                                @endforeach
                            </ul>
                        @endif
                    @endforeach
                </ul>
                @endif

                @foreach($bookChildren as $bookChild)
                    <div class="page-break"></div>
                    <h1 id="{{$bookChild->getType()}}-{{$bookChild->id}}">{{ $bookChild->name }}</h1>
                    @if($bookChild->isA('chapter'))
                        <p>{{ $bookChild->description }}</p>
                        @if(count($bookChild->pages) > 0)
                            @foreach($bookChild->pages as $page)
                                <div class="page-break"></div>
                                <div class="chapter-hint">{{$bookChild->name}}</div>
                                <h1 id="page-{{$page->id}}">{{ $page->name }}</h1>
                                {!! $page->html !!}
                            @endforeach
                        @endif
                    @else
                        {!! $bookChild->html !!}
                    @endif
                @endforeach

            </div>
        </div>
    </div>
</div>
</body>
</html>

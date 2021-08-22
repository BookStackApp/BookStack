@extends('layouts.export')

@section('title', $book->name)

@section('content')
    <h1 style="font-size: 4.8em">{{$book->name}}</h1>

    <p>{{ $book->description }}</p>

    @if(count($bookChildren) > 0)
        <ul class="contents">
            @foreach($bookChildren as $bookChild)
                <li><a href="#{{$bookChild->getType()}}-{{$bookChild->id}}">{{ $bookChild->name }}</a></li>
                @if($bookChild->isA('chapter') && count($bookChild->visible_pages) > 0)
                    <ul>
                        @foreach($bookChild->visible_pages as $page)
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

            @if(count($bookChild->visible_pages) > 0)
                @foreach($bookChild->visible_pages as $page)
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
@endsection
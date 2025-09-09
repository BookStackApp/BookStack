@extends('layouts.export')

@section('title', $shelf->name)

@section('content')

    <h1 style="font-size: 4.8em">{{$shelf->name}}</h1>
    <div>{!! $shelf->descriptionHtml() !!}</div>

    @if($books->count() > 0)
        <h2>{{ trans('entities.books') }}</h2>
        
        @foreach($books as $book)
            <div class="book-item" style="margin-bottom: 2em; page-break-inside: avoid;">
                <h3 style="font-size: 2.4em; margin-bottom: 0.5em;">{{ $book->name }}</h3>
                <div style="margin-bottom: 1em;">{!! $book->descriptionHtml() !!}</div>
                
                @php
                    $bookTree = (new \BookStack\Entities\Tools\BookContents($book))->getTree(false, true);
                @endphp
                
                @foreach($bookTree as $bookChild)
                    @if($bookChild->isA('chapter'))
                        @include('exports.parts.chapter-item', ['chapter' => $bookChild])
                    @else
                        @include('exports.parts.page-item', ['page' => $bookChild, 'chapter' => null])
                    @endif
                @endforeach
            </div>
        @endforeach
    @else
        <p>{{ trans('entities.shelves_empty_contents') }}</p>
    @endif

@endsection

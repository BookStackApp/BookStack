@extends('layouts.export')

@section('title', $chapter->name)

@section('content')

    <h1 style="font-size: 4.8em">{{$chapter->name}}</h1>
    <p>{{ $chapter->description }}</p>

    @include('exports.parts.chapter-contents-menu', ['pages' => $pages])

    @foreach($pages as $page)
        @include('exports.parts.page-item', ['page' => $page, 'chapter' => null])
    @endforeach

@endsection
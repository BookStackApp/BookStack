@extends('simple-layout')

@section('body')
    <div class="container small pt-xl">
        <div class="card content-wrap">
            <h1 class="list-heading">{{ $title }}</h1>

            <div class="book-contents">
                @include('partials.entity-list', ['entities' => $pages, 'style' => 'detailed'])
            </div>

            <div class="text-center">
                {!! $pages->links() !!}
            </div>
        </div>
    </div>
@stop
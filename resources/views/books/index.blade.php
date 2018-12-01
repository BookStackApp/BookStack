@extends('sidebar-layout')

@section('toolbar')
    <div class="grid halves">
        <div class="action-buttons text-left">
            @include('books.view-toggle', ['booksViewType' => $booksViewType])
        </div>
        <div class="action-buttons">
            @if($currentUser->can('book-create-all'))
                <a href="{{ baseUrl("/create-book") }}" class="text-pos text-button">@icon('add'){{ trans('entities.books_create') }}</a>
            @endif
        </div>
    </div>
@stop

@section('sidebar')
    @if($recents)
        <div id="recents" class="mb-xl">
            <h5>{{ trans('entities.recently_viewed') }}</h5>
            @include('partials.entity-list', ['entities' => $recents, 'style' => 'compact'])
        </div>
    @endif

    <div id="popular" class="mb-xl">
        <h5>{{ trans('entities.books_popular') }}</h5>
        @if(count($popular) > 0)
            @include('partials.entity-list', ['entities' => $popular, 'style' => 'compact'])
        @else
            <div class="body text-muted">{{ trans('entities.books_popular_empty') }}</div>
        @endif
    </div>

    <div id="new" class="mb-xl">
        <h5>{{ trans('entities.books_new') }}</h5>
        @if(count($popular) > 0)
            @include('partials.entity-list', ['entities' => $new, 'style' => 'compact'])
        @else
            <div class="body text-muted">{{ trans('entities.books_new_empty') }}</div>
        @endif
    </div>
@stop

@section('body')
    @include('books.list', ['books' => $books, 'bookViewType' => $booksViewType])
@stop
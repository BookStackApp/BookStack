@extends('sidebar-layout')

@section('toolbar')
    <div class="col-xs-1"></div>
    <div class="col-xs-11 faded">
        <div class="action-buttons">
            @if($currentUser->can('book-create-all'))
                <a href="{{ baseUrl("/books/create") }}" class="text-pos text-button"><i class="zmdi zmdi-plus"></i>{{ trans('entities.books_create') }}</a>
            @endif
        </div>
    </div>
@stop

@section('sidebar')
    @if($recents)
        <div id="recents" class="card">
            <h3><i class="zmdi zmdi-eye"></i> {{ trans('entities.recently_viewed') }}</h3>
            @include('partials/entity-list', ['entities' => $recents, 'style' => 'compact'])
        </div>
    @endif

    <div id="popular" class="card">
        <h3><i class="zmdi zmdi-fire"></i> {{ trans('entities.books_popular') }}</h3>
        @if(count($popular) > 0)
            @include('partials/entity-list', ['entities' => $popular, 'style' => 'compact'])
        @else
            <div class="body text-muted">{{ trans('entities.books_popular_empty') }}</div>
        @endif
    </div>

    <div id="new" class="card">
        <h3><i class="zmdi zmdi-star-circle"></i> {{ trans('entities.books_new') }}</h3>
        @if(count($popular) > 0)
            @include('partials/entity-list', ['entities' => $new, 'style' => 'compact'])
        @else
            <div class="body text-muted">{{ trans('entities.books_new_empty') }}</div>
        @endif
    </div>
@stop

@section('body')

    <div class="container small" ng-non-bindable>
        <h1>{{ trans('entities.books') }}</h1>
        @if(count($books) > 0)
            @foreach($books as $book)
                @include('books/list-item', ['book' => $book])
                <hr>
            @endforeach
            {!! $books->render() !!}
        @else
            <p class="text-muted">{{ trans('entities.books_empty') }}</p>
            @if(userCan('books-create-all'))
                <a href="{{ baseUrl("/books/create") }}" class="text-pos"><i class="zmdi zmdi-edit"></i>{{ trans('entities.create_one_now') }}</a>
            @endif
        @endif
    </div>

@stop
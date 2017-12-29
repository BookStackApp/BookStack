@extends('sidebar-layout')

@section('toolbar')
    <div class="col-xs-1"></div>
    <div class="col-xs-11 faded">
        <div class="action-buttons">
            <form action="{{ baseUrl("/settings/users/{$currentUser->id}/switch-book-view") }}" method="POST" class="inline">
                {!! csrf_field() !!}
                <input type="hidden" value="{{ $booksViewType === 'list'? 'grid' : 'list' }}" name="book_view_type">
                <button type="submit" class="text-pos text-button"><i class="zmdi zmdi-wrap-text"></i>{{ trans('entities.books_toggle_view') }}</button>
            </form>
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
    @if($booksViewType === 'list')
        <div class="container small" ng-non-bindable>
    @else
        <div class="container" ng-non-bindable>
    @endif
        <h1>{{ trans('entities.books') }}</h1>
        @if(count($books) > 0)
            @if($booksViewType === 'list')
                @foreach($books as $book)
                    @include('books/list-item', ['book' => $book])
                    <hr>
                @endforeach
                {!! $books->render() !!}
            @else
             <div class="row auto-clear">
                    @foreach($books as $key => $book)
                            @include('books/grid-item', ['book' => $book])
                    @endforeach
                <div class="col-xs-12">
                    {!! $books->render() !!}
                </div>
             </div>
            @endif
        @else
            <p class="text-muted">{{ trans('entities.books_empty') }}</p>
            @if(userCan('books-create-all'))
                <a href="{{ baseUrl("/books/create") }}" class="text-pos"><i class="zmdi zmdi-edit"></i>{{ trans('entities.create_one_now') }}</a>
            @endif
        @endif
    </div>
@stop
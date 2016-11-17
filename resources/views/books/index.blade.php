@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11 faded">
                    <div class="action-buttons">
                        @if($currentUser->can('book-create-all'))
                            <a href="{{ baseUrl("/books/create") }}" class="text-pos text-button"><i class="zmdi zmdi-plus"></i>{{ trans('entities.books_create') }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container" ng-non-bindable>
        <div class="row">
            <div class="col-sm-7">
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
            <div class="col-sm-4 col-sm-offset-1">
                <div id="recents">
                    @if($recents)
                        <div class="margin-top">&nbsp;</div>
                        <h3>{{ trans('entities.recently_viewed') }}</h3>
                        @include('partials/entity-list', ['entities' => $recents])
                    @endif
                </div>
                <div class="margin-top large">&nbsp;</div>
                <div id="popular">
                    <h3>{{ trans('entities.books_popular') }}</h3>
                    @if(count($popular) > 0)
                        @include('partials/entity-list', ['entities' => $popular])
                    @else
                        <p class="text-muted">{{ trans('entities.books_popular_empty') }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

@stop
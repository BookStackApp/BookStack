@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11 faded">
                    <div class="action-buttons">
                        @if($currentUser->can('book-create-all'))
                            <a href="/books/create" class="text-pos text-button"><i class="zmdi zmdi-plus"></i>Add new book</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container" ng-non-bindable>
        <div class="row">
            <div class="col-sm-7">
                <h1>Books</h1>
                @if(count($books) > 0)
                    @foreach($books as $book)
                        @include('books/list-item', ['book' => $book])
                        <hr>
                    @endforeach
                    {!! $books->render() !!}
                @else
                    <p class="text-muted">No books have been created.</p>
                    <a href="/books/create" class="text-pos"><i class="zmdi zmdi-edit"></i>Create one now</a>
                @endif
            </div>
            <div class="col-sm-4 col-sm-offset-1">
                <div id="recents">
                    @if($recents)
                        <div class="margin-top large">&nbsp;</div>
                        <h3>Recently Viewed</h3>
                        @include('partials/entity-list', ['entities' => $recents])
                    @endif
                </div>
                <div class="margin-top large">&nbsp;</div>
                <div id="popular">
                    <h3>Popular Books</h3>
                    @if(count($popular) > 0)
                        @include('partials/entity-list', ['entities' => $popular])
                    @else
                        <p class="text-muted">The most popular books will appear here.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

@stop
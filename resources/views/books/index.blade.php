@extends('base')

@section('content')

    <div class="faded-small">
        <div class="container">
            <div class="row">
                <div class="col-xs-1"></div>
                <div class="col-xs-11 faded">
                    <div class="action-buttons">
                        @if($currentUser->can('book-create'))
                            <a href="/books/create" class="text-pos text-button"><i class="zmdi zmdi-plus"></i>Add new book</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container">
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
                <div class="margin-top large">&nbsp;</div>
                <h3>Recently Viewed</h3>
                @include('partials/entity-list', ['entities' => $recents])
            </div>
        </div>
    </div>

@stop
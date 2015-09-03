@extends('base')

@section('content')

    <div class="faded-small">
        <div class="container">
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6 faded">
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
            <div class="col-md-8">
                <h1>Books</h1>
                @if(count($books) > 0)
                    @foreach($books as $book)
                        @include('books/list-item', ['book' => $book])
                        <hr>
                    @endforeach
                @else
                    <p class="text-muted">No books have been created.</p>
                    <a href="/books/create" class="text-pos"><i class="zmdi zmdi-edit"></i>Create one now</a>
                @endif
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>

@stop
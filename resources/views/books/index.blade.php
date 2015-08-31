@extends('base')

@section('content')

    <div class="faded-small">
        <div class="container">
            <div class="row">
                <div class="col-md-6"></div>
                <div class="col-md-6 faded">
                    <div class="action-buttons">
                        @if($currentUser->can('book-create'))
                            <a href="/books/create" class="text-pos"><i class="zmdi zmdi-plus"></i>Add new book</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="page-content">
        <h1>Books</h1>
        @foreach($books as $book)
            <div class="book">
                <h3><a href="{{$book->getUrl()}}">{{$book->name}}</a></h3>
                <p class="text-muted">{{$book->description}}</p>
            </div>
            <hr>
        @endforeach
    </div>

@stop
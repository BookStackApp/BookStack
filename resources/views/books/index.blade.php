@extends('base')

@section('content')


    <div class="row">

        <div class="col-md-3 page-menu">
            <h4>Books</h4>
            <a href="/books/create">+ Add new book</a>
        </div>

        <div class="col-md-9">

            <div class="row">
                @foreach($books as $book)
                    <div class="col-md-6">
                        <div class="book page-style">
                            <h3><a href="{{$book->getUrl()}}">{{$book->name}}</a></h3>
                            <p class="text-muted">{{$book->description}}</p>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>




@stop
@extends('base')

@section('content')


    <div class="row">

        <div class="col-md-3 page-menu">
            <h4>You are editing the details for the book '{{$book->name}}'.</h4>
            <hr>
            @include('form/delete-button', ['url' => '/books/' . $book->id . '/destroy', 'text' => 'Delete this book'])
        </div>

        <div class="col-md-9 page-content">
            <form action="/books/{{$book->slug}}" method="POST">
                <input type="hidden" name="_method" value="PUT">
                @include('books/form', ['model' => $book])
            </form>
        </div>

    </div>




@stop
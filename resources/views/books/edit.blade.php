@extends('base')

@section('content')

    <div class="container small">
        <h1>Edit Book</h1>
        <form action="/books/{{$book->slug}}" method="POST">
            <input type="hidden" name="_method" value="PUT">
            @include('books/form', ['model' => $book])
        </form>
    </div>

@stop
@extends('base')

@section('content')
    <h2>Edit Book</h2>

    <form action="/books/{{$book->slug}}" method="POST">
        {{ csrf_field() }}
        <input type="hidden" name="_method" value="PUT">
        @include('books/form', ['model' => $book])
    </form>
@stop
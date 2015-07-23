@extends('base')

@section('content')

    <div class="page-content">
        <h1>Edit Book</h1>
        <form action="/books/{{$book->slug}}" method="POST">
            <input type="hidden" name="_method" value="PUT">
            @include('books/form', ['model' => $book])
        </form>
        <hr class="margin-top large">
        <div class="margin-top large shaded padded">
            <h2 class="margin-top">Delete this book</h2>
            <p>This will delete this book and all it's pages.</p>
            @include('form/delete-button', ['url' => '/books/' . $book->id . '/destroy', 'text' => 'Delete'])
        </div>
    </div>

@stop
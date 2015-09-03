@extends('base')

@section('content')

<div class="container small">
    <h1>Create New Book</h1>
    <form action="/books" method="POST">
        @include('books/form')
    </form>
</div>

@stop
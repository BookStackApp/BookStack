@extends('base')

@section('content')
    <h2>New Book</h2>

    <form action="/books" method="POST">
        {{ csrf_field() }}
        @include('books/form')
    </form>
@stop
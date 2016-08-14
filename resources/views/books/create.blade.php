@extends('base')

@section('content')

<div class="container small" ng-non-bindable>
    <h1>Create New Book</h1>
    <form action="{{ baseUrl("/books") }}" method="POST">
        @include('books/form')
    </form>
</div>

@stop
@extends('base')

@section('content')

<div class="container small" ng-non-bindable>
    <h1>{{ trans('entities.books_create') }}</h1>
    <form action="{{ baseUrl("/books") }}" method="POST" enctype="multipart/form-data">
        @include('books/form')
    </form>
</div>

@stop
@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-8 faded">
        <div class="breadcrumbs">
            <a href="{{ baseUrl('/books') }}" class="text-button"><i class="zmdi zmdi-book"></i>{{ trans('entities.books') }}</a>
            <span class="sep">&raquo;</span>
            <a href="{{ baseUrl('/books/create') }}" class="text-button"><i class="zmdi zmdi-plus"></i>{{ trans('entities.books_create') }}</a>
        </div>
    </div>
@stop

@section('body')

<div ng-non-bindable class="container small">
    <p>&nbsp;</p>
    <div class="card">
        <h3><i class="zmdi zmdi-plus"></i> {{ trans('entities.books_create') }}</h3>
        <div class="body">
            <form action="{{ baseUrl("/books") }}" method="POST">
                @include('books/form')
            </form>
        </div>
    </div>
</div>

@stop
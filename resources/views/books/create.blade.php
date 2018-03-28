@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-8 faded">
        <div class="breadcrumbs">
            <a href="{{ baseUrl('/books') }}" class="text-button">@icon('book'){{ trans('entities.books') }}</a>
            <span class="sep">&raquo;</span>
            <a href="{{ baseUrl('/create-book') }}" class="text-button">@icon('add'){{ trans('entities.books_create') }}</a>
        </div>
    </div>
@stop

@section('body')

<div ng-non-bindable class="container small">
    <p>&nbsp;</p>
    <div class="card">
        <h3>@icon('add') {{ trans('entities.books_create') }}</h3>
        <div class="body">
            <form action="{{ baseUrl("/books") }}" method="POST" enctype="multipart/form-data">
                @include('books/form')
            </form>
        </div>
    </div>
</div>
<p class="margin-top large"><br></p>
    @include('components.image-manager', ['imageType' => 'cover'])
@stop
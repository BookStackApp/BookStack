@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-8 faded">
        <div class="breadcrumbs">
            <a href="{{ baseUrl('/shelves') }}" class="text-button">@icon('bookshelf'){{ trans('entities.shelves') }}</a>
            <span class="sep">&raquo;</span>
            <a href="{{ baseUrl('/create-shelf') }}" class="text-button">@icon('add'){{ trans('entities.shelves_create') }}</a>
        </div>
    </div>
@stop

@section('body')

    <div class="container small">
        <p>&nbsp;</p>
        <div class="card">
            <h3>@icon('add') {{ trans('entities.shelves_create') }}</h3>
            <div class="body">
                <form action="{{ baseUrl("/shelves") }}" method="POST" enctype="multipart/form-data">
                    @include('shelves/form', ['shelf' => null, 'books' => $books])
                </form>
            </div>
        </div>
    </div>

    <p class="margin-top large"><br></p>

    @include('components.image-manager', ['imageType' => 'cover'])

@stop
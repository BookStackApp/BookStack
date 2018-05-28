@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-12 faded">
        @include('books._breadcrumbs', ['book' => $book])
    </div>
@stop

@section('body')

    <div class="container small">
        <p>&nbsp;</p>
        <div class="card">
            <h3>@icon('edit') {{ trans('entities.books_edit') }}</h3>
            <div class="body">
                <form action="{{ $book->getUrl() }}" method="POST">
                    <input type="hidden" name="_method" value="PUT">
                    @include('books/form', ['model' => $book])
                </form>
            </div>
        </div>
    </div>
@include('components.image-manager', ['imageType' => 'cover'])
@stop
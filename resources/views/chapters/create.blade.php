@extends('simple-layout')

@section('toolbar')
    <div class="col-sm-12 faded">
        <div class="breadcrumbs">
            <a href="{{ $book->getUrl() }}" class="text-book text-button">@icon('book'){{ $book->getShortName() }}</a>
            <span class="sep">&raquo;</span>
            <a href="{{ $book->getUrl('/create-chapter')}}" class="text-button">@icon('add'){{ trans('entities.chapters_create') }}</a>
        </div>
    </div>
@stop

@section('body')

    <div class="container small">
        <div class="card">
            <h3>@icon('add') {{ trans('entities.chapters_create') }}</h3>
            <div class="body">
                <form action="{{ $book->getUrl('/create-chapter') }}" method="POST">
                    @include('chapters/form')
                </form>
            </div>
        </div>
    </div>

@stop
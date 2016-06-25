@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 faded">
                    <div class="breadcrumbs">
                        <a href="{{$book->getUrl()}}" class="text-book text-button"><i class="zmdi zmdi-book"></i>{{ $book->getShortName() }}</a>
                        <span class="sep">&raquo;</span>
                        <a href="{{$chapter->getUrl()}}" class="text-page text-button"><i class="zmdi zmdi-file-text"></i>{{ $chapter->getShortName() }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <h1>Move Chapter <small class="subheader">{{$chapter->name}}</small></h1>

        <form action="{{ $chapter->getUrl() }}/move" method="POST">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="PUT">

            @include('partials/entity-selector', ['name' => 'entity_selection', 'selectorSize' => 'large', 'entityTypes' => 'book'])

            <a href="{{ $chapter->getUrl() }}" class="button muted">Cancel</a>
            <button type="submit" class="button pos">Move Chapter</button>
        </form>
    </div>

@stop

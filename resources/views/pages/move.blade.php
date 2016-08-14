@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 faded">
                    <div class="breadcrumbs">
                        <a href="{{ $book->getUrl() }}" class="text-book text-button"><i class="zmdi zmdi-book"></i>{{ $book->getShortName() }}</a>
                        @if($page->hasChapter())
                            <span class="sep">&raquo;</span>
                            <a href="{{ $page->chapter->getUrl() }}" class="text-chapter text-button">
                                <i class="zmdi zmdi-collection-bookmark"></i>
                                {{ $page->chapter->getShortName() }}
                            </a>
                        @endif
                        <span class="sep">&raquo;</span>
                        <a href="{{ $page->getUrl() }}" class="text-page text-button"><i class="zmdi zmdi-file-text"></i>{{ $page->getShortName() }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <h1>Move Page <small class="subheader">{{$page->name}}</small></h1>

        <form action="{{ $page->getUrl('/move') }}" method="POST">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="PUT">

            @include('partials/entity-selector', ['name' => 'entity_selection', 'selectorSize' => 'large', 'entityTypes' => 'book,chapter'])

            <a href="{{ $page->getUrl() }}" class="button muted">Cancel</a>
            <button type="submit" class="button pos">Move Page</button>
        </form>
    </div>

@stop

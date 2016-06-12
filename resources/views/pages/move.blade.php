@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 faded">
                    <div class="breadcrumbs">
                        <a href="{{$book->getUrl()}}" class="text-book text-button"><i class="zmdi zmdi-book"></i>{{ $book->getShortName() }}</a>
                        @if($page->hasChapter())
                            <span class="sep">&raquo;</span>
                            <a href="{{ $page->chapter->getUrl() }}" class="text-chapter text-button">
                                <i class="zmdi zmdi-collection-bookmark"></i>
                                {{$page->chapter->getShortName()}}
                            </a>
                        @endif
                        <span class="sep">&raquo;</span>
                        <a href="{{$page->getUrl()}}" class="text-page text-button"><i class="zmdi zmdi-file-text"></i>{{ $page->getShortName() }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <h1>Move Page <small class="subheader">{{$page->name}}</small></h1>

        <form action="{{ $page->getUrl() }}/move" method="POST">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="PUT">

            <div class="form-group">
                <div entity-selector class="entity-selector large" entity-types="book,chapter">
                    <input type="hidden" entity-selector-input name="entity_selection">
                    <input type="text" placeholder="Search" ng-model="search" ng-model-options="{debounce: 200}" ng-change="searchEntities()">
                    <div class="text-center loading" ng-show="loading">@include('partials/loading-icon')</div>
                    <div ng-show="!loading" ng-bind-html="entityResults"></div>
                </div>
            </div>

            <a href="{{ $page->getUrl() }}" class="button muted">Cancel</a>
            <button type="submit" class="button pos">Move Page</button>
        </form>
    </div>

@stop

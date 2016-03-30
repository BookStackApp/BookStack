@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 faded">
                    <div class="breadcrumbs">
                        <a href="{{$page->book->getUrl()}}" class="text-book text-button"><i class="zmdi zmdi-book"></i>{{ $page->book->getShortName() }}</a>
                        @if($page->hasChapter())
                            <span class="sep">&raquo;</span>
                            <a href="{{ $page->chapter->getUrl() }}" class="text-chapter text-button">
                                <i class="zmdi zmdi-collection-bookmark"></i>
                                {{$page->chapter->getShortName()}}
                            </a>
                        @endif
                        <span class="sep">&raquo;</span>
                        <a href="{{$page->getUrl()}}" class="text-book text-button"><i class="zmdi zmdi-file"></i>{{ $page->getShortName() }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container" ng-non-bindable>
        <h1>Page Permissions</h1>
        @include('form/restriction-form', ['model' => $page])
    </div>

@stop

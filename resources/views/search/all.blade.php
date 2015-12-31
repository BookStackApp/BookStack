@extends('base')

@section('content')

    <div class="container anim fadeIn" ng-non-bindable>

        <h1>Search Results&nbsp;&nbsp;&nbsp; <span class="text-muted">{{$searchTerm}}</span></h1>

        <div class="row">

            <div class="col-md-6">
                <h3>Matching Pages</h3>
                <div class="page-list">
                    @if(count($pages) > 0)
                        @foreach($pages as $page)
                            @include('pages/list-item', ['page' => $page, 'showMeta' => true])
                            <hr>
                        @endforeach
                    @else
                        <p class="text-muted">No pages matched this search</p>
                    @endif
                </div>
            </div>

            <div class="col-md-5 col-md-offset-1">

                @if(count($books) > 0)
                    <h3>Matching Books</h3>
                    <div class="page-list">
                        @foreach($books as $book)
                            @include('books/list-item', ['book' => $book])
                            <hr>
                        @endforeach
                    </div>
                @endif

                @if(count($chapters) > 0)
                    <h3>Matching Chapters</h3>
                    <div class="page-list">
                        @foreach($chapters as $chapter)
                            @include('chapters/list-item', ['chapter' => $chapter, 'hidePages' => true])
                        @endforeach
                    </div>
                @endif

            </div>


        </div>


    </div>


@stop
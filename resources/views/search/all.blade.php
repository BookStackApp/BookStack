@extends('base')

@section('content')

    <div class="container anim fadeIn">

        <h1>Search Results&nbsp;&nbsp;&nbsp; <span class="text-muted">{{$searchTerm}}</span></h1>

        <div class="row">

            <div class="col-md-6">
                <h3>Matching Pages</h3>
                <div class="page-list">
                    @if(count($pages) > 0)
                        @foreach($pages as $page)
                            <div class="book-child">
                                <h3>
                                    <a href="{{$page->getUrl() . '#' . $searchTerm}}" class="page">
                                        <i class="zmdi zmdi-file-text"></i>{{$page->name}}
                                    </a>
                                </h3>
                                <p class="text-muted">
                                    {!! $page->searchSnippet !!}
                                </p>
                                <hr>
                            </div>
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
                            <div class="book-child">
                                <h3>
                                    <a href="{{$book->getUrl()}}" class="text-book">
                                        <i class="zmdi zmdi-book"></i>{{$book->name}}
                                    </a>
                                </h3>
                                <p class="text-muted">
                                    {!! $book->searchSnippet !!}
                                </p>
                                <hr>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if(count($chapters) > 0)
                    <h3>Matching Chapters</h3>
                    <div class="page-list">
                        @foreach($chapters as $chapter)
                            <div class="book-child">
                                <h3>
                                    <a href="{{$chapter->getUrl()}}" class="text-chapter">
                                        <i class="zmdi zmdi-collection-bookmark"></i>{{$chapter->name}}
                                    </a>
                                </h3>
                                <p class="text-muted">
                                    {!! $chapter->searchSnippet !!}
                                </p>
                                <hr>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>


        </div>


    </div>




@stop
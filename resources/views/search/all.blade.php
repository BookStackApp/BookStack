@extends('base')

@section('content')

    <div class="container anim fadeIn" ng-non-bindable>

        <h1>Search Results&nbsp;&nbsp;&nbsp; <span class="text-muted">{{ $searchTerm }}</span></h1>

        <p>

            @if(count($pages) > 0)
                <a href="{{ baseUrl("/search/pages?term={$searchTerm}") }}" class="text-page"><i class="zmdi zmdi-file-text"></i>View all matched pages</a>
            @endif


            @if(count($chapters) > 0)
                &nbsp; &nbsp;&nbsp;
                <a href="{{ baseUrl("/search/chapters?term={$searchTerm}") }}" class="text-chapter"><i class="zmdi zmdi-collection-bookmark"></i>View all matched chapters</a>
            @endif

            @if(count($books) > 0)
                &nbsp; &nbsp;&nbsp;
                <a href="{{ baseUrl("/search/books?term={$searchTerm}") }}" class="text-book"><i class="zmdi zmdi-book"></i>View all matched books</a>
            @endif
        </p>
        <div class="row">

            <div class="col-md-6">
                <h3><a href="{{ baseUrl("/search/pages?term={$searchTerm}") }}" class="no-color">Matching Pages</a></h3>
                @include('partials/entity-list', ['entities' => $pages, 'style' => 'detailed'])
            </div>

            <div class="col-md-5 col-md-offset-1">

                @if(count($books) > 0)
                    <h3><a href="{{ baseUrl("/search/books?term={$searchTerm}") }}" class="no-color">Matching Books</a></h3>
                    @include('partials/entity-list', ['entities' => $books])
                @endif

                @if(count($chapters) > 0)
                    <h3><a href="{{ baseUrl("/search/chapters?term={$searchTerm}") }}" class="no-color">Matching Chapters</a></h3>
                    @include('partials/entity-list', ['entities' => $chapters])
                @endif

            </div>


        </div>


    </div>


@stop
@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 faded">
                    <div class="breadcrumbs">
                        <a href="{{ baseUrl("/search/all?term={$searchTerm}") }}" class="text-button"><i class="zmdi zmdi-search"></i>{{ $searchTerm }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container" ng-non-bindable>

        <h1>{{ trans('entities.search_results') }}</h1>

        <p>
            @if(count($pages) > 0)
                <a href="{{ baseUrl("/search/pages?term={$searchTerm}") }}" class="text-page"><i class="zmdi zmdi-file-text"></i>{{ trans('entities.search_view_pages') }}</a>
            @endif

            @if(count($chapters) > 0)
                &nbsp; &nbsp;&nbsp;
                <a href="{{ baseUrl("/search/chapters?term={$searchTerm}") }}" class="text-chapter"><i class="zmdi zmdi-collection-bookmark"></i>{{ trans('entities.search_view_chapters') }}</a>
            @endif

            @if(count($books) > 0)
                &nbsp; &nbsp;&nbsp;
                <a href="{{ baseUrl("/search/books?term={$searchTerm}") }}" class="text-book"><i class="zmdi zmdi-book"></i>{{ trans('entities.search_view_books') }}</a>
            @endif
        </p>

        <div class="row">
            <div class="col-md-6">
                <h3><a href="{{ baseUrl("/search/pages?term={$searchTerm}") }}" class="no-color">{{ trans('entities.pages') }}</a></h3>
                @include('partials/entity-list', ['entities' => $pages, 'style' => 'detailed'])
            </div>
            <div class="col-md-5 col-md-offset-1">
                @if(count($books) > 0)
                    <h3><a href="{{ baseUrl("/search/books?term={$searchTerm}") }}" class="no-color">{{ trans('entities.books') }}</a></h3>
                    @include('partials/entity-list', ['entities' => $books])
                @endif

                @if(count($chapters) > 0)
                    <h3><a href="{{ baseUrl("/search/chapters?term={$searchTerm}") }}" class="no-color">{{ trans('entities.chapters') }}</a></h3>
                    @include('partials/entity-list', ['entities' => $chapters])
                @endif
            </div>
        </div>


    </div>


@stop
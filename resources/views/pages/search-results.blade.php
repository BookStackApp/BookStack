@extends('base')

@section('content')


    <div class="row">

        <div class="col-md-3 page-menu">

        </div>

        <div class="col-md-9 page-content">
            <h1>Search Results <span class="subheader">For '{{$searchTerm}}'</span></h1>
            <div class="page-list">
                @if(count($pages) > 0)
                    @foreach($pages as $page)
                        <a href="{{$page->getUrl() . '#' . $searchTerm}}">{{$page->name}}</a>
                    @endforeach
                @else
                    <p class="text-muted">No pages matched this search</p>
                @endif
            </div>
        </div>

    </div>




@stop
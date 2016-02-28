@extends('base')

@section('content')

    <div class="faded-small toolbar" ng-non-bindable>
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="action-buttons faded">
                        @if(userCan('page-create', $book))
                            <a href="{{$book->getUrl() . '/page/create'}}" class="text-pos text-button"><i class="zmdi zmdi-plus"></i> New Page</a>
                        @endif
                        @if(userCan('chapter-create', $book))
                            <a href="{{$book->getUrl() . '/chapter/create'}}" class="text-pos text-button"><i class="zmdi zmdi-plus"></i> New Chapter</a>
                        @endif
                        @if(userCan('book-update', $book))
                            <a href="{{$book->getEditUrl()}}" class="text-primary text-button"><i class="zmdi zmdi-edit"></i>Edit</a>
                            <a href="{{ $book->getUrl() }}/sort" class="text-primary text-button"><i class="zmdi zmdi-sort"></i>Sort</a>
                        @endif
                        @if(userCan('restrictions-manage', $book))
                            <a href="{{$book->getUrl()}}/restrict" class="text-primary text-button"><i class="zmdi zmdi-lock-outline"></i>Restrict</a>
                        @endif
                        @if(userCan('book-delete', $book))
                            <a href="{{ $book->getUrl() }}/delete" class="text-neg text-button"><i class="zmdi zmdi-delete"></i>Delete</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container" id="book-dashboard" ng-controller="BookShowController" book-id="{{ $book->id }}">
        <div class="row">
            <div class="col-md-7">

                <h1>{{$book->name}}</h1>
                <div class="book-content" ng-show="!searching">
                    <p class="text-muted" ng-non-bindable>{{$book->description}}</p>

                    <div class="page-list" ng-non-bindable>
                        <hr>
                        @if(count($bookChildren) > 0)
                            @foreach($bookChildren as $childElement)
                                @if($childElement->isA('chapter'))
                                    @include('chapters/list-item', ['chapter' => $childElement])
                                @else
                                    @include('pages/list-item', ['page' => $childElement])
                                @endif
                                <hr>
                            @endforeach
                        @else
                            <p class="text-muted">No pages or chapters have been created for this book.</p>
                            <p>
                                <a href="{{$book->getUrl() . '/page/create'}}" class="text-page"><i class="zmdi zmdi-file-text"></i>Create a new page</a>
                                &nbsp;&nbsp;<em class="text-muted">-or-</em>&nbsp;&nbsp;&nbsp;
                                <a href="{{$book->getUrl() . '/chapter/create'}}" class="text-chapter"><i class="zmdi zmdi-collection-bookmark"></i>Add a chapter</a>
                            </p>
                            <hr>
                        @endif
                        <p class="text-muted small">
                            Created {{$book->created_at->diffForHumans()}} @if($book->createdBy) by {{$book->createdBy->name}} @endif
                            <br>
                            Last Updated {{$book->updated_at->diffForHumans()}} @if($book->updatedBy) by {{$book->updatedBy->name}} @endif
                        </p>
                    </div>
                </div>
                <div class="search-results" ng-cloak ng-show="searching">
                    <h3 class="text-muted">Search Results <a ng-if="searching" ng-click="clearSearch()" class="text-small"><i class="zmdi zmdi-close"></i>Clear Search</a></h3>
                    <div ng-if="!searchResults">
                        @include('partials/loading-icon')
                    </div>
                    <div ng-bind-html="searchResults"></div>
                </div>


            </div>

            <div class="col-md-4 col-md-offset-1">
                <div class="margin-top large"></div>
                <div class="search-box">
                    <form ng-submit="searchBook($event)">
                        <input ng-model="searchTerm" ng-change="checkSearchForm()" type="text" name="term" placeholder="Search This Book">
                        <button type="submit"><i class="zmdi zmdi-search"></i></button>
                        <button ng-if="searching" ng-click="clearSearch()" type="button"><i class="zmdi zmdi-close"></i></button>
                    </form>
                </div>
                <div class="activity anim fadeIn">
                    <h3>Recent Activity</h3>
                    @include('partials/activity-list', ['activity' => Activity::entityActivity($book, 20, 0)])
                </div>
            </div>
        </div>
    </div>

@stop
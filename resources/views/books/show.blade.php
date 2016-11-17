@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="action-buttons faded">
                        @if(userCan('page-create', $book))
                            <a href="{{ $book->getUrl('/page/create') }}" class="text-pos text-button"><i class="zmdi zmdi-plus"></i>{{ trans('entities.pages_new') }}</a>
                        @endif
                        @if(userCan('chapter-create', $book))
                            <a href="{{ $book->getUrl('/chapter/create') }}" class="text-pos text-button"><i class="zmdi zmdi-plus"></i>{{ trans('entities.chapters_new') }}</a>
                        @endif
                        @if(userCan('book-update', $book))
                            <a href="{{$book->getEditUrl()}}" class="text-primary text-button"><i class="zmdi zmdi-edit"></i>{{ trans('entities.edit') }}</a>
                        @endif
                        @if(userCan('book-update', $book) || userCan('restrictions-manage', $book) || userCan('book-delete', $book))
                            <div dropdown class="dropdown-container">
                                <a dropdown-toggle class="text-primary text-button"><i class="zmdi zmdi-more-vert"></i></a>
                                <ul>
                                    @if(userCan('book-update', $book))
                                        <li><a href="{{ $book->getUrl('/sort') }}" class="text-primary"><i class="zmdi zmdi-sort"></i>{{ trans('entities.sort') }}</a></li>
                                    @endif
                                    @if(userCan('restrictions-manage', $book))
                                        <li><a href="{{ $book->getUrl('/permissions') }}" class="text-primary"><i class="zmdi zmdi-lock-outline"></i>{{ trans('entities.permissions') }}</a></li>
                                    @endif
                                    @if(userCan('book-delete', $book))
                                        <li><a href="{{ $book->getUrl('/delete') }}" class="text-neg"><i class="zmdi zmdi-delete"></i>{{ trans('entities.delete') }}</a></li>
                                    @endif
                                </ul>
                            </div>
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
                            <p class="text-muted">{{ trans('entities.books_empty_contents') }}</p>
                            <p>
                                <a href="{{ $book->getUrl('/page/create') }}" class="text-page"><i class="zmdi zmdi-file-text"></i>{{ trans('entities.books_empty_create_page') }}</a>
                                &nbsp;&nbsp;<em class="text-muted">-{{ trans('entities.books_empty_or') }}-</em>&nbsp;&nbsp;&nbsp;
                                <a href="{{ $book->getUrl('/chapter/create') }}" class="text-chapter"><i class="zmdi zmdi-collection-bookmark"></i>{{ trans('entities.books_empty_add_chapter') }}</a>
                            </p>
                            <hr>
                        @endif
                        @include('partials.entity-meta', ['entity' => $book])
                    </div>
                </div>
                <div class="search-results" ng-cloak ng-show="searching">
                    <h3 class="text-muted">{{ trans('entities.search_results') }} <a ng-if="searching" ng-click="clearSearch()" class="text-small"><i class="zmdi zmdi-close"></i>{{ trans('entities.search_clear') }}</a></h3>
                    <div ng-if="!searchResults">
                        @include('partials/loading-icon')
                    </div>
                    <div ng-bind-html="searchResults"></div>
                </div>


            </div>

            <div class="col-md-4 col-md-offset-1">
                <div class="margin-top large"></div>
                @if($book->restricted)
                    <p class="text-muted">
                        @if(userCan('restrictions-manage', $book))
                            <a href="{{ $book->getUrl('/permissions') }}"><i class="zmdi zmdi-lock-outline"></i>{{ trans('entities.books_permissions_active') }}</a>
                        @else
                            <i class="zmdi zmdi-lock-outline"></i>{{ trans('entities.books_permissions_active') }}
                        @endif
                    </p>
                @endif
                <div class="search-box">
                    <form ng-submit="searchBook($event)">
                        <input ng-model="searchTerm" ng-change="checkSearchForm()" type="text" name="term" placeholder="{{ trans('entities.books_search_this') }}">
                        <button type="submit"><i class="zmdi zmdi-search"></i></button>
                        <button ng-if="searching" ng-click="clearSearch()" type="button"><i class="zmdi zmdi-close"></i></button>
                    </form>
                </div>
                <div class="activity anim fadeIn">
                    <h3>{{ trans('entities.recent_activity') }}</h3>
                    @include('partials/activity-list', ['activity' => Activity::entityActivity($book, 20, 0)])
                </div>
            </div>
        </div>
    </div>

@stop
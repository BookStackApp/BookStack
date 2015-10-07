@extends('base')

@section('content')

    <div class="faded-small">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="action-buttons faded">
                        @if($currentUser->can('page-create'))
                            <a href="{{$book->getUrl() . '/page/create'}}" class="text-pos text-button"><i class="zmdi zmdi-plus"></i> New Page</a>
                        @endif
                        @if($currentUser->can('chapter-create'))
                            <a href="{{$book->getUrl() . '/chapter/create'}}" class="text-pos text-button"><i class="zmdi zmdi-plus"></i> New Chapter</a>
                        @endif
                        @if($currentUser->can('book-update'))
                            <a href="{{$book->getEditUrl()}}" class="text-primary text-button"><i class="zmdi zmdi-edit"></i>Edit</a>
                            <a href="{{ $book->getUrl() }}/sort" class="text-primary text-button"><i class="zmdi zmdi-sort"></i>Sort</a>
                        @endif
                        @if($currentUser->can('book-delete'))
                            <a href="{{ $book->getUrl() }}/delete" class="text-neg text-button"><i class="zmdi zmdi-delete"></i>Delete</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container" id="book-dashboard">
        <div class="row">
            <div class="col-md-7">

                <h1>{{$book->name}}</h1>
                <div class="book-content anim fadeIn" v-if="!searching">
                    <p class="text-muted">{{$book->description}}</p>

                    <div class="page-list">
                        <hr>
                        @if(count($book->children()) > 0)
                            @foreach($book->children() as $childElement)
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
                            Last Updated {{$book->updated_at->diffForHumans()}} @if($book->createdBy) by {{$book->updatedBy->name}} @endif
                        </p>
                    </div>
                </div>
                <div class="search-results" v-if="searching">
                    <h3 class="text-muted">Search Results <a v-if="searching" v-on="click: clearSearch" class="text-small"><i class="zmdi zmdi-close"></i>Clear Search</a></h3>
                    <div v-if="!searchResults">
                        @include('partials/loading-icon')
                    </div>
                    <div v-html="searchResults"></div>
                </div>


            </div>

            <div class="col-md-4 col-md-offset-1">
                <div class="margin-top large"></div>
                <div class="search-box">
                    <form v-on="submit: searchBook, input: checkSearchForm" v-el="form" action="/search/book/{{ $book->id }}">
                        {!! csrf_field() !!}
                        <input v-model="searchTerm" type="text" name="term" placeholder="Search This Book">
                        <button type="submit"><i class="zmdi zmdi-search"></i></button>
                        <button v-if="searching" v-on="click: clearSearch" type="button primary"><i class="zmdi zmdi-close"></i></button>
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
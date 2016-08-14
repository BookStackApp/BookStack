@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-8 faded" ng-non-bindable>
                    <div class="breadcrumbs">
                        <a href="{{ $book->getUrl() }}" class="text-book text-button"><i class="zmdi zmdi-book"></i>{{ $book->getShortName() }}</a>
                    </div>
                </div>
                <div class="col-sm-4 faded">
                    <div class="action-buttons">
                        @if(userCan('page-create', $chapter))
                            <a href="{{ $chapter->getUrl('/create-page') }}" class="text-pos text-button"><i class="zmdi zmdi-plus"></i>New Page</a>
                        @endif
                        @if(userCan('chapter-update', $chapter))
                            <a href="{{ $chapter->getUrl('/edit') }}" class="text-primary text-button"><i class="zmdi zmdi-edit"></i>Edit</a>
                        @endif
                        @if(userCan('chapter-update', $chapter) || userCan('restrictions-manage', $chapter) || userCan('chapter-delete', $chapter))
                            <div dropdown class="dropdown-container">
                                <a dropdown-toggle class="text-primary text-button"><i class="zmdi zmdi-more-vert"></i></a>
                                <ul>
                                    @if(userCan('chapter-update', $chapter))
                                        <li><a href="{{ $chapter->getUrl('/move') }}" class="text-primary"><i class="zmdi zmdi-folder"></i>Move</a></li>
                                    @endif
                                    @if(userCan('restrictions-manage', $chapter))
                                        <li><a href="{{ $chapter->getUrl('/permissions') }}" class="text-primary"><i class="zmdi zmdi-lock-outline"></i>Permissions</a></li>
                                    @endif
                                    @if(userCan('chapter-delete', $chapter))
                                        <li><a href="{{ $chapter->getUrl('/delete') }}" class="text-neg"><i class="zmdi zmdi-delete"></i>Delete</a></li>
                                    @endif
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container" ng-non-bindable>
        <div class="row">
            <div class="col-md-8">
                <h1>{{ $chapter->name }}</h1>
                <p class="text-muted">{{ $chapter->description }}</p>

                @if(count($pages) > 0)
                    <div class="page-list">
                        <hr>
                        @foreach($pages as $page)
                            @include('pages/list-item', ['page' => $page])
                            <hr>
                        @endforeach
                    </div>
                @else
                    <hr>
                    <p class="text-muted">No pages are currently in this chapter.</p>
                    <p>
                        @if(userCan('page-create', $chapter))
                            <a href="{{ $chapter->getUrl('/create-page') }}" class="text-page"><i class="zmdi zmdi-file-text"></i>Create a new page</a>
                        @endif
                        @if(userCan('page-create', $chapter) && userCan('book-update', $book))
                            &nbsp;&nbsp;<em class="text-muted">-or-</em>&nbsp;&nbsp;&nbsp;
                        @endif
                        @if(userCan('book-update', $book))
                            <a href="{{ $book->getUrl('/sort') }}" class="text-book"><i class="zmdi zmdi-book"></i>Sort the current book</a>
                        @endif
                    </p>
                    <hr>
                @endif

                <p class="text-muted small">
                    Created {{ $chapter->created_at->diffForHumans() }} @if($chapter->createdBy) by <a href="{{ $chapter->createdBy->getProfileUrl() }}">{{ $chapter->createdBy->name}}</a> @endif
                    <br>
                    Last Updated {{ $chapter->updated_at->diffForHumans() }} @if($chapter->updatedBy) by <a href="{{ $chapter->updatedBy->getProfileUrl() }}">{{  $chapter->updatedBy->name}}</a> @endif
                </p>
            </div>
            <div class="col-md-3 col-md-offset-1">
                <div class="margin-top large"></div>
                @if($book->restricted || $chapter->restricted)
                    <div class="text-muted">

                        @if($book->restricted)
                            @if(userCan('restrictions-manage', $book))
                                <a href="{{ $book->getUrl('/permissions') }}"><i class="zmdi zmdi-lock-outline"></i>Book Permissions Active</a>
                            @else
                                <i class="zmdi zmdi-lock-outline"></i>Book Permissions Active
                            @endif
                                <br>
                        @endif

                        @if($chapter->restricted)
                            @if(userCan('restrictions-manage', $chapter))
                                <a href="{{ $chapter->getUrl('/permissions') }}"><i class="zmdi zmdi-lock-outline"></i>Chapter Permissions Active</a>
                            @else
                                <i class="zmdi zmdi-lock-outline"></i>Chapter Permissions Active
                            @endif
                        @endif
                    </div>
                @endif

                @include('pages/sidebar-tree-list', ['book' => $book, 'sidebarTree' => $sidebarTree])
            </div>
        </div>
    </div>




@stop

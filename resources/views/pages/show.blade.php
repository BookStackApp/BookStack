@extends('base')

@section('content')

    <div class="faded-small toolbar">
        <div class="container">
            <div class="row">
                <div class="col-sm-6 faded">
                    <div class="breadcrumbs">
                        <a href="{{$book->getUrl()}}" class="text-book text-button"><i class="zmdi zmdi-book"></i>{{ $book->getShortName() }}</a>
                        @if($page->hasChapter())
                            <span class="sep">&raquo;</span>
                            <a href="{{ $page->chapter->getUrl() }}" class="text-chapter text-button">
                                <i class="zmdi zmdi-collection-bookmark"></i>
                                {{$page->chapter->getShortName()}}
                            </a>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6 faded">
                    <div class="action-buttons">
                        <span dropdown class="dropdown-container">
                            <div dropdown-toggle class="text-button text-primary"><i class="zmdi zmdi-open-in-new"></i>Export</div>
                            <ul class="wide">
                                <li><a href="{{$page->getUrl()}}/export/html" target="_blank">Contained Web File <span class="text-muted float right">.html</span></a></li>
                                <li><a href="{{$page->getUrl()}}/export/pdf" target="_blank">PDF File <span class="text-muted float right">.pdf</span></a></li>
                                <li><a href="{{$page->getUrl()}}/export/plaintext" target="_blank">Plain Text File <span class="text-muted float right">.txt</span></a></li>
                            </ul>
                        </span>
                        @if(userCan('page-update', $page))
                            <a href="{{$page->getUrl()}}/revisions" class="text-primary text-button"><i class="zmdi zmdi-replay"></i>Revisions</a>
                            <a href="{{$page->getUrl()}}/edit" class="text-primary text-button" ><i class="zmdi zmdi-edit"></i>Edit</a>
                        @endif
                        @if(userCan('restrictions-manage', $page))
                            <a href="{{$page->getUrl()}}/permissions" class="text-primary text-button"><i class="zmdi zmdi-lock-outline"></i>Permissions</a>
                        @endif
                        @if(userCan('page-delete', $page))
                            <a href="{{$page->getUrl()}}/delete" class="text-neg text-button"><i class="zmdi zmdi-delete"></i>Delete</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container" id="page-show" ng-non-bindable>
        <div class="row">
            <div class="col-md-9 print-full-width">
                <div class="page-content anim fadeIn">

                    <div class="pointer-container" id="pointer">
                        <div class="pointer anim">
                            <i class="zmdi zmdi-link"></i>
                            <input readonly="readonly" type="text" placeholder="url">
                            <button class="button icon" title="Copy Link" data-clipboard-text=""><i class="zmdi zmdi-copy"></i></button>
                        </div>
                    </div>

                    @include('pages/page-display')

                    <hr>

                    <p class="text-muted small">
                        Created {{$page->created_at->diffForHumans()}} @if($page->createdBy) by <a href="/user/{{ $page->createdBy->id }}">{{$page->createdBy->name}}</a> @endif
                        <br>
                        Last Updated {{$page->updated_at->diffForHumans()}} @if($page->updatedBy) by <a href="/user/{{ $page->updatedBy->id }}">{{$page->updatedBy->name}}</a> @endif
                    </p>

                </div>
            </div>
            <div class="col-md-3 print-hidden">
                <div class="margin-top large"></div>
                @if($book->restricted || ($page->chapter && $page->chapter->restricted) || $page->restricted)
                    <div class="text-muted">

                        @if($book->restricted)
                            @if(userCan('restrictions-manage', $book))
                                <a href="{{ $book->getUrl() }}/permissions"><i class="zmdi zmdi-lock-outline"></i>Book Permissions Active</a>
                            @else
                                <i class="zmdi zmdi-lock-outline"></i>Book Permissions Active
                            @endif
                            <br>
                        @endif

                        @if($page->chapter && $page->chapter->restricted)
                            @if(userCan('restrictions-manage', $page->chapter))
                                <a href="{{ $page->chapter->getUrl() }}/permissions"><i class="zmdi zmdi-lock-outline"></i>Chapter Permissions Active</a>
                            @else
                                <i class="zmdi zmdi-lock-outline"></i>Chapter Permissions Active
                            @endif
                            <br>
                        @endif

                        @if($page->restricted)
                            @if(userCan('restrictions-manage', $page))
                                <a href="{{ $page->getUrl() }}/permissions"><i class="zmdi zmdi-lock-outline"></i>Page Permissions Active</a>
                            @else
                                <i class="zmdi zmdi-lock-outline"></i>Page Permissions Active
                            @endif
                            <br>
                        @endif
                    </div>
                @endif
                @include('pages/sidebar-tree-list', ['book' => $book, 'sidebarTree' => $sidebarTree])

            </div>
        </div>
    </div>

    @include('partials/highlight')
@stop

@section('scripts')
    <script>
        setupPageShow({{$page->id}});
    </script>
@stop

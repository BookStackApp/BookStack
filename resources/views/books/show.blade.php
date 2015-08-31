@extends('base')

@section('content')

    <div class="faded-small">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="action-buttons faded">
                        @if($currentUser->can('page-create'))
                            <a href="{{$book->getUrl() . '/page/create'}}" class="text-pos"><i class="zmdi zmdi-plus"></i> New Page</a>
                        @endif
                        @if($currentUser->can('chapter-create'))
                            <a href="{{$book->getUrl() . '/chapter/create'}}" class="text-pos"><i class="zmdi zmdi-plus"></i> New Chapter</a>
                        @endif
                        @if($currentUser->can('book-update'))
                            <a href="{{$book->getEditUrl()}}" class="text-primary"><i class="zmdi zmdi-edit"></i>Edit</a>
                            <a href="{{ $book->getUrl() }}/sort" class="text-primary"><i class="zmdi zmdi-sort"></i>Sort</a>
                        @endif
                        @if($currentUser->can('book-delete'))
                            <a href="{{ $book->getUrl() }}/delete" class="text-neg"><i class="zmdi zmdi-delete"></i>Delete</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="container">
        <div class="row">
            <div class="col-md-7">

                <h1>{{$book->name}}</h1>
                <p class="text-muted">{{$book->description}}</p>

                <div class="page-list">
                    <hr>
                    @if(count($book->children()) > 0)
                        @foreach($book->children() as $childElement)
                            <div class="book-child">
                                <h3>
                                    <a href="{{ $childElement->getUrl() }}" class="{{ $childElement->getName() }}">
                                        <i class="zmdi {{ $childElement->isA('chapter') ? 'zmdi-collection-bookmark chapter-toggle':'zmdi-file-text'}}"></i>{{ $childElement->name }}
                                    </a>
                                </h3>
                                <p class="text-muted">
                                    {{$childElement->getExcerpt()}}
                                </p>

                                @if($childElement->isA('chapter') && count($childElement->pages) > 0)
                                    <div class="inset-list">
                                        @foreach($childElement->pages as $page)
                                            <h4><a href="{{$page->getUrl()}}"><i class="zmdi zmdi-file-text"></i>{{$page->name}}</a></h4>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
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
                </div>

                <p class="text-muted small">
                    Created {{$book->created_at->diffForHumans()}} @if($book->createdBy) by {{$book->createdBy->name}} @endif
                    <br>
                    Last Updated {{$book->updated_at->diffForHumans()}} @if($book->createdBy) by {{$book->updatedBy->name}} @endif
                </p>


            </div>

            <div class="col-md-4 col-md-offset-1">
                <div class="margin-top large"><br></div>
                <h3>Recent Activity</h3>
                @include('partials/activity-list', ['activity' => Activity::entityActivity($book, 20, 0)])
            </div>
        </div>
    </div>



    <script>
        $(function() {

            $('.chapter-toggle').click(function(e) {
                e.preventDefault();
                $(this).closest('.book-child').find('.inset-list').slideToggle(180);
            });

        });
    </script>

@stop
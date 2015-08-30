@extends('base')

@section('content')

    <div class="row faded-small">
        <div class="col-md-6"></div>
        <div class="col-md-6">
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

    <div class="row">
        <div class="col-md-6 col-md-offset-1">

            <div class="page-content">
                <h1>{{$book->name}}</h1>
                <p class="text-muted">{{$book->description}}</p>

                <div class="page-list">
                    <hr>
                    @foreach($book->children() as $childElement)
                        <div class="book-child">
                            <h3>
                                <a href="{{ $childElement->getUrl() }}">
                                    @if(is_a($childElement, 'Oxbow\Chapter'))
                                        <i class="zmdi zmdi-collection-bookmark chapter-toggle"></i>
                                    @else
                                        <i class="zmdi zmdi-file-text"></i>
                                    @endif
                                    {{ $childElement->name }}
                                </a>
                            </h3>
                            <p class="text-muted">
                                {{$childElement->getExcerpt()}}
                            </p>

                            @if(is_a($childElement, 'Oxbow\Chapter') && count($childElement->pages) > 0)
                                <div class="inset-list">
                                    @foreach($childElement->pages as $page)
                                        <h4><a href="{{$page->getUrl()}}"><i class="zmdi zmdi-file-text"></i> {{$page->name}}</a></h4>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <hr>
                    @endforeach
                </div>

                <p class="text-muted small">
                    Created {{$book->created_at->diffForHumans()}} @if($book->createdBy) by {{$book->createdBy->name}} @endif
                    <br>
                    Last Updated {{$book->updated_at->diffForHumans()}} @if($book->createdBy) by {{$book->updatedBy->name}} @endif
                </p>

            </div>

        </div>

        <div class="col-md-3 col-md-offset-1">
            <div class="margin-top large"><br></div>
            <h3>Recent Activity</h3>
            @include('partials/activity-list', ['activity' => Activity::entityActivity($book, 20, 0)])
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
@extends('base')

@section('content')

    <div class="row faded-small">
        <div class="col-md-6"></div>
        <div class="col-md-6">
            <div class="action-buttons faded">
                <a href="{{$book->getEditUrl()}}"><i class="fa fa-pencil"></i>Edit</a>
                <a href="{{ $book->getUrl() }}/sort"><i class="fa fa-sort"></i>Sort</a>
                <a href="{{ $book->getUrl() }}/delete"><i class="fa fa-trash"></i>Delete</a>
            </div>
        </div>
    </div>

    <div class="page-content">
        <h1>{{$book->name}}</h1>
        <p class="text-muted">{{$book->description}}</p>

        <div class="clearfix header-group">
            <h4 class="float">Contents</h4>
            <div class="float right">
                <a href="{{$book->getUrl() . '/page/create'}}" class="text-pos">+ New Page</a>
                <a href="{{$book->getUrl() . '/chapter/create'}}" class="text-pos">+ New Chapter</a>
            </div>
        </div>

        <div>
            @foreach($book->children() as $childElement)
                <div >
                    <h3>
                        <a href="{{ $childElement->getUrl() }}">
                            @if(is_a($childElement, 'Oxbow\Chapter'))
                                <i class="fa fa-archive"></i>
                            @else
                                <i class="fa fa-file"></i>
                            @endif
                            {{ $childElement->name }}
                        </a>
                    </h3>
                </div>
                <hr>
            @endforeach
        </div>

        {{--@include('pages/page-tree-list', ['pageTree' => $pageTree])--}}

    </div>


    <script>
        $(function() {

            $('.nested-page-list i.arrow').click(function() {
                var list = $(this).closest('.nested-page-list');
                var listItem = $(this).closest('li');
                listItem.toggleClass('expanded');
            });

        });
    </script>

@stop
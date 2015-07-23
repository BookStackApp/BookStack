@extends('base')

@section('content')

    <div class="row faded-small">
        <div class="col-md-6"></div>
        <div class="col-md-6">
            <div class="action-buttons">
                <a href="{{$book->getEditUrl()}}"><i class="fa fa-pencil"></i>Edit Book</a>
                <a href="{{ $book->getUrl() }}/sort"><i class="fa fa-sort"></i>Sort Pages</a>
            </div>
        </div>
    </div>

    <div class="page-content">
        <h1>{{$book->name}}</h1>
        <p class="text-muted">{{$book->description}}</p>

        <div class="clearfix header-group">
            <h4 class="float">Pages</h4>
            <a href="{{$book->getUrl() . '/page/create'}}" class="text-pos float right">+ New Page</a>
        </div>

        @include('pages/page-tree-list', ['pageTree' => $pageTree])

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
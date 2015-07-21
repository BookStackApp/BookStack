@extends('base')

@section('content')


    <div class="row">

        <div class="col-md-3 page-menu">
            <h4>Book Actions</h4>
            <div class="buttons">
                <a href="{{$book->getEditUrl()}}"><i class="fa fa-pencil"></i>Edit Book</a>
                <a href="{{ $book->getUrl() }}/sort"><i class="fa fa-sort"></i>Sort Pages</a>
            </div>
        </div>

        <div class="page-content col-md-9 float left">
            <h1>{{$book->name}}</h1>
            <p class="text-muted">{{$book->description}}</p>

            <div class="clearfix header-group">
                <h4 class="float">Pages</h4>
                <a href="{{$book->getUrl() . '/page/create'}}" class="text-pos float right">+ New Page</a>
            </div>

            @include('pages/page-tree-list', ['pageTree' => $pageTree])

        </div>


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
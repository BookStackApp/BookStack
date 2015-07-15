@extends('base')

@section('content')


    <div class="row">

        <div class="col-md-3 page-menu">
            <h4>Book Actions</h4>
            <div class="buttons">
                <a href="{{$book->getEditUrl()}}"><i class="fa fa-pencil"></i>Edit Book</a>
            </div>
        </div>

        <div class="page-content col-md-9 float left">
            <h1>{{$book->name}}</h1>
            <p class="text-muted">{{$book->description}}</p>

            <div class="clearfix header-group">
                <h4 class="float">Pages</h4>
                <a href="{{$book->getUrl() . '/page/create'}}" class="text-pos float right">+ New Page</a>
            </div>
            <div class="page-list">
                @if(count($book->pages) > 0)
                    @foreach($book->pages as $page)
                        <a href="{{$page->getUrl()}}">{{$page->name}}</a>
                    @endforeach
                @else
                    <p class="text-muted">This book has no pages</p>
                @endif
            </div>

            <p>

            </p>


        </div>


    </div>

@stop
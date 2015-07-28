@extends('base')

@section('content')

    <div class="row faded-small">
        <div class="col-md-6"></div>
        <div class="col-md-6 faded">
            <div class="action-buttons">
                <a href="{{$chapter->getUrl() . '/edit'}}" ><i class="fa fa-pencil"></i>Edit</a>
                <a href="{{$chapter->getUrl() . '/delete'}}"><i class="fa fa-trash"></i>Delete</a>
            </div>
        </div>
    </div>


    <div class="page-content">
        <h1>{{ $chapter->name }}</h1>
        <p class="text-muted">{{ $chapter->description }}</p>
        @if(count($chapter->pages) > 0)
            <h4 class="text-muted">Pages</h4>
            <div class="page-list">
                @foreach($chapter->pages as $page)
                    <div >
                        <h3>
                            <a href="{{ $page->getUrl() }}">
                                <i class="fa fa-file"></i>
                                {{ $page->name }}
                            </a>
                        </h3>
                    </div>
                    <hr>
                @endforeach
            </div>
        @endif
    </div>


@stop

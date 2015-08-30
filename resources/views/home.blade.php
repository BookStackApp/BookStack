@extends('base')

@section('content')


    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <h2>Books</h2>
                @foreach($books as $book)
                    <div class="book">
                        <h3><a href="{{$book->getUrl()}}">{{$book->name}}</a></h3>
                        <p class="text-muted">{{$book->description}}</p>
                    </div>
                    <hr>
                @endforeach
            </div>
            <div class="col-md-4 col-md-offset-1">
                <div class="margin-top large">&nbsp;</div>
                <h3>Recent Activity</h3>
                @include('partials/activity-list', ['activity' => $activity])
            </div>
        </div>
    </div>


@stop
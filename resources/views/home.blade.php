@extends('base')

@section('content')

    <div class="container">
        <div class="row">
            <div class="col-md-7">
                <h2>Books</h2>
                @if(count($books) > 0)
                    @foreach($books as $book)
                        @include('books/list-item', ['book' => $book])
                        <hr>
                    @endforeach
                @else
                    <p class="text-muted">No books have been created.</p>
                    <a href="/books/create" class="text-pos"><i class="zmdi zmdi-edit"></i>Create one now</a>
                @endif
            </div>
            <div class="col-md-4 col-md-offset-1">
                <div class="margin-top large">&nbsp;</div>
                <h3>Recent Activity</h3>
                @include('partials/activity-list', ['activity' => $activity])
            </div>
        </div>
    </div>


@stop
@extends('base')

@section('content')

    <div class="row">
        <div class="col-md-6">
            <a href="/books/create">+ Add new book</a>
        </div>
    </div>

    <div class="row">
        @foreach($books as $book)
            <div class="col-md-4 shaded book">
                <h3><a href="{{$book->getUrl()}}">{{$book->name}}</a></h3>
                <p>{{$book->description}}</p>
                <div class="buttons">
                    <a href="{{$book->getEditUrl()}}" class="button secondary">Edit</a>
                    @include('form/delete-button', ['url' => '/books/' . $book->id . '/destroy', 'text' => 'Delete'])
                </div>
            </div>
        @endforeach
    </div>


@stop
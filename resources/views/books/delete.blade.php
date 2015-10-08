@extends('base')

@section('content')

    <div class="container small">
        <h1>Delete Book</h1>
        <p>This will delete the book with the name '{{$book->name}}', All pages and chapters will be removed.</p>
        <p class="text-neg">Are you sure you want to delete this book?</p>

        <form action="{{$book->getUrl()}}" method="POST">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="DELETE">
            <a href="{{$book->getUrl()}}" class="button">Cancel</a>
            <button type="submit" class="button neg">Confirm</button>
        </form>
    </div>

@stop
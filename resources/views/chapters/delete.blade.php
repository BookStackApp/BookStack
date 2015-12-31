@extends('base')

@section('content')

    <div class="container small" ng-non-bindable>
        <h1>Delete Chapter</h1>
        <p>This will delete the chapter with the name '{{$chapter->name}}', All pages will be removed
        and added directly to the book.</p>
        <p class="text-neg">Are you sure you want to delete this chapter?</p>

        <form action="{{$chapter->getUrl()}}" method="POST">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="DELETE">
            <a href="{{$chapter->getUrl()}}" class="button primary">Cancel</a>
            <button type="submit" class="button neg">Confirm</button>
        </form>
    </div>

@stop
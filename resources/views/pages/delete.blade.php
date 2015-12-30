@extends('base')

@section('content')

    <div class="container small" ng-non-bindable>
        <h1>Delete Page</h1>
        <p class="text-neg">Are you sure you want to delete this page?</p>

        <form action="{{$page->getUrl()}}" method="POST">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="DELETE">
            <a href="{{$page->getUrl()}}" class="button primary">Cancel</a>
            <button type="submit" class="button neg">Confirm</button>
        </form>
    </div>

@stop
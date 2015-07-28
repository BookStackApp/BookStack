@extends('base')

@section('content')

    <div class="page-content">
        <h1>Delete Page</h1>
        <p>Are you sure you want to delete this page?</p>

        <form action="{{$page->getUrl()}}" method="POST">
            {!! csrf_field() !!}
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit" class="button neg">Confirm</button>
            <a href="{{$page->getUrl()}}" class="button">Cancel</a>
        </form>
    </div>

@stop

@section('bottom')
    @include('pages/image-manager')
@stop
@extends('simple-layout')

@section('body')
    <div class="content tag">
    @include('tags/characterlist', ['tags' => $tags, 'searchTerm' => $searchTerm])
    </div>
@stop
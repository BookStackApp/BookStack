@extends('simple-layout')

@section('body')
    <div class="content tag">
    @include('tags/list', ['tags' => $tags])
    </div>
@stop
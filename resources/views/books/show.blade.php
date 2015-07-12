@extends('base')

@section('content')

    <h2>{{$book->name}}</h2>
    <p class="text-muted">{{$book->description}}</p>
@stop
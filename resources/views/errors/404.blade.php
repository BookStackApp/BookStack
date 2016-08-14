@extends('base')

@section('content')


<div class="container">
    <h1 class="text-muted">{{ $message or 'Page Not Found' }}</h1>
    <p>Sorry, The page you were looking for could not be found.</p>
    <a href="{{ baseUrl('/') }}" class="button">Return To Home</a>
</div>

@stop
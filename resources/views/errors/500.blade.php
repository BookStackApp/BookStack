@extends('base')

@section('content')

    <div class="container">
        <h1 class="text-muted">{{ trans('errors.error_occurred') }}</h1>
        <p>{{ $message }}</p>
    </div>

@stop
@extends('public')

@section('content')

    <div class="container">
        <h1 class="text-muted">{{ trans('errors.app_down', ['appName' => setting('app-name')]) }}</h1>
        <p>{{ trans('errors.back_soon') }}</p>
    </div>

@stop
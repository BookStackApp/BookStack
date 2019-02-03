@extends('simple-layout')

@section('content')

    <div class="container small mt-xl">
        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('errors.app_down', ['appName' => setting('app-name')]) }}</h1>
            <p>{{ trans('errors.back_soon') }}</p>
        </div>
    </div>

@stop
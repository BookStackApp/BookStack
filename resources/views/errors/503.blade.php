@extends('public')

@section('content')

    <div class="container small">
        <div class="card">
            <div class="body">
                <h4 class="text-muted">@icon('danger') {{ trans('errors.app_down', ['appName' => setting('app-name')]) }}</h4>
                <p>{{ trans('errors.back_soon') }}</p>
            </div>
        </div>
    </div>

@stop
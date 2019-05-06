@extends('base')

@section('content')

    <div class="container">
        <div class="card">
            <h3 class="text-muted">{{ trans('errors.error_occurred') }}</h3>
            <div class="body">
                <h5>{{ $message ?? 'An unknown error occurred' }}</h5>
                <p><a href="{{ baseUrl('/') }}" class="button outline">{{ trans('errors.return_home') }}</a></p>
            </div>
        </div>
    </div>

@stop
@extends('layouts.base')

@section('content')

    <div class="container small py-xl">

        <main class="card content-wrap auto-height">
            <div id="main-content" class="body">
                <h3>{{ trans('errors.error_occurred') }}</h3>
                <h5 class="mb-m">{{ $message ?? 'An unknown error occurred' }}</h5>
                <p><a href="{{ url('/') }}" class="button outline">{{ trans('errors.return_home') }}</a></p>
            </div>
        </main>

    </div>

@stop
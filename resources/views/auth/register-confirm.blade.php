@extends('public')

@section('header-buttons')
    @if(!$signedIn)
        <a href="{{ baseUrl("/login") }}"><i class="zmdi zmdi-sign-in"></i>{{ trans('auth.log_in') }}</a>
    @endif
@stop

@section('content')

    <div class="text-center">
        <div class="center-box">
            <h2>{{ trans('auth.register_thanks') }}</h2>
            <p>{{ trans('auth.register_confirm', ['appName' => setting('app-name')]) }}</p>
        </div>
    </div>

@stop

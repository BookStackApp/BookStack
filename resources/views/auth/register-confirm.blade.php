@extends('public')

@section('header-buttons')
    @if(!$signedIn)
        <a href="{{ baseUrl("/login") }}">@icon('login') {{ trans('auth.log_in') }}</a>
    @endif
@stop

@section('content')

    <div class="text-center">
        <div class="card center-box">
            <h3><i class="zmdi zmdi-accounts"></i> {{ trans('auth.register_thanks') }}</h3>
            <div class="body">
                <p>{{ trans('auth.register_confirm', ['appName' => setting('app-name')]) }}</p>
            </div>
        </div>
    </div>

@stop

@extends('layouts.simple')

@section('content')

    <div class="container very-small">

        <div class="my-l">&nbsp;</div>

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ Str::title(trans('auth.log_in')) }}</h1>

            @include('auth.parts.login-message')

            @foreach($authMethods as $authMethod)
                @if(!$loop->first)
                    <hr class="my-l">
                @endif

                @include('auth.parts.login-form-' . $authMethod, ['formId' => 'login-form-' . $authMethod])
            @endforeach

            @if(count($socialDrivers) > 0)
                <hr class="my-l">
                @foreach($socialDrivers as $driver => $name)
                    <div>
                        <a id="social-login-{{$driver}}" class="button outline svg" href="{{ url("/login/service/" . $driver) }}">
                            @icon('auth/' . $driver)
                            <span>{{ trans('auth.log_in_with', ['socialDriver' => $name]) }}</span>
                        </a>
                    </div>
                @endforeach
            @endif

            @if(setting('registration-enabled') && auth_method_enabled('standard'))
                <div class="text-center pb-s">
                    <hr class="my-l">
                    <a href="{{ url('/register') }}">{{ trans('auth.dont_have_account') }}</a>
                </div>
            @endif
        </div>
    </div>

@stop

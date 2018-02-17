@extends('public')

@section('header-buttons')
    @if(setting('registration-enabled', false))
        <a href="{{ baseUrl("/register") }}">@icon('new-user') {{ trans('auth.sign_up') }}</a>
    @endif
@stop

@section('content')

    <div class="text-center">
        <div class="card center-box">
            <h3>@icon('login') {{ title_case(trans('auth.log_in')) }}</h3>

            <div class="body">
                <form action="{{ baseUrl("/login") }}" method="POST" id="login-form">
                    {!! csrf_field() !!}

                    @include('auth/forms/login/' . $authMethod)

                    <div class="form-group">
                        <label for="remember" class="inline">{{ trans('auth.remember_me') }}</label>
                        <input type="checkbox" id="remember" name="remember"  class="toggle-switch-checkbox">
                        <label for="remember" class="toggle-switch"></label>
                    </div>

                    <div class="from-group">
                        <button class="button block pos" tabindex="3">@icon('login') {{ title_case(trans('auth.log_in')) }}</button>
                    </div>
                </form>

                @if(count($socialDrivers) > 0)
                    <hr class="margin-top">
                    @foreach($socialDrivers as $driver => $name)
                        <a id="social-login-{{$driver}}" class="button block muted-light svg text-left" href="{{ baseUrl("/login/service/" . $driver) }}">
                            @icon('auth/' . $driver)
                            {{ trans('auth.log_in_with', ['socialDriver' => $name]) }}
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

@stop
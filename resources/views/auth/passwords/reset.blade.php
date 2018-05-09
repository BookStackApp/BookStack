@extends('public')

@section('header-buttons')
    <a href="{{ baseUrl("/login") }}">@icon('login') {{ trans('auth.log_in') }}</a>
    @if(setting('registration-enabled'))
        <a href="{{ baseUrl("/register") }}">@icon('new-user') {{ trans('auth.sign_up') }}</a>
    @endif
@stop

@section('content')

    <div class="text-center">
        <div class="card center-box">
            <h3>@icon('permission') {{ trans('auth.reset_password') }}</h3>

            <div class="body">
                <form action="{{ baseUrl("/password/reset") }}" method="POST">
                    {!! csrf_field() !!}
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
                        <label for="email">{{ trans('auth.email') }}</label>
                        @include('form/text', ['name' => 'email'])
                    </div>

                    <div class="form-group">
                        <label for="password">{{ trans('auth.password') }}</label>
                        @include('form/password', ['name' => 'password'])
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation">{{ trans('auth.password_confirm') }}</label>
                        @include('form/password', ['name' => 'password_confirmation'])
                    </div>

                    <div class="from-group text-right">
                        <button class="button primary">{{ trans('auth.reset_password') }}</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

@stop
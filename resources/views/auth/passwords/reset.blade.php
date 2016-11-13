1@extends('public')

@section('header-buttons')
    <a href="{{ baseUrl("/login") }}"><i class="zmdi zmdi-sign-in"></i>{{ trans('auth.log_in') }}</a>
    @if(setting('registration-enabled'))
        <a href="{{ baseUrl("/register") }}"><i class="zmdi zmdi-account-add"></i>{{ trans('auth.sign_up') }}</a>
    @endif
@stop

@section('body-class', 'image-cover login')

@section('content')


    <div class="text-center">
        <div class="center-box text-left">
            <h1>{{ trans('auth.reset_password') }}</h1>

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

                <div class="from-group">
                    <button class="button block pos">{{ trans('auth.reset_password') }}</button>
                </div>
            </form>
        </div>
    </div>

@stop
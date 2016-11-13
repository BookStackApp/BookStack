@extends('public')

@section('header-buttons')
    <a href="{{ baseUrl("/login") }}"><i class="zmdi zmdi-sign-in"></i>{{ trans('auth.log_in') }}</a>
    @if(setting('registration-enabled'))
        <a href="{{ baseUrl("/register") }}"><i class="zmdi zmdi-account-add"></i>{{ trans('auth.sign_up') }}</a>
    @endif
@stop

@section('content')


    <div class="text-center">
        <div class="center-box text-left">
            <h1>{{ trans('auth.reset_password') }}</h1>

            <p class="muted small">{{ trans('auth.reset_password_send_instructions') }}</p>

            <form action="{{ baseUrl("/password/email") }}" method="POST">
                {!! csrf_field() !!}

                <div class="form-group">
                    <label for="email">{{ trans('auth.email') }}</label>
                    @include('form/text', ['name' => 'email'])
                </div>

                <div class="from-group">
                    <button class="button block pos">{{ trans('auth.reset_password_send_button') }}</button>
                </div>
            </form>
        </div>
    </div>

@stop
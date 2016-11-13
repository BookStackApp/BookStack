@extends('public')

@section('header-buttons')
    <a href="{{ baseUrl("/login") }}"><i class="zmdi zmdi-sign-in"></i>Sign in</a>
    @if(setting('registration-enabled'))
        <a href="{{ baseUrl("/register") }}"><i class="zmdi zmdi-account-add"></i>Sign up</a>
    @endif
@stop

@section('content')


    <div class="text-center">
        <div class="center-box text-left">
            <h1>Reset Password</h1>

            <p class="muted small">Enter your email below and you will be sent an email with a password reset link.</p>

            <form action="{{ baseUrl("/password/email") }}" method="POST">
                {!! csrf_field() !!}

                <div class="form-group">
                    <label for="email">Email</label>
                    @include('form/text', ['name' => 'email'])
                </div>

                <div class="from-group">
                    <button class="button block pos">Send Reset Link</button>
                </div>
            </form>
        </div>
    </div>

@stop
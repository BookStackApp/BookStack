@extends('public')

@section('header-buttons')
    @if(Setting::get('registration-enabled'))
        <a href="/register"><i class="zmdi zmdi-account-add"></i>Sign up</a>
    @endif
@stop

@section('content')

    <div class="text-center">
        <div class="center-box">
            <h1>Log In</h1>

            <form action="/login" method="POST" id="login-form">
                {!! csrf_field() !!}

                <div class="form-group">
                    <label for="email">Email</label>
                    @include('form/text', ['name' => 'email', 'tabindex' => 1])
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    @include('form/password', ['name' => 'password', 'tabindex' => 2])
                    <span class="block small"><a href="/password/email">Forgot Password?</a></span>
                </div>

                <div class="form-group">
                    <label for="remember" class="inline">Remember Me</label>
                    <input type="checkbox" id="remember" name="remember"  class="toggle-switch-checkbox">
                    <label for="remember" class="toggle-switch"></label>
                </div>


                <div class="from-group">
                    <button class="button block pos" tabindex="3">Sign In</button>
                </div>
            </form>

            @if(count($socialDrivers) > 0)
                <hr class="margin-top">
                <h3 class="text-muted">Social Login</h3>
                @if(isset($socialDrivers['google']))
                    <a href="/login/service/google" style="color: #DC4E41;"><i class="zmdi zmdi-google-plus-box zmdi-hc-4x"></i></a>
                @endif
                @if(isset($socialDrivers['github']))
                    <a href="/login/service/github" style="color:#444;"><i class="zmdi zmdi-github zmdi-hc-4x"></i></a>
                @endif
            @endif
        </div>
    </div>

@stop
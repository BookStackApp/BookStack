@extends('public')

@section('content')

    <div class="center-box">
        <h1>Log In</h1>

        <form action="/login" method="POST">
            {!! csrf_field() !!}

            <div class="form-group">
                <label for="email">Email</label>
                @include('form/text', ['name' => 'email'])
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                @include('form/password', ['name' => 'password'])
                <span class="block small"><a href="/password/email">Forgot Password?</a></span>
            </div>

            <div class="from-group">
                <button class="button block pos">Sign In</button>
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

@stop
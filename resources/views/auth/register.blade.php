@extends('public')

@section('header-buttons')
    <a href="/login"><i class="zmdi zmdi-sign-in"></i>Sign in</a>
@stop

@section('content')

    <div class="text-center">
        <div class="center-box">
            <h1>Sign Up</h1>

            <form action="/register" method="POST">
                {!! csrf_field() !!}

                <div class="form-group">
                    <label for="email">Name</label>
                    @include('form/text', ['name' => 'name'])
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    @include('form/text', ['name' => 'email'])
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    @include('form/password', ['name' => 'password', 'placeholder' => 'Must be over 5 characters'])
                </div>

                <div class="from-group">
                    <button class="button block pos">Create Account</button>
                </div>
            </form>

            @if(count($socialDrivers) > 0)
                <hr class="margin-top">
                <h3 class="text-muted">Social Registration</h3>
                <p class="text-small">Register and sign in using another service.</p>
                @if(isset($socialDrivers['google']))
                    <a href="/register/service/google" style="color: #DC4E41;"><i class="zmdi zmdi-google-plus-box zmdi-hc-4x"></i></a>
                @endif
                @if(isset($socialDrivers['github']))
                    <a href="/register/service/github" style="color:#444;"><i class="zmdi zmdi-github zmdi-hc-4x"></i></a>
                @endif
            @endif
        </div>
    </div>


@stop

@extends('public')

@section('sidebar')

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
    </div>

@stop
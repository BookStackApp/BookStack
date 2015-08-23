@extends('public')

@section('body-class', 'image-cover login')

@section('sidebar')


    <div class="text-center">
        <div class="center-box text-left">
            <h1>Reset Password</h1>

            <p class="muted small">Enter your email below and you will be sent an email with a password reset link.</p>

            <form action="/password/email" method="POST">
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
@extends('public')

@section('body-class', 'image-cover login')

@section('content')


    <div class="text-center">
        <div class="center-box text-left">
            <h1>Reset Password</h1>

            <form action="/password/reset" method="POST">
                {!! csrf_field() !!}
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label for="email">Email</label>
                    @include('form/text', ['name' => 'email'])
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    @include('form/password', ['name' => 'password'])
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password</label>
                    @include('form/password', ['name' => 'password_confirmation'])
                </div>

                <div class="from-group">
                    <button class="button block pos">Reset Password</button>
                </div>
            </form>
        </div>
    </div>

@stop
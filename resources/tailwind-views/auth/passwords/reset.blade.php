@extends('layouts.simple')

@section('content')

    <div class="container very-small mt-xl">
        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('auth.reset_password') }}</h1>

            <form action="{{ url("/password/reset") }}" method="POST" class="stretch-inputs">
                {!! csrf_field() !!}
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="form-group">
                    <label for="email">{{ trans('auth.email') }}</label>
                    @include('form.text', ['name' => 'email'])
                </div>

                <div class="form-group">
                    <label for="password">{{ trans('auth.password') }}</label>
                    @include('form.password', ['name' => 'password'])
                </div>

                <div class="form-group">
                    <label for="password_confirmation">{{ trans('auth.password_confirm') }}</label>
                    @include('form.password', ['name' => 'password_confirmation'])
                </div>

                <div class="from-group text-right mt-m">
                    <button class="button">{{ trans('auth.reset_password') }}</button>
                </div>
            </form>

        </div>
    </div>

@stop
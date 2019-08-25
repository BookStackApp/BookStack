@extends('simple-layout')

@section('content')

    <div class="container very-small mt-xl">
        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('auth.user_invite_page_welcome', ['appName' => setting('app-name')]) }}</h1>
            <p>{{ trans('auth.user_invite_page_text', ['appName' => setting('app-name')]) }}</p>

            <form action="{{ url('/register/invite/' . $token) }}" method="POST" class="stretch-inputs">
                {!! csrf_field() !!}

                <div class="form-group">
                    <label for="password">{{ trans('auth.password') }}</label>
                    @include('form.password', ['name' => 'password', 'placeholder' => trans('auth.password_hint')])
                </div>

                <div class="text-right">
                    <button class="button">{{ trans('auth.user_invite_page_confirm_button') }}</button>
                </div>

            </form>

        </div>
    </div>

@stop

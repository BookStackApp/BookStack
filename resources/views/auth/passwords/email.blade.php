@extends('public')

@section('header-buttons')
    <a href="{{ baseUrl("/login") }}">@icon('login') {{ trans('auth.log_in') }}</a>
    @if(setting('registration-enabled'))
        <a href="{{ baseUrl("/register") }}">@icon('new-user') {{ trans('auth.sign_up') }}</a>
    @endif
@stop

@section('content')


    <div class="text-center">
        <div class="card center-box">
            <h3>@icon('permission') {{ trans('auth.reset_password') }}</h3>

            <div class="body">
                <p class="muted small">{{ trans('auth.reset_password_send_instructions') }}</p>

                <form action="{{ baseUrl("/password/email") }}" method="POST">
                    {!! csrf_field() !!}

                    <div class="form-group">
                        <label for="email">{{ trans('auth.email') }}</label>
                        @include('form/text', ['name' => 'email'])
                    </div>

                    <div class="from-group text-right">
                        <button class="button primary">{{ trans('auth.reset_password_send_button') }}</button>
                    </div>
                </form>
            </div>

        </div>
    </div>

@stop
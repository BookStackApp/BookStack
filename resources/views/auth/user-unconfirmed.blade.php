@extends('layouts.simple')

@section('content')

    <div class="container very-small mt-xl">
        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('auth.email_not_confirmed') }}</h1>

            <p>{{ trans('auth.email_not_confirmed_text') }}<br>
                {{ trans('auth.email_not_confirmed_click_link') }}
            </p>
            <p>
                {{ trans('auth.email_not_confirmed_resend') }}
            </p>

            <form action="{{ url("/register/confirm/resend") }}" method="POST" class="stretch-inputs">
                {!! csrf_field() !!}
                <div class="form-group">
                    <label for="email">{{ trans('auth.email') }}</label>
                    @if($user)
                        @include('form.text', ['name' => 'email', 'model' => $user])
                    @else
                        @include('form.text', ['name' => 'email'])
                    @endif
                </div>
                <div class="form-group text-right mt-m">
                    <button type="submit" class="button">{{ trans('auth.email_not_confirmed_resend_button') }}</button>
                </div>
            </form>

        </div>
    </div>

@stop

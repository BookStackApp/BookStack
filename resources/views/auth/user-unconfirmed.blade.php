@extends('public')

@section('content')

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <h2>{{ trans('auth.email_not_confirmed') }}</h2>
            <p class="text-muted">{{ trans('auth.email_not_confirmed_text') }}<br>
                {{ trans('auth.email_not_confirmed_click_link') }} <br>
                {{ trans('auth.email_not_confirmed_resend') }}
            </p>
            <hr>
            <form action="{{ baseUrl("/register/confirm/resend") }}" method="POST">
                {!! csrf_field() !!}
                <div class="form-group">
                    <label for="email">{{ trans('auth.email') }}</label>
                    @if(auth()->check())
                        @include('form/text', ['name' => 'email', 'model' => auth()->user()])
                    @else
                        @include('form/text', ['name' => 'email'])
                    @endif
                </div>
                <div class="form-group">
                    <button type="submit" class="button pos">{{ trans('auth.email_not_confirmed_resend_button') }}</button>
                </div>
            </form>
        </div>
    </div>

@stop

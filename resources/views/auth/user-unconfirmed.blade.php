@extends('public')

@section('content')

    <div class="container small">
        <p>&nbsp;</p>
        <div class="card">
            <h3><i class="zmdi zmdi-accounts"></i> {{ trans('auth.email_not_confirmed') }}</h3>
            <div class="body">
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

    </div>

@stop

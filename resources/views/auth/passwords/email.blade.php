@extends('simple-layout')

@section('content')
    <div class="container very-small mt-xl">
        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('auth.reset_password') }}</h1>

            <p class="text-muted small">{{ trans('auth.reset_password_send_instructions') }}</p>

            <form action="{{ baseUrl("/password/email") }}" method="POST" class="stretch-inputs">
                {!! csrf_field() !!}

                <div class="form-group">
                    <label for="email">{{ trans('auth.email') }}</label>
                    @include('form.text', ['name' => 'email'])
                </div>

                <div class="from-group text-right mt-m">
                    <button class="button primary">{{ trans('auth.reset_password_send_button') }}</button>
                </div>
            </form>

        </div>
    </div>
@stop
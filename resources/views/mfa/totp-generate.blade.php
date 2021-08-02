@extends('simple-layout')

@section('body')

    <div class="container very-small py-xl">
        <div class="card content-wrap auto-height">
            <h1 class="list-heading">Mobile App Setup</h1>
            <p>
                To use multi-factor authentication you'll need a mobile application
                that supports TOTP such as Google Authenticator, Authy or Microsoft Authenticator.
            </p>
            <p>
                Scan the QR code below using your preferred authentication app to get started.
            </p>

            <div class="text-center">
                <div class="block inline">
                    {!! $svg !!}
                </div>
            </div>

            <h2 class="list-heading">Verify Setup</h2>
            <p id="totp-verify-input-details" class="mb-s">
                Verify that all is working by entering a code, generated within your
                authentication app, in the input box below:
            </p>
            <form action="{{ url('/mfa/totp/confirm') }}" method="POST">
                {{ csrf_field() }}
                <input type="text"
                       name="code"
                       aria-labelledby="totp-verify-input-details"
                       placeholder="Provide your app generated code here"
                       class="input-fill-width {{ $errors->has('code') ? 'neg' : '' }}">
                @if($errors->has('code'))
                    <div class="text-neg text-small px-xs">{{ $errors->first('code') }}</div>
                @endif
                <div class="mt-s text-right">
                    <a href="{{ url('/mfa/setup') }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button class="button">Confirm and Enable</button>
                </div>
            </form>
        </div>
    </div>

@stop

@extends('simple-layout')

@section('body')

    <div class="container very-small py-xl">
        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('auth.mfa_gen_totp_title') }}</h1>
            <p>{{ trans('auth.mfa_gen_totp_desc') }}</p>
            <p>{{ trans('auth.mfa_gen_totp_scan') }}</p>

            <div class="text-center">
                <div class="block inline">
                    {!! $svg !!}
                </div>
            </div>

            <h2 class="list-heading">{{ trans('auth.mfa_gen_totp_verify_setup') }}</h2>
            <p id="totp-verify-input-details" class="mb-s">{{ trans('auth.mfa_gen_totp_verify_setup_desc') }}</p>
            <form action="{{ url('/mfa/totp/confirm') }}" method="POST">
                {{ csrf_field() }}
                <input type="text"
                       name="code"
                       aria-labelledby="totp-verify-input-details"
                       placeholder="{{ trans('auth.mfa_gen_totp_provide_code_here') }}"
                       class="input-fill-width {{ $errors->has('code') ? 'neg' : '' }}">
                @if($errors->has('code'))
                    <div class="text-neg text-small px-xs">{{ $errors->first('code') }}</div>
                @endif
                <div class="mt-s text-right">
                    <a href="{{ url('/mfa/setup') }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button class="button">{{ trans('auth.mfa_gen_confirm_and_enable') }}</button>
                </div>
            </form>
        </div>
    </div>

@stop

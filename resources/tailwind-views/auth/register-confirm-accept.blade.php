@extends('layouts.simple')

@section('content')

    <div class="container very-small mt-xl">
        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('auth.email_confirm_thanks') }}</h1>

            <p class="mb-none">{{ trans('auth.email_confirm_thanks_desc') }}</p>

            <div class="flex-container-row items-center wrap">
                <div class="flex min-width-s">
                    @include('common.loading-icon')
                </div>
                <div class="flex min-width-s text-s-right">
                    <form component="auto-submit" action="{{ url('/register/confirm/accept') }}" method="post">
                        {{ csrf_field() }}
                        <input type="hidden" name="token" value="{{ $token }}">
                        <button class="text-button">{{ trans('common.continue') }}</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

@stop

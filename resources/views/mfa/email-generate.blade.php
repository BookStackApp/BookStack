@extends('layouts.simple')

@section('body')
    <div class="container very-small py-xl">

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('auth.mfa_verify_access') }}</h1>
            <p class="mb-none">{{ trans('auth.mfa_verify_access_desc') }}</p>

            <hr class="my-l">

            <form action="{{ url('/mfa/email/confirm') }}" method="POST">
                {{ csrf_field() }}
                <div class="mt-s text-right">
                    <a href="{{ url('/login') }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button class="button">{{ trans('auth.mfa_gen_confirm_and_enable') }}</button>
                </div>
            </form>
        </div>
    </div>
@stop

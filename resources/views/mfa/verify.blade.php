@extends('layouts.simple')

@section('body')
    <div class="container very-small py-xl">

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('auth.mfa_verify_access') }}</h1>
            <p class="mb-none">{{ trans('auth.mfa_verify_access_desc') }}</p>

            @if(!$method)
                <hr class="my-l">
                <h5>{{ trans('auth.mfa_verify_no_methods') }}</h5>
                <p class="small">{{ trans('auth.mfa_verify_no_methods_desc') }}</p>
                <div>
                    <a href="{{ url('/mfa/setup') }}" class="button outline">{{ trans('common.configure') }}</a>
                </div>
            @endif

            @if($method)
                <hr class="my-l">
                @include('mfa.parts.verify-' . $method)
            @endif

            @if(count($otherMethods) > 0)
                <hr class="my-l">
                @foreach($otherMethods as $otherMethod)
                    <div class="text-center">
                        <a href="{{ url("/mfa/verify?method={$otherMethod}") }}">{{ trans('auth.mfa_verify_use_' . $otherMethod) }}</a>
                    </div>
                @endforeach
            @endif

        </div>
    </div>
@stop

@extends('simple-layout')

@section('body')
    <div class="container very-small py-xl">

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">Verify Access</h1>
            <p class="mb-none">
                Your user account requires you to confirm your identity via an additional level
                of verification before you're granted access.
                Verify using one of your configured methods to continue.
            </p>

            @if(!$method)
                <hr class="my-l">
                <h5>No Methods Configured</h5>
                <p class="small">
                    No multi-factor authentication methods could be found for your account.
                    You'll need to set up at least one method before you gain access.
                </p>
                <div>
                    <a href="{{ url('/mfa/setup') }}" class="button outline">Configure</a>
                </div>
            @endif


            @if($method)
                <hr class="my-l">
                @include('mfa.verify.' . $method)
            @endif

            @if(count($otherMethods) > 0)
                <hr class="my-l">
                @foreach($otherMethods as $otherMethod)
                    <div class="text-center">
                        <a href="{{ url("/mfa/verify?method={$otherMethod}") }}">{{ trans('auth.mfa_use_' . $otherMethod) }}</a>
                    </div>
                @endforeach
            @endif

        </div>
    </div>
@stop

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
                    <a href="{{ url('/mfa/verify/totp') }}" class="button outline">Configure</a>
                </div>
            @endif

            <div class="setting-list">
                <div class="grid half gap-xl">
                    <div>
                        <div class="setting-list-label">METHOD A</div>
                        <p class="small">
                            ...
                        </p>
                    </div>
                    <div class="pt-m">
                            <a href="{{ url('/mfa/verify/totp') }}" class="button outline">BUTTON</a>
                    </div>
                </div>

            </div>

            @if(count($otherMethods) > 0)
                <hr class="my-l">
                @foreach($otherMethods as $otherMethod)
                    <a href="{{ url("/mfa/verify?method={$otherMethod}") }}">Use {{$otherMethod}}</a>
                @endforeach
            @endif

        </div>
    </div>
@stop

@extends('simple-layout')

@section('body')
    <div class="container small py-xl">

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">Verify Access</h1>
            <p class="mb-none">
                Your user account requires you to confirm your identity via an additional level
                of verification before you're granted access.
                Verify using one of your configure methods to continue.
            </p>

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

        </div>
    </div>
@stop

@extends('public')

@section('header-buttons')
    @if(!$signedIn)
        <a href="/login"><i class="zmdi zmdi-sign-in"></i>Sign in</a>
    @endif
@stop

@section('content')

    <div class="text-center">
        <div class="center-box">
            <h2>Thanks for registering!</h2>
            <p>Please check your email and click the confirmation button to access {{ \Setting::get('app-name') }}.</p>
        </div>
    </div>


@stop

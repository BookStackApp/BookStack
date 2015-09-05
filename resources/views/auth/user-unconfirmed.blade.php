@extends('public')

@section('content')

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <h2>Email Address not confirmed</h2>
            <p class="text-muted">Your email address has not yet been confirmed. <br>
                Please click the link in the email that was sent shortly after you registered. <br>
                If you cannot find the email you can re-send the confirmation email by submitting the form below.
            </p>
            <hr>
            <form action="/register/confirm/resend" method="POST">
                {!! csrf_field() !!}
                <div class="form-group">
                    <label for="email">Email Address</label>
                    @if(auth()->check())
                        @include('form/text', ['name' => 'email', 'model' => auth()->user()])
                    @else
                        @include('form/text', ['name' => 'email'])
                    @endif
                </div>
                <div class="form-group">
                    <button type="submit" class="button pos">Resend Confirmation Email</button>
                </div>
            </form>
        </div>
    </div>

@stop

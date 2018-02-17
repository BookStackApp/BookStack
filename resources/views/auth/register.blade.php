@extends('public')

@section('header-buttons')
    <a href="{{ baseUrl("/login") }}">@icon('login') {{ trans('auth.log_in') }}</a>
@stop

@section('content')

    <div class="text-center">
        <div class="card center-box">
            <h3>@icon('new-user')  {{ title_case(trans('auth.sign_up')) }}</h3>
            <div class="body">
                <form action="{{ baseUrl("/register") }}" method="POST">
                    {!! csrf_field() !!}

                    <div class="form-group">
                        <label for="email">{{ trans('auth.name') }}</label>
                        @include('form/text', ['name' => 'name'])
                    </div>

                    <div class="form-group">
                        <label for="email">{{ trans('auth.email') }}</label>
                        @include('form/text', ['name' => 'email'])
                    </div>

                    <div class="form-group">
                        <label for="password">{{ trans('auth.password') }}</label>
                        @include('form/password', ['name' => 'password', 'placeholder' => trans('auth.password_hint')])
                    </div>

                    <div class="from-group">
                        <button class="button block pos">{{ trans('auth.create_account') }}</button>
                    </div>
                </form>

                @if(count($socialDrivers) > 0)
                    <hr class="margin-top">
                    @foreach($socialDrivers as $driver => $name)
                        <a id="social-register-{{$driver}}" class="button block muted-light svg text-left" href="{{ baseUrl("/register/service/" . $driver) }}">
                            @icon('auth/' . $driver)
                            {{ trans('auth.sign_up_with', ['socialDriver' => $name]) }}
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
    </div>


@stop

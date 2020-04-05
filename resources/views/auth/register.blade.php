@extends('simple-layout')

@section('content')
    <div class="container very-small">

        <div class="my-l">&nbsp;</div>

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ Str::title(trans('auth.sign_up')) }}</h1>

            <form action="{{ url("/register") }}" method="POST" class="mt-l stretch-inputs">
                {!! csrf_field() !!}

                <div class="form-group">
                    <label for="email">{{ trans('auth.name') }}</label>
                    @include('form.text', ['name' => 'name'])
                </div>

                <div class="form-group">
                    <label for="email">{{ trans('auth.email') }}</label>
                    @include('form.text', ['name' => 'email'])
                </div>

                <div class="form-group">
                    <label for="password">{{ trans('auth.password') }}</label>
                    @include('form.password', ['name' => 'password', 'placeholder' => trans('auth.password_hint')])
                </div>

                <div class="grid half collapse-xs gap-xl v-center mt-m">
                    <div class="text-small">
                        <a href="{{ url('/login') }}">{{ trans('auth.already_have_account') }}</a>
                    </div>
                    <div class="from-group text-right">
                        <button class="button">{{ trans('auth.create_account') }}</button>
                    </div>
                </div>


            </form>

            @if(count($socialDrivers) > 0)
                <hr class="my-l">
                @foreach($socialDrivers as $driver => $name)
                    <div>
                        <a id="social-register-{{$driver}}" class="button block outline svg" href="{{ url("/register/service/" . $driver) }}">
                            @icon('auth/' . $driver)
                            <span>{{ trans('auth.sign_up_with', ['socialDriver' => $name]) }}</span>
                        </a>
                    </div>
                @endforeach
            @endif

        </div>
    </div>
@stop

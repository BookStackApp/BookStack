@extends('simple-layout')

@section('content')

    <div class="container very-small">

        <div class="my-l">&nbsp;</div>

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ Str::title(trans('auth.log_in')) }}</h1>

            <form action="{{ url('/login') }}" method="POST" id="login-form" class="mt-l">
                {!! csrf_field() !!}

                <div class="stretch-inputs">
                    @include('auth.forms.login.' . $authMethod)
                </div>

                <div class="grid half collapse-xs gap-xl v-center">
                    <div class="text-left ml-xxs">
                        @include('components.custom-checkbox', [
                            'name' => 'remember',
                            'checked' => false,
                            'value' => 'on',
                            'label' => trans('auth.remember_me'),
                        ])
                    </div>

                    <div class="text-right">
                        <button class="button">{{ Str::title(trans('auth.log_in')) }}</button>
                    </div>
                </div>

            </form>

            @if(count($socialDrivers) > 0)
                <hr class="my-l">
                @foreach($socialDrivers as $driver => $name)
                    <div>
                        <a id="social-login-{{$driver}}" class="button outline block svg" href="{{ url("/login/service/" . $driver) }}">
                            @icon('auth/' . $driver)
                            {{ trans('auth.log_in_with', ['socialDriver' => $name]) }}
                        </a>
                    </div>
                @endforeach
            @endif

            @if($samlEnabled)
              <hr class="my-l">
              <div>
                  <a id="saml-login" class="button outline block svg" href="{{ url("/saml2/login") }}">
                      {{-- @icon('auth/github') --}}
                      {{ trans('auth.log_in_with', ['socialDriver' => config('services.saml.name')]) }}
                  </a>
              </div>
            @endif

            @if(setting('registration-enabled', false))
                <div class="text-center pb-s">
                    <hr class="my-l">
                    <a href="{{ url('/register') }}">{{ trans('auth.dont_have_account') }}</a>
                </div>
            @endif
        </div>
    </div>

@stop

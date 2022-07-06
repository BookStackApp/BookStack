@extends('layouts.simple')
@section('content')
    <div class="container very-small">
        <div class="my-l">&nbsp;</div>
<!-- 
        <div class="card content-wrap auto-height"> -->
           
            @include('auth.parts.login-form-' . $authMethod)

            @if(count($socialDrivers) > 0)
                <hr class="my-l">
                @foreach($socialDrivers as $driver => $name)
                    <div style="border: 13px solid #FBF4F4;">
                        <a id="social-login-{{$driver}}" class="button outline svg" href="{{ url("/login/service/" . $driver) }}">
                            @icon('auth/' . $driver)
                            <span>{{ trans('auth.log_in_with', ['socialDriver' => $name]) }}</span>
                        </a>
                    </div>
                @endforeach
            @endif


            @if(setting('registration-enabled') && config('auth.method') === 'standard')
                <div class="text-center pb-s">
                    <hr class="my-l">
                    <a href="{{ url('/register') }}">{{ trans('auth.dont_have_account') }}</a>
                </div>
            @endif
        
    </div>

@stop

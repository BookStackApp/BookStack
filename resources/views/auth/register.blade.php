@extends('layouts.simple')

@section('content')
@include('common/nci_custom_styles')
    <!-- <div class="container very-small">

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
                        <a id="social-register-{{$driver}}" class="button outline svg" href="{{ url("/register/service/" . $driver) }}">
                            @icon('auth/' . $driver)
                            <span>{{ trans('auth.sign_up_with', ['socialDriver' => $name]) }}</span>
                        </a>
                    </div>
                @endforeach
            @endif

        </div>
    </div> -->
    <div class="signup">
<div class="signup-classic" style="  border: 13px solid #FBF4F4;">
<h1 class="list-heading">{{ Str::title(trans('auth.register')) }}</h1>
<form action="{{ url('/register') }}" method="POST" class="mt-l stretch-inputs">
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
                    <label style="display: inline" for="designation">{{ trans('auth.designation') }}</label>
                    <select style="display: inline"class="form-select" id="inputGroupSelect01" name="designation">
                    <option selected disabled>Choose...designation</option>
                    <option value="1">Admin</option>
                    <option value="2">Editor</option>
                    <option value="3">Public</option>
                </select>
                </div>
                <div class="form-group">
                    <label for="country">{{ trans('auth.county') }}</label>
                    <select class="form-select" id="inputGroupSelect01" name="country">
                        <option  selected disabled>Choose...country</option>
                        <option value="1">Kenya</option>
                        <option value="2">Uganda</option>
                        <option value="3">Tanzania</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="password">{{ trans('auth.password') }}</label>
                    @include('form.password', ['name' => 'password', 'placeholder' => trans('auth.password_hint')])
                </div>
                <div class="form-group">
                    <label for="confirm_password">{{ trans('auth.confirm_password') }}</label>
                    @include('form.password', ['name' => 'password_confirmed', 'placeholder' => trans('auth.confirm_password')])
                </div>
                <div class=" row grid half collapse-xs gap-xl v-center mt-m">
                    <div class="col from-group text-right" styl>
                        <button class="button" style="background-color: #D820C5;border:none">{{ trans('auth.create_account') }}</button><br>
                        Already have an account?<a href="{{ url('/login') }}">{{ trans('auth.already_have_account') }}</a>

                    </div>
                </div>

            </form>
  </div>
  <div class="signup-connect">
  <h1 class="list-heading">{{ Str::title(trans('auth.welcome')) }}</h1>
  <div class="sidestyle">
  <img src="{{ asset('/uploads/welcome.png') }}" width="420" height="350">
    <h4 class="b-title">National Guidelines for
    Establishment of Cancer
    Management Centers in
    Kenya</h4>
  </div>
  </div>
</div>
@stop

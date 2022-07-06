<!-- <form action="{{ url('/login') }}" method="POST" id="login-form" class="mt-l">
    {!! csrf_field() !!}

    <div class="stretch-inputs">
        <div class="form-group">
            <label for="email">{{ trans('auth.email') }}</label>
            @include('form.text', ['name' => 'email', 'autofocus' => true])
        </div>
        <div class="form-group">
            <label for="password">{{ trans('auth.password') }}</label>
            @include('form.password', ['name' => 'password'])
            <div class="small mt-s">
                <a href="{{ url('/password/email') }}">{{ trans('auth.forgot_password') }}</a>
            </div>
        </div>
    </div>

    <div class="grid half collapse-xs gap-xl v-center">
        <div class="text-left ml-xxs">
            @include('form.custom-checkbox', [
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

</form> -->
<div class="signup">
<div class="signup-classic">
<h1 class="list-heading">{{ Str::title(trans('auth.log_in')) }}</h1>
<form action="{{ url('/login') }}" method="POST" id="login-form" class="mt-l form">
    {!! csrf_field() !!}
      <fieldset  class="email mt-5">{{ trans('auth.email') }}
        <input id="infield" type="text" placeholder="Email" name="email" autofocus='true'/>
      </fieldset>
      <!-- <fieldset class="email">
        <input id="infield" type="email" placeholder="email" />
      </fieldset> -->
      <fieldset class="password">{{ trans('auth.password') }}
      <input id="infield" type="password" placeholder="password" name="password" />
      </fieldset>
      <a style="float:right" style="margin-bottom:-2px;"href="{{ url('/password/email') }}">Forgot password?</a>
      <button type="submit" class="btn btn-sm" style="width:150px;text-align:center" >{{trans('auth.submit')}}</button>
      <p>Don’t have an account? <a href="{{ url('/register') }}">{{trans('auth.register')}}</a></p>
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



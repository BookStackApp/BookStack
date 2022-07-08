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


<div class="signup row">
<div class="signup-classic col-md-6" style="  border: 13px solid #FBF4F4;">
<h1 class="list-heading">{{ Str::title(trans('auth.log_in')) }}</h1>
<form action="{{ url('/login') }}" method="POST" style="margin-top:70px" class="mt-70 stretch-inputs">
                {!! csrf_field() !!}

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label"  for="email">{{ trans('auth.email') }}:</label>
                   <div class="col-sm-10">
                   @include('form.text', ['name' => 'email'])
                   </div>
                </div>

               
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label" for="password">{{ trans('auth.password') }}:</label>
                    <div class="col-sm-10">
                    @include('form.password', ['name' => 'password', 'placeholder' => trans('auth.password_hint')])

                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-sm-12">
                    <a style="float:right"  style="margin-bottom:-2px;"href="{{ url('/password/email') }}">Forgot password?</a>

                    </div>
                </div>
               
                <div class="form-group row">
                    <div class="col-sm-12" style="margin-left:180px;margin-right:50px">
                        <button class="button" style="background-color: #D820C5;border:none;border-radius:10px;width:100px">{{trans('auth.submit')}}</button>
                        <p  style="margin-bottom:2px;">Don’t have an account? <a href="{{ url('/register') }}">{{trans('auth.register')}}</a></p>

                    </div>
                </div>

            </form>
  </div>
  <div class="signup-connect col-md-6">
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

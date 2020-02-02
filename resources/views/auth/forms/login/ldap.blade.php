<form action="{{ url('/login') }}" method="POST" id="login-form" class="mt-l">
    {!! csrf_field() !!}

    <div class="stretch-inputs">
        <div class="form-group">
            <label for="username">{{ trans('auth.username') }}</label>
            @include('form.text', ['name' => 'username', 'autofocus' => true])
        </div>

        @if(session('request-email', false) === true)
            <div class="form-group">
                <label for="email">{{ trans('auth.email') }}</label>
                @include('form.text', ['name' => 'email'])
                <span class="text-neg">{{ trans('auth.ldap_email_hint') }}</span>
            </div>
        @endif

        <div class="form-group">
            <label for="password">{{ trans('auth.password') }}</label>
            @include('form.password', ['name' => 'password'])
        </div>

        <div class="form-group text-right pt-s">
            <button class="button">{{ Str::title(trans('auth.log_in')) }}</button>
        </div>
    </div>

</form>
<div class="form-group">
    <label for="username">{{ trans('auth.username') }}</label>
    @include('form.text', ['name' => 'username', 'autofocus' => true])
</div>

@if(session('request-email', false) === true)
    <div class="form-group">
        <label for="email">{{ trans('auth.email') }}</label>
        @include('form.text', ['name' => 'email'])
        <span class="text-neg">
            {{ trans('auth.ldap_email_hint') }}
        </span>
    </div>
@endif

<div class="form-group">
    <label for="password">{{ trans('auth.password') }}</label>
    @include('form.password', ['name' => 'password'])
</div>
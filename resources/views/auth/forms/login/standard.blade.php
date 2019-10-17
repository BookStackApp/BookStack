<div class="form-group">
    <label for="email">{{ trans('auth.email') }}</label>
    @include('form.text', ['name' => 'email', 'autofocus' => true])
</div>

<div class="form-group">
    <label for="password">{{ trans('auth.password') }}</label>
    @include('form.password', ['name' => 'password'])
    <span class="block small mt-s">
        <a href="{{ url('/password/email') }}">{{ trans('auth.forgot_password') }}</a>
    </span>
</div>

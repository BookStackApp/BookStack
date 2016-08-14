<div class="form-group">
    <label for="email">Email</label>
    @include('form/text', ['name' => 'email', 'tabindex' => 1])
</div>

<div class="form-group">
    <label for="password">Password</label>
    @include('form/password', ['name' => 'password', 'tabindex' => 2])
    <span class="block small"><a href="{{ baseUrl('/password/email') }}">Forgot Password?</a></span>
</div>
<div class="form-group">
    <label for="username">Username</label>
    @include('form/text', ['name' => 'username', 'tabindex' => 1])
</div>

<div class="form-group">
    <label for="password">Password</label>
    @include('form/password', ['name' => 'password', 'tabindex' => 2])
</div>
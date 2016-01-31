<div class="form-group">
    <label for="username">Username</label>
    @include('form/text', ['name' => 'username', 'tabindex' => 1])
</div>

@if(session('request-email', false) === true)
    <div class="form-group">
        <label for="email">Email</label>
        @include('form/text', ['name' => 'email', 'tabindex' => 1])
        <span class="text-neg">
            Please enter an email to use for this account.
        </span>
    </div>
@endif

<div class="form-group">
    <label for="password">Password</label>
    @include('form/password', ['name' => 'password', 'tabindex' => 2])
</div>
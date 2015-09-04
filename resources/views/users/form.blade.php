<div class="form-group">
    <label for="name">Name</label>
    @include('form/text', ['name' => 'name'])
</div>

<div class="form-group">
    <label for="email">Email</label>
    @include('form/text', ['name' => 'email'])
</div>

@if($currentUser->can('user-update'))
    <div class="form-group">
        <label for="role">User Role</label>
        @include('form.role-select', ['name' => 'role', 'options' => \Oxbow\Role::all(), 'displayKey' => 'display_name'])
    </div>
@endif

@if(isset($model))
    <div class="form-group">
        <span class="text-muted">
            Only fill the below if you would like <br>to change your password:
        </span>
    </div>
@endif

<div class="form-group">
    <label for="password">Password</label>
    @include('form/password', ['name' => 'password'])
</div>

<div class="form-group">
    <label for="password-confirm">Confirm Password</label>
    @include('form/password', ['name' => 'password-confirm'])
</div>

<div class="form-group">
    <a href="/users" class="button muted">Cancel</a>
    <button class="button pos" type="submit">Save</button>
</div>
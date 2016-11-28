@if($user->system_name == 'public')
    <p>This user represents any guest users that visit your instance. It cannot be used for logins but is assigned&nbsp;automatically.</p>
@endif

<div class="form-group">
    <label for="name">Name</label>
    @include('form.text', ['name' => 'name'])
</div>

<div class="form-group">
    <label for="email">Email</label>
    @include('form.text', ['name' => 'email'])
</div>

@if(userCan('users-manage'))
    <div class="form-group">
        <label for="role">User Role</label>
        @include('form/role-checkboxes', ['name' => 'roles', 'roles' => $roles])
    </div>
@endif

<div class="form-group">
    <a href="{{ baseUrl("/settings/users") }}" class="button muted">Cancel</a>
    <button class="button pos" type="submit">Save</button>
</div>

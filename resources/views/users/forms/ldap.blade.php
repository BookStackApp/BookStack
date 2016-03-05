<div class="form-group">
    <label for="name">Name</label>
    @include('form.text', ['name' => 'name'])
</div>

@if(userCan('users-manage'))
<div class="form-group">
    <label for="email">Email</label>
    @include('form.text', ['name' => 'email'])
</div>
@endif

@if(userCan('users-manage'))
    <div class="form-group">
        <label for="role">User Role</label>
        @include('form/role-checkboxes', ['name' => 'roles', 'roles' => \BookStack\Role::all()])
    </div>
@endif

@if(userCan('users-manage'))
    <div class="form-group">
        <label for="external_auth_id">External Authentication ID</label>
        @include('form.text', ['name' => 'external_auth_id'])
    </div>
@endif

<div class="form-group">
    <a href="/settings/users" class="button muted">Cancel</a>
    <button class="button pos" type="submit">Save</button>
</div>
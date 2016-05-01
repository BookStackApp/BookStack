{!! csrf_field() !!}

<div class="row">

    <div class="col-md-9">
        <div class="row">
            <div class="col-md-5">
                <h3>Role Details</h3>
                <div class="form-group">
                    <label for="name">Role Name</label>
                    @include('form/text', ['name' => 'display_name'])
                </div>
                <div class="form-group">
                    <label for="name">Short Role Description</label>
                    @include('form/text', ['name' => 'description'])
                </div>
                <h3>System Permissions</h3>
                <label>@include('settings/roles/checkbox', ['permission' => 'users-manage']) Manage users</label>
                <label>@include('settings/roles/checkbox', ['permission' => 'user-roles-manage']) Manage roles & role permissions</label>
                <label>@include('settings/roles/checkbox', ['permission' => 'restrictions-manage-all']) Manage all Book, Chapter & Page permissions</label>
                <label>@include('settings/roles/checkbox', ['permission' => 'restrictions-manage-own']) Manage permissions on own Book, Chapter & Pages</label>
                <label>@include('settings/roles/checkbox', ['permission' => 'settings-manage']) Manage app settings</label>
            </div>

            <div class="col-md-6">

                <h3>Asset Permissions</h3>
                <p>
                    These permissions control default access to the assets within the system.
                    Permissions on Books, Chapters and Pages will override these permissions.
                </p>
                <table class="table">
                    <tr>
                        <th width="20%"></th>
                        <th width="20%">Create</th>
                        <th width="20%">View</th>
                        <th width="20%">Edit</th>
                        <th width="20%">Delete</th>
                    </tr>
                    <tr>
                        <td>Books</td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'book-create-all']) All</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'book-view-own']) Own</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'book-view-all']) All</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'book-update-own']) Own</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'book-update-all']) All</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'book-delete-own']) Own</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'book-delete-all']) All</label>
                        </td>
                    </tr>
                    <tr>
                        <td>Chapters</td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'chapter-create-own']) Own</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'chapter-create-all']) All</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'chapter-view-own']) Own</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'chapter-view-all']) All</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'chapter-update-own']) Own</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'chapter-update-all']) All</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'chapter-delete-own']) Own</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'chapter-delete-all']) All</label>
                        </td>
                    </tr>
                    <tr>
                        <td>Pages</td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'page-create-own']) Own</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'page-create-all']) All</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'page-view-own']) Own</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'page-view-all']) All</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'page-update-own']) Own</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'page-update-all']) All</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'page-delete-own']) Own</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'page-delete-all']) All</label>
                        </td>
                    </tr>
                    <tr>
                        <td>Images</td>
                        <td>@include('settings/roles/checkbox', ['permission' => 'image-create-all'])</td>
                        <td style="line-height:1.2;"><small class="faded">Controlled by the asset they are uploaded to</small></td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'image-update-own']) Own</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'image-update-all']) All</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'image-delete-own']) Own</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'image-delete-all']) All</label>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <a href="/settings/roles" class="button muted">Cancel</a>
        <button type="submit" class="button pos">Save Role</button>
    </div>
    <div class="col-md-3">
        <h3>Users in this role</h3>

        @if(isset($role) && count($role->users) > 0)
        <table class="list-table">
            @foreach($role->users as $user)
                <tr>
                    <td style="line-height: 0;"><img class="avatar small" src="{{$user->getAvatar(40)}}" alt="{{$user->name}}"></td>
                    <td>
                        @if(userCan('users-manage') || $currentUser->id == $user->id)
                            <a href="/settings/users/{{$user->id}}">
                                @endif
                                {{ $user->name }}
                                @if(userCan('users-manage') || $currentUser->id == $user->id)
                            </a>
                        @endif
                    </td>
                </tr>
            @endforeach
        </table>
        @else
            <p class="text-muted">
                No users currently in this role.
            </p>
        @endif

    </div>



</div>
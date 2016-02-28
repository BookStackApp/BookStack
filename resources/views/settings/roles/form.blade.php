{!! csrf_field() !!}

<div class="row">

    <div class="col-md-6">
        <div class="form-group">
            <label for="name">Role Name</label>
            @include('form/text', ['name' => 'display_name'])
        </div>
        <div class="form-group">
            <label for="name">Short Role Description</label>
            @include('form/text', ['name' => 'description'])
        </div>
        <hr class="even">
        <div class="row">
            <div class="col-md-6">
                <label> @include('settings/roles/checkbox', ['permission' => 'users-manage']) Manage users</label>
            </div>
            <div class="col-md-6">
                <label>@include('settings/roles/checkbox', ['permission' => 'user-roles-manage']) Manage user roles & Permissions</label>
            </div>
        </div>
        <hr class="even">
        <div class="row">
            <div class="col-md-6">
                <label>@include('settings/roles/checkbox', ['permission' => 'restrictions-manage-all']) Manage all restrictions</label>
            </div>
            <div class="col-md-6">
                <label>@include('settings/roles/checkbox', ['permission' => 'restrictions-manage-own']) Manage restrictions on own content</label>
            </div>
        </div>
        <hr class="even">
        <div class="form-group">
            <label>@include('settings/roles/checkbox', ['permission' => 'settings-manage']) Manage app settings</label>
        </div>

    </div>

    <div class="col-md-6">
        <table class="table">
            <tr>
                <th></th>
                <th>Create</th>
                <th>Edit</th>
                <th>Delete</th>
            </tr>
            <tr>
                <td>Books</td>
                <td>
                    <label>@include('settings/roles/checkbox', ['permission' => 'book-create-all']) All</label>
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
<button type="submit" class="button pos">Save Role</button>
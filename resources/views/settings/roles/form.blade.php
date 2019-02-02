{!! csrf_field() !!}

{{--TODO - Add select-all shortcuts--}}

<div class="grid left-focus large-gap">

    <div class="card content-wrap fill-width">
        <h1 class="list-heading">{{ $title }}</h1>

        <div class="setting-list">

            <div class="grid half">
                <div>
                    <label class="setting-list-label">{{ trans('settings.role_details') }}</label>
                </div>
                <div>
                    <div class="form-group">
                        <label for="name">{{ trans('settings.role_name') }}</label>
                        @include('form/text', ['name' => 'display_name'])
                    </div>
                    <div class="form-group">
                        <label for="name">{{ trans('settings.role_desc') }}</label>
                        @include('form/text', ['name' => 'description'])
                    </div>

                    @if(config('auth.method') === 'ldap')
                        <div class="form-group">
                            <label for="name">{{ trans('settings.role_external_auth_id') }}</label>
                            @include('form/text', ['name' => 'external_auth_id'])
                        </div>
                    @endif
                </div>
            </div>

            <div class="grid half">
                <div>
                    <label class="setting-list-label">{{ trans('settings.role_system') }}</label>
                </div>
                <div>
                    <label>@include('settings/roles/checkbox', ['permission' => 'users-manage']) {{ trans('settings.role_manage_users') }}</label>
                    <label>@include('settings/roles/checkbox', ['permission' => 'user-roles-manage']) {{ trans('settings.role_manage_roles') }}</label>
                    <label>@include('settings/roles/checkbox', ['permission' => 'restrictions-manage-all']) {{ trans('settings.role_manage_entity_permissions') }}</label>
                    <label>@include('settings/roles/checkbox', ['permission' => 'restrictions-manage-own']) {{ trans('settings.role_manage_own_entity_permissions') }}</label>
                    <label>@include('settings/roles/checkbox', ['permission' => 'settings-manage']) {{ trans('settings.role_manage_settings') }}</label>
                </div>
            </div>

            <div>
                <label class="setting-list-label">{{ trans('settings.role_asset') }}</label>
                <p>{{ trans('settings.role_asset_desc') }}</p>

                @if (isset($role) && $role->system_name === 'admin')
                    <p>{{ trans('settings.role_asset_admins') }}</p>
                @endif

                <table class="table">
                    <tr>
                        <th width="20%"></th>
                        <th width="20%">{{ trans('common.create') }}</th>
                        <th width="20%">{{ trans('common.view') }}</th>
                        <th width="20%">{{ trans('common.edit') }}</th>
                        <th width="20%">{{ trans('common.delete') }}</th>
                    </tr>
                    <tr>
                        <td>{{ trans('entities.shelves_long') }}</td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'bookshelf-create-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'bookshelf-view-own']) {{ trans('settings.role_own') }}</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'bookshelf-view-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'bookshelf-update-own']) {{ trans('settings.role_own') }}</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'bookshelf-update-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'bookshelf-delete-own']) {{ trans('settings.role_own') }}</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'bookshelf-delete-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('entities.books') }}</td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'book-create-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'book-view-own']) {{ trans('settings.role_own') }}</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'book-view-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'book-update-own']) {{ trans('settings.role_own') }}</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'book-update-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'book-delete-own']) {{ trans('settings.role_own') }}</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'book-delete-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('entities.chapters') }}</td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'chapter-create-own']) {{ trans('settings.role_own') }}</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'chapter-create-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'chapter-view-own']) {{ trans('settings.role_own') }}</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'chapter-view-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'chapter-update-own']) {{ trans('settings.role_own') }}</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'chapter-update-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'chapter-delete-own']) {{ trans('settings.role_own') }}</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'chapter-delete-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('entities.pages') }}</td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'page-create-own']) {{ trans('settings.role_own') }}</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'page-create-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'page-view-own']) {{ trans('settings.role_own') }}</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'page-view-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'page-update-own']) {{ trans('settings.role_own') }}</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'page-update-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'page-delete-own']) {{ trans('settings.role_own') }}</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'page-delete-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('entities.images') }}</td>
                        <td>@include('settings/roles/checkbox', ['permission' => 'image-create-all'])</td>
                        <td style="line-height:1.2;"><small class="faded">{{ trans('settings.role_controlled_by_asset') }}</small></td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'image-update-own']) {{ trans('settings.role_own') }}</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'image-update-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'image-delete-own']) {{ trans('settings.role_own') }}</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'image-delete-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('entities.attachments') }}</td>
                        <td>@include('settings/roles/checkbox', ['permission' => 'attachment-create-all'])</td>
                        <td style="line-height:1.2;"><small class="faded">{{ trans('settings.role_controlled_by_asset') }}</small></td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'attachment-update-own']) {{ trans('settings.role_own') }}</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'attachment-update-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'attachment-delete-own']) {{ trans('settings.role_own') }}</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'attachment-delete-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                    </tr>
                    <tr>
                        <td>{{ trans('entities.comments') }}</td>
                        <td>@include('settings/roles/checkbox', ['permission' => 'comment-create-all'])</td>
                        <td style="line-height:1.2;"><small class="faded">{{ trans('settings.role_controlled_by_asset') }}</small></td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'comment-update-own']) {{ trans('settings.role_own') }}</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'comment-update-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                        <td>
                            <label>@include('settings/roles/checkbox', ['permission' => 'comment-delete-own']) {{ trans('settings.role_own') }}</label>
                            <label>@include('settings/roles/checkbox', ['permission' => 'comment-delete-all']) {{ trans('settings.role_all') }}</label>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="form-group text-right">
            <a href="{{ baseUrl("/settings/roles") }}" class="button outline">{{ trans('common.cancel') }}</a>
            @if (isset($role) && $role->id)
                <a href="{{ baseUrl("/settings/roles/delete/{$role->id}") }}" class="button outline">{{ trans('settings.role_delete') }}</a>
            @endif
            <button type="submit" class="button primary">{{ trans('settings.role_save') }}</button>
        </div>

    </div>

    <div>
        <div class="auto-height fill-width px-l">
            <h2 class="list-heading">{{ trans('settings.role_users') }}</h2>
            @if(isset($role) && count($role->users) > 0)
                <table class="list-table">
                    @foreach($role->users as $user)
                        <tr>
                            <td style="line-height: 0;"><img class="avatar small" src="{{ $user->getAvatar(40) }}" alt="{{ $user->name }}"></td>
                            <td>
                                @if(userCan('users-manage') || $currentUser->id == $user->id)
                                    <a href="{{ baseUrl("/settings/users/{$user->id}") }}">
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
                    {{ trans('settings.role_users_none') }}
                </p>
            @endif
        </div>
    </div>
</div>
@extends('users.account.layout')

@section('main')
    <section class="card content-wrap">
        <form action="{{ url('/my-account/shortcuts') }}" method="post">
            {{ method_field('put') }}
            {{ csrf_field() }}

            <h1 class="list-heading">{{ trans('preferences.shortcuts_interface') }}</h1>

            <div class="flex-container-row items-center gap-m wrap mb-m">
                <p class="flex mb-none min-width-m text-small text-muted">
                    {{ trans('preferences.shortcuts_toggle_desc') }}
                    {{ trans('preferences.shortcuts_customize_desc') }}
                </p>
                <div class="flex min-width-m text-m-center">
                    @include('form.toggle-switch', [
                        'name' => 'enabled',
                        'value' => $enabled,
                        'label' => trans('preferences.shortcuts_toggle_label'),
                    ])
                </div>
            </div>

            <hr>

            <h2 class="list-heading mb-m">{{ trans('preferences.shortcuts_section_navigation') }}</h2>
            <div class="flex-container-row wrap gap-m mb-xl">
                <div class="flex min-width-l item-list">
                    @include('users.account.parts.shortcut-control', ['label' => trans('common.homepage'), 'id' => 'home_view'])
                    @include('users.account.parts.shortcut-control', ['label' => trans('entities.shelves'), 'id' => 'shelves_view'])
                    @include('users.account.parts.shortcut-control', ['label' => trans('entities.books'), 'id' => 'books_view'])
                    @include('users.account.parts.shortcut-control', ['label' => trans('settings.settings'), 'id' => 'settings_view'])
                    @include('users.account.parts.shortcut-control', ['label' => trans('entities.my_favourites'), 'id' => 'favourites_view'])
                </div>
                <div class="flex min-width-l item-list">
                    @include('users.account.parts.shortcut-control', ['label' => trans('common.view_profile'), 'id' => 'profile_view'])
                    @include('users.account.parts.shortcut-control', ['label' => trans('auth.logout'), 'id' => 'logout'])
                    @include('users.account.parts.shortcut-control', ['label' => trans('common.global_search'), 'id' => 'global_search'])
                    @include('users.account.parts.shortcut-control', ['label' => trans('common.next'), 'id' => 'next'])
                    @include('users.account.parts.shortcut-control', ['label' => trans('common.previous'), 'id' => 'previous'])
                </div>
            </div>

            <h2 class="list-heading mb-m">{{ trans('preferences.shortcuts_section_actions') }}</h2>
            <div class="flex-container-row wrap gap-m mb-xl">
                <div class="flex min-width-l item-list">
                    @include('users.account.parts.shortcut-control', ['label' => trans('common.new'), 'id' => 'new'])
                    @include('users.account.parts.shortcut-control', ['label' => trans('common.edit'), 'id' => 'edit'])
                    @include('users.account.parts.shortcut-control', ['label' => trans('common.copy'), 'id' => 'copy'])
                    @include('users.account.parts.shortcut-control', ['label' => trans('common.delete'), 'id' => 'delete'])
                    @include('users.account.parts.shortcut-control', ['label' => trans('common.favourite'), 'id' => 'favourite'])
                </div>
                <div class="flex min-width-l item-list">
                    @include('users.account.parts.shortcut-control', ['label' => trans('entities.export'), 'id' => 'export'])
                    @include('users.account.parts.shortcut-control', ['label' => trans('common.sort'), 'id' => 'sort'])
                    @include('users.account.parts.shortcut-control', ['label' => trans('entities.permissions'), 'id' => 'permissions'])
                    @include('users.account.parts.shortcut-control', ['label' => trans('common.move'), 'id' => 'move'])
                    @include('users.account.parts.shortcut-control', ['label' => trans('entities.revisions'), 'id' => 'revisions'])
                </div>
            </div>

            <p class="text-small text-muted">{{ trans('preferences.shortcuts_overlay_desc') }}</p>

            <div class="form-group text-right">
                <button class="button">{{ trans('preferences.shortcuts_save') }}</button>
            </div>

        </form>
    </section>
@stop

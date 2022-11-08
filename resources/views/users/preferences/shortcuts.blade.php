@extends('layouts.simple')

@section('body')
    <div class="container small my-xl">

        <section class="card content-wrap">
            <form action="{{ url('/preferences/shortcuts') }}" method="post">
                {{ method_field('put') }}
                {{ csrf_field() }}

                <h1 class="list-heading">Interface Keyboard Shortcuts</h1>

                <div class="flex-container-row items-center gap-m wrap mb-m">
                    <p class="flex mb-none min-width-m text-small text-muted">
                        Here you can enable or disable keyboard system interface shortcuts, used for navigation
                        and actions. You can customize each of the shortcuts below.
                    </p>
                    <div class="flex min-width-m text-m-right">
                        @include('form.toggle-switch', [
                            'name' => 'enabled',
                            'value' => $enabled,
                            'label' => 'Keyboard shortcuts enabled',
                        ])
                    </div>
                </div>

                <hr>

                <h2 class="list-heading mb-m">Navigation</h2>
                <div class="flex-container-row wrap gap-m mb-xl">
                    <div class="flex min-width-l item-list">
                        @include('users.preferences.parts.shortcut-control', ['label' => 'Homepage', 'id' => 'home_view'])
                        @include('users.preferences.parts.shortcut-control', ['label' => trans('entities.shelves'), 'id' => 'shelves_view'])
                        @include('users.preferences.parts.shortcut-control', ['label' => trans('entities.books'), 'id' => 'books_view'])
                        @include('users.preferences.parts.shortcut-control', ['label' => trans('settings.settings'), 'id' => 'settings_view'])
                        @include('users.preferences.parts.shortcut-control', ['label' => trans('entities.my_favourites'), 'id' => 'favourites_view'])
                    </div>
                    <div class="flex min-width-l item-list">
                        @include('users.preferences.parts.shortcut-control', ['label' => trans('common.view_profile'), 'id' => 'profile_view'])
                        @include('users.preferences.parts.shortcut-control', ['label' => trans('auth.logout'), 'id' => 'logout'])
                        @include('users.preferences.parts.shortcut-control', ['label' => 'Global Search', 'id' => 'global_search'])
                        @include('users.preferences.parts.shortcut-control', ['label' => trans('common.next'), 'id' => 'next'])
                        @include('users.preferences.parts.shortcut-control', ['label' => trans('common.previous'), 'id' => 'previous'])
                    </div>
                </div>

                <h2 class="list-heading mb-m">Common Actions</h2>
                <div class="flex-container-row wrap gap-m mb-xl">
                    <div class="flex min-width-l item-list">
                        @include('users.preferences.parts.shortcut-control', ['label' => trans('common.new'), 'id' => 'new'])
                        @include('users.preferences.parts.shortcut-control', ['label' => trans('common.edit'), 'id' => 'edit'])
                        @include('users.preferences.parts.shortcut-control', ['label' => trans('common.copy'), 'id' => 'copy'])
                        @include('users.preferences.parts.shortcut-control', ['label' => trans('common.delete'), 'id' => 'delete'])
                        @include('users.preferences.parts.shortcut-control', ['label' => trans('common.favourite'), 'id' => 'favourite'])
                    </div>
                    <div class="flex min-width-l item-list">
                        @include('users.preferences.parts.shortcut-control', ['label' => trans('entities.export'), 'id' => 'export'])
                        @include('users.preferences.parts.shortcut-control', ['label' => trans('common.sort'), 'id' => 'sort'])
                        @include('users.preferences.parts.shortcut-control', ['label' => trans('entities.permissions'), 'id' => 'permissions'])
                        @include('users.preferences.parts.shortcut-control', ['label' => trans('common.move'), 'id' => 'move'])
                        @include('users.preferences.parts.shortcut-control', ['label' => trans('entities.revisions'), 'id' => 'revisions'])
                    </div>
                </div>

                <p class="text-small text-muted">
                    Note: When shortcuts are enabled a helper overlay is available via pressing "?" which will
                    highlight the available shortcuts for actions currently visible on the screen.
                </p>

                <div class="form-group text-right">
                    <button class="button">{{ 'Save Shortcuts' }}</button>
                </div>

            </form>
        </section>

    </div>
@stop

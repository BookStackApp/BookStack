@extends('layouts.simple')

@section('body')

    <div class="container small">

        @include('settings.parts.navbar', ['selected' => 'roles'])

        <div class="card content-wrap auto-height">

            <div class="grid half v-center">
                <h1 class="list-heading">{{ trans('settings.role_user_roles') }}</h1>

                <div class="text-right">
                    <a href="{{ url("/settings/roles/new") }}" class="button outline my-none">{{ trans('settings.role_create') }}</a>
                </div>
            </div>

            <p class="text-muted">{{ trans('settings.roles_index_desc') }}</p>

            <div class="flex-container-row items-center justify-space-between gap-m mt-m mb-l wrap">
                <div>
                    <div class="block inline mr-xs">
                        <form method="get" action="{{ url("/settings/roles") }}">
                            <input type="text"
                                   name="search"
                                   placeholder="{{ trans('common.search') }}"
                                   value="{{ $listOptions->getSearch() }}">
                        </form>
                    </div>
                </div>
                <div class="justify-flex-end">
                    @include('common.sort', $listOptions->getSortControlData())
                </div>
            </div>

            <div class="item-list">
                @foreach($roles as $role)
                    @include('settings.roles.parts.roles-list-item', ['role' => $role])
                @endforeach
            </div>

            <div class="mb-m">
                {{ $roles->links() }}
            </div>

        </div>
    </div>

@stop

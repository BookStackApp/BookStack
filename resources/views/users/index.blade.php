@extends('layouts.simple')

@section('body')
    <div class="container small">

        @include('settings.parts.navbar', ['selected' => 'users'])

        <main class="card content-wrap">

            <div class="flex-container-row wrap justify-space-between items-center">
                <h1 class="list-heading">{{ trans('settings.users') }}</h1>
                <div>
                    <a href="{{ url("/settings/users/create") }}" class="outline button my-none">{{ trans('settings.users_add_new') }}</a>
                </div>
            </div>

            <p class="text-muted">{{ trans('settings.users_index_desc') }}</p>

            <div class="flex-container-row items-center justify-space-between gap-m mt-m mb-l wrap">
                <div>
                    <div class="block inline mr-xs">
                        <form method="get" action="{{ url("/settings/users") }}">
                            <input type="text"
                                   name="search"
                                   placeholder="{{ trans('settings.users_search') }}"
                                   value="{{ $listOptions->getSearch() }}">
                        </form>
                    </div>
                </div>
                <div class="justify-flex-end">
                    @include('common.sort', $listOptions->getSortControlData())
                </div>
            </div>

            <div class="item-list">
                @foreach($users as $user)
                    @include('users.parts.users-list-item', ['user' => $user])
                @endforeach
            </div>

            <div>
                {{ $users->links() }}
            </div>
        </main>

    </div>

@stop

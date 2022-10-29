@extends('layouts.simple')

@section('body')
    <div class="container small">

        @include('settings.parts.navbar', ['selected' => 'users'])

        <main class="card content-wrap">

            <div class="flex-container-row wrap justify-space-between items-center">
                <h1 class="list-heading">{{ trans('settings.users') }}</h1>
                <div>
                    <a href="{{ url("/settings/users/create") }}" class="outline button mt-none">{{ trans('settings.users_add_new') }}</a>
                </div>
            </div>

            <p class="text-muted">{{ trans('settings.users_index_desc') }}</p>

            <div class="flex-container-row items-center justify-space-between gap-m mt-m mb-l wrap">
                <div>
                    <div class="block inline mr-xs">
                        <form method="get" action="{{ url("/settings/users") }}">
                            <input type="text" name="search" placeholder="{{ trans('settings.users_search') }}" @if($listDetails['search']) value="{{$listDetails['search']}}" @endif>
                        </form>
                    </div>
                </div>
                <div class="justify-flex-end">
                    @include('common.sort', ['options' => [
                        'name' => trans('common.sort_name'),
                        'email' => trans('auth.email'),
                        'created_at' => trans('common.sort_created_at'),
                        'updated_at' => trans('common.sort_updated_at'),
                        'last_activity_at' => trans('settings.users_latest_activity'),
                    ], 'order' => $listDetails['order'], 'sort' => $listDetails['sort'], 'type' => 'users'])
                </div>
            </div>

            <div class="item-list">
                @foreach($users as $user)
                    <div class="flex-container-row item-list-row items-center wrap py-s">
                        <div class="px-m py-xs flex-container-row items-center flex-2 gap-l min-width-m">
                            <img class="avatar med" width="40" height="40" src="{{ $user->getAvatar(40)}}" alt="{{ $user->name }}">
                            <a href="{{ url("/settings/users/{$user->id}") }}">
                                {{ $user->name }}
                                <br>
                                <span class="text-muted">{{ $user->email }}</span>
                                @if($user->mfa_values_count > 0)
                                    <span title="MFA Configured" class="text-pos">@icon('lock')</span>
                                @endif
                            </a>
                        </div>
                        <div class="flex-container-row items-center flex-3 min-width-m">
                            <div class="px-m py-xs flex">
                                @foreach($user->roles as $index => $role)
                                    <small><a href="{{ url("/settings/roles/{$role->id}") }}">{{$role->display_name}}</a>@if($index !== count($user->roles) -1),@endif</small>
                                @endforeach
                            </div>
                            <div class="px-m py-xs flex text-right text-muted">
                                @if($user->last_activity_at)
                                    <small>{{ trans('settings.users_latest_activity') }}</small>
                                    <br>
                                    <small title="{{ $user->last_activity_at->format('Y-m-d H:i:s') }}">{{ $user->last_activity_at->diffForHumans() }}</small>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div>
                {{ $users->links() }}
            </div>
        </main>

    </div>

@stop

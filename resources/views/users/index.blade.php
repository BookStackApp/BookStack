@extends('simple-layout')

@section('body')
    <div class="container small">

        <div class="py-m">
            @include('settings/navbar', ['selected' => 'users'])
        </div>

        <div class="card content-wrap">

            <div class="grid right-focus v-center">
                <h1 class="list-heading">{{ trans('settings.users') }}</h1>

                <div class="text-right">
                    <div class="block inline mr-s">
                        <form method="get" action="{{ baseUrl("/settings/users") }}">
                            @foreach(collect($listDetails)->except('search') as $name => $val)
                                <input type="hidden" name="{{ $name }}" value="{{ $val }}">
                            @endforeach
                            <input type="text" name="search" placeholder="{{ trans('settings.users_search') }}" @if($listDetails['search']) value="{{$listDetails['search']}}" @endif>
                        </form>
                    </div>
                    @if(userCan('users-manage'))
                        <a href="{{ baseUrl("/settings/users/create") }}" style="margin-top: 0;" class="outline button">{{ trans('settings.users_add_new') }}</a>
                    @endif
                </div>
            </div>

            {{--TODO - Add last login--}}
            <table class="table">
                <tr>
                    <th></th>
                    <th>
                        <a href="{{ sortUrl('/settings/users', $listDetails, ['sort' => 'name']) }}">{{ trans('auth.name') }}</a>
                        /
                        <a href="{{ sortUrl('/settings/users', $listDetails, ['sort' => 'email']) }}">{{ trans('auth.email') }}</a>
                    </th>
                    <th>{{ trans('settings.role_user_roles') }}</th>
                </tr>
                @foreach($users as $user)
                    <tr>
                        <td class="text-center" style="line-height: 0;"><img class="avatar med" src="{{ $user->getAvatar(40)}}" alt="{{ $user->name }}"></td>
                        <td>
                            @if(userCan('users-manage') || $currentUser->id == $user->id)
                                <a href="{{ baseUrl("/settings/users/{$user->id}") }}">
                                    @endif
                                    {{ $user->name }} <br> <span class="text-muted">{{ $user->email }}</span>
                                    @if(userCan('users-manage') || $currentUser->id == $user->id)
                                </a>
                            @endif
                        </td>
                        <td>
                            @foreach($user->roles as $index => $role)
                                <small><a href="{{ baseUrl("/settings/roles/{$role->id}") }}">{{$role->display_name}}</a>@if($index !== count($user->roles) -1),@endif</small>
                            @endforeach
                        </td>
                    </tr>
                @endforeach
            </table>

            <div>
                {{ $users->links() }}
            </div>
        </div>

    </div>

@stop

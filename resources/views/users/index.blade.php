@extends('simple-layout')

@section('toolbar')
    @include('settings/navbar', ['selected' => 'users'])
@stop

@section('body')
    <div class="container small">
        <p>&nbsp;</p>
        <div class="card">
            <h3>@icon('users') {{ trans('settings.users') }}</h3>
            <div class="body">
                <div class="container">
                    <div class="row">
                        <div class="col-sm-4">
                            <form method="get" action="{{ baseUrl("/settings/users") }}">
                                @foreach(collect($listDetails)->except('search') as $name => $val)
                                    <input type="hidden" name="{{ $name }}" value="{{ $val }}">
                                @endforeach
                                <input type="text" name="search" placeholder="{{ trans('settings.users_search') }}" @if($listDetails['search']) value="{{$listDetails['search']}}" @endif>
                            </form>
                        </div>
                        <div class="col-sm-8 text-right">
                            @if(userCan('users-manage'))
                                <a href="{{ baseUrl("/settings/users/create") }}" style="margin-top: 0;" class="pos button">{{ trans('settings.users_add_new') }}</a>
                            @endif
                        </div>
                    </div>
                </div>

                <table class="table">
                    <tr>
                        <th></th>
                        <th><a href="{{ sortUrl('/settings/users', $listDetails, ['sort' => 'name']) }}">{{ trans('auth.name') }}</a></th>
                        <th><a href="{{ sortUrl('/settings/users', $listDetails, ['sort' => 'email']) }}">{{ trans('auth.email') }}</a></th>
                        <th>{{ trans('settings.role_user_roles') }}</th>
                    </tr>
                    @foreach($users as $user)
                        <tr>
                            <td style="line-height: 0;"><img class="avatar med" src="{{ $user->getAvatar(40)}}" alt="{{ $user->name }}"></td>
                            <td>
                                @if(userCan('users-manage') || $currentUser->id == $user->id)
                                    <a href="{{ baseUrl("/settings/users/{$user->id}") }}">
                                        @endif
                                        {{ $user->name }}
                                        @if(userCan('users-manage') || $currentUser->id == $user->id)
                                    </a>
                                @endif
                            </td>
                            <td>
                                @if(userCan('users-manage') || $currentUser->id == $user->id)
                                    <a href="{{ baseUrl("/settings/users/{$user->id}") }}">
                                        @endif
                                        {{ $user->email }}
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

    </div>

@stop

@extends('base')


@section('content')

    @include('settings/navbar', ['selected' => 'users'])

    <div class="container small">
        <form action="{{ baseUrl("/settings/users/{$user->id}") }}" method="post">
            <div class="row">
                <div class="col-sm-8">
                    <h1>{{ $user->id === $currentUser->id ? trans('settings.users_edit_profile') : trans('settings.users_edit') }}</h1>
                </div>
                <div class="col-sm-4">
                    <p></p>
                    @if($authMethod !== 'system')
                        <a href="{{ baseUrl("/settings/users/{$user->id}/delete") }}" class="neg button float right">{{ trans('settings.users_delete') }}</a>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6" ng-non-bindable>
                    {!! csrf_field() !!}
                    <input type="hidden" name="_method" value="put">
                    @include('users.forms.' . $authMethod, ['model' => $user])

                </div>
                <div class="col-sm-6">
                    <div class="form-group" id="logo-control">
                        <label for="user-avatar">{{ trans('settings.users_avatar') }}</label>
                        <p class="small">{{ trans('settings.users_avatar_desc') }}</p>

                        @include('components.image-picker', [
                              'resizeHeight' => '512',
                              'resizeWidth' => '512',
                              'showRemove' => false,
                              'defaultImage' => baseUrl('/user_avatar.png'),
                              'currentImage' => $user->getAvatar(80),
                              'currentId' => $user->image_id,
                              'name' => 'image_id',
                              'imageClass' => 'avatar large'
                          ])
                    </div>
                    <div class="form-group">
                        <label for="user-language">{{ trans('settings.users_preferred_language') }}</label>
                        <select name="setting[language]" id="user-language">
                            @foreach(trans('settings.language_select') as $lang => $label)
                                <option @if(setting()->getUser($user, 'language') === $lang) selected @endif value="{{ $lang }}">{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <!--Select display type -->
                    <div class="form-group">
                        <label for="display">Type of view</label>
                        <select name="display" id="display">
                            <option @if($user->display === 'grid') selected @endif value="grid">Grid</option>
                            <option @if($user->display === 'list') selected @endif value="list">List</option>
                        </select>
                    </div>
                    <!---->
                </div>
            </div>
            <div class="form-group">
                <a href="{{  baseUrl($currentUser->can('users-manage') ? "/settings/users" : "/") }}" class="button muted">{{ trans('common.cancel') }}</a>
                <button class="button pos" type="submit">{{ trans('common.save') }}</button>
            </div>
        </form>

        <hr class="margin-top large">

        @if($currentUser->id === $user->id && count($activeSocialDrivers) > 0)
            <h3>{{ trans('settings.users_social_accounts') }}</h3>
            <p class="text-muted">{{ trans('settings.users_social_accounts_info') }}</p>
            <div class="row">
                @foreach($activeSocialDrivers as $driver => $enabled)
                    <div class="col-sm-3 col-xs-6 text-center">
                        <div>@icon($driver, ['width' => 56])</div>
                        <div>
                            @if($user->hasSocialAccount($driver))
                                <a href="{{ baseUrl("/login/service/{$driver}/detach") }}" class="button neg">{{ trans('settings.users_social_disconnect') }}</a>
                            @else
                                <a href="{{ baseUrl("/login/service/{$driver}") }}" class="button pos">{{ trans('settings.users_social_connect') }}</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif


    </div>

    <p class="margin-top large"><br></p>
    @include('components.image-manager', ['imageType' => 'user'])
@stop

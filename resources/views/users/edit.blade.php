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
            <div class="col-md-6" ng-non-bindable>
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="put">
                @include('users.forms.' . $authMethod, ['model' => $user])

            </div>
            <div class="col-md-6">
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
            </div>
        </div>
        </form>

        <hr class="margin-top large">

        @if($currentUser->id === $user->id && count($activeSocialDrivers) > 0)
            <h3>{{ trans('settings.users_social_accounts') }}</h3>
            <p class="text-muted">{{ trans('settings.users_social_accounts_info') }}</p>
            <div class="row">
                @if(isset($activeSocialDrivers['google']))
                    <div class="col-md-3 text-center">
                        <div><i class="zmdi zmdi-google-plus-box zmdi-hc-4x" style="color: #DC4E41;"></i></div>
                        <div>
                            @if($user->hasSocialAccount('google'))
                                <a href="{{ baseUrl("/login/service/google/detach") }}" class="button neg">{{ trans('settings.users_social_disconnect') }}</a>
                            @else
                                <a href="{{ baseUrl("/login/service/google") }}" class="button pos">{{ trans('settings.users_social_connect') }}</a>
                            @endif
                        </div>
                    </div>
                @endif
                @if(isset($activeSocialDrivers['github']))
                    <div class="col-md-3 text-center">
                        <div><i class="zmdi zmdi-github zmdi-hc-4x" style="color: #444;"></i></div>
                        <div>
                            @if($user->hasSocialAccount('github'))
                                <a href="{{ baseUrl("/login/service/github/detach") }}" class="button neg">{{ trans('settings.users_social_disconnect') }}</a>
                            @else
                                <a href="{{ baseUrl("/login/service/github") }}" class="button pos">{{ trans('settings.users_social_connect') }}</a>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif


    </div>

    <p class="margin-top large"><br></p>
    @include('components.image-manager', ['imageType' => 'user'])
@stop

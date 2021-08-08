@extends('simple-layout')

@section('body')
    <div class="container small">

        <div class="py-m">
            @include('settings.navbar', ['selected' => 'users'])
        </div>

        <section class="card content-wrap">
            <h1 class="list-heading">{{ $user->id === user()->id ? trans('settings.users_edit_profile') : trans('settings.users_edit') }}</h1>
            <form action="{{ url("/settings/users/{$user->id}") }}" method="post" enctype="multipart/form-data">
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="PUT">

                <div class="setting-list">
                    @include('users.form', ['model' => $user, 'authMethod' => $authMethod])

                    <div class="grid half gap-xl">
                        <div>
                            <label for="user-avatar" class="setting-list-label">{{ trans('settings.users_avatar') }}</label>
                            <p class="small">{{ trans('settings.users_avatar_desc') }}</p>
                        </div>
                        <div>
                            @include('components.image-picker', [
                                'resizeHeight' => '512',
                                'resizeWidth' => '512',
                                'showRemove' => false,
                                'defaultImage' => url('/user_avatar.png'),
                                'currentImage' => $user->getAvatar(80),
                                'currentId' => $user->image_id,
                                'name' => 'profile_image',
                                'imageClass' => 'avatar large'
                            ])
                        </div>
                    </div>

                    <div class="grid half gap-xl v-center">
                        <div>
                            <label for="user-language" class="setting-list-label">{{ trans('settings.users_preferred_language') }}</label>
                            <p class="small">
                                {{ trans('settings.users_preferred_language_desc') }}
                            </p>
                        </div>
                        <div>
                            <select name="setting[language]" id="user-language">
                                @foreach(trans('settings.language_select') as $lang => $label)
                                    <option @if(setting()->getUser($user, 'language', config('app.default_locale')) === $lang) selected @endif value="{{ $lang }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                </div>

                <div class="text-right">
                    <a href="{{  url(userCan('users-manage') ? "/settings/users" : "/") }}" class="button outline">{{ trans('common.cancel') }}</a>
                    @if($authMethod !== 'system')
                        <a href="{{ url("/settings/users/{$user->id}/delete") }}" class="button outline">{{ trans('settings.users_delete') }}</a>
                    @endif
                    <button class="button" type="submit">{{ trans('common.save') }}</button>
                </div>
            </form>
        </section>

        <section class="card content-wrap auto-height">
            <h2 class="list-heading">{{ trans('settings.users_mfa') }}</h2>
            <p>{{ trans('settings.users_mfa_desc') }}</p>
            <div class="grid half gap-xl v-center pb-s">
                <div>
                    @if ($mfaMethods->count() > 0)
                        <span class="text-pos">@icon('check-circle')</span>
                    @else
                        <span class="text-neg">@icon('cancel')</span>
                    @endif
                    {{ trans_choice('settings.users_mfa_x_methods', $mfaMethods->count()) }}
                    {{ $mfaMethods->count() }} {{ $mfaMethods->count() === 1 ? 'method' : 'methods' }} configured
                </div>
                <div class="text-m-right">
                    @if($user->id === user()->id)
                        <a href="{{ url('/mfa/setup')  }}" class="button outline">{{ trans('settings.users_mfa_configure') }}</a>
                    @endif
                </div>
            </div>

        </section>

        @if(user()->id === $user->id && count($activeSocialDrivers) > 0)
            <section class="card content-wrap auto-height">
                <h2 class="list-heading">{{ trans('settings.users_social_accounts') }}</h2>
                <p class="text-muted">{{ trans('settings.users_social_accounts_info') }}</p>
                <div class="container">
                    <div class="grid third">
                        @foreach($activeSocialDrivers as $driver => $enabled)
                            <div class="text-center mb-m">
                                <div role="presentation">@icon('auth/'. $driver, ['style' => 'width: 56px;height: 56px;'])</div>
                                <div>
                                    @if($user->hasSocialAccount($driver))
                                        <form action="{{ url("/login/service/{$driver}/detach") }}" method="POST">
                                            {{ csrf_field() }}
                                            <button aria-label="{{ trans('settings.users_social_disconnect') }} - {{ $driver }}"
                                                    class="button small outline">{{ trans('settings.users_social_disconnect') }}</button>
                                        </form>
                                    @else
                                        <a href="{{ url("/login/service/{$driver}") }}" aria-label="{{ trans('settings.users_social_connect') }} - {{ $driver }}"
                                           class="button small outline">{{ trans('settings.users_social_connect') }}</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @if((user()->id === $user->id && userCan('access-api')) || userCan('users-manage'))
            @include('users.api-tokens.list', ['user' => $user])
        @endif
    </div>

@stop

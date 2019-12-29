@extends('simple-layout')

@section('body')
    <div class="container small">

        <div class="py-m">
            @include('settings.navbar', ['selected' => 'users'])
        </div>

        <section class="card content-wrap">
            <h1 class="list-heading">{{ $user->id === $currentUser->id ? trans('settings.users_edit_profile') : trans('settings.users_edit') }}</h1>
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
                    <a href="{{  url($currentUser->can('users-manage') ? "/settings/users" : "/") }}" class="button outline">{{ trans('common.cancel') }}</a>
                    @if($authMethod !== 'system')
                        <a href="{{ url("/settings/users/{$user->id}/delete") }}" class="button outline">{{ trans('settings.users_delete') }}</a>
                    @endif
                    <button class="button" type="submit">{{ trans('common.save') }}</button>
                </div>
            </form>
        </section>

        @if($currentUser->id === $user->id && count($activeSocialDrivers) > 0)
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
                                        <a href="{{ url("/login/service/{$driver}/detach") }}" aria-label="{{ trans('settings.users_social_disconnect') }} - {{ $driver }}"
                                           class="button small outline">{{ trans('settings.users_social_disconnect') }}</a>
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

        {{-- TODO - Review Control--}}
        @if(($currentUser->id === $user->id && userCan('access-api')) || userCan('manage-users'))
            <section class="card content-wrap auto-height" id="api_tokens">
                <div class="grid half">
                    <div><h2 class="list-heading">{{ trans('settings.users_api_tokens') }}</h2></div>
                    <div class="text-right pt-xs">
                        @if(userCan('access-api'))
                            <a href="{{ $user->getEditUrl('/create-api-token') }}" class="button outline">{{ trans('settings.users_api_tokens_create') }}</a>
                        @endif
                    </div>
                </div>
                @if (count($user->apiTokens) > 0)
                    <table class="table">
                        <tr>
                            <th>{{ trans('common.name') }}</th>
                            <th>{{ trans('settings.users_api_tokens_expires') }}</th>
                            <th></th>
                        </tr>
                        @foreach($user->apiTokens as $token)
                        <tr>
                            <td>
                                {{ $token->name }} <br>
                                <span class="small text-muted italic">{{ $token->client_id }}</span>
                            </td>
                            <td>{{ $token->expires_at->format('Y-m-d') ?? '' }}</td>
                            <td class="text-right">
                                <a class="button outline small" href="{{ $user->getEditUrl('/api-tokens/' . $token->id) }}">{{ trans('common.edit') }}</a>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                @else
                    <p class="text-muted italic py-m">{{ trans('settings.users_api_tokens_none') }}</p>
                @endif
            </section>
        @endif
    </div>

@stop

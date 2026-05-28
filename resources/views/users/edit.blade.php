@extends('layouts.simple')

@section('body')
    <div class="container small">

        @include('settings.parts.navbar', ['selected' => 'users'])

        <section class="card content-wrap">
            <h1 class="list-heading">{{ $user->id === user()->id ? trans('settings.users_edit_profile') : trans('settings.users_edit') }}</h1>
            <form action="{{ url("/settings/users/{$user->id}") }}" method="post" enctype="multipart/form-data">
                {!! csrf_field() !!}
                <input type="hidden" name="_method" value="PUT">

                <div class="setting-list">
                    @include('users.parts.form', ['model' => $user, 'authMethod' => $authMethod])

                    <div class="grid half gap-xl">
                        <div>
                            <label for="user-avatar"
                                   class="setting-list-label">{{ trans('settings.users_avatar') }}</label>
                            <p class="small">{{ trans('settings.users_avatar_desc') }}</p>
                        </div>
                        <div>
                            @include('form.image-picker', [
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

                    @if(!$user->isGuest())
                        @include('users.parts.language-option-row', ['value' => old('language') ?? $user->getLocale()->appLocale()])
                    @endif
                </div>

                <div class="text-right">
                    <a href="{{  url("/settings/users") }}"
                       class="button outline">{{ trans('common.cancel') }}</a>
                    @if($authMethod !== 'system')
                        <a href="{{ url("/settings/users/{$user->id}/delete") }}"
                           class="button outline">{{ trans('settings.users_delete') }}</a>
                    @endif
                    <button class="button" type="submit">{{ trans('common.save') }}</button>
                </div>
            </form>
        </section>

        <section class="card content-wrap auto-height">
            <h2 class="list-heading">{{ trans('settings.users_mfa') }}</h2>
            <p class="text-small">{{ trans('settings.users_mfa_desc') }}</p>
            <div class="grid half gap-xl v-center pb-s">
                <div>
                    @if ($mfaMethods->count() > 0)
                        <span class="text-pos">@icon('check-circle')</span>
                    @else
                        <span class="text-neg">@icon('cancel')</span>
                    @endif
                    {{ trans_choice('settings.users_mfa_x_methods', $mfaMethods->count()) }}
                </div>
                <div class="text-m-right">
                    @if($user->id === user()->id)
                        <a href="{{ url('/mfa/setup')  }}"
                           class="button outline">{{ trans('settings.users_mfa_configure') }}</a>
                    @endif
                </div>
            </div>

            @if($mfaMethods->count() > 0)
                <div id="reset-mfa" component="collapsible" class="form-group collapsible mt-s">
                    <button refs="collapsible@trigger" type="button" class="collapse-title text-link" aria-expanded="false">
                        <label for="mfa-reset">{{ trans('settings.users_mfa_reset') }}</label>
                    </button>
                    <div refs="collapsible@content" class="collapse-content">
                        <div class="flex-container-row justify-space-between gap-m wrap items-center">
                            <p class="text-small text-muted mb-none flex-2 min-width-m">{{ trans('settings.users_mfa_reset_desc') }}</p>
                            <form action="{{ url("/settings/users/{$user->id}/mfa") }}" method="POST" style="display: inline;">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <div class="dropdown-container" component="dropdown" option:dropdown:bubble-escapes="true">
                                    <button class="button" refs="dropdown@toggle"
                                            type="button"
                                            aria-haspopup="listbox"
                                            aria-expanded="{{ trans('settings.users_mfa_reset') }}">
                                        {{ trans('common.reset') }}
                                    </button>
                                    <div refs="dropdown@menu" class="dropdown-menu" role="menu" aria-label="{{ trans('settings.users_mfa_reset') }}">
                                        <p class="text-neg small px-m mb-xs">
                                            {{ trans('settings.users_mfa_reset_confirm') }}
                                        </p>
                                        <div class="text-link small delete text-item">
                                            <button type="submit" class="text-button neg">
                                                {{ trans('common.reset') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </section>

        @if(count($activeSocialDrivers) > 0)
            <section class="card content-wrap auto-height">
                <div class="flex-container-row items-center justify-space-between wrap">
                    <h2 class="list-heading">{{ trans('settings.users_social_accounts') }}</h2>
                    <div>
                        @if(user()->id === $user->id)
                            <a class="button outline" href="{{ url('/my-account/auth#social-accounts') }}">{{ trans('common.manage') }}</a>
                        @endif
                    </div>
                </div>
                <p class="text-muted text-small">{{ trans('settings.users_social_accounts_desc') }}</p>
                <div class="container">
                    <div class="grid third">
                        @foreach($activeSocialDrivers as $driver => $driverName)
                            <div class="text-center mb-m">
                                <div role="presentation">@icon('auth/'. $driver, ['style' => 'width: 56px;height: 56px;'])</div>
                                <p class="my-none bold">{{ $driverName }}</p>
                                @if($user->hasSocialAccount($driver))
                                    <p class="text-pos bold text-small my-none">{{ trans('settings.users_social_status_connected') }}</p>
                                @else
                                    <p class="text-neg bold text-small my-none">{{ trans('settings.users_social_status_disconnected') }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>
        @endif

        @include('users.api-tokens.parts.list', ['user' => $user, 'context' => 'settings'])
    </div>

@stop

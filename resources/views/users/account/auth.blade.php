@extends('users.account.layout')

@section('main')

    @if($authMethod === 'standard')
        <section class="card content-wrap auto-height">
            <form action="{{ url('/my-account/auth/password') }}" method="post">
                {{ method_field('put') }}
                {{ csrf_field() }}

                <h2 class="list-heading">{{ trans('preferences.auth_change_password') }}</h2>

                <p class="text-muted text-small">
                    {{ trans('preferences.auth_change_password_desc') }}
                </p>

                <div class="grid half mt-m gap-xl wrap stretch-inputs mb-m">
                    <div>
                        <label for="password">{{ trans('auth.password') }}</label>
                        @include('form.password', ['name' => 'password', 'autocomplete' => 'new-password'])
                    </div>
                    <div>
                        <label for="password-confirm">{{ trans('auth.password_confirm') }}</label>
                        @include('form.password', ['name' => 'password-confirm'])
                    </div>
                </div>

                <div class="form-group text-right">
                    <button class="button">{{ trans('common.update') }}</button>
                </div>

            </form>
        </section>
    @endif

    <section class="card content-wrap auto-height items-center flex-container-row gap-m gap-x-l wrap justify-space-between">
        <div class="flex-min-width-m">
            <h2 class="list-heading">{{ trans('settings.users_mfa') }}</h2>
            <p class="text-muted text-small">{{ trans('settings.users_mfa_desc') }}</p>
            <p class="text-muted">
                @if ($mfaMethods->count() > 0)
                    <span class="text-pos">@icon('check-circle')</span>
                @else
                    <span class="text-neg">@icon('cancel')</span>
                @endif
                {{ trans_choice('settings.users_mfa_x_methods', $mfaMethods->count()) }}
            </p>
        </div>
        <div class="text-right">
            <a href="{{ url('/mfa/setup')  }}"
               class="button outline">{{ trans('common.manage') }}</a>
        </div>
    </section>

    @if(count($activeSocialDrivers) > 0)
        <section id="social-accounts" class="card content-wrap auto-height">
            <h2 class="list-heading">{{ trans('settings.users_social_accounts') }}</h2>
            <p class="text-muted text-small">{{ trans('settings.users_social_accounts_info') }}</p>
            <div class="container">
                <div class="grid third">
                    @foreach($activeSocialDrivers as $driver => $enabled)
                        <div class="text-center mb-m">
                            <div role="presentation">@icon('auth/'. $driver, ['style' => 'width: 56px;height: 56px;'])</div>
                            <div>
                                @if(user()->hasSocialAccount($driver))
                                    <form action="{{ url("/login/service/{$driver}/detach") }}" method="POST">
                                        {{ csrf_field() }}
                                        <button aria-label="{{ trans('settings.users_social_disconnect') }} - {{ $driver }}"
                                                class="button small outline">{{ trans('settings.users_social_disconnect') }}</button>
                                    </form>
                                @else
                                    <a href="{{ url("/login/service/{$driver}") }}"
                                       aria-label="{{ trans('settings.users_social_connect') }} - {{ $driver }}"
                                       class="button small outline">{{ trans('settings.users_social_connect') }}</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if(userCan('access-api'))
        @include('users.api-tokens.parts.list', ['user' => user(), 'context' => 'my-account'])
    @endif
@stop

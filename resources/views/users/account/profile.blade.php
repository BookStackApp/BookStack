@extends('users.account.layout')

@section('main')

    <section class="card content-wrap auto-height">
        <form action="{{ url('/my-account/profile') }}" method="post" enctype="multipart/form-data">
            {{ method_field('put') }}
            {{ csrf_field() }}

            <div class="flex-container-row gap-l items-center wrap justify-space-between">
                <h1 class="list-heading">{{ trans('preferences.profile') }}</h1>
                <div>
                    <a href="{{ user()->getProfileUrl() }}" class="button outline">View Public Profile</a>
                </div>
            </div>

            <p class="text-muted text-small mb-none">
                Manage the details of your account that represent you to other users, in addition to
                details that are used for communication and system personalisation.
            </p>

            <div class="setting-list">

                <div class="flex-container-row gap-l items-center wrap">
                    <div class="flex">
                        <label class="setting-list-label" for="name">{{ trans('auth.name') }}</label>
                        <p class="text-small mb-none">
                            Configure your display name which will be visible to other users in the system
                            within the activity you perform, and content you own.
                        </p>
                    </div>
                    <div class="flex stretch-inputs">
                        @include('form.text', ['name' => 'name'])
                    </div>
                </div>

                <div>
                    <div class="flex-container-row gap-l items-center wrap">
                        <div class="flex">
                            <label class="setting-list-label" for="email">{{ trans('auth.email') }}</label>
                            <p class="text-small mb-none">
                                This email will be used for notifications and, depending on active system authentication, system access.
                            </p>
                        </div>
                        <div class="flex stretch-inputs">
                            @include('form.text', ['name' => 'email', 'disabled' => !userCan('users-manage')])
                        </div>
                    </div>
                    @if(!userCan('users-manage'))
                        <p class="text-small text-muted">
                            Unfortunately you don't have permission to change your email address.
                            If you want to change this, you'd need to ask an administrator to change this for you.
                        </p>
                    @endif
                </div>

                <div class="grid half gap-xl">
                    <div>
                        <label for="user-avatar"
                               class="setting-list-label">{{ trans('settings.users_avatar') }}</label>
                        <p class="text-small">
                            Select an image which will be used to represent yourself to others
                            in the system. Ideally this image should be square and about 256px in width and height.
                        </p>
                    </div>
                    <div>
                        @include('form.image-picker', [
                            'resizeHeight' => '512',
                            'resizeWidth' => '512',
                            'showRemove' => false,
                            'defaultImage' => url('/user_avatar.png'),
                            'currentImage' => user()->getAvatar(80),
                            'currentId' => user()->image_id,
                            'name' => 'profile_image',
                            'imageClass' => 'avatar large'
                        ])
                    </div>
                </div>

                @include('users.parts.language-option-row', ['value' => old('language') ?? user()->getLocale()->appLocale()])

            </div>

            <div class="form-group text-right">
                <button class="button">{{ trans('common.save') }}</button>
            </div>

        </form>
    </section>

    @if(userCan('users-manage'))
        <section class="card content-wrap auto-height">
            <div class="flex-container-row gap-l items-center wrap">
                <div class="flex">
                    <h2 class="list-heading">Administrator Options</h2>
                    <p class="text-small">
                        Additional administrator-level options, like role options, can be found for your user account in the
                        <nobr>"Settings > Users"</nobr> area of the application.
                    </p>
                </div>
                <div class="text-m-right">
                    <a class="button outline" href="{{ user()->getEditUrl() }}">Open</a>
                </div>
            </div>
        </section>
    @endif
@stop

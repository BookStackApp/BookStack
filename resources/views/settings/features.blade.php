@extends('settings.layout')

@section('card')
    <h1 id="features" class="list-heading">{{ trans('settings.app_features_security') }}</h1>
    <form action="{{ url("/settings/features") }}" method="POST">
        {!! csrf_field() !!}
        <input type="hidden" name="section" value="features">

        <div class="setting-list">


            <div class="grid half gap-xl">
                <div>
                    <label for="setting-app-public" class="setting-list-label">{{ trans('settings.app_public_access') }}</label>
                    <p class="small">{!! trans('settings.app_public_access_desc') !!}</p>
                    @if(userCan('users-manage'))
                        <p class="small mb-none">
                            <a href="{{ url($guestUser->getEditUrl()) }}">{!! trans('settings.app_public_access_desc_guest') !!}</a>
                        </p>
                    @endif
                </div>
                <div>
                    @include('form.toggle-switch', [
                        'name' => 'setting-app-public',
                        'value' => setting('app-public'),
                        'label' => trans('settings.app_public_access_toggle'),
                    ])
                </div>
            </div>

            <div class="grid half gap-xl">
                <div>
                    <label class="setting-list-label">{{ trans('settings.app_secure_images') }}</label>
                    <p class="small">{{ trans('settings.app_secure_images_desc') }}</p>
                </div>
                <div>
                    @include('form.toggle-switch', [
                        'name' => 'setting-app-secure-images',
                        'value' => setting('app-secure-images'),
                        'label' => trans('settings.app_secure_images_toggle'),
                    ])
                </div>
            </div>

            <div class="grid half gap-xl">
                <div>
                    <label class="setting-list-label">{{ trans('settings.app_disable_comments') }}</label>
                    <p class="small">{!! trans('settings.app_disable_comments_desc') !!}</p>
                </div>
                <div>
                    @include('form.toggle-switch', [
                        'name' => 'setting-app-disable-comments',
                        'value' => setting('app-disable-comments'),
                        'label' => trans('settings.app_disable_comments_toggle'),
                    ])
                </div>
            </div>


        </div>

        <div class="form-group text-right">
            <button type="submit" class="button">{{ trans('settings.settings_save') }}</button>
        </div>
    </form>
@endsection
@extends('simple-layout')

@section('body')
    <div class="container small">

        @include('settings.navbar-with-version', ['selected' => 'settings'])

        <div class="card content-wrap auto-height">
            <h2 id="features" class="list-heading">{{ trans('settings.app_features_security') }}</h2>
            <form action="{{ url("/settings") }}" method="POST">
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
                            @include('components.toggle-switch', [
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
                            @include('components.toggle-switch', [
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
                            @include('components.toggle-switch', [
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
        </div>

        <div class="card content-wrap auto-height">
            <h2 id="customization" class="list-heading">{{ trans('settings.app_customization') }}</h2>
            <form action="{{ url("/settings") }}" method="POST" enctype="multipart/form-data">
                {!! csrf_field() !!}
                <input type="hidden" name="section" value="customization">

                <div class="setting-list">

                    <div class="grid half gap-xl">
                        <div>
                            <label for="setting-app-name" class="setting-list-label">{{ trans('settings.app_name') }}</label>
                            <p class="small">{{ trans('settings.app_name_desc') }}</p>
                        </div>
                        <div class="pt-xs">
                            <input type="text" value="{{ setting('app-name', 'BookStack') }}" name="setting-app-name" id="setting-app-name">
                            @include('components.toggle-switch', [
                                'name' => 'setting-app-name-header',
                                'value' => setting('app-name-header'),
                                'label' => trans('settings.app_name_header'),
                            ])
                        </div>
                    </div>

                    <div class="grid half gap-xl">
                        <div>
                            <label class="setting-list-label">{{ trans('settings.app_editor') }}</label>
                            <p class="small">{{ trans('settings.app_editor_desc') }}</p>
                        </div>
                        <div class="pt-xs">
                            <select name="setting-app-editor" id="setting-app-editor">
                                <option @if(setting('app-editor') === 'wysiwyg') selected @endif value="wysiwyg">WYSIWYG</option>
                                <option @if(setting('app-editor') === 'markdown') selected @endif value="markdown">Markdown</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid half gap-xl">
                        <div>
                            <label class="setting-list-label">{{ trans('settings.app_logo') }}</label>
                            <p class="small">{!! trans('settings.app_logo_desc') !!}</p>
                        </div>
                        <div class="pt-xs">
                            @include('components.image-picker', [
                                     'removeName' => 'setting-app-logo',
                                     'removeValue' => 'none',
                                     'defaultImage' => url('/logo.png'),
                                     'currentImage' => setting('app-logo'),
                                     'name' => 'app_logo',
                                     'imageClass' => 'logo-image',
                                 ])
                        </div>
                    </div>

                    <!-- Primary Color -->
                    <div class="grid half gap-xl">
                        <div>
                            <label class="setting-list-label">{{ trans('settings.app_primary_color') }}</label>
                            <p class="small">{!! trans('settings.app_primary_color_desc') !!}</p>
                        </div>
                        <div setting-app-color-picker class="text-m-right pt-xs">
                            <input type="color" data-default="#206ea7" data-current="{{ setting('app-color') }}" value="{{ setting('app-color') }}" name="setting-app-color" id="setting-app-color" placeholder="#206ea7">
                            <input type="hidden" value="{{ setting('app-color-light') }}" name="setting-app-color-light" id="setting-app-color-light">
                            <div class="pr-s">
                                <button type="button" class="text-button text-muted mt-s" setting-app-color-picker-default>{{ trans('common.default') }}</button>
                                <span class="sep">|</span>
                                <button type="button" class="text-button text-muted mt-s" setting-app-color-picker-reset>{{ trans('common.reset') }}</button>
                            </div>

                        </div>
                    </div>

                    <!-- Entity Color -->
                    <div class="pb-l">
                        <div>
                            <label class="setting-list-label">{{ trans('settings.content_colors') }}</label>
                            <p class="small">{!! trans('settings.content_colors_desc') !!}</p>
                        </div>
                        <div class="grid half pt-m">
                            <div>
                                @include('components.setting-entity-color-picker', ['type' => 'bookshelf'])
                                @include('components.setting-entity-color-picker', ['type' => 'book'])
                                @include('components.setting-entity-color-picker', ['type' => 'chapter'])
                            </div>
                            <div>
                                @include('components.setting-entity-color-picker', ['type' => 'page'])
                                @include('components.setting-entity-color-picker', ['type' => 'page-draft'])
                            </div>
                        </div>
                    </div>

                    <div homepage-control id="homepage-control" class="grid half gap-xl">
                        <div>
                            <label for="setting-app-homepage" class="setting-list-label">{{ trans('settings.app_homepage') }}</label>
                            <p class="small">{{ trans('settings.app_homepage_desc') }}</p>
                        </div>
                        <div class="pt-xs">
                            <select name="setting-app-homepage-type" id="setting-app-homepage-type">
                                <option @if(setting('app-homepage-type') === 'default') selected @endif value="default">{{ trans('common.default') }}</option>
                                <option @if(setting('app-homepage-type') === 'books') selected @endif value="books">{{ trans('entities.books') }}</option>
                                <option @if(setting('app-homepage-type') === 'bookshelves') selected @endif value="bookshelves">{{ trans('entities.shelves') }}</option>
                                <option @if(setting('app-homepage-type') === 'page') selected @endif value="page">{{ trans('entities.pages_specific') }}</option>
                            </select>

                            <div page-picker-container style="display: none;" class="mt-m">
                                @include('components.page-picker', ['name' => 'setting-app-homepage', 'placeholder' => trans('settings.app_homepage_select'), 'value' => setting('app-homepage')])
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="setting-app-privacy-link" class="setting-list-label">{{ trans('settings.app_footer_links') }}</label>
                        <p class="small">{{ trans('settings.app_footer_links_desc') }}</p>
                        <div class="grid half pt-m">
                            <div>
                                <label for="setting-app-privacy-policy" class="text-dark">{{ trans('settings.app_privacy_policy') }}</label>
                                <input type="text" value="{{ setting('app-privacy-policy', '') }}" name="setting-app-privacy-policy" id="setting-app-privacy-policy">
                            </div>
                            <div>
                                <label for="setting-app-terms-of-service" class="text-dark">{{ trans('settings.app_terms_of_service') }}</label>
                                <input type="text" value="{{ setting('app-terms-of-service', '') }}" name="setting-app-terms-of-service" id="setting-app-terms-of-service">
                            </div>
                        </div>
                    </div>


                    <div>
                        <label for="setting-app-custom-head" class="setting-list-label">{{ trans('settings.app_custom_html') }}</label>
                        <p class="small">{{ trans('settings.app_custom_html_desc') }}</p>
                        <textarea name="setting-app-custom-head" id="setting-app-custom-head" class="simple-code-input mt-m">{{ setting('app-custom-head', '') }}</textarea>
                        <p class="small text-right">{{ trans('settings.app_custom_html_disabled_notice') }}</p>
                    </div>


                </div>

                <div class="form-group text-right">
                    <button type="submit" class="button">{{ trans('settings.settings_save') }}</button>
                </div>
            </form>
        </div>

        <div class="card content-wrap auto-height">
            <h2 id="registration" class="list-heading">{{ trans('settings.reg_settings') }}</h2>
            <form action="{{ url("/settings") }}" method="POST">
                {!! csrf_field() !!}
                <input type="hidden" name="section" value="registration">

                <div class="setting-list">
                    <div class="grid half gap-xl">
                        <div>
                            <label class="setting-list-label">{{ trans('settings.reg_enable') }}</label>
                            <p class="small">{!! trans('settings.reg_enable_desc') !!}</p>
                        </div>
                        <div>
                            @include('components.toggle-switch', [
                                'name' => 'setting-registration-enabled',
                                'value' => setting('registration-enabled'),
                                'label' => trans('settings.reg_enable_toggle')
                            ])

                            @if(in_array(config('auth.method'), ['ldap', 'saml2']))
                                <div class="text-warn text-small mb-l">{{ trans('settings.reg_enable_external_warning') }}</div>
                            @endif

                            <label for="setting-registration-role">{{ trans('settings.reg_default_role') }}</label>
                            <select id="setting-registration-role" name="setting-registration-role" @if($errors->has('setting-registration-role')) class="neg" @endif>
                                @foreach(\BookStack\Auth\Role::all() as $role)
                                    <option value="{{$role->id}}"
                                            data-system-role-name="{{ $role->system_name ?? '' }}"
                                            @if(setting('registration-role', \BookStack\Auth\Role::first()->id) == $role->id) selected @endif
                                    >
                                        {{ $role->display_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid half gap-xl">
                        <div>
                            <label for="setting-registration-restrict" class="setting-list-label">{{ trans('settings.reg_confirm_restrict_domain') }}</label>
                            <p class="small">{!! trans('settings.reg_confirm_restrict_domain_desc') !!}</p>
                        </div>
                        <div class="pt-xs">
                            <input type="text" id="setting-registration-restrict" name="setting-registration-restrict" placeholder="{{ trans('settings.reg_confirm_restrict_domain_placeholder') }}" value="{{ setting('registration-restrict', '') }}">
                        </div>
                    </div>

                    <div class="grid half gap-xl">
                        <div>
                            <label class="setting-list-label">{{ trans('settings.reg_email_confirmation') }}</label>
                            <p class="small">{{ trans('settings.reg_confirm_email_desc') }}</p>
                        </div>
                        <div>
                            @include('components.toggle-switch', [
                                'name' => 'setting-registration-confirmation',
                                'value' => setting('registration-confirmation'),
                                'label' => trans('settings.reg_email_confirmation_toggle')
                            ])
                        </div>
                    </div>

                </div>

                <div class="form-group text-right">
                    <button type="submit" class="button">{{ trans('settings.settings_save') }}</button>
                </div>
            </form>
        </div>

    </div>

    @include('components.entity-selector-popup', ['entityTypes' => 'page'])
@stop

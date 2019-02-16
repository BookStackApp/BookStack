@extends('simple-layout')

@section('body')
    <div class="container small">

        <div class="grid left-focus v-center">
            <div class="py-m">
                @include('settings.navbar', ['selected' => 'settings'])
            </div>
            <div class="text-right mb-l px-m">
                <br>
                BookStack @if(strpos($version, 'v') !== 0) version @endif {{ $version }}
            </div>
        </div>

        <div class="card content-wrap auto-height">
            <h2 class="list-heading">{{ trans('settings.app_features_security') }}</h2>
            <form action="{{ baseUrl("/settings") }}" method="POST">
                {!! csrf_field() !!}

                <div class="setting-list">


                    <div class="grid half large-gap">
                        <div>
                            <label for="setting-app-public" class="setting-list-label">{{ trans('settings.app_public_access') }}</label>
                            <p class="small">{!! trans('settings.app_public_access_desc') !!}</p>
                            @if(userCan('users-manage'))
                                <p class="small mb-none">
                                    <a href="{{ baseUrl($guestUser->getEditUrl()) }}">{!! trans('settings.app_public_access_desc_guest') !!}</a>
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

                    <div class="grid half large-gap">
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

                    <div class="grid half large-gap">
                        <div>
                            <label class="setting-list-label">{{ trans('settings.app_disable_comments') }}</label>
                            <p class="small">{!! trans('settings.app_disable_comments_desc') !!}</p>
                        </div>
                        <div>
                            @include('components.toggle-switch', [
                                'name' => 'setting-app-disable-comments',
                                'value' => setting('app-disable-comments'),
                                'label' => trans('settings.app_disable_comments'),
                            ])
                        </div>
                    </div>


                </div>

                <div class="form-group text-right">
                    <button type="submit" class="button primary">{{ trans('settings.settings_save') }}</button>
                </div>
            </form>
        </div>

        <div class="card content-wrap auto-height">
            <h2 class="list-heading">{{ trans('settings.app_customization') }}</h2>
            <form action="{{ baseUrl("/settings") }}" method="POST">
                {!! csrf_field() !!}

                <div class="setting-list">

                    <div class="grid half large-gap">
                        <div>
                            <label for="setting-app-name" class="setting-list-label">{{ trans('settings.app_name') }}</label>
                            <p class="small">{{ trans('settings.app_name_desc') }}</p>
                        </div>
                        <div>
                            <input type="text" value="{{ setting('app-name', 'BookStack') }}" name="setting-app-name" id="setting-app-name">
                            @include('components.toggle-switch', [
                                'name' => 'setting-app-name-header',
                                'value' => setting('app-name-header'),
                                'label' => trans('settings.app_name_header'),
                            ])
                        </div>
                    </div>

                    <div class="grid half large-gap">
                        <div>
                            <label class="setting-list-label">{{ trans('settings.app_editor') }}</label>
                            <p class="small">{{ trans('settings.app_editor_desc') }}</p>
                        </div>
                        <div>
                            <select name="setting-app-editor" id="setting-app-editor">
                                <option @if(setting('app-editor') === 'wysiwyg') selected @endif value="wysiwyg">WYSIWYG</option>
                                <option @if(setting('app-editor') === 'markdown') selected @endif value="markdown">Markdown</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid half large-gap">
                        <div>
                            <label class="setting-list-label">{{ trans('settings.app_logo') }}</label>
                            <p class="small">{!! trans('settings.app_logo_desc') !!}</p>
                        </div>
                        <div>
                            @include('components.image-picker', [
                                     'resizeHeight' => '43',
                                     'resizeWidth' => '200',
                                     'showRemove' => true,
                                     'defaultImage' => baseUrl('/logo.png'),
                                     'currentImage' => setting('app-logo'),
                                     'name' => 'setting-app-logo',
                                     'imageClass' => 'logo-image',
                                     'currentId' => false
                                 ])
                        </div>
                    </div>

                    <div class="grid half large-gap">
                        <div>
                            <label class="setting-list-label">{{ trans('settings.app_primary_color') }}</label>
                            <p class="small">{!! trans('settings.app_primary_color_desc') !!}</p>
                        </div>
                        <div>
                            <input type="text" value="{{ setting('app-color') }}" name="setting-app-color" id="setting-app-color" placeholder="#0288D1">
                            <input type="hidden" value="{{ setting('app-color-light') }}" name="setting-app-color-light" id="setting-app-color-light">
                        </div>
                    </div>

                    <div homepage-control id="homepage-control" class="grid half large-gap">
                        <div>
                            <label for="setting-app-homepage" class="setting-list-label">{{ trans('settings.app_homepage') }}</label>
                            <p class="small">{{ trans('settings.app_homepage_desc') }}</p>
                        </div>
                        <div>
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
                        <label for="setting-app-custom-head" class="setting-list-label">{{ trans('settings.app_custom_html') }}</label>
                        <p class="small">{{ trans('settings.app_custom_html_desc') }}</p>
                        <textarea name="setting-app-custom-head" id="setting-app-custom-head" class="simple-code-input mt-m">{{ setting('app-custom-head', '') }}</textarea>
                    </div>


                </div>

                <div class="form-group text-right">
                    <button type="submit" class="button primary">{{ trans('settings.settings_save') }}</button>
                </div>
            </form>
        </div>

        <div class="card content-wrap auto-height">
            <h2 class="list-heading">{{ trans('settings.reg_settings') }}</h2>
            <form action="{{ baseUrl("/settings") }}" method="POST">
                {!! csrf_field() !!}

                <div class="setting-list">
                    <div class="grid half large-gap">
                        <div>
                            <label class="setting-list-label">{{ trans('settings.reg_enable') }}</label>
                            <p class="small">{!! trans('settings.reg_enable_desc') !!}</p>
                        </div>
                        <div>
                            @include('components.toggle-switch', [
                                'name' => 'setting-registration-enabled',
                                'value' => setting('registration-enabled'),
                                'label' => trans('settings.reg_enable')
                            ])

                            <label for="setting-registration-role">{{ trans('settings.reg_default_role') }}</label>
                            <select id="setting-registration-role" name="setting-registration-role" @if($errors->has('setting-registration-role')) class="neg" @endif>
                                @foreach(\BookStack\Auth\Role::all() as $role)
                                    <option value="{{$role->id}}" data-role-name="{{ $role->name }}"
                                            @if(setting('registration-role', \BookStack\Auth\Role::first()->id) == $role->id) selected @endif
                                    >
                                        {{ $role->display_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="grid half large-gap">
                        <div>
                            <label for="setting-registration-restrict" class="setting-list-label">{{ trans('settings.reg_confirm_restrict_domain') }}</label>
                            <p class="small">{!! trans('settings.reg_confirm_restrict_domain_desc') !!}</p>
                        </div>
                        <div>
                            <input type="text" id="setting-registration-restrict" name="setting-registration-restrict" placeholder="{{ trans('settings.reg_confirm_restrict_domain_placeholder') }}" value="{{ setting('registration-restrict', '') }}">
                        </div>
                    </div>

                    <div class="grid half large-gap">
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
                    <button type="submit" class="button primary">{{ trans('settings.settings_save') }}</button>
                </div>
            </form>
        </div>

    </div>

    @include('components.image-manager', ['imageType' => 'system'])
    @include('components.entity-selector-popup', ['entityTypes' => 'page'])
@stop

@section('scripts')
    <script src="{{ baseUrl("/libs/jq-color-picker/tiny-color-picker.min.js?version=1.0.0") }}"></script>
    <script type="text/javascript">
        $('#setting-app-color').colorPicker({
            opacity: false,
            renderCallback: function($elm, toggled) {
                const hexVal = '#' + this.color.colors.HEX;
                const rgb = this.color.colors.RND.rgb;
                const rgbLightVal = 'rgba('+ [rgb.r, rgb.g, rgb.b, '0.15'].join(',') +')';

                // Set textbox color to hex color code.
                const isEmpty = $.trim($elm.val()).length === 0;
                if (!isEmpty) $elm.val(hexVal);
                $('#setting-app-color-light').val(isEmpty ? '' : rgbLightVal);

                const customStyles = document.getElementById('custom-styles');
                const oldColor = customStyles.getAttribute('data-color');
                const oldColorLight = customStyles.getAttribute('data-color-light');

                customStyles.innerHTML = customStyles.innerHTML.split(oldColor).join(hexVal);
                customStyles.innerHTML = customStyles.innerHTML.split(oldColorLight).join(rgbLightVal);

                customStyles.setAttribute('data-color', hexVal);
                customStyles.setAttribute('data-color-light', rgbLightVal);
            }
        });
    </script>
@stop
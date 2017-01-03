@extends('base')

@section('content')

    @include('settings/navbar', ['selected' => 'settings'])

<div class="container small settings-container">

    <h1>{{ trans('settings.settings') }}</h1>

    <form action="{{ baseUrl("/settings") }}" method="POST">
        {!! csrf_field() !!}

        <h3>{{ trans('settings.app_settings') }}</h3>

        <div class="row">

            <div class="col-md-6">
                <div class="form-group">
                    <label for="setting-app-name">{{ trans('settings.app_name') }}</label>
                    <p class="small">{{ trans('settings.app_name_desc') }}</p>
                    <input type="text" value="{{ setting('app-name', 'BookStack') }}" name="setting-app-name" id="setting-app-name">
                </div>
                <div class="form-group">
                    <label>{{ trans('settings.app_name_header') }}</label>
                    @include('components.toggle-switch', ['name' => 'setting-app-name-header', 'value' => setting('app-name-header')])
                </div>
                <div class="form-group">
                    <label for="setting-app-public">{{ trans('settings.app_public_viewing') }}</label>
                    @include('components.toggle-switch', ['name' => 'setting-app-public', 'value' => setting('app-public')])
                </div>
                <div class="form-group">
                    <label>{{ trans('settings.app_secure_images') }}</label>
                    <p class="small">{{ trans('settings.app_secure_images_desc') }}</p>
                    @include('components.toggle-switch', ['name' => 'setting-app-secure-images', 'value' => setting('app-secure-images')])
                </div>
                <div class="form-group">
                    <label for="setting-app-editor">{{ trans('settings.app_editor') }}</label>
                    <p class="small">{{ trans('settings.app_editor_desc') }}</p>
                    <select name="setting-app-editor" id="setting-app-editor">
                        <option @if(setting('app-editor') === 'wysiwyg') selected @endif value="wysiwyg">WYSIWYG</option>
                        <option @if(setting('app-editor') === 'markdown') selected @endif value="markdown">Markdown</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group" id="logo-control">
                    <label for="setting-app-logo">{{ trans('settings.app_logo') }}</label>
                    <p class="small">{!! trans('settings.app_logo_desc') !!}</p>

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
                <div class="form-group" id="color-control">
                    <label for="setting-app-color">{{ trans('settings.app_primary_color') }}</label>
                    <p class="small">{!! trans('settings.app_primary_color_desc') !!}</p>
                    <input  type="text" value="{{ setting('app-color', '') }}" name="setting-app-color" id="setting-app-color" placeholder="#0288D1">
                    <input  type="hidden" value="{{ setting('app-color-light', '') }}" name="setting-app-color-light" id="setting-app-color-light" placeholder="rgba(21, 101, 192, 0.15)">
                </div>
            </div>

        </div>

        <div class="form-group">
            <label for="setting-app-custom-head">{{ trans('settings.app_custom_html') }}</label>
            <p class="small">{{ trans('settings.app_custom_html_desc') }}</p>
            <textarea name="setting-app-custom-head" id="setting-app-custom-head">{{ setting('app-custom-head', '') }}</textarea>
        </div>

        <hr class="margin-top">

        <h3>{{ trans('settings.reg_settings') }}</h3>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="setting-registration-enabled">{{ trans('settings.reg_allow') }}</label>
                    @include('components.toggle-switch', ['name' => 'setting-registration-enabled', 'value' => setting('registration-enabled')])
                </div>
                <div class="form-group">
                    <label for="setting-registration-role">{{ trans('settings.reg_default_role') }}</label>
                    <select id="setting-registration-role" name="setting-registration-role" @if($errors->has('setting-registration-role')) class="neg" @endif>
                        @foreach(\BookStack\Role::all() as $role)
                            <option value="{{$role->id}}" data-role-name="{{ $role->name }}"
                                    @if(setting('registration-role', \BookStack\Role::first()->id) == $role->id) selected @endif
                                    >
                                {{ $role->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="setting-registration-confirmation">{{ trans('settings.reg_confirm_email') }}</label>
                    <p class="small">{{ trans('settings.reg_confirm_email_desc') }}</p>
                    @include('components.toggle-switch', ['name' => 'setting-registration-confirmation', 'value' => setting('registration-confirmation')])
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="setting-registration-restrict">{{ trans('settings.reg_confirm_restrict_domain') }}</label>
                    <p class="small">{!! trans('settings.reg_confirm_restrict_domain_desc') !!}</p>
                    <input type="text" id="setting-registration-restrict" name="setting-registration-restrict" placeholder="{{ trans('settings.reg_confirm_restrict_domain_placeholder') }}" value="{{ setting('registration-restrict', '') }}">
                </div>
            </div>
        </div>

        <hr class="margin-top">

        <div class="form-group">
            <span class="float right muted">
                BookStack @if(strpos($version, 'v') !== 0) version @endif {{ $version }}
            </span>
            <button type="submit" class="button pos">{{ trans('settings.settings_save') }}</button>
        </div>
    </form>

</div>

@include('components.image-manager', ['imageType' => 'system'])

@stop

@section('scripts')
    <script src="{{ baseUrl("/libs/jq-color-picker/tiny-color-picker.min.js?version=1.0.0") }}"></script>
    <script type="text/javascript">
        $('#setting-app-color').colorPicker({
            opacity: false,
            renderCallback: function($elm, toggled) {
                var hexVal = '#' + this.color.colors.HEX;
                var rgb = this.color.colors.RND.rgb;
                var rgbLightVal = 'rgba('+ [rgb.r, rgb.g, rgb.b, '0.15'].join(',') +')';
                // Set textbox color to hex color code.
                var isEmpty = $.trim($elm.val()).length === 0;
                if (!isEmpty) $elm.val(hexVal);
                $('#setting-app-color-light').val(isEmpty ? '' : rgbLightVal);

                var customStyles = document.getElementById('custom-styles');
                var oldColor = customStyles.getAttribute('data-color');
                var oldColorLight = customStyles.getAttribute('data-color-light');

                customStyles.innerHTML = customStyles.innerHTML.split(oldColor).join(hexVal);
                customStyles.innerHTML = customStyles.innerHTML.split(oldColorLight).join(rgbLightVal);

                customStyles.setAttribute('data-color', hexVal);
                customStyles.setAttribute('data-color-light', rgbLightVal);
            }
        });
    </script>
@stop
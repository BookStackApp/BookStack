@extends('base')

@section('content')

    @include('settings/navbar', ['selected' => 'settings'])

<div class="container small">

    <h1>Settings</h1>

    <form action="/settings" method="POST">
        {!! csrf_field() !!}

        <h3>App Settings</h3>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="setting-app-name">Application name</label>
                    <input type="text" value="{{ setting('app-name', 'BookStack') }}" name="setting-app-name" id="setting-app-name">
                </div>
                <div class="form-group">
                    <label>Allow public viewing?</label>
                    <toggle-switch name="setting-app-public" value="{{ setting('app-public') }}"></toggle-switch>
                </div>
                <div class="form-group">
                    <label>Enable higher security image uploads?</label>
                    <p class="small">For performance reasons, all images are public by default, This option adds a random, hard-to-guess characters in front of image names. Ensure directory indexes are not enabled to prevent easy access.</p>
                    <toggle-switch name="setting-app-secure-images" value="{{ setting('app-secure-images') }}"></toggle-switch>
                </div>
                <div class="form-group">
                    <label for="setting-app-editor">Page editor</label>
                    <p class="small">Select which editor will be used by all users to edit pages.</p>
                    <select name="setting-app-editor" id="setting-app-editor">
                        <option @if(setting('app-editor') === 'wysiwyg') selected @endif value="wysiwyg">WYSIWYG</option>
                        <option @if(setting('app-editor') === 'markdown') selected @endif value="markdown">Markdown</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group" id="logo-control">
                    <label for="setting-app-logo">Application logo</label>
                    <p class="small">This image should be 43px in height. <br>Large images will be scaled down.</p>
                    <image-picker resize-height="43" show-remove="true" resize-width="200" current-image="{{ setting('app-logo', '') }}" default-image="/logo.png" name="setting-app-logo" image-class="logo-image"></image-picker>
                </div>
                <div class="form-group" id="color-control">
                    <label for="setting-app-color">Application primary color</label>
                    <p class="small">This should be a hex value. <br> Leave empty to reset to the default color.</p>
                    <input  type="text" value="{{ setting('app-color', '') }}" name="setting-app-color" id="setting-app-color" placeholder="#0288D1">
                    <input  type="hidden" value="{{ setting('app-color-light', '') }}" name="setting-app-color-light" id="setting-app-color-light" placeholder="rgba(21, 101, 192, 0.15)">
                </div>
            </div>
        </div>



        <hr class="margin-top">

        <h3>Registration Settings</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="setting-registration-enabled">Allow registration?</label>
                    <toggle-switch name="setting-registration-enabled" value="{{ setting('registration-enabled') }}"></toggle-switch>
                </div>
                <div class="form-group">
                    <label for="setting-registration-role">Default user role after registration</label>
                    <select id="setting-registration-role" name="setting-registration-role" @if($errors->has('setting-registration-role')) class="neg" @endif>
                        @foreach(\BookStack\Role::all() as $role)
                            <option value="{{$role->id}}"
                                    @if(setting('registration-role', \BookStack\Role::first()->id) == $role->id) selected @endif
                                    >
                                {{ $role->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="setting-registration-confirmation">Require email confirmation?</label>
                    <p class="small">If domain restriction is used then email confirmation will be required and the below value will be ignored.</p>
                    <toggle-switch name="setting-registration-confirmation" value="{{ setting('registration-confirmation') }}"></toggle-switch>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="setting-registration-restrict">Restrict registration to domain</label>
                    <p class="small">Enter a comma separated list of email domains you would like to restrict registration to. Users will be sent an email to confirm their address before being allowed to interact with the application.
                        <br> Note that users will be able to change their email addresses after successful registration.</p>
                    <input type="text" id="setting-registration-restrict" name="setting-registration-restrict" placeholder="No restriction set" value="{{ setting('registration-restrict', '') }}">
                </div>
            </div>
        </div>

        <hr class="margin-top">

        <div class="form-group">
            <button type="submit" class="button pos">Save Settings</button>
        </div>
    </form>

</div>

@include('partials/image-manager', ['imageType' => 'system'])

@stop

@section('scripts')
    <script src="/libs/jq-color-picker/tiny-color-picker.min.js?version=1.0.0"></script>
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
                // Set page elements to provide preview
                $('#header, .image-picker .button').css('background-color', hexVal);
                $('.faded-small').css('background-color', rgbLightVal);
                $('.setting-nav a.selected').css('border-bottom-color', hexVal);
            }
        });
    </script>
@stop
@extends('base')

@section('content')

    @include('settings/navbar', ['selected' => 'settings'])

<div class="container small">

    <h1>Settings</h1>

    <form action="/settings" method="POST">
        {!! csrf_field() !!}

        <h3>App Settings</h3>
        <div class="form-group">
            <label for="setting-app-name">Application name</label>
            <input type="text" value="{{ Setting::get('app-name', 'BookStack') }}" name="setting-app-name" id="setting-app-name">
        </div>
        <div class="form-group">
            <label for="setting-app-public">Allow public viewing?</label>
            <label><input type="radio" name="setting-app-public" @if(Setting::get('app-public')) checked @endif value="true"> Yes</label>
            <label><input type="radio" name="setting-app-public" @if(!Setting::get('app-public')) checked @endif value="false"> No</label>
        </div>

        <hr class="margin-top">

        <h3>Registration Settings</h3>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="setting-registration-enabled">Allow registration?</label>
                    <label><input type="radio" name="setting-registration-enabled" @if(Setting::get('registration-enabled')) checked @endif value="true"> Yes</label>
                    <label><input type="radio" name="setting-registration-enabled" @if(!Setting::get('registration-enabled')) checked @endif value="false"> No</label>
                </div>
                <div class="form-group">
                    <label for="setting-registration-role">Default user role after registration</label>
                    <select id="setting-registration-role" name="setting-registration-role" @if($errors->has('setting-registration-role')) class="neg" @endif>
                        @foreach(\BookStack\Role::all() as $role)
                            <option value="{{$role->id}}"
                                    @if(\Setting::get('registration-role', \BookStack\Role::getDefault()->id) == $role->id) selected @endif
                                    >
                                {{ $role->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="setting-registration-confirmation">Require Email Confirmation?</label>
                    <p class="small">If domain restriction is used then email confirmation will be required and the below value will be ignored.</p>
                    <label><input type="radio" name="setting-registration-confirmation" @if(Setting::get('registration-confirmation')) checked @endif value="true"> Yes</label>
                    <label><input type="radio" name="setting-registration-confirmation" @if(!Setting::get('registration-confirmation')) checked @endif value="false"> No</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="setting-registration-restrict">Restrict registration to domain</label>
                    <p class="small">Enter a comma separated list of email domains you would like to restrict registration to. Users will be sent an email to confirm their address before being allowed to interact with the application.
                        <br> Note that users will be able to change their email addresses after successful registration.</p>
                    <input type="text" id="setting-registration-restrict" name="setting-registration-restrict" placeholder="No restriction set" value="{{ Setting::get('registration-restrict', '') }}">
                </div>
            </div>
        </div>

        <hr class="margin-top">

        <div class="form-group">
            <button type="submit" class="button pos">Save Settings</button>
        </div>
    </form>

</div>

@stop
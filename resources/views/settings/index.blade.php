@extends('base')

@section('content')

    @include('settings/navbar', ['selected' => 'settings'])

    <div class="page-content">
        <h1>Settings</h1>

        <form action="/settings" method="POST">
            {!! csrf_field() !!}
            <div class="form-group">
                <label for="setting-app-name">Application Name</label>
                <input type="text" value="{{ Setting::get('app-name') }}" name="setting-app-name" id="setting-app-name">
            </div>
            <div class="form-group">
                <button type="submit" class="button pos">Update Settings</button>
            </div>
        </form>

    </div>

@stop
@extends('layouts.simple')

@section('body')
    <div class="container small py-xl">

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('auth.mfa_setup') }}</h1>
            <p class="mb-none"> {{ trans('auth.mfa_setup_desc') }}</p>

            <div class="setting-list">
                @foreach(['totp', 'backup_codes'] as $method)
                    @include('mfa.parts.setup-method-row', ['method' => $method])
                @endforeach
            </div>

        </div>
    </div>
@stop

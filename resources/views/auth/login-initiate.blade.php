@extends('layouts.simple')

@section('content')

    <div class="container very-small">

        <div class="my-l">&nbsp;</div>

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('auth.auto_init_starting') }}</h1>

            <div style="display:none">
                @include('auth.parts.login-form-' . $authMethod)
            </div>

            <div class="grid half left-focus">
                <div>
                    <p class="text-small">{{ trans('auth.auto_init_starting_desc') }}</p>
                    <p>
                        <button type="submit" form="login-form" class="p-none text-button hover-underline">
                            {{ trans('auth.auto_init_start_link') }}
                        </button>
                    </p>
                </div>
                <div class="text-center">
                    @include('common.loading-icon')
                </div>
            </div>

            <script nonce="{{ $cspNonce }}">
                window.addEventListener('load', () => document.forms['login-form'].submit());
            </script>

        </div>
    </div>

@stop

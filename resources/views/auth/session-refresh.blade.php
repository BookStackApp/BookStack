

@extends('layouts.simple')

@section('content')

    <div class="container very-small">

        <div class="my-l">&nbsp;</div>

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">Refreshing Session</h1>


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

            <input type="text" id="csrf_token" value="{{ csrf_token() }}">

            <script nonce="{{ $cspNonce }}">
                if (window.opener) {
                    const csrfValue = document.getElementById('csrf_token').value;
                    window.opener.postMessage({
                        csrf_token: csrfValue,
                    }, {
                        targetOrigin: window.location.origin,
                    });
                }
            </script>

        </div>
    </div>

@stop

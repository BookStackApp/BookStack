@extends('layouts.simple')

@section('content')

    <div class="container very-small">

        <div class="my-l">&nbsp;</div>

        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('auth.session_refreshing') }}</h1>

            <p class="text-small">{{ trans('auth.session_refreshing_desc') }}</p>

            <div class="text-center">
                @include('common.loading-icon')
            </div>

            <input type="hidden" id="csrf_token" value="{{ csrf_token() }}">
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

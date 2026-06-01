@extends('layouts.simple')

@section('body')

    <div class="container very-small py-xl">
        <div class="card content-wrap auto-height">
            <h1 class="list-heading">{{ trans('auth.mfa_gen_hardware_key_title') }}</h1>
            <p>{{ trans('auth.mfa_gen_hardware_key_desc') }}</p>

            <div class="text-center mb-xs">
                <button type="button" id="setup">setup_key</button>
            </div>

            <form action="{{ url('/mfa/hardware_key/confirm') }}" method="POST">
                {{ csrf_field() }}
                <div class="mt-s text-right">
                    <a href="{{ url('/mfa/setup') }}" class="button outline">{{ trans('common.cancel') }}</a>
                    <button class="button">{{ trans('auth.mfa_gen_confirm_and_enable') }}</button>
                </div>
            </form>
        </div>
    </div>

    <script type="module" nonce="{{ $cspNonce }}">
        // TODO - Extract to its own compontent
        // TODO - Add some guidance and call this on click
        // https://webauthn-doc.spomky-labs.com/prerequisites/javascript#using-native-browser-api
        const setupButton = document.getElementById('setup');
        const options = {!! $options !!};

        const publicKeyCredentialCreationOptions = {
            ...options,
            challenge: base64urlDecode(options.challenge),
            user: {
                ...options.user,
                id: base64urlDecode(options.user.id)
            },
            excludeCredentials: options.excludeCredentials?.map(cred => ({
                ...cred,
                id: base64urlDecode(cred.id)
            }))
        };

        setupButton.addEventListener('click', async () => {
            console.log(publicKeyCredentialCreationOptions);
            const credential = await navigator.credentials.create({
                publicKey: publicKeyCredentialCreationOptions,
            });
            console.log(credential);
        });

        function base64urlEncode(buffer) {
            const base64 = btoa(String.fromCharCode(...new Uint8Array(buffer)));
            return base64
                .replace(/\+/g, '-')
                .replace(/\//g, '_')
                .replace(/=/g, '');
        }

        function base64urlDecode(base64url) {
            const base64 = base64url
                .replace(/-/g, '+')
                .replace(/_/g, '/');
            const padding = '='.repeat((4 - base64.length % 4) % 4);
            const binary = atob(base64 + padding);
            const bytes = new Uint8Array(binary.length);
            for (let i = 0; i < binary.length; i++) {
                bytes[i] = binary.charCodeAt(i);
            }
            return bytes.buffer;
        }
    </script>

@stop

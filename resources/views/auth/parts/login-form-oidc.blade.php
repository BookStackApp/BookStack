@if(!empty($oidcProviders))
    <hr class="my-l">
    @foreach($oidcProviders as $provider)
        @php
            $providerName = config("oidc.providers.{$provider}.name", ucfirst($provider));
        @endphp
        <div>
            <form method="POST" action="{{ url("/oidc/login/" . $provider) }}">
                {{ csrf_field() }}
                <button type="submit" id="oidc-login-{{ $provider }}" class="button outline svg">
                    @icon('auth/oidc')
                    <span>{{ trans('auth.log_in_with', ['socialDriver' => $providerName]) }}</span>
                </button>
            </form>
        </div>
    @endforeach
@endif
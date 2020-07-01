<form action="{{ url('/openid/login') }}" method="POST" id="login-form" class="mt-l">
    {!! csrf_field() !!}

    <div>
        <button id="saml-login" class="button outline block svg">
            @icon('saml2')
            <span>{{ trans('auth.log_in_with', ['socialDriver' => config('openid.name')]) }}</span>
        </button>
    </div>

</form>
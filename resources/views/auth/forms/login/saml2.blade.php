<form action="{{ url('/saml2/login') }}" method="POST" id="login-form" class="mt-l">
    {!! csrf_field() !!}

    <div>
        <button id="saml-login" class="button outline svg">
            @icon('saml2')
            <span>{{ trans('auth.log_in_with', ['socialDriver' => config('saml2.name')]) }}</span>
        </button>
    </div>

</form>
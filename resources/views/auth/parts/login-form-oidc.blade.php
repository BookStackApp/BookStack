<form action="{{ url('/oidc/login') }}" method="POST" id="login-form" class="mt-l">
    {!! csrf_field() !!}

    <div>
        <button id="oidc-login" class="button outline svg">
            @icon('oidc')
            <span>{{ trans('auth.log_in_with', ['socialDriver' => config('oidc.name')]) }}</span>
        </button>
    </div>

</form>

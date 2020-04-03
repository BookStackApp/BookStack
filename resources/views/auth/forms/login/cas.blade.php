<div class="mt-l">
    <a id="cas-login" class="button outline block svg" href="{{ url('/cas/login') }}">
        @icon('saml2')
        {{ trans('auth.log_in_with', ['socialDriver' => config('cas.name')]) }}
    </a>
</div>

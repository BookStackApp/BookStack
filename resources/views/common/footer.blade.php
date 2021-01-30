@if(setting('app-privacy-policy') | setting('app-terms-of-service'))
<footer>
    @if(setting('app-privacy-policy'))
        <a href="{{ setting('app-privacy-policy') }}">{{ trans('settings.app_privacy_policy') }}</a>
    @endif
    @if(setting('app-terms-of-service'))
        <a href="{{ setting('app-terms-of-service') }}">{{ trans('settings.app_terms_of_service') }}</a>
    @endif
</footer>
@endif
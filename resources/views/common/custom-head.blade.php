@if(setting('app-custom-head') && \Route::currentRouteName() !== 'settings')
<!-- Custom user content -->
{!! \BookStack\Util\HtmlNonceApplicator::apply(setting('app-custom-head'), $cspNonce) !!}
<!-- End custom user content -->
@endif
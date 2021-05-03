@if(setting('app-custom-head'))
<!-- Custom user content -->
{!! \BookStack\Util\HtmlContentFilter::removeScripts(setting('app-custom-head')) !!}
<!-- End custom user content -->
@endif
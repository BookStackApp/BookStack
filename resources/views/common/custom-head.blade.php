@inject('headContent', 'BookStack\Theming\CustomHtmlHeadContentProvider')

@if(setting('app-custom-head') && \Route::currentRouteName() !== 'settings')
<!-- Custom user content -->
{!! $headContent->forWeb() !!}
<!-- End custom user content -->
@endif
{{--

    Fills in the header and footers styled in partials/export-styles.blade.php

    Relies on the following in .env

    EXPORT_HEADER_PAGE_SLUG=header
    EXPORT_SHOW_PAGE_NUMBERS=true

--}}


@if(env("EXPORT_HEADER_PAGE_SLUG"))
<div class="header">
    @php
        $PAGE = new \BookStack\Entities\Page();
        $page = $PAGE::where("slug", "=", env("EXPORT_HEADER_PAGE_SLUG"))->first();
        $page->html = (new \BookStack\Entities\Managers\PageContent($page))->render();
    @endphp
    {!! $page->html !!}
</div>
@endif
@if(env('EXPORT_SHOW_PAGE_NUMBERS'))
<div class="footer">
    Page <span class="pagenum"></span>
</div>
@endif

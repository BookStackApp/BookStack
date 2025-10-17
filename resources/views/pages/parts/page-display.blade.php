<div dir="auto">

    <div class="page-attachment-preview card"
         refs="page-display@preview"
         hidden
         data-preview-error="{{ trans('errors.attachment_preview_error') }}"
         data-preview-unsupported="{{ trans('errors.attachment_preview_unsupported') }}">
        <div class="page-attachment-preview-header">
            <h2 class="page-attachment-preview-title break-text"
                refs="page-display@previewTitle"></h2>
            <button type="button"
                    class="button outline small"
                    refs="page-display@previewClose">{{ trans('common.close') }}</button>
        </div>
        <div class="page-attachment-preview-body"
             refs="page-display@previewBody"></div>
    </div>

    <div refs="page-display@content" data-page-content>
        <h1 class="break-text" id="bkmrk-page-title">{{$page->name}}</h1>

        <div style="clear:left;"></div>

        @if (isset($diff) && $diff)
            {!! $diff !!}
        @else
            {!! isset($page->renderedHTML) ? $page->renderedHTML : $page->html !!}
        @endif
    </div>
</div>
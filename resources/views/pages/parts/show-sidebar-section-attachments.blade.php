@if($page->attachments->count() > 0)
    <div id="page-attachments" class="mb-l">
        <h5>{{ trans('entities.pages_attachments') }}</h5>
        <div class="body">
            @include('attachments.list', ['attachments' => $page->attachments])
        </div>
    </div>
@endif
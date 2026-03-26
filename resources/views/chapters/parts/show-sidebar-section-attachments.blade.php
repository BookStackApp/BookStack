@if($chapter->attachments->count() > 0)
    <div id="chapter-attachments" class="mb-l">
        <h5>{{ trans('entities.attachments') }}</h5>
        @include('attachments.list', ['attachments' => $chapter->attachments])
    </div>
@endif

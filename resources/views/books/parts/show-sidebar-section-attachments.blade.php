@if($book->attachments->count() > 0)
    <div id="book-attachments" class="mb-l">
        <h5>{{ trans('entities.attachments') }}</h5>
        @include('attachments.list', ['attachments' => $book->attachments])
    </div>
@endif

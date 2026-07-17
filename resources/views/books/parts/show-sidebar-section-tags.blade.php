@if($book->tags->count() > 0)
    <div class="mb-xl">
        @include('entities.tag-list', ['entity' => $book])
    </div>
@endif
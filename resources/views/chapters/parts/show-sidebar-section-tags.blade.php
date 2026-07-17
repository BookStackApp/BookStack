@if($chapter->tags->count() > 0)
    <div class="mb-xl">
        @include('entities.tag-list', ['entity' => $chapter])
    </div>
@endif
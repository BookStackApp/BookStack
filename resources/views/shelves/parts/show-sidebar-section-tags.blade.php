@if($shelf->tags->count() > 0)
    <div id="tags" class="mb-xl">
        @include('entities.tag-list', ['entity' => $shelf])
    </div>
@endif
@if($page->tags->count() > 0)
    <section>
        @include('entities.tag-list', ['entity' => $page])
    </section>
@endif
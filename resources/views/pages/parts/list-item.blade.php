@component('entities.list-item-basic', ['entity' => $page])
<!-- </?php dd(['entity' => $page]); ?> -->
    <div class="entity-item-snippet">
        <p class="text-muted break-text">{{ $page->getExcerpt() }}</p>
    </div>
@endcomponent
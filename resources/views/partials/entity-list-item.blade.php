@component('partials.entity-list-item-basic', ['entity' => $entity])
<div class="entity-item-snippet">
    <p class="text-muted break-text">{{ $entity->getExcerpt() }}</p>
</div>
@endcomponent
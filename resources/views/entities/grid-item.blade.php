<a href="{{ $entity->getUrl() }}" class="grid-card"
   data-entity-type="{{ $entity->getType() }}" data-entity-id="{{ $entity->id }}">
    <div class="bg-{{ $entity->getType() }} featured-image-container-wrap">
        <div class="featured-image-container" @if($entity->coverInfo()->exists()) style="background-image: url('{{ $entity->coverInfo()->getUrl() }}')"@endif>
        </div>
        @icon($entity->getType())
    </div>
    <div class="grid-card-content">
        <h2 class="text-limit-lines-2">{{ $entity->name }}</h2>
        <p class="text-muted">{{ $entity->getExcerpt(130) }}</p>
    </div>
    <div class="grid-card-footer text-muted ">
        <p>@icon('star')<span title="{{ $dates->absolute($entity->created_at) }}">{{ trans('entities.meta_created', ['timeLength' => $dates->relative($entity->created_at)]) }}</span></p>
        <p>@icon('edit')<span title="{{ $dates->absolute($entity->updated_at) }}">{{ trans('entities.meta_updated', ['timeLength' => $dates->relative($entity->updated_at)]) }}</span></p>
    </div>
</a>
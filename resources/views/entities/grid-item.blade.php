<a href="{{ $entity->getUrl() }}" class="grid-card"
   data-entity-type="{{ $entity->getType() }}" data-entity-id="{{ $entity->id }}">
    <div class="bg-{{ $entity->getType() }} featured-image-container-wrap">
        <div class="featured-image-container" @if($entity->cover) style="background-image: url('{{ $entity->getBookCover() }}')"@endif>
        </div>
        @icon($entity->getType())
    </div>
    <div class="grid-card-content">
        <h2 class="text-limit-lines-2">{{ $entity->name }}</h2>
        <p class="text-muted">{{ $entity->getExcerpt(130) }}</p>
    </div>
    <div class="grid-card-footer text-muted ">
        <p>@icon('star')<span title="{{ $entity->created_at->toDayDateTimeString() }}">{{ trans('entities.meta_created', ['timeLength' => $entity->created_at->diffForHumans()]) }}</span></p>
        <p>@icon('edit')<span title="{{ $entity->updated_at->toDayDateTimeString() }}">{{ trans('entities.meta_updated', ['timeLength' => $entity->updated_at->diffForHumans()]) }}</span></p>
    </div>
</a>
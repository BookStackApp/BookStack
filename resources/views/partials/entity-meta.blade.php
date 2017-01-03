<p class="text-muted small">
    @if ($entity->createdBy)
        {!! trans('entities.meta_created_name', ['timeLength' => $entity->created_at->diffForHumans(), 'user' => "<a href='{$entity->createdBy->getProfileUrl()}'>".htmlentities($entity->createdBy->name). "</a>"]) !!}
    @else
        {{ trans('entities.meta_created', ['timeLength' => $entity->created_at->diffForHumans()]) }}
    @endif
    <br>
    @if ($entity->updatedBy)
        {!! trans('entities.meta_updated_name', ['timeLength' => $entity->updated_at->diffForHumans(), 'user' => "<a href='{$entity->updatedBy->getProfileUrl()}'>".htmlentities($entity->updatedBy->name). "</a>"]) !!}
    @else
        {{ trans('entities.meta_updated', ['timeLength' => $entity->updated_at->diffForHumans()]) }}
    @endif
</p>
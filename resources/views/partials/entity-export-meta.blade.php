<div class="entity-meta">
    @if ($entity->isA('page'))
        @icon('history'){{ trans('entities.meta_revision', ['revisionCount' => $entity->revision_count]) }} <br>
    @endif

    @icon('star'){!! trans('entities.meta_created' . ($entity->createdBy ? '_name' : ''), [
        'timeLength' => $entity->created_at->toDayDateTimeString(),
        'user' => e($entity->createdBy->name ?? ''),
        ]) !!}
    <br>

    @icon('edit'){!! trans('entities.meta_updated' . ($entity->updatedBy ? '_name' : ''), [
            'timeLength' => $entity->updated_at->toDayDateTimeString(),
            'user' => e($entity->updatedBy->name ?? '')
        ]) !!}
</div>
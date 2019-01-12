<div class="entity-meta">
    @if($entity->isA('revision'))
        @icon('history'){{ trans('entities.pages_revision') }}
        {{ trans('entities.pages_revisions_number') }}{{ $entity->revision_number == 0 ? '' : $entity->revision_number }}
        <br>
    @endif

    @if ($entity->isA('page'))
        @if (userCan('page-update', $entity)) <a href="{{ $entity->getUrl('/revisions') }}"> @endif
        @icon('history'){{ trans('entities.meta_revision', ['revisionCount' => $entity->revision_count]) }} <br>
        @if (userCan('page-update', $entity))</a>@endif
    @endif

    @if ($entity->createdBy)
        @icon('star'){!! trans('entities.meta_created_name', [
            'timeLength' => '<span>'.$entity->created_at->toDayDateTimeString() . '</span>',
            'user' => "<a href='{$entity->createdBy->getProfileUrl()}'>".htmlentities($entity->createdBy->name). "</a>"
            ]) !!}
    @else
        @icon('star')<span>{{ trans('entities.meta_created', ['timeLength' => $entity->created_at->toDayDateTimeString()]) }}</span>
    @endif

    <br>

    @if ($entity->updatedBy)
        @icon('edit'){!! trans('entities.meta_updated_name', [
                'timeLength' => '<span>' . $entity->updated_at->toDayDateTimeString() .'</span>',
                'user' => "<a href='{$entity->updatedBy->getProfileUrl()}'>".htmlentities($entity->updatedBy->name). "</a>"
            ]) !!}
    @elseif (!$entity->isA('revision'))
        @icon('edit')<span>{{ trans('entities.meta_updated', ['timeLength' => $entity->updated_at->toDayDateTimeString()]) }}</span>
    @endif
</div>
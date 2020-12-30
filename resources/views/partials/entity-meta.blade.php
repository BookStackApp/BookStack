<div class="entity-meta">
    @if($entity->isA('revision'))
        <div>
            @icon('history'){{ trans('entities.pages_revision') }}
            {{ trans('entities.pages_revisions_number') }}{{ $entity->revision_number == 0 ? '' : $entity->revision_number }}
        </div>
    @endif

    @if ($entity->isA('page'))
        <div>
            @if (userCan('page-update', $entity)) <a href="{{ $entity->getUrl('/revisions') }}"> @endif
            @icon('history'){{ trans('entities.meta_revision', ['revisionCount' => $entity->revision_count]) }}
            @if (userCan('page-update', $entity))</a>@endif
        </div>
    @endif

    @if ($entity->ownedBy && $entity->ownedBy->id !== $entity->createdBy->id)
        <div>
            @icon('user'){!! trans('entities.meta_owned_name', [
            'user' => "<a href='{$entity->ownedBy->getProfileUrl()}'>".e($entity->ownedBy->name). "</a>"
        ]) !!}
        </div>
    @endif

    @if ($entity->createdBy)
        <div>
            @icon('star'){!! trans('entities.meta_created_name', [
            'timeLength' => '<span title="'.$entity->created_at->toDayDateTimeString().'">'.$entity->created_at->diffForHumans() . '</span>',
            'user' => "<a href='{$entity->createdBy->getProfileUrl()}'>".e($entity->createdBy->name). "</a>"
            ]) !!}
        </div>
    @else
        <div>
            @icon('star')<span title="{{$entity->created_at->toDayDateTimeString()}}">{{ trans('entities.meta_created', ['timeLength' => $entity->created_at->diffForHumans()]) }}</span>
        </div>
    @endif

    @if ($entity->updatedBy)
        <div>
            @icon('edit'){!! trans('entities.meta_updated_name', [
                'timeLength' => '<span title="' . $entity->updated_at->toDayDateTimeString() .'">' . $entity->updated_at->diffForHumans() .'</span>',
                'user' => "<a href='{$entity->updatedBy->getProfileUrl()}'>".e($entity->updatedBy->name). "</a>"
            ]) !!}
        </div>
    @elseif (!$entity->isA('revision'))
        <div>
            @icon('edit')<span title="{{ $entity->updated_at->toDayDateTimeString() }}">{{ trans('entities.meta_updated', ['timeLength' => $entity->updated_at->diffForHumans()]) }}</span>
        </div>
    @endif
</div>
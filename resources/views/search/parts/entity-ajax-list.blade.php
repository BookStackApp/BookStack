<div class="entity-list">
    @if(count($entities) > 0)
        @foreach($entities as $index => $entity)

            @include('entities.list-item', [
            'entity' => $entity,
            'showPath' => true,
            'locked' => $permission !== 'view' && !userCan($permission, $entity)
            ])
        
            @if($index !== count($entities) - 1)
                <hr>
            @endif

        @endforeach
    @else
        <p class="text-muted text-large p-xl">
            {{ trans('common.no_items') }}
        </p>
    @endif
</div>
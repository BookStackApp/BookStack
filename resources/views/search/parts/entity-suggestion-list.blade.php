<div class="entity-list">
    @if(count($entities) > 0)
        @foreach($entities as $index => $entity)

            @include('entities.list-item', [
                'entity' => $entity,
                'showPath' => true,
                'locked' => false,
            ])
        
            @if($index !== count($entities) - 1)
                <hr>
            @endif

        @endforeach
    @else
        <div class="text-muted px-m py-m">
            {{ trans('common.no_items') }}
        </div>
    @endif
</div>
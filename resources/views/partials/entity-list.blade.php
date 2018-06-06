
<div class="entity-list @if(isset($style)){{ $style }}@endif">
    @if(count($entities) > 0)
        @foreach($entities as $index => $entity)
            @if($entity->isA('page'))
                @include('pages/list-item', ['page' => $entity])
            @elseif($entity->isA('book'))
                @include('books/list-item', ['book' => $entity])
            @elseif($entity->isA('chapter'))
                @include('chapters/list-item', ['chapter' => $entity, 'hidePages' => true])
            @endif

            @if($index !== count($entities) - 1)
                <hr>
            @endif

        @endforeach
    @else
        <p class="text-muted empty-text">
            {{ $emptyText or trans('common.no_items') }}
        </p>
    @endif
</div>
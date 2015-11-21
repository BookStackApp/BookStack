
@if(count($entities) > 0)
    @foreach($entities as $entity)
        @if($entity->isA('page'))
            @include('pages/list-item', ['page' => $entity])
        @elseif($entity->isA('book'))
            @include('books/list-item', ['book' => $entity])
        @elseif($entity->isA('chapter'))
            @include('chapters/list-item', ['chapter' => $entity, 'hidePages' => true])
        @endif
        <hr>
    @endforeach
@else
    <p class="text-muted">
        No items available :(
    </p>
@endif
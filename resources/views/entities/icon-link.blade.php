<a href="{{ $entity->getUrl() }}" class="flex-container-row items-center">
    <span role="presentation"
          class="icon flex-none text-{{$entity->getType()}}">@icon($entity->getType())</span>
    <div class="flex text-{{ $entity->getType() }}">
        {{ $entity->name }}
    </div>
</a>
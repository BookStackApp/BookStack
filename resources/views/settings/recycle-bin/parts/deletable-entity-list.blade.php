@include('settings.recycle-bin.parts.entity-display-item', ['entity' => $entity])
@if($entity->isA('book'))
    @foreach($entity->chapters()->withTrashed()->get() as $chapter)
        @include('settings.recycle-bin.parts.entity-display-item', ['entity' => $chapter])
    @endforeach
@endif
@if($entity->isA('book') || $entity->isA('chapter'))
    @foreach($entity->pages()->withTrashed()->get() as $page)
        @include('settings.recycle-bin.parts.entity-display-item', ['entity' => $page])
    @endforeach
@endif
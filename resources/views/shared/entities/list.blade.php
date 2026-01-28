{{-- Shared version of entity list --}}
@foreach($entities as $entity)
    @if($entity->isA('page'))
        @include('shared.pages.list-item', [
            'page' => $entity,
            'shareLink' => $shareLink ?? null,
            'token' => $token ?? null
        ])
    @elseif($entity->isA('chapter'))
        @include('shared.chapters.list-item', [
            'chapter' => $entity,
            'shareLink' => $shareLink ?? null,
            'token' => $token ?? null
        ])
    @else
        @include('entities.list-item-basic', ['entity' => $entity])
    @endif
@endforeach

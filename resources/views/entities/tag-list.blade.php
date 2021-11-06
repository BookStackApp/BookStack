@foreach($entity->tags as $tag)
    @include('entities.tag', ['tag' => $tag])
@endforeach
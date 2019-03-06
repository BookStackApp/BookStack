<h1>{{ trans('entities.tags') }} {{$searchTerm}}</h1>
<div class="tag grid ">
    @if(count($tags) > 0)

           @foreach($tags as $tag)
                @include('tags/list-item', ['tag' => $tag])
            @endforeach
    @else
        <p class="text-muted">Tag not found</p>
    @endif
</div>
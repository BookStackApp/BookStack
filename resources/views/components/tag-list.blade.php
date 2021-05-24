@foreach($entity->tags as $tag)
    <div class="tag-item primary-background-light">
        @if($disableLinks ?? false)
            <div class="tag-name"><span>@icon('tag'){{ $tag->name }}</span></div>
            @if($tag->value) <div class="tag-value"><span>{{$tag->value}}</span></div> @endif
        @else
            <div class="tag-name"><a href="{{ url('/search?term=%5B' . urlencode($tag->name) .'%5D') }}">@icon('tag'){{ $tag->name }}</a></div>
            @if($tag->value) <div class="tag-value"><a href="{{ url('/search?term=%5B' . urlencode($tag->name) .'%3D' . urlencode($tag->value) . '%5D') }}">{{$tag->value}}</a></div> @endif
        @endif
    </div>
@endforeach
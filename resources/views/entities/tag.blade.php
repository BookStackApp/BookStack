<div class="tag-item primary-background-light" data-name="{{ $tag->name }}" data-value="{{ $tag->value }}">
    @if($linked ?? true)
        <div class="tag-name"><a href="{{ $tag->nameUrl() }}">@icon('tag'){{ $tag->name }}</a></div>
        @if($tag->value) <div class="tag-value"><a href="{{ $tag->valueUrl() }}">{{$tag->value}}</a></div> @endif
    @else
        <div class="tag-name"><span>@icon('tag'){{ $tag->name }}</span></div>
        @if($tag->value) <div class="tag-value"><span>{{$tag->value}}</span></div> @endif
    @endif
</div>
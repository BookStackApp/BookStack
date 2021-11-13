<div class="tag-item primary-background-light" data-name="{{ $tag->name }}" data-value="{{ $tag->value }}">
    @if($linked ?? true)
        <div class="tag-name {{ $tag->highlight_name ? 'highlight' : '' }}"><a href="{{ $tag->nameUrl() }}">@icon('tag'){{ $tag->name }}</a></div>
        @if($tag->value) <div class="tag-value {{ $tag->highlight_value ? 'highlight' : '' }}"><a href="{{ $tag->valueUrl() }}">{{$tag->value}}</a></div> @endif
    @else
        <div class="tag-name {{ $tag->highlight_name ? 'highlight' : '' }}"><span>@icon('tag'){{ $tag->name }}</span></div>
        @if($tag->value) <div class="tag-value {{ $tag->highlight_value ? 'highlight' : '' }}"><span>{{$tag->value}}</span></div> @endif
    @endif
</div>
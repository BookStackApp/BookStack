<div class="link entity-list-item" data-entity-type="link" data-entity-id="{{$link->id}}">
    <h4>
        @if (isset($showPath) && $showPath)
            <a href="{{ $link->book->getUrl() }}" class="text-book">
                @icon('book'){{ $link->book->getShortName() }}
            </a>
            <span class="text-muted">&nbsp;&nbsp;&raquo;&nbsp;&nbsp;</span>
        @endif
        <a href="{{ $link->getUrl() }}" class="text-link entity-list-item-link">
            @icon('link')<span class="entity-list-item-name break-text">{{ $link->name }}</span>
        </a>
    </h4>
    @if ($link->html)
    <div class="entity-item-snippet">
        <a href="{{$link->html}}" target="_blank" class="text-muted break-text">{!! $link->html !!}</p>
    </div>
    @endif
    @if ($link->link_to)
    <div class="entity-item-snippet">
        <a href="{{$link->link_to}}" target="_blank" class="text-muted break-text">{!! $link->link_to !!}</p>
    </div>
    @endif
</div>
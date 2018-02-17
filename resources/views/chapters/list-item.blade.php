<div class="chapter entity-list-item" data-entity-type="chapter" data-entity-id="{{$chapter->id}}">
    <h4>
        @if (isset($showPath) && $showPath)
            <a href="{{ $chapter->book->getUrl() }}" class="text-book">
                @icon('book'){{ $chapter->book->getShortName() }}
            </a>
            <span class="text-muted">&nbsp;&nbsp;&raquo;&nbsp;&nbsp;</span>
        @endif
        <a href="{{ $chapter->getUrl() }}" class="text-chapter entity-list-item-link">
            @icon('chapter')<span class="entity-list-item-name break-text">{{ $chapter->name }}</span>
        </a>
    </h4>

    <div class="entity-item-snippet">
        @if(isset($chapter->searchSnippet))
            <p class="text-muted break-text">{!! $chapter->searchSnippet !!}</p>
        @else
            <p class="text-muted break-text">{{ $chapter->getExcerpt() }}</p>
        @endif
    </div>


    @if(!isset($hidePages) && count($chapter->pages) > 0)
        <p chapter-toggle class="text-muted"><i class="zmdi zmdi-caret-right"></i> @icon('page') <span>{{ trans_choice('entities.x_pages', $chapter->pages->count()) }}</span></p>
        <div class="inset-list">
            @foreach($chapter->pages as $page)
                <h5 class="@if($page->draft) draft @endif"><a href="{{ $page->getUrl() }}" class="text-page @if($page->draft) draft @endif">@icon('page'){{$page->name}}</a></h5>
            @endforeach
        </div>
    @endif
</div>
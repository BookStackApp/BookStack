<div class="chapter entity-list-item" data-entity-type="chapter" data-entity-id="{{$chapter->id}}">
    <h4>
        @if (isset($showPath) && $showPath)
            <a href="{{ $chapter->book->getUrl() }}" class="text-book">
                <i class="zmdi zmdi-book"></i>{{ $chapter->book->getShortName() }}
            </a>
            <span class="text-muted">&nbsp;&nbsp;&raquo;&nbsp;&nbsp;</span>
        @endif
        <a href="{{ $chapter->getUrl() }}" class="text-chapter entity-list-item-link">
            <i class="zmdi zmdi-collection-bookmark"></i><span class="entity-list-item-name">{{ $chapter->name }}</span>
        </a>
    </h4>

    <div class="entity-item-snippet">
        @if(isset($chapter->searchSnippet))
            <p class="text-muted">{!! $chapter->searchSnippet !!}</p>
        @else
            <p class="text-muted">{{ $chapter->getExcerpt() }}</p>
        @endif
    </div>


    @if(!isset($hidePages) && count($chapter->pages) > 0)
        <p chapter-toggle class="text-muted"><i class="zmdi zmdi-caret-right"></i> <i class="zmdi zmdi-file-text"></i> <span>{{ trans('entities.x_pages', ['count' => $chapter->pages->count()]) }}</span></p>
        <div class="inset-list">
            @foreach($chapter->pages as $page)
                <h5 class="@if($page->draft) draft @endif"><a href="{{ $page->getUrl() }}" class="text-page @if($page->draft) draft @endif"><i class="zmdi zmdi-file-text"></i>{{$page->name}}</a></h5>
            @endforeach
        </div>
    @endif
</div>
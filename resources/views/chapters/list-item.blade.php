<div class="chapter entity-list-item"
   data-entity-type="chapter" data-entity-id="{{$chapter->id}}">
    <div class="icon text-chapter">@icon('chapter')</div>
    <div class="content">
        <a href="{{ $chapter->getUrl() }}" ><h4 class="entity-list-item-name break-text">{{ $chapter->name }}</h4></a>
        <div>

            <div class="entity-item-snippet">
                <p class="text-muted break-text">{{ $chapter->getExcerpt() }}</p>
            </div>

            @if(count($chapter->pages) > 0)
                <p chapter-toggle class="text-muted">@icon('caret-right') @icon('page') <span>{{ trans_choice('entities.x_pages', $chapter->pages->count()) }}</span></p>
                <div class="inset-list">
                    @foreach($chapter->pages as $page)
                        <a href="{{ $page->getUrl() }}" class="inner-page {{$page->draft ? 'draft' : ''}} entity-list-item">
                            <div class="icon text-page">@icon('page')</div>
                            <div class="content">
                                <h6 class="entity-list-item-name break-text">{{ $page->name }}</h6>
                                {{ $slot ?? '' }}
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</div>
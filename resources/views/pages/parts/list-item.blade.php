<a href="{{ $page->getUrl() }}" class="page entity-list-item " data-entity-type="page" data-entity-id="{{$page->id}}">
    <span class="icon text-page">@icon('page')</span>
    <div class="entity-list-item-image bg-page mr-xxs" style="background-image: url('{{$page->coverInfo()->getUrl()}}');width: 120px;">
        @icon('page')
    </div>
    <div class="content">
        <h4 class="entity-list-item-name break-text">{{ $page->name }}</h4>
        <div class="entity-item-snippet">
            <p class="text-muted break-text">{{ $page->getExcerpt() }}</p>
        </div>
    </div>
</a>
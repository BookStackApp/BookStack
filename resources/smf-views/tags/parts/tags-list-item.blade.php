<div class="item-list-row flex-container-row items-center wrap">
    <div class="{{ isset($nameFilter) && $tag->value ? 'flex-2' : 'flex' }} py-s px-m min-width-m">
        <span class="text-bigger mr-xl">@include('entities.tag', ['tag' => $tag])</span>
    </div>
    <div class="flex-2 flex-container-row justify-center items-center gap-m py-s px-m min-width-l wrap">
        <a href="{{ isset($tag->value) ? $tag->valueUrl() : $tag->nameUrl() }}"
           title="{{ trans('entities.tags_usages') }}"
           class="flex fill-area min-width-xxs bold text-right text-muted"><span class="opacity-60">@icon('leaderboard')</span>{{ $tag->usages }}</a>
        <a href="{{ isset($tag->value) ? $tag->valueUrl() : $tag->nameUrl() . '+{type:page}' }}"
           title="{{ trans('entities.tags_assigned_pages') }}"
           class="flex fill-area min-width-xxs bold text-right text-page"><span class="opacity-60">@icon('page')</span>{{ $tag->page_count }}</a>
        <a href="{{ isset($tag->value) ? $tag->valueUrl() : $tag->nameUrl() . '+{type:chapter}' }}"
           title="{{ trans('entities.tags_assigned_chapters') }}"
           class="flex fill-area min-width-xxs bold text-right text-chapter"><span class="opacity-60">@icon('chapter')</span>{{ $tag->chapter_count }}</a>
        <a href="{{ isset($tag->value) ? $tag->valueUrl() : $tag->nameUrl() . '+{type:book}' }}"
           title="{{ trans('entities.tags_assigned_books') }}"
           class="flex fill-area min-width-xxs bold text-right text-book"><span class="opacity-60">@icon('book')</span>{{ $tag->book_count }}</a>
        <a href="{{ isset($tag->value) ? $tag->valueUrl() : $tag->nameUrl() . '+{type:bookshelf}' }}"
           title="{{ trans('entities.tags_assigned_shelves') }}"
           class="flex fill-area min-width-xxs bold text-right text-bookshelf"><span class="opacity-60">@icon('bookshelf')</span>{{ $tag->shelf_count }}</a>
    </div>
    @if($tag->values ?? false)
        <div class="flex text-s-right text-muted py-s px-m min-width-s">
            <a href="{{ url('/tags?name=' . urlencode($tag->name)) }}">{{ trans('entities.tags_x_unique_values', ['count' => $tag->values]) }}</a>
        </div>
    @elseif(empty($nameFilter))
        <div class="flex text-s-right text-muted py-s px-m min-width-s">
            <a href="{{ url('/tags?name=' . urlencode($tag->name)) }}">{{ trans('entities.tags_all_values') }}</a>
        </div>
    @endif
</div>
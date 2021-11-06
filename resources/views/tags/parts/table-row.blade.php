<tr>
    <td>
        <span class="text-bigger mr-xl">@include('entities.tag', ['tag' => $tag])</span>
    </td>
    <td width="70" class="px-xs">
        <a href="{{ isset($tag->value) ? $tag->valueUrl() : $tag->nameUrl() }}"
           title="{{ trans('entities.tags_usages') }}"
           class="pill text-muted">@icon('leaderboard'){{ $tag->usages }}</a>
    </td>
    <td width="70" class="px-xs">
        <a href="{{ isset($tag->value) ? $tag->valueUrl() : $tag->nameUrl() . '+{type:page}' }}"
           title="{{ trans('entities.tags_assigned_pages') }}"
           class="pill text-page">@icon('page'){{ $tag->page_count }}</a>
    </td>
    <td width="70" class="px-xs">
        <a href="{{ isset($tag->value) ? $tag->valueUrl() : $tag->nameUrl() . '+{type:chapter}' }}"
           title="{{ trans('entities.tags_assigned_chapters') }}"
           class="pill text-chapter">@icon('chapter'){{ $tag->chapter_count }}</a>
    </td>
    <td width="70" class="px-xs">
        <a href="{{ isset($tag->value) ? $tag->valueUrl() : $tag->nameUrl() . '+{type:book}' }}"
           title="{{ trans('entities.tags_assigned_books') }}"
           class="pill text-book">@icon('book'){{ $tag->book_count }}</a>
    </td>
    <td width="70" class="px-xs">
        <a href="{{ isset($tag->value) ? $tag->valueUrl() : $tag->nameUrl() . '+{type:bookshelf}' }}"
           title="{{ trans('entities.tags_assigned_shelves') }}"
           class="pill text-bookshelf">@icon('bookshelf'){{ $tag->shelf_count }}</a>
    </td>
    <td class="text-right text-muted">
        @if($tag->values ?? false)
            <a href="{{ url('/tags?name=' . urlencode($tag->name)) }}">{{ trans('entities.tags_x_unique_values', ['count' => $tag->values]) }}</a>
        @elseif(empty($nameFilter))
            <a href="{{ url('/tags?name=' . urlencode($tag->name)) }}">{{ trans('entities.tags_all_values') }}</a>
        @endif
    </td>
</tr>
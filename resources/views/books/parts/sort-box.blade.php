<div class="sort-box" data-type="book" data-id="{{ $book->id }}">
    <h5 class="text-book entity-list-item no-hover py-xs pl-none">
        <span>@icon('book')</span>
        <span>{{ $book->name }}</span>
    </h5>
    <div class="sort-box-options pb-sm">
        <button type="button" data-sort="name" class="button outline small">{{ trans('entities.books_sort_name') }}</button>
        <button type="button" data-sort="created" class="button outline small">{{ trans('entities.books_sort_created') }}</button>
        <button type="button" data-sort="updated" class="button outline small">{{ trans('entities.books_sort_updated') }}</button>
        <button type="button" data-sort="chaptersFirst" class="button outline small">{{ trans('entities.books_sort_chapters_first') }}</button>
        <button type="button" data-sort="chaptersLast" class="button outline small">{{ trans('entities.books_sort_chapters_last') }}</button>
    </div>
    <ul class="sortable-page-list sort-list">

        @foreach($bookChildren as $bookChild)
            <li class="text-{{ $bookChild->getType() }}"
                data-id="{{$bookChild->id}}" data-type="{{ $bookChild->getType() }}"
                data-name="{{ $bookChild->name }}" data-created="{{ $bookChild->created_at->timestamp }}"
                data-updated="{{ $bookChild->updated_at->timestamp }}">
                <div class="entity-list-item">
                    <span>@icon($bookChild->getType()) </span>
                    <div>
                        {{ $bookChild->name }}
                        <div>

                        </div>
                    </div>
                </div>
                @if($bookChild->isA('chapter'))
                    <ul>
                        @foreach($bookChild->visible_pages as $page)
                            <li class="text-page"
                                data-id="{{$page->id}}" data-type="page"
                                data-name="{{ $page->name }}" data-created="{{ $page->created_at->timestamp }}"
                                data-updated="{{ $page->updated_at->timestamp }}">
                                <div class="entity-list-item">
                                    <span>@icon('page')</span>
                                    <span>{{ $page->name }}</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach

    </ul>
</div>
<details class="sort-box" data-type="book" data-id="{{ $book->id }}" open>
    <summary>
        <h5 class="flex-container-row items-center justify-flex-start gap-xs">
            <div class="text-book text-bigger caret-container">
                @icon('caret-right')
            </div>
            <div class="entity-list-item no-hover py-s text-book px-none">
                <span>@icon('book')</span>
                <span>{{ $book->name }}</span>
            </div>
        </h5>
    </summary>
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
                data-id="{{$bookChild->id}}"
                data-type="{{ $bookChild->getType() }}"
                data-name="{{ $bookChild->name }}"
                data-created="{{ $bookChild->created_at->timestamp }}"
                data-updated="{{ $bookChild->updated_at->timestamp }}"
                tabindex="0">
                <div class="flex-container-row items-center">
                    <div class="text-muted sort-list-handle px-s py-m">@icon('grip')</div>
                    <div class="entity-list-item px-none no-hover">
                        <span>@icon($bookChild->getType()) </span>
                        <div>
                            {{ $bookChild->name }}
                            <div>

                            </div>
                        </div>
                    </div>
                    @include('books.parts.sort-box-actions')
                </div>
                @if($bookChild->isA('chapter'))
                    <ul class="sortable-page-sublist">
                        @foreach($bookChild->visible_pages as $page)
                            <li class="text-page flex-container-row items-center"
                                data-id="{{$page->id}}" data-type="page"
                                data-name="{{ $page->name }}" data-created="{{ $page->created_at->timestamp }}"
                                data-updated="{{ $page->updated_at->timestamp }}"
                                tabindex="0">
                                <div class="text-muted sort-list-handle px-s py-m">@icon('grip')</div>
                                <div class="entity-list-item px-none no-hover">
                                    <span>@icon('page')</span>
                                    <span>{{ $page->name }}</span>
                                </div>
                                @include('books.parts.sort-box-actions')
                            </li>
                        @endforeach
                    </ul>
                @endif
            </li>
        @endforeach

    </ul>
</details>
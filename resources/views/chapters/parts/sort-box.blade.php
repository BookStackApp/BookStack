<div class="sort-box" data-type="chapter" data-id="{{ $chapter->id }}">
    <h5 class="text-chapter entity-list-item no-hover py-xs pl-none">
        <span>@icon('chapter')</span>
        <span>{{ $chapter->name }}</span>
    </h5>
    <div class="sort-box-options pb-sm">
        <a href="#" data-sort="name" class="button outline small">{{ trans('entities.chapters_sort_name') }}</a>
        <a href="#" data-sort="created" class="button outline small">{{ trans('entities.chapters_sort_created') }}</a>
        <a href="#" data-sort="updated" class="button outline small">{{ trans('entities.chapters_sort_updated') }}</a>
        <a href="#" data-sort="pagesFirst" class="button outline small">{{ trans('entities.chapters_sort_pages_first') }}</a>
        <a href="#" data-sort="pagesLast" class="button outline small">{{ trans('entities.chapters_sort_pages_last') }}</a>
    </div>
    <ul class="sortable-page-list sort-list">
        @foreach($chapter->getVisiblePages() as $page)
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
</div>
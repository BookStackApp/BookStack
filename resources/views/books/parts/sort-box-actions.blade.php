<div class="sort-box-actions flex-container-row items-center px-s gap-xxs">
    <button type="button" data-move="up" class="icon-button p-xs text-bigger"
            title="{{ trans('entities.books_sort_move_up') }}">@icon('chevron-up')</button>
    <button type="button" data-move="down" class="icon-button p-xs text-bigger"
            title="{{ trans('entities.books_sort_move_down') }}">@icon('chevron-down')</button>
    <div class="dropdown-container" component="dropdown">
        <button refs="dropdown@toggle"
                type="button"
                title="{{ trans('common.more') }}"
                class="icon-button p-xs text-bigger"
                aria-haspopup="true"
                aria-expanded="false">
            @icon('more')
        </button>
        <div refs="dropdown@menu" class="dropdown-menu" role="menu">
            <button type="button" class="text-item" data-move="prev_book">{{ trans('entities.books_sort_move_prev_book') }}</button>
            <button type="button" class="text-item" data-move="next_book">{{ trans('entities.books_sort_move_next_book') }}</button>
            <button type="button" class="text-item" data-move="prev_chapter">{{ trans('entities.books_sort_move_prev_chapter') }}</button>
            <button type="button" class="text-item" data-move="next_chapter">{{ trans('entities.books_sort_move_next_chapter') }}</button>
            <button type="button" class="text-item" data-move="book_start">{{ trans('entities.books_sort_move_book_start') }}</button>
            <button type="button" class="text-item" data-move="book_end">{{ trans('entities.books_sort_move_book_end') }}</button>
            <button type="button" class="text-item" data-move="before_chapter">{{ trans('entities.books_sort_move_before_chapter') }}</button>
            <button type="button" class="text-item" data-move="after_chapter">{{ trans('entities.books_sort_move_after_chapter') }}</button>
        </div>
    </div>
</div>
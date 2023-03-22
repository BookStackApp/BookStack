<div component="shelf-sort" class="grid half gap-xl">
    <div class="form-group">
        <label for="books" id="shelf-sort-books-label">{{ trans($shelfLabelOne) }}</label>
        <input refs="shelf-sort@input" type="hidden" name="books"
            value="{{ isset($shelf) ? $shelf->visibleBooks->implode('id', ',') : '' }}">
        <div class="scroll-box-header-item flex-container-row items-center py-xs">
            <span class="px-m py-xs">{{ trans($shelfLabelOneDrag) }}</span>
            <div class="dropdown-container ml-auto" component="dropdown">
                <button refs="dropdown@toggle" type="button" title="{{ trans('common.more') }}"
                    class="icon-button px-xs py-xxs mx-xs text-bigger" aria-haspopup="true" aria-expanded="false">
                    @icon('more')
                </button>
                <div refs="dropdown@menu shelf-sort@sort-button-container" class="dropdown-menu" role="menu">
                    <button type="button" class="text-item"
                        data-sort="name">{{ trans('entities.books_sort_name') }}</button>
                    <button type="button" class="text-item"
                        data-sort="created">{{ trans('entities.books_sort_created') }}</button>
                    <button type="button" class="text-item"
                        data-sort="updated">{{ trans('entities.books_sort_updated') }}</button>
                </div>
            </div>
        </div>
        <ul refs="shelf-sort@shelf-book-list" aria-labelledby="shelf-sort-books-label" class="scroll-box">
            @foreach ($shelf->visibleBooks ?? [] as $book)
                @include('shelves.parts.shelf-sort-book-item', ['book' => $book])
            @endforeach
        </ul>
    </div>
    <div class="form-group">
        <label for="books" id="shelf-sort-all-books-label">{{ trans($shelfLabelTwo) }}</label>
        <input type="text" refs="shelf-sort@book-search" class="scroll-box-search"
            placeholder="{{ trans('common.search') }}">
        <ul refs="shelf-sort@all-book-list" aria-labelledby="shelf-sort-all-books-label" class="scroll-box">
            @foreach ($books as $book)
                @include('shelves.parts.shelf-sort-book-item', ['book' => $book])
            @endforeach
        </ul>
    </div>
</div>

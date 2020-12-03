<div refs="entity-search@searchView" class="search-results hidden">
    <div class="grid half v-center">
        <h3 class="text-muted px-none">
            {{ trans('entities.search_results') }}
        </h3>
        <div class="text-right">
            <a refs="entity-search@clearButton" class="button outline">{{ trans('entities.search_clear') }}</a>
        </div>
    </div>

    <div refs="entity-search@loadingBlock">
        @include('partials.loading-icon')
    </div>
    <div class="book-contents" refs="entity-search@searchResults"></div>
</div>
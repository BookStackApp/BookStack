{{--
@label - Placeholder/aria-label text
--}}
<div class="mb-xl">
    <form refs="entity-search@searchForm" class="search-box flexible" role="search">
        <input refs="entity-search@searchInput" type="text"
               aria-label="{{ $label }}" name="term" placeholder="{{ $label }}">
        <button type="submit" aria-label="{{ trans('common.search') }}">@icon('search')</button>
    </form>
</div>
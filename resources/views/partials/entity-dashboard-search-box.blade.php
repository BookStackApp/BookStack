<div class="mb-xl">
    <form v-on:submit.prevent="searchBook" class="search-box flexible" role="search">
        <input v-model="searchTerm" v-on:change="checkSearchForm" type="text" aria-label="{{ trans('entities.books_search_this') }}" name="term" placeholder="{{ trans('entities.books_search_this') }}">
        <button type="submit" aria-label="{{ trans('common.search') }}">@icon('search')</button>
        <button v-if="searching" v-cloak class="search-box-cancel text-neg" v-on:click="clearSearch"
                type="button" aria-label="{{ trans('common.search_clear') }}">@icon('close')</button>
    </form>
</div>
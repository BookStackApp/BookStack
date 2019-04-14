<div class="mb-xl">
    <form v-on:submit.prevent="searchBook" class="search-box flexible">
        <input v-model="searchTerm" v-on:change="checkSearchForm" type="text" name="term" placeholder="{{ trans('entities.books_search_this') }}">
        <button type="submit">@icon('search')</button>
        <button v-if="searching" v-cloak class="search-box-cancel text-neg" v-on:click="clearSearch" type="button">@icon('close')</button>
    </form>
</div>
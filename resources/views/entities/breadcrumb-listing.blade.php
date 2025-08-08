<div components="dropdown dropdown-search"
     option:dropdown-search:url="/search/entity/siblings?entity_type={{$entity->getType()}}&entity_id={{ $entity->id }}"
     option:dropdown-search:local-search-selector=".entity-list-item"
     class="dropdown-search">
    <button class="dropdown-search-toggle-breadcrumb"
            refs="dropdown@toggle"
            aria-haspopup="true"
            aria-expanded="false"
            title="{{ trans('entities.breadcrumb_siblings_for_' . $entity->getType()) }}">
        <div role="presentation" class="separator">@icon('chevron-right')</div>
    </button>
    <div refs="dropdown@menu" class="dropdown-search-dropdown card">
        <div class="dropdown-search-search">
            @icon('search')
            <input refs="dropdown-search@searchInput"
                   aria-label="{{ trans('common.search') }}"
                   autocomplete="off"
                   placeholder="{{ trans('common.search') }}"
                   type="text">
        </div>
        <div refs="dropdown-search@loading">
            @include('common.loading-icon')
        </div>
        <div refs="dropdown-search@listContainer" class="dropdown-search-list px-m" tabindex="-1" role="list"></div>
    </div>
</div>
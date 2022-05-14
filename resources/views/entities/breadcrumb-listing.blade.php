<div class="dropdown-search" components="dropdown dropdown-search"
     option:dropdown-search:url="/search/entity/siblings?entity_type={{$entity->getType()}}&entity_id={{ $entity->id }}"
     option:dropdown-search:local-search-selector=".entity-list-item"
>
    <div class="dropdown-search-toggle" refs="dropdown@toggle"
         aria-haspopup="true" aria-expanded="false" tabindex="0">
        <div class="separator">@icon('chevron-right')</div>
    </div>
    <div refs="dropdown@menu" class="dropdown-search-dropdown card" role="menu">
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
        <div refs="dropdown-search@listContainer" class="dropdown-search-list px-m" tabindex="-1"></div>
    </div>
</div>
<div class="breadcrumb-listing" components="dropdown breadcrumb-listing"
     option:breadcrumb-listing:entity-type="{{ $entity->getType() }}"
     option:breadcrumb-listing:entity-id="{{ $entity->id }}"
     breadcrumb-listing="{{ $entity->getType() }}:{{ $entity->id }}">
    <div class="breadcrumb-listing-toggle" refs="dropdown@toggle"
         aria-haspopup="true" aria-expanded="false" tabindex="0">
        <div class="separator">@icon('chevron-right')</div>
    </div>
    <div refs="dropdown@menu" class="breadcrumb-listing-dropdown card" role="menu">
        <div class="breadcrumb-listing-search">
            @icon('search')
            <input refs="breadcrumb-listing@searchInput"
                   aria-label="{{ trans('common.search') }}"
                   autocomplete="off"
                   name="entity-search"
                   placeholder="{{ trans('common.search') }}"
                   type="text">
        </div>
        <div refs="breadcrumb-listing@loading">
            @include('partials.loading-icon')
        </div>
        <div refs="breadcrumb-listing@entityList" class="breadcrumb-listing-entity-list px-m"></div>
    </div>
</div>
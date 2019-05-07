<div class="breadcrumb-listing" dropdown breadcrumb-listing="{{ $entity->getType() }}:{{ $entity->id }}">
    <div class="breadcrumb-listing-toggle" dropdown-toggle>
        <div class="separator">@icon('chevron-right')</div>
    </div>
    <div dropdown-menu class="breadcrumb-listing-dropdown card">
        <div class="breadcrumb-listing-search">
            @icon('search')
            <input autocomplete="off" type="text" name="entity-search">
        </div>
        @include('partials.loading-icon')
        <div class="breadcrumb-listing-entity-list px-m"></div>
    </div>
</div>
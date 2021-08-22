<div class="dropdown-search custom-select-input" components="dropdown dropdown-search user-select"
     option:dropdown-search:url="/search/users/select"
>
    <input refs="user-select@input" type="hidden" name="{{ $name }}" value="{{ $user->id ?? '' }}">
    <div refs="dropdown@toggle"
         class="dropdown-search-toggle {{ $compact ? 'compact' : '' }} flex-container-row items-center"
         aria-haspopup="true" aria-expanded="false" tabindex="0">
        <div refs="user-select@user-info" class="flex-container-row items-center px-s">
            @if($user)
                <img class="avatar small mr-m" src="{{ $user->getAvatar($compact ? 22 : 30) }}" alt="{{ $user->name }}">
                <span>{{ $user->name }}</span>
            @else
                <span>{{ trans('settings.users_none_selected') }}</span>
            @endif
        </div>
        <span style="font-size: {{ $compact ? '1.15rem' : '1.5rem' }}; margin-left: auto;">
            @icon('caret-down')
        </span>
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
        <div refs="dropdown-search@loading" class="text-center">
            @include('common.loading-icon')
        </div>
        <div refs="dropdown-search@listContainer" class="dropdown-search-list"></div>
    </div>
</div>
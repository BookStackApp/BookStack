<div class="flex-container-row mobile-column item-list-row items-center wrap py-xs">
    <div class="px-m py-xs flex-container-row items-center flex-2 gap-l min-width-m">
        <img class="avatar med" width="40" height="40" src="{{ $user->getAvatar(40) }}" alt="{{ $user->name }}">
        <a href="{{ $user->getProfileUrl() }}">
            {{ $user->name }}
        </a>
    </div>
    <div class="items-center flex-2 py-xs">
        @if ($user->activity)
            @include('users.parts.activity-details', ['activity' => $user->activity])
        @endif
    </div>
    
    <div class="items-center flex-3">
        <div class="px-m py-xs flex text-right text-muted">
            <div id="content-counts">
                <div class="grid half v-center no-row-gap">
                    <div class="icon-list">
                        <span class="text-page draft icon-list-item">
                            <span>@icon('star')</span>
                            <span>{{ trans_choice('entities.x_symbols', $user->asset_counts['symbols']) }}</span>
                        </span>
                    </div>
                    <div class="icon-list">
                        <span class="text-chapter icon-list-item">
                            <span>@icon('file')</span>
                            <span>{{ trans_choice('entities.x_drafts', $user->asset_counts['drafts']) }}</span>
                        </span>
                    </div>
                    <div class="icon-list">
                        <span class="text-book icon-list-item">
                            <span>@icon('edit')</span>
                            <span>{{ trans_choice('entities.x_updates', $user->asset_counts['updates']) }}</span>
                        </span>
                    </div>
                    <div class="icon-list">
                        <a href="{{ url('/search?term=' . urlencode('{created_by:' . $user->slug . '} {type:page}')) }}" class="text-page icon-list-item">
                            <span>@icon('page')</span>
                            <span>All Created</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
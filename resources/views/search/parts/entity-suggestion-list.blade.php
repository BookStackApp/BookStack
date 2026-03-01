{{-- NEW CODE: Display matching users at the top of the suggestions --}}
@if(isset($users) && count($users) > 0)
    <div class="px-m py-s">
        <div class="text-muted text-sm mb-xs" style="font-weight: bold; text-transform: uppercase; letter-spacing: 0.05em;">Users</div>
        <div class="entity-list">
            @foreach($users as $user)
                <a href="{{ $user->getProfileUrl() }}" class="flex-container-row items-center py-xs px-s entity-list-item hover-bg-wash" style="text-decoration: none;">
                    <img class="avatar me-m" src="{{ $user->getAvatar(30) }}" alt="{{ $user->name }}" style="border-radius: 50%; width: 30px; height: 30px;">
                    <div class="content flex">
                        <h4 class="text-user mb-none" style="font-size: 1rem; color: var(--color-link);">{{ $user->name }}</h4>
                    </div>
                </a>
            @endforeach
        </div>
    </div>
    @if(count($entities) > 0)
        <hr class="m-none">
    @endif
@endif

{{-- ORIGINAL CODE: Displaying standard entities --}}
<div class="entity-list">
    @if(count($entities) > 0)
        @foreach($entities as $index => $entity)

            @include('entities.list-item', [
                'entity' => $entity,
                'showPath' => true,
                'locked' => false,
            ])
        
            @if($index !== count($entities) - 1)
                <hr>
            @endif

        @endforeach
    @elseif(!isset($users) || count($users) === 0)
        <div class="text-muted px-m py-m">
            {{ trans('common.no_items') }}
        </div>
    @endif
</div>
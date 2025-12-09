@if($users->isEmpty())
<li class="px-s py-xs">
    <span>{{ trans('common.no_items') }}</span>
</li>
@endif
@foreach($users as $user)
<li>
    <a href="{{ $user->getProfileUrl() }}" class="flex-container-row items-center dropdown-search-item"
       data-id="{{ $user->id }}"
       data-name="{{ $user->name }}"
       data-slug="{{ $user->slug }}">
        <img class="avatar mr-m" src="{{ $user->getAvatar(30) }}" alt="{{ $user->name }}">
        <span>{{ $user->name }}</span>
    </a>
</li>
@endforeach
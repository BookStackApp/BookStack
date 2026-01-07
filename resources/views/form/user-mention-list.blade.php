@if($users->isEmpty())
    <div class="flex-container-row items-center dropdown-search-item dropdown-search-item text-muted mt-m">
        <span>{{ trans('common.no_items') }}</span>
    </div>
@endif
@foreach($users as $user)
<a href="{{ $user->getProfileUrl() }}" class="flex-container-row items-center dropdown-search-item"
   data-id="{{ $user->id }}"
   data-name="{{ $user->name }}"
   data-slug="{{ $user->slug }}">
    <img class="avatar mr-m" src="{{ $user->getAvatar(30) }}" alt="{{ $user->name }}">
    <span>{{ $user->name }}</span>
</a>
@endforeach
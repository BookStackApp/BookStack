@php
 $isFavourite = $entity->isFavourite();
@endphp
<form action="{{ url('/favourites/' . ($isFavourite ? 'remove' : 'add')) }}" method="POST">
    {{ csrf_field() }}
    <input type="hidden" name="type" value="{{ get_class($entity) }}">
    <input type="hidden" name="id" value="{{ $entity->id }}">
    <button type="submit" class="icon-list-item text-primary">
        <span>@icon($isFavourite ? 'star' : 'star-outline')</span>
        <span>{{ $isFavourite ? trans('common.unfavourite') : trans('common.favourite') }}</span>
    </button>
</form>
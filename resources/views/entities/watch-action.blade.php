<form action="{{ $entity->getUrl('/') }}" method="GET">
    {{ csrf_field() }}
    <input type="hidden" name="type" value="{{ get_class($entity) }}">
    <input type="hidden" name="id" value="{{ $entity->id }}">
    <button type="submit" data-shortcut="favourite" class="icon-list-item text-link">
        <span>@icon('watch')</span>
        <span>{{ trans('entities.watch') }}</span>
    </button>
</form>
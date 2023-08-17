<form action="{{ url('/watching/update') }}" method="POST">
    {{ csrf_field() }}
    {{ method_field('PUT') }}
    <input type="hidden" name="type" value="{{ get_class($entity) }}">
    <input type="hidden" name="id" value="{{ $entity->id }}">
    <button type="submit"
            name="level"
            value="updates"
            class="icon-list-item text-link">
        <span>@icon('watch')</span>
        <span>{{ trans('entities.watch') }}</span>
    </button>
</form>
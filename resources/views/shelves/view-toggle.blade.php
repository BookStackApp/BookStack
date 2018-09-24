<form action="{{ baseUrl("/settings/users/{$currentUser->id}/switch-shelf-view") }}" method="POST" class="inline">
    {!! csrf_field() !!}
    {!! method_field('PATCH') !!}
    <input type="hidden" value="{{ $shelvesViewType === 'list'? 'grid' : 'list' }}" name="view_type">
    @if ($shelvesViewType === 'list')
        <button type="submit" class="text-pos text-button">@icon('grid'){{ trans('common.grid_view') }}</button>
    @else
        <button type="submit" class="text-pos text-button">@icon('list'){{ trans('common.list_view') }}</button>
    @endif
</form>
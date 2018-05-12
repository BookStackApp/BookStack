<form action="{{ baseUrl("/settings/users/{$currentUser->id}/switch-book-view") }}" method="POST" class="inline">
    {!! csrf_field() !!}
    {!! method_field('PATCH') !!}
    <input type="hidden" value="{{ $booksViewType === 'list'? 'grid' : 'list' }}" name="book_view_type">
    @if ($booksViewType === 'list')
        <button type="submit" class="text-pos text-button">@icon('grid'){{ trans('common.grid_view') }}</button>
    @else
        <button type="submit" class="text-pos text-button">@icon('list'){{ trans('common.list_view') }}</button>
    @endif
</form>
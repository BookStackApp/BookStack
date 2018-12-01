<div>
    <form action="{{ baseUrl("/settings/users/{$currentUser->id}/switch-book-view") }}" method="POST" class="inline">
        {!! csrf_field() !!}
        {!! method_field('PATCH') !!}
        <input type="hidden" value="{{ $booksViewType === 'list'? 'grid' : 'list' }}" name="view_type">
        @if ($booksViewType === 'list')
            <a onclick="this.closest('form').submit()" type="submit" class="icon-list-item">
                <span class="icon">@icon('grid')</span>
                <span>{{ trans('common.grid_view') }}</span>
            </a>
        @else
            <a onclick="this.closest('form').submit()" type="submit" class="icon-list-item">
                <span class="icon">@icon('list')</span>
                <span>{{ trans('common.list_view') }}</span>
            </a>
        @endif
    </form>
</div>
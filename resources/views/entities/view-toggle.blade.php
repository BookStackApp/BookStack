<div>
    <form action="{{ url("/preferences/change-view/" . $type) }}" method="POST" class="inline">
        {{ csrf_field() }}
        {{ method_field('patch') }}
        <input type="hidden" name="_return" value="{{ url()->current() }}">

        @if ($view === 'list')
            <button type="submit" name="view" value="grid" class="icon-list-item text-link">
                <span class="icon">@icon('grid')</span>
                <span>{{ trans('common.grid_view') }}</span>
            </button>
        @else
            <button type="submit" name="view" value="list" class="icon-list-item text-link">
                <span class="icon">@icon('list')</span>
                <span>{{ trans('common.list_view') }}</span>
            </button>
        @endif
    </form>
</div>
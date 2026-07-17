<div id="actions" class="actions mb-xl">
    <h5>{{ trans('common.actions') }}</h5>
    <div class="icon-list text-link">
        @if(userCan(\BookStack\Permissions\Permission::BookshelfCreateAll))
            <a href="{{ url("/create-shelf") }}" data-shortcut="new" class="icon-list-item">
                <span>@icon('add')</span>
                <span>{{ trans('entities.shelves_new_action') }}</span>
            </a>
        @endif

        @include('entities.view-toggle', ['view' => $view, 'type' => 'bookshelves'])

        <a href="{{ url('/tags') }}" class="icon-list-item">
            <span>@icon('tag')</span>
            <span>{{ trans('entities.tags_view_tags') }}</span>
        </a>
    </div>
</div>
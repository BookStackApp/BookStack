<div id="actions" class="actions mb-xl">
    <h5>{{ trans('common.actions') }}</h5>
    <div class="icon-list text-link">
        @if(userCan(\BookStack\Permissions\Permission::BookCreateAll))
            <a href="{{ url("/create-book") }}" data-shortcut="new" class="icon-list-item">
                <span>@icon('add')</span>
                <span>{{ trans('entities.books_create') }}</span>
            </a>
        @endif

        @include('entities.view-toggle', ['view' => $view, 'type' => 'books'])

        <a href="{{ url('/tags') }}" class="icon-list-item">
            <span>@icon('tag')</span>
            <span>{{ trans('entities.tags_view_tags') }}</span>
        </a>

        @if(userCan(\BookStack\Permissions\Permission::ContentImport))
            <a href="{{ url('/import') }}" class="icon-list-item">
                <span>@icon('upload')</span>
                <span>{{ trans('entities.import') }}</span>
            </a>
        @endif
    </div>
</div>
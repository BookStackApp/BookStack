{{--
$homeView - string - The type of home view being used
$view - string - Configured view type from parent (for books/shelves list/grid view)
--}}
<div class="actions mb-xl">
    <h5>{{ trans('common.actions') }}</h5>
    <div class="icon-list text-link">
        @if($homeView === 'books')
            @if(userCan(\BookStack\Permissions\Permission::BookCreateAll))
                <a href="{{ url("/create-book") }}" class="icon-list-item">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.books_create') }}</span>
                </a>
            @endif
            @include('entities.view-toggle', ['view' => $view, 'type' => 'books'])
            <a href="{{ url('/tags') }}" class="icon-list-item">
                <span>@icon('tag')</span>
                <span>{{ trans('entities.tags_view_tags') }}</span>
            </a>
        @elseif ($homeView === 'bookshelves')
            @if(userCan(\BookStack\Permissions\Permission::BookshelfCreateAll))
                <a href="{{ url("/create-shelf") }}" class="icon-list-item">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.shelves_new_action') }}</span>
                </a>
            @endif
            @include('entities.view-toggle', ['view' => $view, 'type' => 'bookshelves'])
            <a href="{{ url('/tags') }}" class="icon-list-item">
                <span>@icon('tag')</span>
                <span>{{ trans('entities.tags_view_tags') }}</span>
            </a>
        @endif
        @include('home.parts.expand-toggle', ['classes' => 'text-link', 'target' => '.entity-list.compact .entity-item-snippet', 'key' => 'home-details'])
        @include('common.dark-mode-toggle', ['classes' => 'icon-list-item text-link'])
    </div>
</div>
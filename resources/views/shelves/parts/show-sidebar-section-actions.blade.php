<div id="actions" class="actions mb-xl">
    <h5>{{ trans('common.actions') }}</h5>
    <div class="icon-list text-link">

        @if(userCan(\BookStack\Permissions\Permission::BookCreateAll) && userCan(\BookStack\Permissions\Permission::BookshelfUpdate, $shelf))
            <a href="{{ $shelf->getUrl('/create-book') }}" data-shortcut="new" class="icon-list-item">
                <span class="icon">@icon('add')</span>
                <span>{{ trans('entities.books_new_action') }}</span>
            </a>
        @endif

        @include('entities.view-toggle', ['view' => $view, 'type' => 'bookshelf'])

        <hr class="primary-background">

        @if(userCan(\BookStack\Permissions\Permission::BookshelfUpdate, $shelf))
            <a href="{{ $shelf->getUrl('/edit') }}" data-shortcut="edit" class="icon-list-item">
                <span>@icon('edit')</span>
                <span>{{ trans('common.edit') }}</span>
            </a>
        @endif

        @if(userCan(\BookStack\Permissions\Permission::RestrictionsManage, $shelf))
            <a href="{{ $shelf->getUrl('/permissions') }}" data-shortcut="permissions" class="icon-list-item">
                <span>@icon('lock')</span>
                <span>{{ trans('entities.permissions') }}</span>
            </a>
        @endif

        @if(userCan(\BookStack\Permissions\Permission::BookshelfDelete, $shelf))
            <a href="{{ $shelf->getUrl('/delete') }}" data-shortcut="delete" class="icon-list-item">
                <span>@icon('delete')</span>
                <span>{{ trans('common.delete') }}</span>
            </a>
        @endif

        @if(!user()->isGuest())
            <hr class="primary-background">
            @include('entities.favourite-action', ['entity' => $shelf])
        @endif

    </div>
</div>
<div class="actions mb-xl">
    <h5>{{ trans('common.actions') }}</h5>
    <div class="icon-list text-link">

        @if(userCan(\BookStack\Permissions\Permission::PageCreate, $book))
            <a href="{{ $book->getUrl('/create-page') }}" data-shortcut="new" class="icon-list-item">
                <span>@icon('add')</span>
                <span>{{ trans('entities.pages_new') }}</span>
            </a>
        @endif
        @if(userCan(\BookStack\Permissions\Permission::ChapterCreate, $book))
            <a href="{{ $book->getUrl('/create-chapter') }}" data-shortcut="new" class="icon-list-item">
                <span>@icon('add')</span>
                <span>{{ trans('entities.chapters_new') }}</span>
            </a>
        @endif

        <hr class="primary-background">

        @if(userCan(\BookStack\Permissions\Permission::BookUpdate, $book))
            <a href="{{ $book->getUrl('/edit') }}" data-shortcut="edit" class="icon-list-item">
                <span>@icon('edit')</span>
                <span>{{ trans('common.edit') }}</span>
            </a>
            <a href="{{ $book->getUrl('/sort') }}" data-shortcut="sort" class="icon-list-item">
                <span>@icon('sort')</span>
                <span>{{ trans('common.sort') }}</span>
            </a>
        @endif
        @if(userCan(\BookStack\Permissions\Permission::BookCreateAll))
            <a href="{{ $book->getUrl('/copy') }}" data-shortcut="copy" class="icon-list-item">
                <span>@icon('copy')</span>
                <span>{{ trans('common.copy') }}</span>
            </a>
        @endif
        @if(userCan(\BookStack\Permissions\Permission::RestrictionsManage, $book))
            <a href="{{ $book->getUrl('/permissions') }}" data-shortcut="permissions" class="icon-list-item">
                <span>@icon('lock')</span>
                <span>{{ trans('entities.permissions') }}</span>
            </a>
        @endif
        @if(userCan(\BookStack\Permissions\Permission::BookDelete, $book))
            <a href="{{ $book->getUrl('/delete') }}" data-shortcut="delete" class="icon-list-item">
                <span>@icon('delete')</span>
                <span>{{ trans('common.delete') }}</span>
            </a>
        @endif

        <hr class="primary-background">

        @if($watchOptions->canWatch() && !$watchOptions->isWatching())
            @include('entities.watch-action', ['entity' => $book])
        @endif
        @if(!user()->isGuest())
            @include('entities.favourite-action', ['entity' => $book])
        @endif
        @if(userCan(\BookStack\Permissions\Permission::ContentExport))
            @include('entities.export-menu', ['entity' => $book])
        @endif
    </div>
</div>
<div class="actions mb-xl">
    <h5>{{ trans('common.actions') }}</h5>
    <div class="icon-list text-link">

        @if(userCan(\BookStack\Permissions\Permission::PageCreate, $chapter))
            <a href="{{ $chapter->getUrl('/create-page') }}" data-shortcut="new" class="icon-list-item">
                <span>@icon('add')</span>
                <span>{{ trans('entities.pages_new') }}</span>
            </a>
        @endif

        <hr class="primary-background"/>

        @if(userCan(\BookStack\Permissions\Permission::ChapterUpdate, $chapter))
            <a href="{{ $chapter->getUrl('/edit') }}" data-shortcut="edit" class="icon-list-item">
                <span>@icon('edit')</span>
                <span>{{ trans('common.edit') }}</span>
            </a>
        @endif
        @if(userCanOnAny(\BookStack\Permissions\Permission::Create, \BookStack\Entities\Models\Book::class) || userCan(\BookStack\Permissions\Permission::ChapterCreateAll) || userCan(\BookStack\Permissions\Permission::ChapterCreateOwn))
            <a href="{{ $chapter->getUrl('/copy') }}" data-shortcut="copy" class="icon-list-item">
                <span>@icon('copy')</span>
                <span>{{ trans('common.copy') }}</span>
            </a>
        @endif
        @if(userCan(\BookStack\Permissions\Permission::ChapterUpdate, $chapter) && userCan(\BookStack\Permissions\Permission::ChapterDelete, $chapter))
            <a href="{{ $chapter->getUrl('/move') }}" data-shortcut="move" class="icon-list-item">
                <span>@icon('folder')</span>
                <span>{{ trans('common.move') }}</span>
            </a>
        @endif
        @if(userCan(\BookStack\Permissions\Permission::RestrictionsManage, $chapter))
            <a href="{{ $chapter->getUrl('/permissions') }}" data-shortcut="permissions" class="icon-list-item">
                <span>@icon('lock')</span>
                <span>{{ trans('entities.permissions') }}</span>
            </a>
        @endif
        @if(userCan(\BookStack\Permissions\Permission::ChapterDelete, $chapter))
            <a href="{{ $chapter->getUrl('/delete') }}" data-shortcut="delete" class="icon-list-item">
                <span>@icon('delete')</span>
                <span>{{ trans('common.delete') }}</span>
            </a>
        @endif

        @if($chapter->book && userCan(\BookStack\Permissions\Permission::BookUpdate, $chapter->book))
            <hr class="primary-background"/>
            <a href="{{ $chapter->book->getUrl('/sort') }}" data-shortcut="sort" class="icon-list-item">
                <span>@icon('sort')</span>
                <span>{{ trans('entities.chapter_sort_book') }}</span>
            </a>
        @endif

        <hr class="primary-background"/>

        @if($watchOptions->canWatch() && !$watchOptions->isWatching())
            @include('entities.watch-action', ['entity' => $chapter])
        @endif
        @if(!user()->isGuest())
            @include('entities.favourite-action', ['entity' => $chapter])
        @endif
        @if(userCan(\BookStack\Permissions\Permission::ContentExport))
            @include('entities.export-menu', ['entity' => $chapter])
        @endif
    </div>
</div>
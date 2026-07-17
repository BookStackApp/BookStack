<div id="actions" class="actions mb-xl">
    <h5>{{ trans('common.actions') }}</h5>

    <div class="icon-list text-link">

        {{--User Actions--}}
        @if(userCan(\BookStack\Permissions\Permission::PageUpdate, $page))
            <a href="{{ $page->getUrl('/edit') }}" data-shortcut="edit" class="icon-list-item">
                <span>@icon('edit')</span>
                <span>{{ trans('common.edit') }}</span>
            </a>
        @endif
        @if(userCan(\BookStack\Permissions\Permission::PageCreateAll) || userCan(\BookStack\Permissions\Permission::PageCreateOwn) || userCanOnAny(\BookStack\Permissions\Permission::Create, \BookStack\Entities\Models\Book::class) || userCanOnAny(\BookStack\Permissions\Permission::Create, \BookStack\Entities\Models\Chapter::class))
            <a href="{{ $page->getUrl('/copy') }}" data-shortcut="copy" class="icon-list-item">
                <span>@icon('copy')</span>
                <span>{{ trans('common.copy') }}</span>
            </a>
        @endif
        @if(userCan(\BookStack\Permissions\Permission::PageUpdate, $page))
            @if(userCan(\BookStack\Permissions\Permission::PageDelete, $page))
                <a href="{{ $page->getUrl('/move') }}" data-shortcut="move" class="icon-list-item">
                    <span>@icon('folder')</span>
                    <span>{{ trans('common.move') }}</span>
                </a>
            @endif
        @endif
        @if(userCan(\BookStack\Permissions\Permission::RevisionViewAll))
            <a href="{{ $page->getUrl('/revisions') }}" data-shortcut="revisions" class="icon-list-item">
                <span>@icon('history')</span>
                <span>{{ trans('entities.revisions') }}</span>
            </a>
        @endif
        @if(userCan(\BookStack\Permissions\Permission::RestrictionsManage, $page))
            <a href="{{ $page->getUrl('/permissions') }}" data-shortcut="permissions" class="icon-list-item">
                <span>@icon('lock')</span>
                <span>{{ trans('entities.permissions') }}</span>
            </a>
        @endif
        @if(userCan(\BookStack\Permissions\Permission::PageDelete, $page))
            <a href="{{ $page->getUrl('/delete') }}" data-shortcut="delete" class="icon-list-item">
                <span>@icon('delete')</span>
                <span>{{ trans('common.delete') }}</span>
            </a>
        @endif

        <hr class="primary-background"/>

        @if($watchOptions->canWatch() && !$watchOptions->isWatching())
            @include('entities.watch-action', ['entity' => $page])
        @endif
        @if(!user()->isGuest())
            @include('entities.favourite-action', ['entity' => $page])
        @endif
        @if(userCan(\BookStack\Permissions\Permission::ContentExport))
            @include('entities.export-menu', ['entity' => $page])
        @endif
    </div>

</div>
<div id="page-details" class="entity-details mb-xl">
    <h5>{{ trans('common.details') }}</h5>
    <div class="blended-links">
        @include('entities.meta', ['entity' => $page, 'watchOptions' => $watchOptions])

        @if($book->hasPermissions())
            <div class="active-restriction">
                @if(userCan(\BookStack\Permissions\Permission::RestrictionsManage, $book))
                    <a href="{{ $book->getUrl('/permissions') }}" class="entity-meta-item">
                        @icon('lock')
                        <div>{{ trans('entities.books_permissions_active') }}</div>
                    </a>
                @else
                    <div class="entity-meta-item">
                        @icon('lock')
                        <div>{{ trans('entities.books_permissions_active') }}</div>
                    </div>
                @endif
            </div>
        @endif

        @if($page->chapter && $page->chapter->hasPermissions())
            <div class="active-restriction">
                @if(userCan(\BookStack\Permissions\Permission::RestrictionsManage, $page->chapter))
                    <a href="{{ $page->chapter->getUrl('/permissions') }}" class="entity-meta-item">
                        @icon('lock')
                        <div>{{ trans('entities.chapters_permissions_active') }}</div>
                    </a>
                @else
                    <div class="entity-meta-item">
                        @icon('lock')
                        <div>{{ trans('entities.chapters_permissions_active') }}</div>
                    </div>
                @endif
            </div>
        @endif

        @if($page->hasPermissions())
            <div class="active-restriction">
                @if(userCan(\BookStack\Permissions\Permission::RestrictionsManage, $page))
                    <a href="{{ $page->getUrl('/permissions') }}" class="entity-meta-item">
                        @icon('lock')
                        <div>{{ trans('entities.pages_permissions_active') }}</div>
                    </a>
                @else
                    <div class="entity-meta-item">
                        @icon('lock')
                        <div>{{ trans('entities.pages_permissions_active') }}</div>
                    </div>
                @endif
            </div>
        @endif

        @if($page->template)
            <div class="entity-meta-item">
                @icon('template')
                <div>{{ trans('entities.pages_is_template') }}</div>
            </div>
        @endif
    </div>
</div>
<div class="mb-xl">
    <h5>{{ trans('common.details') }}</h5>
    <div class="blended-links">
        @include('entities.meta', ['entity' => $chapter, 'watchOptions' => $watchOptions])

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

        @if($chapter->hasPermissions())
            <div class="active-restriction">
                @if(userCan(\BookStack\Permissions\Permission::RestrictionsManage, $chapter))
                    <a href="{{ $chapter->getUrl('/permissions') }}" class="entity-meta-item">
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
    </div>
</div>
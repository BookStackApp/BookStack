<div class="item-list-row flex-container-row items-center wrap">
    <div class="flex fit-content min-width-xxxxs px-m py-xs">
        <span class="hide-over-l">{{ trans('entities.pages_revisions_number') }}</span>
        {{ $revision->revision_number == 0 ? '' : $revision->revision_number }}
    </div>
    <div class="flex-2 px-m py-xs min-width-s">
        {{ $revision->name }}
        <br>
        <small class="text-muted">(<strong class="hide-over-l">{{ trans('entities.pages_revisions_editor') }}: </strong>{{ $revision->is_markdown ? 'Markdown' : 'WYSIWYG' }})</small>
    </div>
    <div class="flex-3 px-m py-xs min-width-l">
        <div class="flex-container-row items-center gap-s">
            @if($revision->createdBy)
                <img class="avatar flex-none" height="30" width="30" src="{{ $revision->createdBy->getAvatar(30) }}" alt="{{ $revision->createdBy->name }}">
            @endif
            <div>
                @if($revision->createdBy) {{ $revision->createdBy->name }} @else {{ trans('common.deleted_user') }} @endif
                <br>
                <div class="text-muted">
                    <small>{{ $revision->created_at->formatLocalized('%e %B %Y %H:%M:%S') }}</small>
                    <small>({{ $revision->created_at->diffForHumans() }})</small>
                </div>
            </div>
        </div>
    </div>
    <div class="flex-2 px-m py-xs min-width-m text-small">
        {{ $revision->summary }}
    </div>
    <div class="flex-2 px-m py-xs actions text-small text-l-right min-width-l">
        <a href="{{ $revision->getUrl('changes') }}" target="_blank" rel="noopener">{{ trans('entities.pages_revisions_changes') }}</a>
        <span class="text-muted opacity-70">&nbsp;|&nbsp;</span>


        @if ($current)
            <a target="_blank" rel="noopener" href="{{ $revision->page->getUrl() }}"><i>{{ trans('entities.pages_revisions_current') }}</i></a>
        @else
            <a href="{{ $revision->getUrl() }}" target="_blank" rel="noopener">{{ trans('entities.pages_revisions_preview') }}</a>

            @if(userCan('page-update', $revision->page))
                <span class="text-muted opacity-70">&nbsp;|&nbsp;</span>
                <div component="dropdown" class="dropdown-container">
                    <a refs="dropdown@toggle" href="#" aria-haspopup="true" aria-expanded="false">{{ trans('entities.pages_revisions_restore') }}</a>
                    <ul refs="dropdown@menu" class="dropdown-menu" role="menu">
                        <li class="px-m py-s"><small class="text-muted">{{trans('entities.revision_restore_confirm')}}</small></li>
                        <li>
                            <form action="{{ $revision->getUrl('/restore') }}" method="POST">
                                {!! csrf_field() !!}
                                <input type="hidden" name="_method" value="PUT">
                                <button type="submit" class="text-primary icon-item">
                                    @icon('history')
                                    <div>{{ trans('entities.pages_revisions_restore') }}</div>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endif

            @if(userCan('page-delete', $revision->page))
                <span class="text-muted opacity-70">&nbsp;|&nbsp;</span>
                <div component="dropdown" class="dropdown-container">
                    <a refs="dropdown@toggle" href="#" aria-haspopup="true" aria-expanded="false">{{ trans('common.delete') }}</a>
                    <ul refs="dropdown@menu" class="dropdown-menu" role="menu">
                        <li class="px-m py-s"><small class="text-muted">{{trans('entities.revision_delete_confirm')}}</small></li>
                        <li>
                            <form action="{{ $revision->getUrl('/delete/') }}" method="POST">
                                {!! csrf_field() !!}
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="text-neg icon-item">
                                    @icon('delete')
                                    <div>{{ trans('common.delete') }}</div>
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endif
        @endif
    </div>
</div>
@php
    $entityType = $entity->getType();
    $entityId = $entity->id;
    $entityUrl = $entity->getUrl();
    $isFavourite = $entity->isFavourite();
    $watchOptions = $entity->watchOptions;
@endphp

<div components="context-menu" class="context-menu" data-entity-type="{{ $entityType }}" data-entity-id="{{ $entityId }}">
    <ul>
        @if(userCan('view', $entity))
        <li><a href="{{ $entityUrl }}">View</a></li>
        @endif
        @if($entityType === 'bookshelf' && userCan('book-create', $entity))
        <li><a href="{{ $entityUrl . '/create-book' }}">{{ trans('entities.books_new') }}</a></li>
        @endif
        @if($entityType === 'book')
        @if(userCan('page-create', $entity))
        <li><a href="{{ $entityUrl . '/create-page' }}">{{ trans('entities.pages_new') }}</a></li>
        <li><a href="{{ $entityUrl . '/create-chapter' }}">{{ trans('entities.chapters_new') }}</a></li>
        @endif
        @if(userCan('book-update', $entity))
        <li><a href="{{ $entityUrl . '/sort' }}">{{ trans('common.sort') }}</a></li>
        <li><a href="{{ $entityUrl . '/copy' }}">{{ trans('common.copy') }}</a></li>
        @endif
        @endif
        @if(userCan('book-update', $entity))
        <li><a href="{{ $entityUrl . '/edit' }}">{{ trans('common.edit') }}</a></li>
        @endif
        @if(userCan('restrictions-manage', $entity))
        <li><a href="{{ $entityUrl . '/permissions' }}">{{ trans('entities.permissions') }}</a></li>
        @endif
        @if(userCan('book-delete', $entity))
        <li><a href="{{ $entityUrl . '/delete' }}">{{ trans('common.delete') }}</a></li>
        @endif

        @if($entityType === 'book')
        @if($watchOptions && $watchOptions->canWatch() && !$watchOptions->isWatching())
        <form action="{{ url('/watching/update') }}" method="POST">
            {{ csrf_field() }}
            {{ method_field('PUT') }}
            <input type="hidden" name="type" value="{{ $entity->getMorphClass() }}">
            <input type="hidden" name="id" value="{{ $entity->id }}">
            <button type="submit"
                    name="level"
                    value="updates"
                    style="padding: 0px; width: 100%;">
                <li><a style="text-align: start;">{{ trans('entities.watch') }}</a></li>
            </button>
        </form>
        @endif
        @endif

        @if(!user()->isGuest())
        <form action="{{ url('/favourites/' . ($isFavourite ? 'remove' : 'add')) }}" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="type" value="{{ $entity->getMorphClass() }}">
            <input type="hidden" name="id" value="{{ $entity->id }}">
            <button type="submit" data-shortcut="favourite" style="padding: 0px; width: 100%;">
                <li><a style="text-align: start;">{{ $isFavourite ? trans('common.unfavourite') : trans('common.favourite') }}</a></li>
            </button>
        </form>
        @endif

        @if($entityType === 'book' && userCan('content-export'))
        <div component="dropdown" class="dropdown-container" id="export-menu" style="width: 100%;">
            <div refs="dropdown@toggle" aria-haspopup="true" aria-expanded="false" aria-label="{{ trans('entities.export') }}" data-shortcut="export" tabindex="0">
                <li><a>{{ trans('entities.export') }}</a></li>
            </div>
            <ul refs="dropdown@menu" class="wide dropdown-menu" role="menu">
                <li><a href="{{ $entity->getUrl('/export/html') }}" target="_blank" class="label-item"><span>{{ trans('entities.export_html') }}</span><span>.html</span></a></li>
                <li><a href="{{ $entity->getUrl('/export/pdf') }}" target="_blank" class="label-item"><span>{{ trans('entities.export_pdf') }}</span><span>.pdf</span></a></li>
                <li><a href="{{ $entity->getUrl('/export/plaintext') }}" target="_blank" class="label-item"><span>{{ trans('entities.export_text') }}</span><span>.txt</span></a></li>
                <li><a href="{{ $entity->getUrl('/export/markdown') }}" target="_blank" class="label-item"><span>{{ trans('entities.export_md') }}</span><span>.md</span></a></li>
            </ul>
        </div>
        @endif
    </ul>
</div>

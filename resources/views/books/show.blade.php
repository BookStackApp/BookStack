@extends('tri-layout')

@section('container-attrs')
    id="entity-dashboard"
    entity-id="{{ $book->id }}"
    entity-type="book"
@stop

@section('body')

    <div class="mb-s">
        @include('partials.breadcrumbs', ['crumbs' => [
            $book,
        ]])
    </div>

    <div class="content-wrap card">
        <h1 class="break-text" v-pre>{{$book->name}}</h1>
        <div class="book-content" v-show="!searching">
            <p class="text-muted" v-pre>{!! nl2br(e($book->description)) !!}</p>
            @if(count($bookChildren) > 0)
                <div class="entity-list book-contents" v-pre>
                    @foreach($bookChildren as $childElement)
                        @if($childElement->isA('chapter'))
                            @include('chapters.list-item', ['chapter' => $childElement])
                        @else
                            @include('pages.list-item', ['page' => $childElement])
                        @endif
                    @endforeach
                </div>
            @else
                <div class="mt-xl" v-pre>
                    <hr>
                    <p class="text-muted italic mb-m mt-xl">{{ trans('entities.books_empty_contents') }}</p>

                    <div class="icon-list block inline">
                        @if(userCan('page-create', $book))
                            <a href="{{ $book->getUrl('/create-page') }}" class="icon-list-item text-page">
                                <span class="icon">@icon('page')</span>
                                <span>{{ trans('entities.books_empty_create_page') }}</span>
                            </a>
                        @endif
                        @if(userCan('chapter-create', $book))
                            <a href="{{ $book->getUrl('/create-chapter') }}" class="icon-list-item text-chapter">
                                <span class="icon">@icon('chapter')</span>
                                <span>{{ trans('entities.books_empty_add_chapter') }}</span>
                            </a>
                        @endif
                    </div>

                </div>
            @endif
        </div>

        @include('partials.entity-dashboard-search-results')
    </div>

@stop


@section('right')

    <div class="mb-xl">
        <h5>{{ trans('common.details') }}</h5>
        <div class="text-small text-muted blended-links">
            @include('partials.entity-meta', ['entity' => $book])
            @if($book->restricted)
                <div class="active-restriction">
                    @if(userCan('restrictions-manage', $book))
                        <a href="{{ $book->getUrl('/permissions') }}">@icon('lock'){{ trans('entities.books_permissions_active') }}</a>
                    @else
                        @icon('lock'){{ trans('entities.books_permissions_active') }}
                    @endif
                </div>
            @endif
        </div>
    </div>


    <div class="actions mb-xl">
        <h5>{{ trans('common.actions') }}</h5>
        <div class="icon-list text-primary">
            <div dropdown class="dropdown-container">
                <div dropdown-toggle class="icon-list-item">
                    <span>@icon('export')</span>
                    <span>{{ trans('entities.export') }}</span>
                </div>
                <ul class="wide">
                    <li><a href="{{ $book->getUrl('/export/html') }}" target="_blank">{{ trans('entities.export_html') }} <span class="text-muted float right">.html</span></a></li>
                    <li><a href="{{ $book->getUrl('/export/pdf') }}" target="_blank">{{ trans('entities.export_pdf') }} <span class="text-muted float right">.pdf</span></a></li>
                    <li><a href="{{ $book->getUrl('/export/plaintext') }}" target="_blank">{{ trans('entities.export_text') }} <span class="text-muted float right">.txt</span></a></li>
                </ul>
            </div>

            @if(userCan('page-create', $book))
                <a href="{{ $book->getUrl('/create-page') }}" class="icon-list-item">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.pages_new') }}</span>
                </a>
            @endif
            @if(userCan('chapter-create', $book))
                <a href="{{ $book->getUrl('/create-chapter') }}" class="icon-list-item">
                    <span>@icon('add')</span>
                    <span>{{ trans('entities.chapters_new') }}</span>
                </a>
            @endif
            @if(userCan('book-update', $book))
                <a href="{{ $book->getUrl('/edit') }}" class="icon-list-item">
                    <span>@icon('edit')</span>
                    <span>{{ trans('common.edit') }}</span>
                </a>
                <a href="{{ $book->getUrl('/sort') }}" class="icon-list-item">
                    <span>@icon('sort')</span>
                    <span>{{ trans('common.sort') }}</span>
                </a>
            @endif
            @if(userCan('restrictions-manage', $book))
                <a href="{{ $book->getUrl('/permissions') }}" class="icon-list-item">
                    <span>@icon('lock')</span>
                    <span>{{ trans('entities.permissions') }}</span>
                </a>
            @endif
            @if(userCan('book-delete', $book))
                <a href="{{ $book->getUrl('/delete') }}" class="icon-list-item">
                    <span>@icon('delete')</span>
                    <span>{{ trans('common.delete') }}</span>
                </a>
            @endif
        </div>
    </div>

@stop

@section('left')

    @include('partials.entity-dashboard-search-box')

    @if($book->tags->count() > 0)
        <div class="mb-xl">
            @include('components.tag-list', ['entity' => $book])
        </div>
    @endif

    @if(count($activity) > 0)
        <div class="mb-xl">
            <h5>{{ trans('entities.recent_activity') }}</h5>
            @include('partials.activity-list', ['activity' => $activity])
        </div>
    @endif
@stop


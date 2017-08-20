@extends('sidebar-layout')

@section('toolbar')
    <div class="col-sm-8 col-xs-5 faded">
        @include('pages._breadcrumbs', ['page' => $page])
    </div>
    <div class="col-sm-4 col-xs-7 faded">
        <div class="action-buttons">
            <span dropdown class="dropdown-container">
                <div dropdown-toggle class="text-button text-primary"><i class="zmdi zmdi-open-in-new"></i>{{ trans('entities.export') }}</div>
                <ul class="wide">
                    <li><a href="{{ $page->getUrl('/export/html') }}" target="_blank">{{ trans('entities.export_html') }} <span class="text-muted float right">.html</span></a></li>
                    <li><a href="{{ $page->getUrl('/export/pdf') }}" target="_blank">{{ trans('entities.export_pdf') }} <span class="text-muted float right">.pdf</span></a></li>
                    <li><a href="{{ $page->getUrl('/export/plaintext') }}" target="_blank">{{ trans('entities.export_text') }} <span class="text-muted float right">.txt</span></a></li>
                </ul>
            </span>
            @if(userCan('page-update', $page))
                <a href="{{ $page->getUrl('/edit') }}" class="text-primary text-button" ><i class="zmdi zmdi-edit"></i>{{ trans('common.edit') }}</a>
            @endif
            @if(userCan('page-update', $page) || userCan('restrictions-manage', $page) || userCan('page-delete', $page))
                <div dropdown class="dropdown-container">
                    <a dropdown-toggle class="text-primary text-button"><i class="zmdi zmdi-more-vert"></i></a>
                    <ul>
                        @if(userCan('page-update', $page))
                            <li><a href="{{ $page->getUrl('/move') }}" class="text-primary" ><i class="zmdi zmdi-folder"></i>{{ trans('common.move') }}</a></li>
                            <li><a href="{{ $page->getUrl('/revisions') }}" class="text-primary"><i class="zmdi zmdi-replay"></i>{{ trans('entities.revisions') }}</a></li>
                        @endif
                        @if(userCan('restrictions-manage', $page))
                            <li><a href="{{ $page->getUrl('/permissions') }}" class="text-primary"><i class="zmdi zmdi-lock-outline"></i>{{ trans('entities.permissions') }}</a></li>
                        @endif
                        @if(userCan('page-delete', $page))
                            <li><a href="{{ $page->getUrl('/delete') }}" class="text-neg"><i class="zmdi zmdi-delete"></i>{{ trans('common.delete') }}</a></li>
                        @endif
                    </ul>
                </div>
            @endif

        </div>
    </div>
@stop

@section('sidebar')
    @if($book->restricted || ($page->chapter && $page->chapter->restricted) || $page->restricted)
        <div class="card">
            <h3><i class="zmdi zmdi-key"></i> {{ trans('entities.permissions') }}</h3>
            <div class="body">
                <div class="text-muted">

                    @if($book->restricted)
                        @if(userCan('restrictions-manage', $book))
                            <a href="{{ $book->getUrl('/permissions') }}"><i class="zmdi zmdi-lock-outline"></i>{{ trans('entities.books_permissions_active') }}</a>
                        @else
                            <i class="zmdi zmdi-lock-outline"></i>{{ trans('entities.books_permissions_active') }}
                        @endif
                        <br>
                    @endif

                    @if($page->chapter && $page->chapter->restricted)
                        @if(userCan('restrictions-manage', $page->chapter))
                            <a href="{{ $page->chapter->getUrl('/permissions') }}"><i class="zmdi zmdi-lock-outline"></i>{{ trans('entities.chapters_permissions_active') }}</a>
                        @else
                            <i class="zmdi zmdi-lock-outline"></i>{{ trans('entities.chapters_permissions_active') }}
                        @endif
                        <br>
                    @endif

                    @if($page->restricted)
                        @if(userCan('restrictions-manage', $page))
                            <a href="{{ $page->getUrl('/permissions') }}"><i class="zmdi zmdi-lock-outline"></i>{{ trans('entities.pages_permissions_active') }}</a>
                        @else
                            <i class="zmdi zmdi-lock-outline"></i>{{ trans('entities.pages_permissions_active') }}
                        @endif
                        <br>
                    @endif
                </div>
            </div>
        </div>
    @endif

    @include('pages/sidebar-tree-list', ['book' => $book, 'sidebarTree' => $sidebarTree, 'pageNav' => $pageNav])

    <div class="card">
        <h3><i class="zmdi zmdi-info-outline"></i> {{ trans('common.details') }}</h3>
        <div class="body">
            @include('partials.entity-meta', ['entity' => $book])
        </div>
    </div>
@stop

@section('body')
    <div class="page-content" ng-non-bindable>

        <div class="pointer-container" id="pointer">
            <div class="pointer anim" >
                <span class="icon text-primary"><i class="zmdi zmdi-link"></i></span>
                <input readonly="readonly" type="text" id="pointer-url" placeholder="url">
                <button class="button icon" data-clipboard-target="#pointer-url" type="button" title="{{ trans('entities.pages_copy_link') }}"><i class="zmdi zmdi-copy"></i></button>
            </div>
        </div>

        @include('pages/page-display')

    </div>
    <div class="container small">
        @include('comments/comments', ['pageId' => $page->id])
    </div>
@stop

@section('scripts')
    <script>
        setupPageShow({{$page->id}});
    </script>
@stop

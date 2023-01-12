@extends('layouts.tri')

@section('left')
    <div id="revision-details" class="entity-details mb-xl">
        <h5>{{ trans('common.details') }}</h5>
        <div class="body text-small text-muted">
            @include('entities.meta', ['entity' => $revision])
        </div>
    </div>
@stop

@section('body')

    <div class="mb-m print-hidden">
        @include('entities.breadcrumbs', ['crumbs' => [
            $page->$book,
            $page->chapter,
            $page,
            $page->getUrl('/revisions') => [
                'text' => trans('entities.pages_revisions'),
                'icon' => 'history',
            ],
            $revision->getUrl('/changes') => $diff ? trans('entities.pages_revisions_numbered_changes', ['id' => $revision->id]) : null,
            $revision->getUrl() => !$diff ? trans('entities.pages_revisions_numbered', ['id' => $revision->id]) : null,
        ]])
    </div>

    <main class="card content-wrap">
        <div class="page-content page-revision">
            @include('pages.parts.page-display')
        </div>
    </main>

@stop
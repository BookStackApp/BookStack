@extends('layouts.simple')

@section('body')
    <div class="container">

        <div class="my-s">
            @include('entities.breadcrumbs', ['crumbs' => [
                $page->book,
                $page->chapter,
                $page,
                $page->getUrl('/revisions') => [
                    'text' => trans('entities.pages_revisions'),
                    'icon' => 'history',
                ]
            ]])
        </div>

        <main class="card content-wrap">
            <h1 class="list-heading">{{ trans('entities.pages_revisions') }}</h1>

            <p class="text-muted">{{ trans('entities.pages_revisions_desc') }}</p>

            <div class="flex-container-row my-m items-center justify-space-between wrap gap-x-m gap-y-s">
                {{ $revisions->links() }}
                <div>
                    @include('common.sort', $listOptions->getSortControlData())
                </div>
            </div>

            @if(count($revisions) > 0)
                <div class="item-list">
                    <div class="item-list-row flex-container-row items-center strong hide-under-l">
                        <div class="flex fit-content min-width-xxxxs px-m py-xs">{{ trans('entities.pages_revisions_number') }}</div>
                        <div class="flex-2 px-m py-xs">{{ trans('entities.pages_name') }} / {{ trans('entities.pages_revisions_editor') }}</div>
                        <div class="flex-3 px-m py-xs">{{ trans('entities.pages_revisions_created_by') }} / {{ trans('entities.pages_revisions_date') }}</div>
                        <div class="flex-2 px-m py-xs">{{ trans('entities.pages_revisions_changelog') }}</div>
                        <div class="flex-2 px-m py-xs text-right">{{ trans('common.actions') }}</div>
                    </div>
                    @foreach($revisions as $index => $revision)
                        @include('pages.parts.revisions-index-row', ['revision' => $revision, 'current' => $page->revision_count === $revision->revision_number])
                    @endforeach
                </div>
            @else
                <p>{{ trans('entities.pages_revisions_none') }}</p>
            @endif

            <div class="my-m">
                {{ $revisions->links() }}
            </div>
        </main>

    </div>

@stop

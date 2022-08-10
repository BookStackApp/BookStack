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
            @if(count($revisions) > 0)

                <table class="table">
                    <tr>
                        <th width="56">{{ trans('entities.pages_revisions_number') }}</th>
                        <th>
                            {{ trans('entities.pages_name') }} / {{ trans('entities.pages_revisions_editor') }}
                        </th>
                        <th colspan="2">{{ trans('entities.pages_revisions_created_by') }} / {{ trans('entities.pages_revisions_date') }}</th>
                        <th>{{ trans('entities.pages_revisions_changelog') }}</th>
                        <th class="text-right">{{ trans('common.actions') }}</th>
                    </tr>
                    @foreach($revisions as $index => $revision)
                        @include('pages.parts.revision-table-row', ['revision' => $revision])
                    @endforeach
                </table>

            @else
                <p>{{ trans('entities.pages_revisions_none') }}</p>
            @endif
        </main>

    </div>

@stop

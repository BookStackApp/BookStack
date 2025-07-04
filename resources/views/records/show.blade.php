@extends('layouts.tri')

@section('container-attrs')
    component="entity-search"
    option:entity-search:entity-id="{{ $record->id }}"
    option:entity-search:entity-type="record"
@stop

@push('social-meta')
    <meta property="og:description" content="{{ Str::limit($record->description, 100, '...') }}">
    @if($record->cover)
        <meta property="og:image" content="{{ $record->getRecordCover() }}">
    @endif
@endpush

@include('entities.body-tag-classes', ['entity' => $record])

@section('body')

    <div class="mb-s print-hidden">
        @include('entities.breadcrumbs', ['crumbs' => [
            $record,
        ]])
    </div>

    <main class="content-wrap card">
        <h1 class="break-text">{{$record->name}}</h1>
        <div refs="entity-search@contentView" class="record-content">
            <div class="text-muted break-text">{!! $record->descriptionHtml() !!}</div>
            @if(count($recordChildren) > 0)
                <div class="entity-list record-contents">
                    @foreach($recordChildren as $childElement)
                        @if($childElement->isA('chapter'))
                            @include('chapters.parts.list-item', ['chapter' => $childElement])
                        @else
                            @include('pages.parts.list-item', ['page' => $childElement])
                        @endif
                    @endforeach
                </div>
            @else
                <div class="mt-xl">
                    <hr>
                    <p class="text-muted italic mb-m mt-xl">{{ trans('entities.records_empty_contents') }}</p>

                    <div class="icon-list block inline">
                        @if(userCan('page-create', $record))
                            <a href="{{ $record->getUrl('/create-page') }}" class="icon-list-item text-page">
                                <span class="icon">@icon('page')</span>
                                <span>{{ trans('entities.records_empty_create_page') }}</span>
                            </a>
                        @endif
                        @if(userCan('chapter-create', $record))
                            <a href="{{ $record->getUrl('/create-chapter') }}" class="icon-list-item text-chapter">
                                <span class="icon">@icon('chapter')</span>
                                <span>{{ trans('entities.records_empty_add_chapter') }}</span>
                            </a>
                        @endif
                    </div>

                </div>
            @endif
        </div>
    </main>
@stop

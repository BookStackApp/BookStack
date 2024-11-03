@extends('layouts.simple')

@section('body')

    <div class="container small">

        <main class="card content-wrap auto-height mt-xxl">
            <h1 class="list-heading">{{ trans('entities.import_continue') }}</h1>
            <p class="text-muted">{{ trans('entities.import_continue_desc') }}</p>

            <div class="mb-m">
                @php
                    $type = $import->getType();
                @endphp
                <div class="flex-container-row items-center justify-space-between wrap">
                    <div class="py-s">
                        <p class="text-{{ $type }} mb-xs bold">@icon($type) {{ $import->name }}</p>
                        @if($type === 'book')
                            <p class="text-chapter mb-xs ml-l">@icon('chapter') {{ trans_choice('entities.x_chapters', $import->chapter_count) }}</p>
                        @endif
                        @if($type === 'book' || $type === 'chapter')
                            <p class="text-page mb-xs ml-l">@icon('page') {{ trans_choice('entities.x_pages', $import->page_count) }}</p>
                        @endif
                    </div>
                    <div class="py-s">
                        <div class="opacity-80">
                            <strong>{{ trans('entities.import_size') }}</strong>
                            <span>{{ $import->getSizeString() }}</span>
                        </div>
                        <div class="opacity-80">
                            <strong>{{ trans('entities.import_uploaded_at') }}</strong>
                            <span title="{{ $import->created_at->toISOString() }}">{{ $import->created_at->diffForHumans() }}</span>
                        </div>
                        @if($import->createdBy)
                            <div class="opacity-80">
                                <strong>{{ trans('entities.import_uploaded_by') }}</strong>
                                <a href="{{ $import->createdBy->getProfileUrl() }}">{{ $import->createdBy->name }}</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <form id="import-run-form"
                  action="{{ $import->getUrl() }}"
                  method="POST">
                {{ csrf_field() }}
            </form>

            <div class="text-right">
                <a href="{{ url('/import') }}" class="button outline">{{ trans('common.cancel') }}</a>
                <div component="dropdown" class="inline block mx-s">
                    <button refs="dropdown@toggle"
                            type="button"
                            title="{{ trans('common.delete') }}"
                            class="button outline">{{ trans('common.delete') }}</button>
                    <div refs="dropdown@menu" class="dropdown-menu">
                        <p class="text-neg bold small px-m mb-xs">{{ trans('entities.import_delete_confirm') }}</p>
                        <p class="small px-m mb-xs">{{ trans('entities.import_delete_desc') }}</p>
                        <button type="submit" form="import-delete-form" class="text-link small text-item">{{ trans('common.confirm') }}</button>
                    </div>
                </div>
                <button type="submit" form="import-run-form" class="button">{{ trans('entities.import_run') }}</button>
            </div>
        </main>
    </div>

    <form id="import-delete-form"
          action="{{ $import->getUrl() }}"
          method="post">
        {{ method_field('DELETE') }}
        {{ csrf_field() }}
    </form>

@stop

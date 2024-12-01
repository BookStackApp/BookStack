@extends('layouts.simple')

@section('body')
    <div class="container small">

        <main class="card content-wrap auto-height mt-xxl">
            <h1 class="list-heading">{{ trans('entities.import_continue') }}</h1>
            <p class="text-muted">{{ trans('entities.import_continue_desc') }}</p>

            @if(session()->has('import_errors'))
                <div class="mb-m">
                    <label class="setting-list-label mb-xs text-neg">@icon('warning') {{ trans('entities.import_errors') }}</label>
                    <p class="mb-xs small">{{ trans('entities.import_errors_desc') }}</p>
                    @foreach(session()->get('import_errors') ?? [] as $error)
                        <p class="mb-none text-neg">{{ $error }}</p>
                    @endforeach
                    <hr class="mt-m">
                </div>
            @endif

            <div class="mb-m">
                <label class="setting-list-label mb-m">{{ trans('entities.import_details') }}</label>
                <div class="flex-container-row justify-space-between wrap">
                    <div>
                        @include('exports.parts.import-item', ['type' => $import->type, 'model' => $data])
                    </div>
                    <div class="text-right text-muted">
                        <div>{{ trans('entities.import_size', ['size' => $import->getSizeString()]) }}</div>
                        <div><span title="{{ $import->created_at->toISOString() }}">{{ trans('entities.import_uploaded_at', ['relativeTime' => $import->created_at->diffForHumans()]) }}</span></div>
                        @if($import->createdBy)
                            <div>
                                {{ trans('entities.import_uploaded_by') }}
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

                @if($import->type === 'page' || $import->type === 'chapter')
                    <hr>
                    <label class="setting-list-label">{{ trans('entities.import_location') }}</label>
                    <p class="small mb-s">{{ trans('entities.import_location_desc') }}</p>
                    @if($errors->has('parent'))
                        <div class="mb-s">
                            @include('form.errors', ['name' => 'parent'])
                        </div>
                    @endif
                    @include('entities.selector', [
                        'name' => 'parent',
                        'entityTypes' => $import->type === 'page' ? 'chapter,book' : 'book',
                        'entityPermission' => "{$import->type}-create",
                        'selectorSize' => 'compact small',
                    ])
                @endif

                <div class="flex-container-row items-center justify-flex-end">
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
                    <button component="loading-button" type="submit" class="button">{{ trans('entities.import_run') }}</button>
                </div>
            </form>
        </main>
    </div>

    <form id="import-delete-form"
          action="{{ $import->getUrl() }}"
          method="post">
        {{ method_field('DELETE') }}
        {{ csrf_field() }}
    </form>

@stop

@push('head')
    <script src="{{ versioned_asset('libs/tinymce/tinymce.min.js') }}" nonce="{{ $cspNonce }}"></script>
@endpush

{{ csrf_field() }}
<div class="form-group title-input">
    <label for="name">{{ trans('common.name') }}</label>
    @include('form.text', ['name' => 'name', 'autofocus' => true])
</div>

<div class="form-group description-input">
    <label for="description_html">{{ trans('common.description') }}</label>
    @include('form.description-html-input')
</div>

<div class="form-group collapsible" component="collapsible" id="logo-control">
    <button refs="collapsible@trigger" type="button" class="collapse-title text-link" aria-expanded="false">
        <label for="tags">{{ trans('entities.chapter_tags') }}</label>
    </button>
    <div refs="collapsible@content" class="collapse-content">
        @include('entities.tag-manager', ['entity' => $chapter ?? null])
    </div>
</div>

<div class="form-group collapsible" component="collapsible" id="template-control">
    <button refs="collapsible@trigger" type="button" class="collapse-title text-link" aria-expanded="false">
        <label for="template-manager">{{ trans('entities.chapter_default_template') }}</label>
    </button>
    <div refs="collapsible@content" class="collapse-content">
        <div class="flex-container-row gap-l justify-space-between pb-xs wrap">
            <p class="text-muted small my-none min-width-xs flex">
                {{ trans('entities.chapter_default_template_explain') }}
            </p>

            <div class="min-width-m">
                @include('form.page-picker', [
                    'name' => 'default_template_id',
                    'placeholder' => trans('entities.chapter_default_template_select'),
                    'value' => $chapter->default_template_id ?? null,
                    'selectorEndpoint' => '/search/entity-selector-templates',
                ])
            </div>
        </div>

    </div>
</div>

<div class="form-group text-right">
    <a href="{{ isset($chapter) ? $chapter->getUrl() : $book->getUrl() }}" class="button outline">{{ trans('common.cancel') }}</a>
    <button type="submit" class="button">{{ trans('entities.chapters_save') }}</button>
</div>

@include('entities.selector-popup')
@include('form.editor-translations')
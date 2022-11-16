<div component="template-manager">
    @if(userCan('templates-manage'))
        <p class="text-muted small mb-none">
            {{ trans('entities.templates_explain_set_as_template') }}
        </p>
        @include('form.toggle-switch', [
               'name' => 'template',
               'value' => old('template', $page->template ? 'true' : 'false') === 'true',
               'label' => trans('entities.templates_set_as_template')
        ])
        <hr>
    @endif

    <div class="search-box flexible mb-m" style="display: {{ count($templates) > 0 ? 'block' : 'none' }}">
        <input refs="template-manager@searchInput" type="text" name="template-search" placeholder="{{ trans('common.search') }}">
        <button refs="template-manager@searchButton" type="button">@icon('search')</button>
        <button refs="template-manager@searchCancel" class="search-box-cancel text-neg" type="button" style="display: none">@icon('close')</button>
    </div>

    <div refs="template-manager@list">
        @include('pages.parts.template-manager-list', ['templates' => $templates])
    </div>
</div>
<div template-manager>
    @if(userCan('templates-manage'))
        <p class="text-muted small mb-none">
            {{ trans('entities.templates_explain_set_as_template') }}
        </p>
        @include('components.toggle-switch', [
               'name' => 'template',
               'value' => old('template', $page->template ? 'true' : 'false') === 'true',
               'label' => trans('entities.templates_set_as_template')
        ])
        <hr>
    @endif

    @if(count($templates) > 0)
        <div class="search-box flexible mb-m">
            <input type="text" name="template-search" placeholder="{{ trans('common.search') }}">
            <button type="button">@icon('search')</button>
            <button class="search-box-cancel text-neg hidden" type="button">@icon('close')</button>
        </div>
    @endif

    <div template-manager-list>
        @include('pages.template-manager-list', ['templates' => $templates])
    </div>
</div>
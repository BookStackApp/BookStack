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
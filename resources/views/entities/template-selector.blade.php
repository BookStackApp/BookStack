<div class="flex-container-row gap-l justify-space-between pb-xs wrap">
    <p class="text-muted small my-none min-width-xs flex">
        {{ trans('entities.default_template_explain') }}
    </p>

    <div class="min-width-m">
        @include('form.page-picker', [
            'name' => 'default_template_id',
            'placeholder' => trans('entities.default_template_select'),
            'value' => $entity->default_template_id ?? null,
            'selectorEndpoint' => '/search/entity-selector-templates',
        ])
    </div>
</div>
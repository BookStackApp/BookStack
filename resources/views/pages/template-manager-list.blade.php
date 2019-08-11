{{ $templates->links() }}

@foreach($templates as $template)
    <div class="card template-item border-card p-m mb-m" draggable="true" template-id="{{ $template->id }}">
        <div class="template-item-content" title="{{ trans('entities.templates_replace_content') }}">
            <div>{{ $template->name }}</div>
            <div class="text-muted">{{ trans('entities.meta_updated', ['timeLength' => $template->updated_at->diffForHumans()]) }}</div>
        </div>
        <div class="template-item-actions">
            <button type="button"
                    title="{{ trans('entities.templates_prepend_content') }}"
                    template-action="prepend">@icon('chevron-up')</button>
            <button type="button"
                    title="{{ trans('entities.templates_append_content') }}"
                    template-action="append">@icon('chevron-down')</button>
        </div>
    </div>
@endforeach

{{ $templates->links() }}
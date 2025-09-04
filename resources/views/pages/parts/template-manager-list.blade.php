{{ $templates->links() }}

@foreach($templates as $template)
    <div class="card template-item border-card p-m mb-m" tabindex="0"
         aria-label="{{ trans('entities.templates_replace_content') }} - {{ $template->name }}"
         draggable="true" template-id="{{ $template->id }}">
        <div class="template-item-content" title="{{ trans('entities.templates_replace_content') }}">
            <div>{{ $template->name }}</div>
            <div class="text-muted" title="{{ $dates->absolute($template->updated_at) }}">{{ trans('entities.meta_updated', ['timeLength' => $dates->relative($template->updated_at)]) }}</div>
        </div>
        <div class="template-item-actions">
            <button type="button"
                    title="{{ trans('entities.templates_prepend_content') }}"
                    aria-label="{{ trans('entities.templates_prepend_content') }} - {{ $template->name }}"
                    template-action="prepend">@icon('chevron-up')</button>
            <button type="button"
                    title="{{ trans('entities.templates_append_content') }}"
                    aria-label="{{ trans('entities.templates_append_content') }} -- {{ $template->name }}"
                    template-action="append">@icon('chevron-down')</button>
        </div>
    </div>
@endforeach

{{ $templates->links() }}
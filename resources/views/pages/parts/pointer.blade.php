
<div component="pointer"
      option:pointer:page-id="{{ $page->id }}">
    <div id="pointer"
         refs="pointer@pointer"
         tabindex="-1"
         aria-label="{{ trans('entities.pages_pointer_label') }}"
         class="pointer-container">
        <div class="pointer flex-container-row items-center justify-space-between p-s anim {{ userCan('page-update', $page) ? 'is-page-editable' : ''}}" >
            <div refs="pointer@mode-section" class="flex-container-row items-center gap-s">
                <button refs="pointer@mode-toggle"
                        title="{{ trans('entities.pages_pointer_toggle_link') }}"
                        class="text-button icon px-xs">@icon('link')</button>
                <div class="input-group">
                    <input refs="pointer@link-input" aria-label="{{ trans('entities.pages_pointer_permalink') }}" readonly="readonly" type="text" id="pointer-url" placeholder="url">
                    <button refs="pointer@link-button" class="button outline icon" type="button" title="{{ trans('entities.pages_copy_link') }}">@icon('copy')</button>
                </div>
            </div>
            <div refs="pointer@mode-section" hidden class="flex-container-row items-center gap-s">
                <button refs="pointer@mode-toggle"
                        title="{{ trans('entities.pages_pointer_toggle_include') }}"
                        class="text-button icon px-xs">@icon('include')</button>
                <div class="input-group">
                    <input refs="pointer@include-input" aria-label="{{ trans('entities.pages_pointer_include_tag') }}" readonly="readonly" type="text" id="pointer-include" placeholder="include">
                    <button refs="pointer@include-button" class="button outline icon" type="button" title="{{ trans('entities.pages_copy_link') }}">@icon('copy')</button>
                </div>
            </div>
            @if(userCan('page-update', $page))
                <a href="{{ $page->getUrl('/edit') }}" id="pointer-edit" data-edit-href="{{ $page->getUrl('/edit') }}"
                   class="button primary outline icon heading-edit-icon ml-s px-s" title="{{ trans('entities.pages_edit_content_link')}}">@icon('edit')</a>
            @endif
        </div>
    </div>

    <button refs="pointer@section-mode-button" class="screen-reader-only">{{ trans('entities.pages_pointer_enter_mode') }}</button>
</div>

<div component="pointer"
     option:pointer:page-id="{{ $page->id }}"
     id="pointer"
     class="pointer-container">
    <div class="pointer anim {{ userCan('page-update', $page) ? 'is-page-editable' : ''}}" >
        <span class="icon mr-xxs">@icon('link') @icon('include', ['style' => 'display:none;'])</span>
        <div class="input-group inline block">
            <input refs="pointer@input" readonly="readonly" type="text" id="pointer-url" placeholder="url">
            <button refs="pointer@button" class="button outline icon" data-clipboard-target="#pointer-url" type="button" title="{{ trans('entities.pages_copy_link') }}">@icon('copy')</button>
        </div>
        @if(userCan('page-update', $page))
            <a href="{{ $page->getUrl('/edit') }}" id="pointer-edit" data-edit-href="{{ $page->getUrl('/edit') }}"
               class="button primary outline icon heading-edit-icon ml-s px-s" title="{{ trans('entities.pages_edit_content_link')}}">@icon('edit')</a>
        @endif
    </div>
</div>
@php /** @var $block class-string<\BookStack\View\ViewBlockInterface> */ @endphp
<li data-block-id="{{ $block::getId() }}"
    class="scroll-box-item items-center">
    <div class="handle px-s">@icon('grip')</div>
    <div class="text-small">{{ $block::getLabel() }}</div>
    <div component="dropdown"
         option:dropdown:fixed-position-menu="true"
         class="dropdown-container ml-auto">
        <button type="button"
                refs="dropdown@toggle"
                aria-haspopup="menu"
                aria-expanded="false"
                class="icon-button p-xxs">@icon('more')</button>
        <ul refs="dropdown@menu layout-editor@action-menu" role="menu" aria-label="{{ trans('common.actions') }}" class="dropdown-menu">
            <li><button type="button" data-action="move_left" class="icon-item">@icon('chevron-left') {{ trans('common.move_left') }}</button></li>
            <li><button type="button" data-action="move_up" class="icon-item">@icon('chevron-up') {{ trans('entities.books_sort_move_up') }}</button></li>
            <li><button type="button" data-action="move_down" class="icon-item">@icon('chevron-down') {{ trans('entities.books_sort_move_down') }}</button></li>
            <li><button type="button" data-action="move_right" class="icon-item">@icon('chevron-right') {{ trans('common.move_right') }}</button></li>
            <li><button type="button" data-action="add" class="icon-item">@icon('add-small') {{ trans('common.add') }}</button></li>
            <li><button type="button" data-action="remove" class="icon-item">@icon('remove') {{ trans('common.remove') }}</button></li>
        </ul>
    </div>
</li>
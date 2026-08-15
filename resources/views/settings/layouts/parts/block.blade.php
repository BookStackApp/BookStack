{{--
$block - ViewBlockInterface - View Block instance to show
--}}
<li data-block-id="{{ $block->getId() }}"
    class="scroll-box-item items-center">
    <div class="handle px-s">@icon('grip')</div>
    <div class="text-small">{{ $block->getLabel() }}</div>
    <div class="buttons flex-container-row items-center ml-auto px-xxs py-xxs">
        <button type="button" data-action="move_left" class="icon-button p-xxs"
                title="{{ trans('common.move_left') }}">@icon('chevron-left')</button>
        <button type="button" data-action="move_up" class="icon-button p-xxs"
                title="{{ trans('entities.books_sort_move_up') }}">@icon('chevron-up')</button>
        <button type="button" data-action="move_down" class="icon-button p-xxs"
                title="{{ trans('entities.books_sort_move_down') }}">@icon('chevron-down')</button>
        <button type="button" data-action="move_right" class="icon-button p-xxs"
                title="{{ trans('common.move_right') }}">@icon('chevron-right')</button>
        <button type="button" data-action="add" class="icon-button p-xxs"
                title="{{ trans('common.add') }}">@icon('add-small')</button>
        <button type="button" data-action="remove" class="icon-button p-xxs"
                title="{{ trans('common.remove') }}">@icon('remove')</button>
    </div>
</li>
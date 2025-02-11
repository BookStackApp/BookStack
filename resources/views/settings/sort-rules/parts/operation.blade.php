<li data-id="{{ $operation->value }}"
    class="scroll-box-item items-center">
    <div class="handle px-s">@icon('grip')</div>
    <div class="text-small">{{ $operation->getLabel() }}</div>
    <div class="buttons flex-container-row items-center ml-auto px-xxs py-xxs">
        <button type="button" data-action="move_up" class="icon-button p-xxs"
                title="{{ trans('entities.books_sort_move_up') }}">@icon('chevron-up')</button>
        <button type="button" data-action="move_down" class="icon-button p-xxs"
                title="{{ trans('entities.books_sort_move_down') }}">@icon('chevron-down')</button>
        <button type="button" data-action="remove" class="icon-button p-xxs"
                title="{{ trans('common.remove') }}">@icon('remove')</button>
        <button type="button" data-action="add" class="icon-button p-xxs"
                title="{{ trans('common.add') }}">@icon('add-small')</button>
    </div>
</li>
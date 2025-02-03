
<div class="setting-list">
    <div class="grid half">
        <div>
            <label class="setting-list-label">{{ trans('settings.sort_set_details') }}</label>
            <p class="text-muted text-small">{{ trans('settings.sort_set_details_desc') }}</p>
        </div>
        <div>
            <div class="form-group">
                <label for="name">{{ trans('common.name') }}</label>
                @include('form.text', ['name' => 'name'])
            </div>
        </div>
    </div>

    <div>
        <label class="setting-list-label">{{ trans('settings.sort_set_operations') }}</label>
        <p class="text-muted text-small">{{ trans('settings.sort_set_operations_desc') }}</p>



        <div class="grid half">
            <div class="form-group">
                <label for="books" id="sort-set-configured-operations">{{ trans('settings.sort_set_configured_operations') }}</label>
                <ul refs="sort-set@configured-operations-list"
                    aria-labelledby="sort-set-configured-operations"
                    class="scroll-box">
                    @foreach(($model?->getOptions() ?? []) as $option)
                        <li data-id="{{ $option->value }}"
                            class="scroll-box-item">
                            <div class="handle px-s">@icon('grip')</div>
                            <div>{{ $option->getLabel() }}</div>
                            <div class="buttons flex-container-row items-center ml-auto px-xxs py-xs">
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
                    @endforeach
                </ul>
            </div>

            <div class="form-group">
                <label for="books" id="sort-set-available-operations">{{ trans('settings.sort_set_available_operations') }}</label>
                <ul refs="sort-set@available-operations-list"
                    aria-labelledby="sort-set-available-operations"
                    class="scroll-box">
                    @foreach(\BookStack\Sorting\SortSetOption::allExcluding($model?->getOptions() ?? []) as $option)
                        <li data-id="{{ $option->value }}"
                            class="scroll-box-item">
                            <div class="handle px-s">@icon('grip')</div>
                            <div>{{ $option->getLabel() }}</div>
                            <div class="buttons flex-container-row items-center ml-auto px-xxs py-xs">
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
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
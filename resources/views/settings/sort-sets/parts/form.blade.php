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

    <div component="sort-set-manager">
        <label class="setting-list-label">{{ trans('settings.sort_set_operations') }}</label>
        <p class="text-muted text-small">{{ trans('settings.sort_set_operations_desc') }}</p>

        <input refs="sort-set-manager@input" type="hidden" name="books"
               value="{{ $model?->sequence ?? '' }}">

        <div class="grid half">
            <div class="form-group">
                <label for="books"
                       id="sort-set-configured-operations">{{ trans('settings.sort_set_configured_operations') }}</label>
                <ul refs="sort-set-manager@configured-operations-list"
                    aria-labelledby="sort-set-configured-operations"
                    class="scroll-box configured-option-list">
                    <li class="text-muted empty-state px-m py-s italic text-small">{{ trans('settings.sort_set_configured_operations_empty') }}</li>
                    @foreach(($model?->getOperations() ?? []) as $option)
                        @include('settings.sort-sets.parts.operation')
                    @endforeach
                </ul>
            </div>

            <div class="form-group">
                <label for="books"
                       id="sort-set-available-operations">{{ trans('settings.sort_set_available_operations') }}</label>
                <ul refs="sort-set-manager@available-operations-list"
                    aria-labelledby="sort-set-available-operations"
                    class="scroll-box available-option-list">
                    <li class="text-muted empty-state px-m py-s italic text-small">{{ trans('settings.sort_set_available_operations_empty') }}</li>
                    @foreach(\BookStack\Sorting\SortSetOperation::allExcluding($model?->getOperations() ?? []) as $operation)
                        @include('settings.sort-sets.parts.operation', ['operation' => $operation])
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
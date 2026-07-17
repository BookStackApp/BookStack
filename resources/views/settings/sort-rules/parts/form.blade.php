<div class="setting-list">
    <div class="grid half">
        <div>
            <label class="setting-list-label">{{ trans('settings.sort_rule_details') }}</label>
            <p class="text-muted text-small">{{ trans('settings.sort_rule_details_desc') }}</p>
        </div>
        <div>
            <div class="form-group">
                <label for="name">{{ trans('common.name') }}</label>
                @include('form.text', ['name' => 'name'])
            </div>
        </div>
    </div>

    <div component="sort-rule-manager">
        <label class="setting-list-label">{{ trans('settings.sort_rule_operations') }}</label>
        <p class="text-muted text-small">{{ trans('settings.sort_rule_operations_desc') }}</p>
        @include('form.errors', ['name' => 'sequence'])

        <input refs="sort-rule-manager@input" type="hidden" name="sequence"
               value="{{ old('sequence') ?? $model?->sequence ?? '' }}">

        @php
            $configuredOps = old('sequence') ? \BookStack\Sorting\SortRuleOperation::fromSequence(old('sequence')) : ($model?->getOperations() ?? []);
        @endphp

        <div class="grid half">
            <div class="form-group">
                <label for="books"
                       id="sort-rule-configured-operations">{{ trans('settings.sort_rule_configured_operations') }}</label>
                <ul refs="sort-rule-manager@configured-operations-list"
                    aria-labelledby="sort-rule-configured-operations"
                    class="scroll-box configured-option-list">
                    <li class="text-muted empty-state px-m py-s italic text-small">{{ trans('settings.sort_rule_configured_operations_empty') }}</li>

                    @foreach($configuredOps as $operation)
                        @include('settings.sort-rules.parts.operation', ['operation' => $operation])
                    @endforeach
                </ul>
            </div>

            <div class="form-group">
                <label for="books"
                       id="sort-rule-available-operations">{{ trans('settings.sort_rule_available_operations') }}</label>
                <ul refs="sort-rule-manager@available-operations-list"
                    aria-labelledby="sort-rule-available-operations"
                    class="scroll-box available-option-list">
                    <li class="text-muted empty-state px-m py-s italic text-small">{{ trans('settings.sort_rule_available_operations_empty') }}</li>
                    @foreach(\BookStack\Sorting\SortRuleOperation::allExcluding($configuredOps) as $operation)
                        @include('settings.sort-rules.parts.operation', ['operation' => $operation])
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
</div>
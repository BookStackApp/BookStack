<div id="entity-selector-wrap">
    <div components="popup entity-selector-popup" class="popup-background">
        <div class="popup-body small" tabindex="-1">
            <div class="popup-header primary-background">
                <div class="popup-title">{{ trans('entities.entity_select') }}</div>
                <button refs="popup@hide" type="button" class="popup-header-close">@icon('close')</button>
            </div>
            @include('entities.selector', ['name' => 'entity-selector'])
            <div class="popup-footer">
                <button refs="entity-selector-popup@select" type="button" disabled="true" class="button">{{ trans('common.select') }}</button>
            </div>
        </div>
    </div>
</div>
<div components="popup confirm-dialog"
     refs="confirm-dialog@popup"
     class="popup-background">
    <div class="popup-body very-small" tabindex="-1">

        <div class="popup-header primary-background">
            <div class="popup-title">{{ $title }}</div>
            <button refs="popup@hide" type="button" class="popup-header-close">x</button>
        </div>

        <div class="px-m py-m">
            {{ $slot }}

            <div class="text-right">
                <button type="button" class="button outline" refs="popup@hide">{{ trans('common.cancel') }}</button>
                <button type="button" class="button" refs="confirm-dialog@confirm">{{ trans('common.continue') }}</button>
            </div>
        </div>

    </div>
</div>